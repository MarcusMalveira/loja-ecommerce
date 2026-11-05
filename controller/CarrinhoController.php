<?php

session_start();

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/dao/ProdutoDAO.php';
require_once __DIR__ . '/../model/dto/ProdutoDTO.php';

$produtoDAO = new ProdutoDAO($pdo);

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$acao = $_GET['acao'] ?? 'ver';

switch ($acao) {
    case 'ver':
        $carrinho = $_SESSION['carrinho'];
        $total = 0;
        $ga4Adicionado = $_SESSION['ga4_add_to_cart'] ?? null;
        unset($_SESSION['ga4_add_to_cart']);
        require __DIR__ . '/../view/carrinho.php';
        break;

    case 'adicionar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produtoId = (int) $_POST['produto_id'];
            $quantidade = (int) $_POST['quantidade'];

            $produto = $produtoDAO->buscarPorId($produtoId);

            if (!$produto) {
                die("Produto não encontrado.");
            }

            if ($quantidade < 1 || $quantidade > $produto->getQuantidade()) {
                die("Quantidade inválida ou estoque insuficiente.");
            }

            $produtoEncontrado = false;

            foreach ($_SESSION['carrinho'] as &$itemCarrinho) {
                if ($itemCarrinho['id'] == $produto->getId()) {
                    $novaQuantidade = $itemCarrinho['quantidade'] + $quantidade;

                    if ($novaQuantidade > $produto->getQuantidade()) {
                        die("Estoque insuficiente.");
                    }

                    $itemCarrinho['quantidade'] = $novaQuantidade;
                    $produtoEncontrado = true;
                    break;
                }
            }
            unset($itemCarrinho);

            if (!$produtoEncontrado) {
                $_SESSION['carrinho'][] = [
                    'id' => $produto->getId(),
                    'nome' => $produto->getNome(),
                    'valor' => $produto->getValor(),
                    'quantidade' => $quantidade
                ];
            }

            // Evento pendente: somente após a inclusão ser validada no servidor.
            $_SESSION['ga4_add_to_cart'] = [
                'currency' => 'BRL',
                'value' => round((float) $produto->getValor() * $quantidade, 2),
                'items' => [[
                    'item_id' => (string) $produto->getId(),
                    'item_name' => $produto->getNome(),
                    'price' => (float) $produto->getValor(),
                    'quantity' => $quantidade
                ]]
            ];
        }

        header('Location: CarrinhoController.php?acao=ver');
        exit;

    case 'alterar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) $_POST['id'];
            $operacao = $_POST['operacao'];
            $produtoBanco = $produtoDAO->buscarPorId($id);

            foreach ($_SESSION['carrinho'] as $indice => &$item) {
                if ($item['id'] == $id) {
                    if ($operacao === 'aumentar' && $produtoBanco) {
                        if ($item['quantidade'] < $produtoBanco->getQuantidade()) {
                            $item['quantidade']++;
                        }
                    } elseif ($operacao === 'diminuir') {
                        $item['quantidade']--;

                        if ($item['quantidade'] <= 0) {
                            unset($_SESSION['carrinho'][$indice]);
                        }
                    } elseif ($operacao === 'definir' && $produtoBanco) {
                        $novaQuantidade = (int) $_POST['quantidade'];

                        if ($novaQuantidade <= 0) {
                            unset($_SESSION['carrinho'][$indice]);
                        } elseif ($novaQuantidade <= $produtoBanco->getQuantidade()) {
                            $item['quantidade'] = $novaQuantidade;
                        }
                    }
                    break;
                }
            }

            unset($item);
            $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
        }

        header('Location: CarrinhoController.php?acao=ver');
        exit;

    case 'limpar':
        $_SESSION['carrinho'] = [];
        header('Location: CarrinhoController.php?acao=ver');
        exit;

    case 'finalizar':        
        if (!isset($_SESSION['usuario'])) {
            header('Location: UsuarioController.php?acao=formLogin');
            exit;
        }
        if (empty($_SESSION['carrinho'])) {
            die("O carrinho está vazio.");
        }

        try {
            $itensCompra = array_values($_SESSION['carrinho']);
            $produtoDAO->finalizarCompra($itensCompra);
            $ga4Compra = [
                'transaction_id' => 'DEMO-' . bin2hex(random_bytes(8)),
                'currency' => 'BRL',
                'value' => round(array_reduce($itensCompra, static function ($total, $item) {
                    return $total + (float) $item['valor'] * (int) $item['quantidade'];
                }, 0.0), 2),
                'items' => array_map(static function ($item) {
                    return [
                        'item_id' => (string) $item['id'],
                        'item_name' => $item['nome'],
                        'price' => (float) $item['valor'],
                        'quantity' => (int) $item['quantidade']
                    ];
                }, $itensCompra)
            ];
            $_SESSION['carrinho'] = [];
            require __DIR__ . '/../view/finalizar_compra.php';
        } catch (Exception $e) {
            die("Erro ao finalizar a compra: " . $e->getMessage());
        }
        break;
}