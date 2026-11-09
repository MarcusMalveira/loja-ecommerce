<?php
// Pesquisa de produtos pelo nome
$busca = trim((string) ($_GET['busca'] ?? ''));
if ($busca !== '') {
    $produtos = array_values(
        array_filter(
            $produtos,
            static function ($produto) use ($busca) {
                return stripos($produto->getNome(), $busca) !== false;
            }
        )
    );
}
// Informações do usuário autenticado
$usuario = $_SESSION['usuario'] ?? null;
$ehAdmin = ($usuario['tipo'] ?? null) === 'admin';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechLoja | Produtos</title>
    <link
        rel="stylesheet"
        href="<?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/controller/')
            ? '../public/style.css'
            : '/style.css' ?>"
    >
    <!-- Google Analytics 4 -->
    <script
        async
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
            <!-- LOGOTIPO -->
            <a
                class="brand"
                href="ProdutoController.php?acao=listar">
                Tech<span>Loja</span>
            </a>
            <!-- BARRA DE PESQUISA -->
            <form
                class="search"
                action="ProdutoController.php"
                method="GET"
                role="search">
                <input
                    type="hidden"
                    name="acao"
                    value="listar"
                >
                <input
                    type="search"
                    name="busca"
                    placeholder="O que você está procurando?"
                    aria-label="Pesquisar produtos"
                    value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>"
                >
                <button type="submit">
                    Buscar
                </button>
            </form>
            <!-- LINKS DE NAVEGAÇÃO -->
            <div class="navlinks">
                <a href="ProdutoController.php?acao=listar">
                    Início
                </a>
                <a href="CarrinhoController.php?acao=ver">
                    🛒 Carrinho
                </a>
                <!-- USUÁRIO AUTENTICADO -->
                <?php if ($usuario !== null): ?>
                    <?php if ($ehAdmin): ?>
                        <a href="ProdutoController.php?acao=formulario">
                            Gerenciar produtos
                        </a>
                    <?php endif; ?>
                    <span>
                        Olá,
                        <?= htmlspecialchars(
                            $usuario['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>!
                    </span>
                    <!-- SAIR DA CONTA -->
                    <form
                        method="POST"
                        action="UsuarioController.php?acao=sair">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                $_SESSION['csrf_token'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >
                        <button type="submit">
                            Sair
                        </button>
                    </form>
                <!-- VISITANTE -->
                <?php else: ?>
                    <a href="UsuarioController.php?acao=formLogin">
                        Entrar
                    </a>
                    <a href="UsuarioController.php?acao=formulario">
                        Criar conta
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <!-- CONTEÚDO PRINCIPAL -->
    <main class="container">
        <!-- APRESENTAÇÃO DA LOJA -->
        <section class="hero">
            <span class="eyebrow">
                Tecnologia para o seu dia a dia
            </span>
            <h1>
                Seu próximo upgrade começa aqui.
            </h1>
            <p>
                Explore produtos, encontre o que procura e monte
                seu carrinho. Projeto demonstrativo de e-commerce.
            </p>
        </section>
        <!-- TÍTULO DO CATÁLOGO -->
        <div class="section-title">
            <h2>
                <?= $busca !== ''
                    ? 'Resultados da pesquisa'
                    : 'Nossos produtos' ?>
            </h2>
            <span class="muted">
                <?= count($produtos) ?> produto(s)
            </span>
        </div>
        <!-- CATÁLOGO DE PRODUTOS -->
        <section
            class="grid"
            aria-label="Catálogo de produtos">
            <!-- NENHUM PRODUTO ENCONTRADO -->
            <?php if (empty($produtos)): ?>
                <div class="empty">
                    <h3>
                        <?= $busca !== ''
                            ? 'Nenhum produto encontrado'
                            : 'Nossa vitrine está sendo preparada!' ?>
                    </h3>
                    <p>
                        <?= $busca !== ''
                            ? 'Experimente buscar por outro nome.'
                            : 'Em breve teremos produtos disponíveis.' ?>
                    </p>
                    <?php if ($busca !== ''): ?>
                        <a
                            class="primary"
                            href="ProdutoController.php?acao=listar">
                            Ver todos os produtos
                        </a>
                    <?php elseif ($ehAdmin): ?>
                        <a
                            class="primary"
                            href="ProdutoController.php?acao=formulario">
                            Cadastrar produto
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <!-- LISTAGEM DOS PRODUTOS -->
            <?php foreach ($produtos as $produto): ?>
                <?php
                    $produtoId = (int) $produto->getId();
                    $produtoNome = $produto->getNome();
                    $produtoValor = (float) $produto->getValor();
                    $produtoEstoque = (int) $produto->getQuantidade();
                ?>
                <article class="product">
                    <!-- IMAGEM ILUSTRATIVA -->
                    <div class="picture">
                        <span class="pill">
                            Produto #<?= $produtoId ?>
                        </span>
                        <svg
                            viewBox="0 0 64 64"
                            aria-hidden="true">
                            <rect
                                x="9"
                                y="13"
                                width="46"
                                height="33"
                                rx="4"
                            />
                            <path
                                d="M22 52h20M32 46v6M15 39h34"
                            />
                        </svg>
                    </div>
                    <!-- INFORMAÇÕES DO PRODUTO -->
                    <div class="product-content">
                        <h3>
                            <?= htmlspecialchars(
                                $produtoNome,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h3>
                        <!-- PREÇO -->
                        <div class="price">
                            R$ <?= number_format(
                                $produtoValor,
                                2,
                                ',',
                                '.'
                            ) ?>
                        </div>
                        <!-- ESTOQUE -->
                        <p class="stock">

                            <?php if ($produtoEstoque > 0): ?>

                                Estoque:
                                <?= $produtoEstoque ?> unidade(s)

                            <?php else: ?>

                                Esgotado

                            <?php endif; ?>

                        </p>
                        <!-- ADICIONAR AO CARRINHO -->
                        <form
                            class="buy"
                            method="POST"
                            action="CarrinhoController.php?acao=adicionar">                            
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars(
                                    $_SESSION['csrf_token'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                            <input
                                type="hidden"
                                name="produto_id"
                                value="<?= $produtoId ?>"
                            >
                            <input
                                type="number"
                                name="quantidade"
                                min="1"
                                max="<?= $produtoEstoque ?>"
                                value="1"
                                aria-label="Quantidade de <?= htmlspecialchars(
                                    $produtoNome,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                required
                            >
                            <button
                                type="submit"
                                <?= $produtoEstoque < 1 ? 'disabled' : '' ?>>
                                Adicionar 🛒
                            </button>
                        </form>                        
                        <?php if ($ehAdmin): ?>
                            <div class="admin-actions">

                                <a href="ProdutoController.php?acao=editar&id=<?= $produtoId ?>">
                                    ✏️ Editar
                                </a>

                                <form
                                    method="POST"
                                    action="ProdutoController.php?acao=excluir"
                                    onsubmit="return confirm('Deseja realmente excluir este produto?');"
                                >
                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars(
                                            $_SESSION['csrf_token'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $produtoId ?>"
                                    >

                                    <button type="submit">
                                        🗑️ Excluir
                                    </button>
                                </form>

                            </div>
                        <?php endif; ?>

                    </div>
                </article>
            <?php endforeach; ?>
        </section>
        <!-- RODAPÉ -->
        <p class="footer">
            TechLoja • Projeto acadêmico demonstrativo •
            Nenhuma compra real é processada
        </p>
    </main>
</body>
</html>
