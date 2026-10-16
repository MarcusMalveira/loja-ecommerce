<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Cadastrar produto | TechLoja</title><link rel="stylesheet" href="/style.css"></head>
<body>
<header class="topbar"><nav class="nav" aria-label="Navegação principal"><a class="brand" href="ProdutoController.php?acao=listar">Tech<span>Loja</span></a><div class="navlinks"><a href="ProdutoController.php?acao=listar">Início</a><a href="ProdutoController.php?acao=formulario" aria-current="page">Cadastrar</a><a href="CarrinhoController.php?acao=ver">🛒 Carrinho</a></div></nav></header>
<main class="container inner-page">
<div class="page-heading"><div><span class="eyebrow-dark">Gerenciar catálogo</span><h1>Cadastrar produto</h1><p>Preencha as informações para adicionar um produto à vitrine.</p></div><a class="outline-btn" href="ProdutoController.php?acao=listar">← Voltar à loja</a></div>
<div class="form-panel"><div class="form-panel-title"><span class="form-icon">＋</span><div><h2>Informações do produto</h2><p>Os campos marcados são obrigatórios.</p></div></div>
<form class="product-form" action="ProdutoController.php?acao=cadastrar" method="POST">
<div class="field"><label for="nome">Nome do produto</label><input id="nome" name="nome" type="text" maxlength="255" placeholder="Ex.: Teclado mecânico RGB" required></div>
<div class="form-grid"><div class="field"><label for="valor">Preço (R$)</label><input id="valor" name="valor" type="number" min="0" step="0.01" placeholder="0,00" required></div><div class="field"><label for="quantidade">Quantidade em estoque</label><input id="quantidade" name="quantidade" type="number" min="1" step="1" placeholder="Ex.: 10" required></div></div>
<div class="field"><label for="validade">Data de validade</label><input id="validade" name="validade" type="date" required><span class="field-hint">Campo mantido para compatibilidade com o banco atual.</span></div>
<div class="form-actions"><a class="outline-btn" href="ProdutoController.php?acao=listar">Cancelar</a><button class="full-btn" type="submit" id="cadastrar">＋ Cadastrar produto</button></div>
</form></div></main></body></html>