<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Finalizada</title>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-Z4V0PZP15H"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-Z4V0PZP15H');
</script>
</head>
<body>
    <h1>Compra realizada com sucesso!</h1>
    <p>Obrigado por sua compra!</p>
    <p>Os produtos foram retirados do estoque.</p>
    <p>
        Forma de pagamento:
        <strong>
            <?= htmlspecialchars(
                $pagamentoSelecionado,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </strong>
    </p>

    <p>
        Pagamento aprovado por simulação acadêmica.
        Nenhuma transação financeira foi realizada.
    </p>


    <a href="ProdutoController.php?acao=listar">
        Voltar para a loja
    </a>
<script>
// Evento de compra apenas na página retornada após finalização bem-sucedida.
gtag('event', 'purchase', <?= json_encode($ga4Compra, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
</script>
</body>
</html>
