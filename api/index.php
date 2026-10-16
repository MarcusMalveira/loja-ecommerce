<?php
// Ponto de entrada para a Vercel. No XAMPP, index.php continua funcionando normalmente.
$rota = basename(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
switch ($rota) {
    case '':
    case 'index.php':
    case 'ProdutoController.php':
        require __DIR__ . '/../ProdutoController.php';
        break;
    case 'CarrinhoController.php':
        require __DIR__ . '/../CarrinhoController.php';
        break;
    default:
        http_response_code(404);
        echo 'Página não encontrada.';
}
