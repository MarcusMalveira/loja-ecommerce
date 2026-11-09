
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Compra finalizada | TechLoja</title>

    <link rel="stylesheet"
          href="<?= str_contains(
              $_SERVER['SCRIPT_NAME'] ?? '',
              '/controller/'
          ) ? '../public/style.css' : '/style.css' ?>">

    <!-- Google Analytics 4 -->
    <script async
        src="https://www.googletagmanager.com/gtag/js?id=G-Z4V0PZP15H">
    </script>

    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'G-Z4V0PZP15H');
    </script>
</head>

<body>

    <!-- CABEÇALHO -->
    <header class="topbar">
        <nav class="nav" aria-label="Navegação principal">

            <a class="brand"
               href="ProdutoController.php?acao=listar">
                Tech<span>Loja</span>
            </a>

            <div class="navlinks">
                <a href="ProdutoController.php?acao=listar">
                    Início
                </a>
            </div>

        </nav>
    </header>

    <!-- CONFIRMAÇÃO -->
    <main class="container inner-page">

        <section class="checkout-success">

            <div class="success-icon"
                 aria-hidden="true">
                ✓
            </div>

            <span class="eyebrow-dark">
                Pedido demonstrativo concluído
            </span>

            <h1>Compra realizada com sucesso!</h1>

            <p class="success-description">
                Obrigado por escolher a TechLoja!
                Seu pedido demonstrativo foi finalizado
                e o estoque foi atualizado.
            </p>

            <!-- RESUMO -->
            <div class="success-details">

                <h2>Resumo do pagamento</h2>

                <div class="summary-line">
                    <span>Forma de pagamento</span>

                    <strong>
                        <?= htmlspecialchars(
                            $pagamentoSelecionado,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>
                </div>

                <div class="summary-line">
                    <span>Situação</span>

                    <strong class="success-status">
                        Simulado com sucesso
                    </strong>
                </div>

                <div class="summary-total">
                    <span>Total</span>

                    <strong>
                        R$ <?= number_format(
                            (float) $ga4Compra['value'],
                            2,
                            ',',
                            '.'
                        ) ?>
                    </strong>
                </div>

            </div>

            <!-- AVISO ACADÊMICO -->
            <div class="success-notice">
                Esta é uma simulação acadêmica.
                Nenhuma transação financeira real
                foi realizada.
            </div>

            <a class="full-btn"
               href="ProdutoController.php?acao=listar">
                Voltar para a loja →
            </a>

        </section>

    </main>

    <!-- EVENTO DE COMPRA GA4 -->
    <script>
        gtag(
            'event',
            'purchase',
            <?= json_encode(
                $ga4Compra,
                JSON_HEX_TAG |
                JSON_HEX_AMP |
                JSON_HEX_APOS |
                JSON_HEX_QUOT
            ) ?>
        );
    </script>

</body>
</html>
