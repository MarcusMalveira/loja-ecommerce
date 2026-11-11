<?php
// Nenhum caractere (nem linha em branco) deve existir antes de <?php.
require_once __DIR__ . '/erros.php';
require_once __DIR__ . '/conexao.php'; // Disponibiliza $pdo (PDO).
require_once __DIR__ . '/sessao_banco.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $https = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    );

    // Os mesmos 2 h de inatividade definidos no manipulador MySQL.
    ini_set('session.gc_maxlifetime', '7200');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_trans_sid', '0');

    // A conexão com o MySQL deve existir ANTES de session_start().
    // O PHP grava a sessão no encerramento da requisição ou em session_write_close().
    if (!session_set_save_handler(new SessaoBanco($pdo), true)) {
        throw new RuntimeException('Não foi possível configurar o armazenamento da sessão.');
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    // Não prossiga como se o visitante estivesse autenticado se a sessão falhar.
    if (session_start() !== true) {
        throw new RuntimeException('Não foi possível iniciar a sessão.');
    }
}
