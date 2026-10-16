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
                $produto->getQuantidade(),            ]);
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
            $produto->getQuantidade(),            $produto->getId()
        ]);
    }

    public function excluir($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM produto WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function finalizarCompra($carrinho)
    {
        $this->pdo->beginTransaction();

        try {
            foreach ($carrinho as $item) {
                $id = $item['id'];
                $quantidade = $item['quantidade'];

                $stmt = $this->pdo->prepare("SELECT quantidade FROM produto WHERE id = ? FOR UPDATE");
                $stmt->execute([$id]);
                $produto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$produto) {
                    throw new Exception("Produto com ID $id não encontrado.");
                }

                $novaQuantidade = $produto['quantidade'] - $quantidade;

                if ($novaQuantidade < 0) {
                    throw new Exception("Estoque insuficiente para o produto ID $id.");
                }

                if ($novaQuantidade == 0) {
                    $stmt = $this->pdo->prepare("DELETE FROM produto WHERE id = ?");
                    $stmt->execute([$id]);
                } else {
                    $stmt = $this->pdo->prepare("UPDATE produto SET quantidade = ? WHERE id = ?");
                    $stmt->execute([$novaQuantidade, $id]);
                }
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}