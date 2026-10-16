<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Meu carrinho | TechLoja</title><link rel="stylesheet" href="<?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/controller/') ? '../public/style.css' : '/style.css' ?>"></head>
<body>
<header class="topbar"><nav class="nav" aria-label="Navegação principal"><a class="brand" href="ProdutoController.php?acao=listar">Tech<span>Loja</span></a><div class="navlinks"><a href="ProdutoController.php?acao=listar">Início</a><a href="ProdutoController.php?acao=formulario">Cadastrar</a><a href="CarrinhoController.php?acao=ver" aria-current="page">🛒 Carrinho</a></div></nav></header>
<main class="container inner-page">
<div class="page-heading"><div><span class="eyebrow-dark">Sua seleção</span><h1>Meu carrinho</h1><p>Confira seus produtos antes de finalizar o pedido demonstrativo.</p></div><a class="outline-btn" href="ProdutoController.php?acao=listar" id="continuar_comprando">← Continuar comprando</a></div>
<?php if (empty($carrinho)): ?>
<div class="empty cart-empty"><div class="empty-icon">🛒</div><h2>Seu carrinho está vazio</h2><p>Explore a loja e escolha seus produtos favoritos.</p><a class="primary" id="voltar_loja" href="ProdutoController.php?acao=listar">Explorar produtos</a></div>
<?php else: ?>
<div class="cart-layout"><section class="cart-products" aria-label="Produtos no carrinho">
<?php foreach ($carrinho as $produto): ?>
<?php $subtotal = (float)$produto['valor'] * (int)$produto['quantidade']; $total += $subtotal; ?>
<article class="cart-item">
<div class="cart-thumb" aria-hidden="true">▣</div>
<div class="cart-item-main"><span class="small-label">Produto #<?= (int)$produto['id'] ?></span><h2><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h2><p>Preço unitário: R$ <?= number_format((float)$produto['valor'],2,',','.') ?></p><strong class="item-subtotal">R$ <?= number_format($subtotal,2,',','.') ?></strong></div>
<div class="cart-controls">
<label for="quantidade_carrinho_<?= (int)$produto['id'] ?>">Quantidade</label>
<form class="qty-edit" method="POST" action="CarrinhoController.php?acao=alterar">
<input type="hidden" name="id" value="<?= (int)$produto['id'] ?>"><input type="hidden" name="operacao" value="definir">
<input type="number" id="quantidade_carrinho_<?= (int)$produto['id'] ?>" name="quantidade" min="1" value="<?= (int)$produto['quantidade'] ?>" required>
<button type="submit" id="atualizar_quantidade_<?= (int)$produto['id'] ?>">Atualizar</button></form>
<div class="qty-step">
<form method="POST" action="CarrinhoController.php?acao=alterar"><input type="hidden" name="id" value="<?= (int)$produto['id'] ?>"><input type="hidden" name="operacao" value="diminuir"><button type="submit" id="diminuir" aria-label="Diminuir quantidade">−</button></form>
<form method="POST" action="CarrinhoController.php?acao=alterar"><input type="hidden" name="id" value="<?= (int)$produto['id'] ?>"><input type="hidden" name="operacao" value="aumentar"><button type="submit" id="aumentar" aria-label="Aumentar quantidade">+</button></form>
</div></div></article>
<?php endforeach; ?>
</section>
<aside class="summary-card"><h2>Resumo do pedido</h2><div class="summary-line"><span>Subtotal</span><strong>R$ <?= number_format($total,2,',','.') ?></strong></div><div class="summary-line"><span>Entrega</span><span>Não aplicável</span></div><div class="summary-total"><span>Total</span><strong>R$ <?= number_format($total,2,',','.') ?></strong></div><p class="demo-note">Compra simulada para fins acadêmicos. Nenhum pagamento será cobrado.</p>
<form method="POST" action="CarrinhoController.php?acao=finalizar"><button class="full-btn" id="finalizar_compra" type="submit">Finalizar compra →</button></form>
<form method="POST" action="CarrinhoController.php?acao=limpar"><button class="text-danger-btn" id="limpar_carrinho" type="submit">Limpar carrinho</button></form>
</aside></div>
<?php endif; ?></main></body></html>