<?php

session_start();

// ==================================================
// IMPORTAÇÕES
// ==================================================

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/dao/ProdutoDAO.php';
require_once __DIR__ . '/../model/dao/CarrinhoDAO.php';
require_once __DIR__ . '/../model/dto/ProdutoDTO.php';

$produtoDAO = new ProdutoDAO($pdo);
$carrinhoDAO = new CarrinhoDAO($pdo);


// ==================================================
// INICIALIZAÇÃO DA SESSÃO
// ==================================================

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


// ==================================================
// PROTEÇÃO CSRF
// ==================================================

function validarRequisicaoPost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Método não permitido.');
    }

    $tokenEnviado = $_POST['csrf_token'] ?? '';
    $tokenSessao = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($tokenEnviado) ||
        !is_string($tokenSessao) ||
        !hash_equals($tokenSessao, $tokenEnviado)
    ) {
        http_response_code(403);
        exit('Token CSRF inválido.');
    }
}


// ==================================================
// SINCRONIZAÇÃO COM O BANCO
// ==================================================

function sincronizarCarrinho(
    CarrinhoDAO $carrinhoDAO,
    int $usuarioId,
    array $carrinho
): void {

    // Substitui os itens salvos pelos itens da sessão.
    // A transação evita uma atualização parcial.
    $pdo = $GLOBALS['pdo'];

    $pdo->beginTransaction();

    try {
        $carrinhoDAO->limparPorUsuario($usuarioId);

        foreach ($carrinho as $item) {
            $carrinhoDAO->salvarItem(
                $usuarioId,
                (int) $item['id'],
                (int) $item['quantidade']
            );
        }

        $pdo->commit();

    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}


// ==================================================
// AÇÕES DO CARRINHO
// ==================================================

$acao = $_GET['acao'] ?? 'ver';

switch ($acao) {

    // ----------------------------------------------
    // VISUALIZAR CARRINHO
    // ----------------------------------------------

    case 'ver':

        $carrinho = $_SESSION['carrinho'];
        $total = 0;

        $ga4Adicionado =
            $_SESSION['ga4_add_to_cart'] ?? null;

        unset($_SESSION['ga4_add_to_cart']);

        require __DIR__ . '/../view/carrinho.php';
        break;


    // ----------------------------------------------
    // ADICIONAR PRODUTO
    // ----------------------------------------------

    case 'adicionar':

        validarRequisicaoPost();

        $produtoId = (int) ($_POST['produto_id'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 0);

        $produto = $produtoDAO->buscarPorId($produtoId);

        if (!$produto) {
            http_response_code(404);
            exit('Produto não encontrado.');
        }

        if (
            $quantidade < 1 ||
            $quantidade > $produto->getQuantidade()
        ) {
            http_response_code(400);
            exit('Quantidade inválida ou estoque insuficiente.');
        }

        $produtoEncontrado = false;

        // Verifica se o produto já está no carrinho.
        foreach ($_SESSION['carrinho'] as &$itemCarrinho) {

            if (
                (int) $itemCarrinho['id'] ===
                (int) $produto->getId()
            ) {

                $novaQuantidade =
                    (int) $itemCarrinho['quantidade'] +
                    $quantidade;

                if (
                    $novaQuantidade >
                    $produto->getQuantidade()
                ) {
                    http_response_code(400);
                    exit('Estoque insuficiente.');
                }

                $itemCarrinho['quantidade'] =
                    $novaQuantidade;

                $produtoEncontrado = true;
                break;
            }
        }

        unset($itemCarrinho);

        // Adiciona o produto se ainda não existir.
        if (!$produtoEncontrado) {

            $_SESSION['carrinho'][] = [
                'id' => $produto->getId(),
                'nome' => $produto->getNome(),
                'valor' => $produto->getValor(),
                'quantidade' => $quantidade
            ];
        }

        // Salva no MySQL quando estiver autenticado.
        if (isset($_SESSION['usuario']['id'])) {

            sincronizarCarrinho(
                $carrinhoDAO,
                (int) $_SESSION['usuario']['id'],
                $_SESSION['carrinho']
            );
        }

        // Evento GA4: produto adicionado.
        $_SESSION['ga4_add_to_cart'] = [
            'currency' => 'BRL',
            'value' => round(
                (float) $produto->getValor() * $quantidade,
                2
            ),
            'items' => [[
                'item_id' => (string) $produto->getId(),
                'item_name' => $produto->getNome(),
                'price' => (float) $produto->getValor(),
                'quantity' => $quantidade
            ]]
        ];

        header('Location: CarrinhoController.php?acao=ver');
        exit;


    // ----------------------------------------------
    // ALTERAR QUANTIDADE
    // ----------------------------------------------

    case 'alterar':

        validarRequisicaoPost();

        $id = (int) ($_POST['id'] ?? 0);
        $operacao = $_POST['operacao'] ?? '';

        $produtoBanco = $produtoDAO->buscarPorId($id);

        foreach (
            $_SESSION['carrinho'] as $indice => &$item
        ) {

            if ((int) $item['id'] !== $id) {
                continue;
            }

            if ($operacao === 'aumentar' && $produtoBanco) {

                if (
                    (int) $item['quantidade'] <
                    $produtoBanco->getQuantidade()
                ) {
                    $item['quantidade']++;
                }

            } elseif ($operacao === 'diminuir') {

                $item['quantidade']--;

                if ($item['quantidade'] <= 0) {
                    unset($_SESSION['carrinho'][$indice]);
                }

            } elseif (
                $operacao === 'definir' &&
                $produtoBanco
            ) {

                $novaQuantidade =
                    (int) ($_POST['quantidade'] ?? 0);

                if ($novaQuantidade <= 0) {

                    unset($_SESSION['carrinho'][$indice]);

                } elseif (
                    $novaQuantidade <=
                    $produtoBanco->getQuantidade()
                ) {
                    $item['quantidade'] = $novaQuantidade;
                }
            }

            break;
        }

        unset($item);

        $_SESSION['carrinho'] = array_values(
            $_SESSION['carrinho']
        );

        // Atualiza as quantidades no banco.
        if (isset($_SESSION['usuario']['id'])) {

            sincronizarCarrinho(
                $carrinhoDAO,
                (int) $_SESSION['usuario']['id'],
                $_SESSION['carrinho']
            );
        }

        header('Location: CarrinhoController.php?acao=ver');
        exit;


    // ----------------------------------------------
    // LIMPAR CARRINHO
    // ----------------------------------------------

    case 'limpar':

        validarRequisicaoPost();

        // Remove os itens persistidos primeiro.
        if (isset($_SESSION['usuario']['id'])) {

            $carrinhoDAO->limparPorUsuario(
                (int) $_SESSION['usuario']['id']
            );
        }

        $_SESSION['carrinho'] = [];

        header('Location: CarrinhoController.php?acao=ver');
        exit;


    // ----------------------------------------------
    // FINALIZAR COMPRA
    // ----------------------------------------------

    case 'finalizar':

        validarRequisicaoPost();
        
        // Validação do método de pagamento simulado
        $metodosPermitidos = [
            'pix',
            'cartao',
            'carteira_digital'
        ];

        $metodoPagamento = $_POST['metodo_pagamento'] ?? '';

        if (
            !is_string($metodoPagamento) ||
            !in_array(
                $metodoPagamento,
                $metodosPermitidos,
                true
            )
        ) {
            http_response_code(400);
            exit('Selecione uma forma de pagamento válida.');
        }

        // Descrição apresentada na confirmação
        $nomesPagamento = [
            'pix' => 'Pix',
            'cartao' => 'Cartão de crédito ou débito',
            'carteira_digital' => 'Carteira digital'
        ];

        $pagamentoSelecionado =
            $nomesPagamento[$metodoPagamento];


        // Exige autenticação.
        if (!isset($_SESSION['usuario']['id'])) {

            header(
                'Location: UsuarioController.php?acao=formLogin'
            );
            exit;
        }

        if (empty($_SESSION['carrinho'])) {
            http_response_code(400);
            exit('O carrinho está vazio.');
        }

        try {

            $itensCompra = array_values(
                $_SESSION['carrinho']
            );

            $produtoDAO->finalizarCompra(
            $itensCompra,
            (int) $_SESSION['usuario']['id']
                );

            // Monta os dados do evento GA4.
            $ga4Compra = [
                'transaction_id' =>
                    'DEMO-' . bin2hex(random_bytes(8)),

                'currency' => 'BRL',

                'value' => round(
                    array_reduce(
                        $itensCompra,
                        static function ($total, $item) {
                            return $total +
                                (float) $item['valor'] *
                                (int) $item['quantidade'];
                        },
                        0.0
                    ),
                    2
                ),

                'items' => array_map(
                    static function ($item) {
                        return [
                            'item_id' => (string) $item['id'],
                            'item_name' => $item['nome'],
                            'price' => (float) $item['valor'],
                            'quantity' => (int) $item['quantidade']
                        ];
                    },
                    $itensCompra
                )
            ];

            $_SESSION['carrinho'] = [];

            require __DIR__ .
                '/../view/finalizar_compra.php';

        } catch (Throwable $e) {

            error_log($e->getMessage());

            http_response_code(500);
            exit('Erro ao finalizar a compra.');
        }

        break;


    // ----------------------------------------------
    // AÇÃO NÃO ENCONTRADA
    // ----------------------------------------------

    default:

        http_response_code(404);
        exit('Ação não encontrada.');
}
