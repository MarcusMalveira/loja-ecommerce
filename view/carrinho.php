<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meu carrinho | TechLoja</title>

    <link rel="stylesheet"
          href="<?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/controller/')
              ? '../public/style.css'
              : '/style.css' ?>">
    <?php require __DIR__ . '/analytics.php'; ?>
</head>

<body>

    <!-- CABEÇALHO -->
    <header class="topbar">
        <nav class="nav" aria-label="Navegação principal">

            <a class="brand" href="ProdutoController.php?acao=listar">
                Tech<span>Loja</span>
            </a>

            <div class="navlinks">

                <a href="ProdutoController.php?acao=listar">
                    Início
                </a>

                <?php if (($_SESSION['usuario']['tipo'] ?? null) === 'admin'): ?>
                    <a href="ProdutoController.php?acao=formulario">
                        Cadastrar produto
                    </a>
                <?php endif; ?>

                <a href="CarrinhoController.php?acao=ver"
                   aria-current="page">
                    🛒 Carrinho
                </a>

                <?php if (!isset($_SESSION['usuario'])): ?>

                    <a href="UsuarioController.php?acao=formLogin">
                        Entrar
                    </a>

                    <a href="UsuarioController.php?acao=formulario">
                        Criar conta
                    </a>

                <?php else: ?>

                    <span>
                        Olá,
                        <?= htmlspecialchars(
                            $_SESSION['usuario']['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>!
                    </span>

                <?php endif; ?>

            </div>

        </nav>
    </header>


    <!-- CONTEÚDO PRINCIPAL -->
    <main class="container inner-page">

        <div class="page-heading">

            <div>
                <span class="eyebrow-dark">
                    Sua seleção
                </span>

                <h1>Meu carrinho</h1>

                <p>
                    Confira seus produtos antes de finalizar
                    o pedido demonstrativo.
                </p>
            </div>

            <a class="outline-btn"
               href="ProdutoController.php?acao=listar"
               id="continuar_comprando">
                ← Continuar comprando
            </a>

        </div>


        <!-- CARRINHO VAZIO -->
        <?php if (empty($carrinho)): ?>

            <div class="empty cart-empty">

                <div class="empty-icon">🛒</div>

                <h2>Seu carrinho está vazio</h2>

                <p>
                    Explore a loja e escolha seus
                    produtos favoritos.
                </p>

                <a class="primary"
                   id="voltar_loja"
                   href="ProdutoController.php?acao=listar">
                    Explorar produtos
                </a>

            </div>

        <?php else: ?>

            <!-- CARRINHO COM PRODUTOS -->
            <div class="cart-layout">

                <section class="cart-products"
                         aria-label="Produtos no carrinho">

                    <?php foreach ($carrinho as $produto): ?>

                        <?php
                            $subtotal = (float) $produto['valor']
                                      * (int) $produto['quantidade'];

                            $total += $subtotal;

                            $produtoId = (int) $produto['id'];
                        ?>

                        <article class="cart-item">

                            <div class="cart-thumb" aria-hidden="true">
                                ▣
                            </div>

                            <!-- INFORMAÇÕES DO PRODUTO -->
                            <div class="cart-item-main">

                                <span class="small-label">
                                    Produto #<?= $produtoId ?>
                                </span>

                                <h2>
                                    <?= htmlspecialchars(
                                        $produto['nome'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                                <p>
                                    Preço unitário:
                                    R$ <?= number_format(
                                        (float) $produto['valor'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </p>

                                <strong class="item-subtotal">
                                    R$ <?= number_format(
                                        $subtotal,
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                            </div>


                            <!-- CONTROLE DE QUANTIDADE -->
                            <div class="cart-controls">

                                <label for="quantidade_carrinho_<?= $produtoId ?>">
                                    Quantidade
                                </label>

                                
                              <!-- DEFINIR QUANTIDADE -->
                              <form class="qty-edit"
                                    method="POST"
                                    action="CarrinhoController.php?acao=alterar">

                                  <input type="hidden"
                                        name="id"
                                        value="<?= $produtoId ?>">

                                  <input type="hidden"
                                        name="operacao"
                                        value="definir">

                                  <!-- PROTEÇÃO CSRF -->
                                  <input type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                            $_SESSION['csrf_token'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">

                                  <input type="number"
                                        id="quantidade_carrinho_<?= $produtoId ?>"
                                        name="quantidade"
                                        min="1"
                                        value="<?= (int) $produto['quantidade'] ?>"
                                        required>

                                  <button type="submit"
                                          id="atualizar_quantidade_<?= $produtoId ?>">
                                      Atualizar
                                  </button>

                              </form>



                                <!-- AUMENTAR / DIMINUIR -->
                                <div class="qty-step">

                                    <form method="POST"
      action="CarrinhoController.php?acao=alterar">

                                <input type="hidden"
                                      name="id"
                                      value="<?= $produtoId ?>">

                                <input type="hidden"
                                      name="operacao"
                                      value="aumentar">

                                <input type="hidden"
                                      name="csrf_token"
                                      value="<?= htmlspecialchars(
                                          $_SESSION['csrf_token'],
                                          ENT_QUOTES,
                                          'UTF-8'
                                      ) ?>">

                                <button type="submit"
                                        id="aumentar"
                                        aria-label="Aumentar quantidade">
                                    +
                                </button>

                            </form>

                                    <form method="POST"
      action="CarrinhoController.php?acao=alterar">

                                  <input type="hidden"
                                        name="id"
                                        value="<?= $produtoId ?>">

                                  <input type="hidden"
                                        name="operacao"
                                        value="diminuir">

                                  <input type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                            $_SESSION['csrf_token'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">

                                  <button type="submit"
                                          id="diminuir"
                                          aria-label="Diminuir quantidade">
                                      −
                                  </button>

                              </form>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </section>


                <!-- RESUMO DO PEDIDO -->
                <aside class="summary-card">

                    <h2>Resumo do pedido</h2>

                    <div class="summary-line">
                        <span>Subtotal</span>

                        <strong>
                            R$ <?= number_format($total, 2, ',', '.') ?>
                        </strong>
                    </div>

                    <div class="summary-line">
                        <span>Entrega</span>
                        <span>Não aplicável</span>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>

                        <strong>
                            R$ <?= number_format($total, 2, ',', '.') ?>
                        </strong>
                    </div>

                    <p class="demo-note">
                        Compra simulada para fins acadêmicos.
                        Nenhum pagamento será cobrado.
                    </p>
                    
                      <!-- FINALIZAÇÃO COM AUTENTICAÇÃO -->
                        <?php if (isset($_SESSION['usuario'])): ?>

                            <form method="POST"
                                action="CarrinhoController.php?acao=finalizar">

                                <!-- PROTEÇÃO CSRF -->
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars(
                                        $_SESSION['csrf_token'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <!-- MÉTODOS DE PAGAMENTO -->
                                <fieldset class="payment-options">

                                    <legend>
                                        Escolha a forma de pagamento
                                    </legend>

                                    <label>
                                        <input
                                            type="radio"
                                            name="metodo_pagamento"
                                            value="pix"
                                            required
                                        >
                                        Pix
                                    </label>

                                    <label>
                                        <input
                                            type="radio"
                                            name="metodo_pagamento"
                                            value="cartao"
                                        >
                                        Cartão de crédito ou débito
                                    </label>

                                    <label>
                                        <input
                                            type="radio"
                                            name="metodo_pagamento"
                                            value="carteira_digital"
                                        >
                                        Carteira digital
                                    </label>

                                </fieldset>

                                <p class="demo-note">
                                    Pagamento demonstrativo.
                                    Não informe dados bancários reais.
                                </p>

                                <button
                                    class="full-btn"
                                    id="finalizar_compra"
                                    type="submit">

                                    Confirmar compra simulada →

                                </button>

                            </form>

                        <?php else: ?>

                            <a
                                class="full-btn"
                                href="UsuarioController.php?acao=formLogin">

                                Entrar para finalizar →

                            </a>

                        <?php endif; ?>

                    <!-- LIMPAR CARRINHO -->
                    <form method="POST"
                          action="CarrinhoController.php?acao=limpar">

                        <!-- PROTEÇÃO CSRF -->
                        <input type="hidden"
                              name="csrf_token"
                              value="<?= htmlspecialchars(
                                  $_SESSION['csrf_token'],
                                  ENT_QUOTES,
                                  'UTF-8'
                              ) ?>">

                        <button class="text-danger-btn"
                                id="limpar_carrinho"
                                type="submit">
                            Limpar carrinho
                        </button>

                    </form>


                </aside>

            </div>

        <?php endif; ?>

    </main>


    <!-- EVENTOS DO GOOGLE ANALYTICS 4 -->
    <?php

        $ga4ItensCarrinho = array_map(
            static function ($item) {
                return [
                    'item_id' => (string) $item['id'],
                    'item_name' => $item['nome'],
                    'price' => (float) $item['valor'],
                    'quantity' => (int) $item['quantidade']
                ];
            },
            array_values($carrinho)
        );

        $ga4TotalCarrinho = array_reduce(
            $carrinho,
            static function ($total, $item) {
                return $total
                    + (float) $item['valor']
                    * (int) $item['quantidade'];
            },
            0.0
        );

        $ga4JsonFlags = JSON_HEX_TAG
                      | JSON_HEX_AMP
                      | JSON_HEX_APOS
                      | JSON_HEX_QUOT;

    ?>

    <script>

        // Evento: produto adicionado ao carrinho
        <?php if (is_array($ga4Adicionado)): ?>

            gtag(
                'event',
                'add_to_cart',
                <?= json_encode($ga4Adicionado, $ga4JsonFlags) ?>
            );

        <?php endif; ?>


        // Evento: visualização do carrinho
        <?php if (!empty($ga4ItensCarrinho)): ?>

            const ecommerceCart = <?= json_encode(
                [
                    'currency' => 'BRL',
                    'value' => round($ga4TotalCarrinho, 2),
                    'items' => $ga4ItensCarrinho
                ],
                $ga4JsonFlags
            ) ?>;

            gtag('event', 'view_cart', ecommerceCart);


            // Evento: início da finalização
            const checkoutForm = document.querySelector(
                'form[action*="acao=finalizar"]'
            );

            if (checkoutForm) {

                checkoutForm.addEventListener('submit', function () {

                    gtag(
                        'event',
                        'begin_checkout',
                        ecommerceCart
                    );

                });

            }

        <?php endif; ?>

    </script>

</body>
</html>
