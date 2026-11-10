<?php

if (session_status() !== PHP_SESSION_ACTIVE) {

    // Na Vercel, a aplicação utiliza HTTPS.
    // No XAMPP local, HTTP continua permitido.
    $https = (
        (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    );

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}
