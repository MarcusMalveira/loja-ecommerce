<?php
$host = getenv('DB_HOST') ?: 'localhost';
$banco = getenv('DB_NAME') ?: 'produtos';
$usuario = getenv('DB_USER') ?: 'root';
$senha = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$porta = getenv('DB_PORT') ?: '3306';

$opcoes = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
$caBase64 = getenv('DB_SSL_CA_BASE64');
if ($caBase64 !== false && $caBase64 !== '') {
    $certificado = base64_decode($caBase64, true);
    if ($certificado === false || strpos($certificado, 'BEGIN CERTIFICATE') === false) {
        http_response_code(503);
        exit('Certificado SSL do banco inválido.');
    }
    $caminhoCA = sys_get_temp_dir() . '/techloja-aiven-ca.pem';
    if (file_put_contents($caminhoCA, $certificado, LOCK_EX) === false) {
        http_response_code(503);
        exit('Não foi possível configurar o certificado SSL.');
    }
    $opcoes[PDO::MYSQL_ATTR_SSL_CA] = $caminhoCA;
    $opcoes[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
}
if ($host !== 'localhost' && empty($opcoes[PDO::MYSQL_ATTR_SSL_CA])) {
    http_response_code(503);
    exit('Configure DB_SSL_CA_BASE64 para conectar ao banco remoto com TLS verificado.');
}
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        $opcoes
    );
} catch (PDOException $e) {
    error_log('Falha de conexão ao banco: '.$e->getMessage());
    http_response_code(503);
    exit('Banco de dados indisponível. Confira a configuração do ambiente.');
}
