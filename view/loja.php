<?php
$busca = trim((string) ($_GET['busca'] ?? ''));
if ($busca !== '') {
    $produtos = array_values(array_filter($produtos, static function ($produto) use ($busca) {
        return stripos($produto->getNome(), $busca) !== false;
    }));
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TechLoja | Produtos</title><link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="topbar"><nav class="nav" aria-label="Navegação principal">
<a class="brand" href="ProdutoController.php?acao=listar">Tech<span>Loja</span></a>
<form class="search" action="ProdutoController.php" method="GET" role="search">
<input type="hidden" name="acao" value="listar">
<input type="search" name="busca" placeholder="O que você está procurando?" aria-label="Pesquisar produtos" value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>">
<button type="submit">Buscar</button></form>
<div class="navlinks"><a href="ProdutoController.php?acao=listar">Início</a><a href="ProdutoController.php?acao=formulario">Cadastrar</a><a href="CarrinhoController.php?acao=ver">🛒 Carrinho</a></div>
</nav></header>
<main class="container">
<section class="hero"><span class="eyebrow">Tecnologia para o seu dia a dia</span><h1>Seu próximo upgrade começa aqui.</h1><p>Explore produtos, encontre o que procura e monte seu carrinho. Projeto demonstrativo de e-commerce.</p></section>
<div class="section-title"><h2><?= $busca !== '' ? 'Resultados da pesquisa' : 'Nossos produtos' ?></h2><span class="muted"><?= count($produtos) ?> produto(s)</span></div>
<section class="grid" aria-label="Catálogo de produtos">
<?php if (empty($produtos)): ?>
<div class="empty"><h3><?= $busca !== '' ? 'Nenhum produto encontrado' : 'Nossa vitrine está sendo preparada!' ?></h3>
<p><?= $busca !== '' ? 'Experimente buscar por outro nome.' : 'Cadastre o primeiro produto para ele aparecer automaticamente aqui.' ?></p>
<a class="primary" href="<?= $busca !== '' ? 'ProdutoController.php?acao=listar' : 'ProdutoController.php?acao=formulario' ?>"><?= $busca !== '' ? 'Ver todos os produtos' : 'Cadastrar produto' ?></a></div>
<?php endif; ?>
<?php foreach ($produtos as $produto): ?>
<article class="product">
<div class="picture"><span class="pill">Produto #<?= (int)$produto->getId() ?></span><svg viewBox="0 0 64 64" aria-hidden="true"><rect x="9" y="13" width="46" height="33" rx="4"/><path d="M22 52h20M32 46v6M15 39h34"/></svg></div>
<div class="product-content">
<h3><?= htmlspecialchars($produto->getNome(), ENT_QUOTES, 'UTF-8') ?></h3>
<div class="price">R$ <?= number_format((float)$produto->getValor(), 2, ',', '.') ?></div>
<p class="stock"><?= (int)$produto->getQuantidade() > 0 ? 'Estoque: '.(int)$produto->getQuantidade().' unidade(s)' : 'Esgotado' ?></p>
<form class="buy" method="POST" action="CarrinhoController.php?acao=adicionar">
<input type="hidden" name="produto_id" value="<?= (int)$produto->getId() ?>">
<input aria-label="Quantidade de <?= htmlspecialchars($produto->getNome(), ENT_QUOTES, 'UTF-8') ?>" type="number" name="quantidade" min="1" max="<?= (int)$produto->getQuantidade() ?>" value="1" required>
<button type="submit" <?= (int)$produto->getQuantidade() < 1 ? 'disabled' : '' ?>>Adicionar 🛒</button>
</form>
</div></article>
<?php endforeach; ?>
</section><p class="footer">TechLoja • Projeto acadêmico demonstrativo • Nenhuma compra real é processada</p>
</main>
</body></html>