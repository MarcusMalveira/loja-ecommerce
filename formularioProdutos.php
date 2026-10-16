<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
</head>
<body>
    <h1>Cadastrar Produto</h1>

    <form action="../controller/ProdutoController.php?acao=cadastrar" method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="valor">Valor:</label>
        <input type="number" id="valor" name="valor" step="0.01" required>

        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" min="1" required>

        <label for="validade">Validade:</label>
        <input type="date" id="validade" name="validade" required>

        <button type="submit" id="cadastrar">Cadastrar</button>
    </form>

    <br>
    <a href="../controller/ProdutoController.php?acao=listar">Voltar para a Loja</a>
</body>
</html>
