<?php
$host = getenv('DB_HOST') ?: 'localhost';
$banco = getenv('DB_NAME') ?: 'produtos';
$usuario = getenv('DB_USER') ?: 'root';
$senha = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$porta = getenv('DB_PORT') ?: '3306';
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    error_log('Falha de conexão ao banco: '.$e->getMessage());
    http_response_code(503);
    exit('Banco de dados indisponível. Confira a configuração do ambiente.');
}
