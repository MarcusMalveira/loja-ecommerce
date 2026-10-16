<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
</head>
<body>
    <h1>Editar Produto</h1>

    <form method="POST" action="../controller/ProdutoController.php?acao=atualizar">
        <input type="hidden" name="id" value="<?= $produto->getId() ?>">

        <label for="nome">Nome:</label>
        <input
            type="text"
            id="nome"
            name="nome"
            value="<?= htmlspecialchars($produto->getNome()) ?>"
            required
        >
        <br>

        <label for="valor">Valor:</label>
        <input
            type="number"
            id="valor"
            name="valor"
            step="0.01"
            value="<?= htmlspecialchars($produto->getValor()) ?>"
            required
        >
        <br>

        <label for="validade">Validade:</label>
        <input
            type="date"
            id="validade"
            name="validade"
            value="<?= htmlspecialchars($produto->getValidade()) ?>"
            required
        >
        <br>

        <label for="quantidade">Quantidade:</label>
        <input
            type="number"
            id="quantidade"
            name="quantidade"
            value="<?= htmlspecialchars($produto->getQuantidade()) ?>"
            required
        >
        <br>

        <input type="submit" value="Atualizar" id="atualizar">
    </form>

    <a href="../controller/ProdutoController.php?acao=listar">
        Voltar para a Loja
    </a>
</body>
</html>
