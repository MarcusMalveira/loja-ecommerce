<?php

require_once __DIR__ . '/../../config/conexao.php';
require_once __DIR__ . '/../dto/ProdutoDTO.php';

class ProdutoDAO
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarTodos()
    {
        $stmt = $this->pdo->query("SELECT * FROM produto");
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $produtos = [];

        foreach ($dados as $linha) {
            $produto = new ProdutoDTO();
            $produto->setId($linha['id']);
            $produto->setNome($linha['nome']);
            $produto->setValor($linha['valor']);
            $produto->setQuantidade($linha['quantidade']);

            $produtos[] = $produto;
        }

        return $produtos;
    }

    public function buscarPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produto WHERE id = ?");
        $stmt->execute([$id]);

        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$linha) {
            return null;
        }

        $produto = new ProdutoDTO();
        $produto->setId($linha['id']);
        $produto->setNome($linha['nome']);
        $produto->setValor($linha['valor']);
        $produto->setQuantidade($linha['quantidade']);

        return $produto;
    }

    public function cadastrarOuSomar(ProdutoDTO $produto)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM produto
            WHERE nome = ?
        ");
        $stmt->execute([$produto->getNome()]);

        $produtoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produtoExistente) {
            $novaQuantidade = $produtoExistente['quantidade'] + $produto->getQuantidade();

            $stmt = $this->pdo->prepare("
                UPDATE produto
                SET valor = ?, quantidade = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $produto->getValor(),
                $novaQuantidade,
                $produtoExistente['id']
            ]);
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO produto (nome, valor, quantidade)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $produto->getNome(),
                $produto->getValor(),
                $produto->getQuantidade(),
            ]);
        }
    }

    public function atualizar(ProdutoDTO $produto)
    {
        $stmt = $this->pdo->prepare("
            UPDATE produto
            SET nome = ?, valor = ?, quantidade = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $produto->getNome(),
            $produto->getValor(),
            $produto->getQuantidade(),
            $produto->getId()
        ]);
    }

    public function excluir($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM produto WHERE id = ?");
        $stmt->execute([$id]);
    }

    
public function finalizarCompra(array $carrinho, int $usuarioId): void
{
    if ($usuarioId <= 0 || empty($carrinho)) {
        throw new InvalidArgumentException(
            'Usuário ou carrinho inválido.'
        );
    }

    $this->pdo->beginTransaction();

    try {
        // Bloqueia os produtos e atualiza o estoque.
        foreach ($carrinho as $item) {
            $id = (int) ($item['id'] ?? 0);
            $quantidade = (int) ($item['quantidade'] ?? 0);

            if ($id <= 0 || $quantidade <= 0) {
                throw new InvalidArgumentException(
                    'Item do carrinho inválido.'
                );
            }

            $stmt = $this->pdo->prepare(
                "SELECT quantidade
                 FROM produto
                 WHERE id = ?
                 FOR UPDATE"
            );

            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                throw new RuntimeException(
                    "Produto com ID $id não encontrado."
                );
            }

            if ($quantidade > (int) $produto['quantidade']) {
                throw new RuntimeException(
                    "Estoque insuficiente para o produto ID $id."
                );
            }

            $novaQuantidade =
                (int) $produto['quantidade'] - $quantidade;

            $stmt = $this->pdo->prepare(
                "UPDATE produto
                 SET quantidade = ?
                 WHERE id = ?"
            );

            $stmt->execute([$novaQuantidade, $id]);
        }

        // Limpa o carrinho persistido na MESMA transação.
        $stmt = $this->pdo->prepare(
            "DELETE FROM carrinho_item
             WHERE usuario_id = ?"
        );

        $stmt->execute([$usuarioId]);

        // Confirma as duas operações juntas.
        $this->pdo->commit();

    } catch (Throwable $e) {

        // Desfaz as alterações do banco em caso de falha.
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        throw $e;
    }
}

}