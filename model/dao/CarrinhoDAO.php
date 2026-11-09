<?php

class CarrinhoDAO
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Salva ou atualiza um produto no carrinho do usuário
    public function salvarItem(
        int $usuarioId,
        int $produtoId,
        int $quantidade
    ): void {
        $sql = "
            INSERT INTO carrinho_item (
                usuario_id,
                produto_id,
                quantidade
            )
            VALUES (
                :usuario_id,
                :produto_id,
                :quantidade
            )
            ON DUPLICATE KEY UPDATE
                quantidade = VALUES(quantidade)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':produto_id' => $produtoId,
            ':quantidade' => $quantidade
        ]);
    }

    // Retorna os itens salvos de um usuário
    public function buscarPorUsuario(int $usuarioId): array
    {
        $sql = "
            SELECT
                ci.produto_id AS id,
                p.nome,
                p.valor,
                ci.quantidade
            FROM carrinho_item ci
            INNER JOIN produto p
                ON p.id = ci.produto_id
            WHERE ci.usuario_id = :usuario_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Remove um produto do carrinho salvo
    public function removerItem(
        int $usuarioId,
        int $produtoId
    ): void {
        $sql = "
            DELETE FROM carrinho_item
            WHERE usuario_id = :usuario_id
              AND produto_id = :produto_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':produto_id' => $produtoId
        ]);
    }

    // Limpa o carrinho salvo de um usuário
    public function limparPorUsuario(int $usuarioId): void
    {
        $sql = "
            DELETE FROM carrinho_item
            WHERE usuario_id = :usuario_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':usuario_id' => $usuarioId
        ]);
    }
}
