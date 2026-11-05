
<?php

class UsuarioDAO {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function buscarPorEmail(string $email): ?array {
        $sql = "SELECT id, nome, email, senha_hash, tipo
                FROM usuario
                WHERE email = :email
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ?: null;
    }

    public function cadastrar(
        string $nome,
        string $email,
        string $senha
    ): bool {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario
                (nome, email, senha_hash, tipo)
                VALUES (:nome, :email, :senha_hash, 'cliente')";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha_hash' => $hash
        ]);
    }
}
