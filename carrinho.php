<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
</head>
<body>
    <h1>Meu Carrinho</h1>

    <?php if (empty($carrinho)): ?>
        <p>O carrinho está vazio.</p>
        <a
            href="../controller/ProdutoController.php?acao=listar"
            id="voltar_loja"
        >
            Voltar para a loja
        </a>
    <?php else: ?>

        <?php foreach ($carrinho as $produto): ?>
            <?php
                $subtotal = $produto['valor'] * $produto['quantidade'];
                $total += $subtotal;
            ?>

            <div>
                <h2><?= htmlspecialchars($produto['nome']) ?></h2>

                <p>
                    Preço: R$
                    <?= number_format($produto['valor'], 2, ',', '.') ?>
                </p>

                <p>Quantidade:</p>

                <form
                    method="POST"
                    action="../controller/CarrinhoController.php?acao=alterar"
                >
                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                    <input type="hidden" name="operacao" value="definir">

                    <input
                        type="number"
                        id="quantidade_carrinho_<?= $produto['id'] ?>"
                        name="quantidade"
                        value="<?= $produto['quantidade'] ?>"
                        min="1"
                    >

                    <button
                        type="submit"
                        id="atualizar_quantidade_<?= $produto['id'] ?>"
                    >
                        Atualizar
                    </button>
                </form>

                <div style="display: flex; gap: 5px;">
                    <form
                        method="POST"
                        action="../controller/CarrinhoController.php?acao=alterar"
                    >
                        <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                        <input type="hidden" name="operacao" value="diminuir">
                        <button type="submit" id="diminuir">-</button>
                    </form>

                    <form
                        method="POST"
                        action="../controller/CarrinhoController.php?acao=alterar"
                    >
                        <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                        <input type="hidden" name="operacao" value="aumentar">
                        <button type="submit" id="aumentar">+</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <h2>Total: R$<?= number_format($total, 2, ',', '.') ?></h2>

        <form method="POST" action="../controller/CarrinhoController.php?acao=limpar">
            <button type="submit" id="limpar_carrinho">Limpar Carrinho</button>
        </form>

        <form method="POST" action="../controller/CarrinhoController.php?acao=finalizar">
            <button type="submit" id="finalizar_compra">Finalizar Compra</button>
        </form>

        <br><br>

        <a
            href="../controller/ProdutoController.php?acao=listar"
            id="continuar_comprando"
        >
            Continuar comprando
        </a>
    <?php endif; ?>
</body>
</html>
