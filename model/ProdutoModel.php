<?php

class ProdutoModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarTodos(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM produto");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produto WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cadastrarOuSomar(string $nome, float $valor, int $quantidade, string $validade): void
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM produto WHERE nome = ? AND validade = ?"
        );
        $stmt->execute([$nome, $validade]);

        $produtoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produtoExistente) {
            $novaQuantidade = $produtoExistente['quantidade'] + $quantidade;

            $stmt = $this->pdo->prepare(
                "UPDATE produto SET valor = ?, quantidade = ? WHERE id = ?"
            );
            $stmt->execute([
                $valor,
                $novaQuantidade,
                $produtoExistente['id']
            ]);

            return;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO produto (nome, valor, quantidade, validade)
             VALUES (:nome, :valor, :quantidade, :validade)"
        );

        $stmt->execute([
            ':nome' => $nome,
            ':valor' => $valor,
            ':quantidade' => $quantidade,
            ':validade' => $validade
        ]);
    }

    public function atualizar(
        int $id,
        string $nome,
        float $valor,
        int $quantidade,
        string $validade
    ): void {
        $stmt = $this->pdo->prepare(
            "UPDATE produto
             SET nome = ?, valor = ?, validade = ?, quantidade = ?
             WHERE id = ?"
        );

        $stmt->execute([
            $nome,
            $valor,
            $validade,
            $quantidade,
            $id
        ]);
    }

    public function excluir(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM produto WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function finalizarCompra(array $carrinho): void
    {
        $this->pdo->beginTransaction();

        try {
            foreach ($carrinho as $item) {
                $id = (int) $item['id'];
                $quantidade = (int) $item['quantidade'];

                $stmt = $this->pdo->prepare(
                    "SELECT quantidade FROM produto WHERE id = ? FOR UPDATE"
                );
                $stmt->execute([$id]);

                $produto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$produto) {
                    throw new Exception("Produto com ID $id não encontrado.");
                }

                if ($quantidade > $produto['quantidade']) {
                    throw new Exception("Estoque insuficiente para o produto ID $id.");
                }

                $novaQuantidade = $produto['quantidade'] - $quantidade;

                if ($novaQuantidade === 0) {
                    $stmt = $this->pdo->prepare(
                        "DELETE FROM produto WHERE id = ?"
                    );
                    $stmt->execute([$id]);
                } else {
                    $stmt = $this->pdo->prepare(
                        "UPDATE produto SET quantidade = ? WHERE id = ?"
                    );
                    $stmt->execute([$novaQuantidade, $id]);
                }
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
