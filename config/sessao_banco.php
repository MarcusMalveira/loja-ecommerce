<?php

/**
 * Sessões PHP persistidas no MySQL (Aiven no deploy; MariaDB no XAMPP).
 * Um bloqueio nomeado por ID impede que duas requisições sobrescrevam a
 * mesma sessão. O bloqueio dura até close()/session_write_close().
 *
 * Requer uma tabela `sessoes` criada pelo script SQL entregue junto.
 */
final class SessaoBanco implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    private PDO $pdo;
    private int $duracao;
    private int $tempoEsperaBloqueio;

    /** @var array<string, true> Nomes dos bloqueios mantidos nesta requisição. */
    private array $bloqueios = [];

    public function __construct(PDO $pdo, int $duracao = 7200, int $tempoEsperaBloqueio = 5)
    {
        $this->pdo = $pdo;
        $this->duracao = $duracao;
        $this->tempoEsperaBloqueio = $tempoEsperaBloqueio;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        $sucesso = true;

        // Na regeneração do ID, o PHP pode utilizar mais de um ID na requisição.
        foreach (array_reverse(array_keys($this->bloqueios)) as $nome) {
            try {
                $consulta = $this->pdo->prepare('SELECT RELEASE_LOCK(:nome)');
                $consulta->execute([':nome' => $nome]);
                if ((int) $consulta->fetchColumn() !== 1) {
                    $sucesso = false;
                    error_log('SessaoBanco: não foi possível liberar um bloqueio.');
                }
            } catch (Throwable $e) {
                $sucesso = false;
                error_log('SessaoBanco::close ' . $e->getMessage());
            }
        }

        $this->bloqueios = [];
        return $sucesso;
    }

    private function nomeBloqueio(string $id): string
    {
        // Mantém o nome com menos de 64 caracteres, sem expor o ID diretamente.
        return 'techloja_sess_' . sha1($id);
    }

    private function bloquear(string $id): bool
    {
        $nome = $this->nomeBloqueio($id);
        if (isset($this->bloqueios[$nome])) {
            return true;
        }

        try {
            $consulta = $this->pdo->prepare('SELECT GET_LOCK(:nome, :espera)');
            $consulta->bindValue(':nome', $nome, PDO::PARAM_STR);
            $consulta->bindValue(':espera', $this->tempoEsperaBloqueio, PDO::PARAM_INT);
            $consulta->execute();

            if ((int) $consulta->fetchColumn() !== 1) {
                error_log('SessaoBanco: tempo esgotado ao bloquear a sessão.');
                return false;
            }

            $this->bloqueios[$nome] = true;
            return true;
        } catch (Throwable $e) {
            error_log('SessaoBanco::bloquear ' . $e->getMessage());
            return false;
        }
    }

    public function read(string $id): string|false
    {
        if (!$this->bloquear($id)) {
            // Falha fechada: não devolve uma sessão vazia como se tudo estivesse OK.
            return false;
        }

        try {
            $consulta = $this->pdo->prepare(
                'SELECT dados FROM sessoes
                 WHERE id = :id AND atualizado_em > :limite'
            );
            $consulta->bindValue(':id', $id, PDO::PARAM_STR);
            $consulta->bindValue(':limite', time() - $this->duracao, PDO::PARAM_INT);
            $consulta->execute();
            $dados = $consulta->fetchColumn();

            return $dados === false ? '' : (string) $dados;
        } catch (Throwable $e) {
            error_log('SessaoBanco::read ' . $e->getMessage());
            return false;
        }
    }

    public function write(string $id, string $data): bool
    {
        // Também protege escrita em cenários de regeneração do identificador.
        if (!$this->bloquear($id)) {
            return false;
        }

        try {
            $agora = time();
            $consulta = $this->pdo->prepare(
                'INSERT INTO sessoes (id, dados, atualizado_em)
                 VALUES (:id, :dados, :agora)
                 ON DUPLICATE KEY UPDATE
                     dados = :dados_atualizados,
                     atualizado_em = :agora_atualizado'
            );
            $consulta->bindValue(':id', $id, PDO::PARAM_STR);
            $consulta->bindValue(':dados', $data, PDO::PARAM_LOB);
            $consulta->bindValue(':agora', $agora, PDO::PARAM_INT);
            $consulta->bindValue(':dados_atualizados', $data, PDO::PARAM_LOB);
            $consulta->bindValue(':agora_atualizado', $agora, PDO::PARAM_INT);

            return $consulta->execute();
        } catch (Throwable $e) {
            error_log('SessaoBanco::write ' . $e->getMessage());
            return false;
        }
    }

    public function destroy(string $id): bool
    {
        if (!$this->bloquear($id)) {
            return false;
        }

        try {
            $consulta = $this->pdo->prepare('DELETE FROM sessoes WHERE id = :id');
            return $consulta->execute([':id' => $id]);
        } catch (Throwable $e) {
            error_log('SessaoBanco::destroy ' . $e->getMessage());
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false
    {
        try {
            $consulta = $this->pdo->prepare(
                'DELETE FROM sessoes WHERE atualizado_em <= :limite'
            );
            $consulta->bindValue(':limite', time() - $this->duracao, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->rowCount();
        } catch (Throwable $e) {
            error_log('SessaoBanco::gc ' . $e->getMessage());
            return false;
        }
    }

    public function validateId(string $id): bool
    {
        try {
            $consulta = $this->pdo->prepare(
                'SELECT 1 FROM sessoes
                 WHERE id = :id AND atualizado_em > :limite'
            );
            $consulta->bindValue(':id', $id, PDO::PARAM_STR);
            $consulta->bindValue(':limite', time() - $this->duracao, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetchColumn() !== false;
        } catch (Throwable $e) {
            error_log('SessaoBanco::validateId ' . $e->getMessage());
            return false;
        }
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        if (!$this->bloquear($id)) {
            return false;
        }

        try {
            $consulta = $this->pdo->prepare(
                'UPDATE sessoes SET atualizado_em = :agora WHERE id = :id'
            );
            $consulta->bindValue(':agora', time(), PDO::PARAM_INT);
            $consulta->bindValue(':id', $id, PDO::PARAM_STR);
            // rowCount() == 0 pode significar que o timestamp ainda é igual;
            // por isso não usamos rowCount() para definir sucesso.
            return $consulta->execute();
        } catch (Throwable $e) {
            error_log('SessaoBanco::updateTimestamp ' . $e->getMessage());
            return false;
        }
    }
}
