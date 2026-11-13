<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Entrar | TechLoja</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<header class="topbar">
    <nav class="nav">
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

<main class="container inner-page">

    <div class="page-heading">
        <div>
            <span class="eyebrow-dark">
                Área do cliente
            </span>
            <h1>Entrar na TechLoja</h1>
            <p>Acesse sua conta para continuar.</p>
        </div>
    </div>

    <div class="form-panel">
        <?php if (($_GET['erro'] ?? '') === 'credenciais'): ?>
        <div class="login-alert" role="alert">
            <strong>Não foi possível entrar.</strong>
            <span>E-mail ou senha incorretos. Tente novamente.</span>
        </div>
        <?php endif; ?>
        <form class="product-form"
              method="POST"
              action="UsuarioController.php?acao=login">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= htmlspecialchars(
                       $_SESSION['csrf_token'],
                       ENT_QUOTES,
                       'UTF-8'
                   ) ?>">

            <div class="field">
                <label for="email">E-mail</label>
                <input type="email"
                       id="email"
                       name="email"
                       autocomplete="email"
                       required>
            </div>

            <div class="field">
                <label for="senha">Senha</label>
                <input type="password"
                       id="senha"
                       name="senha"
                       autocomplete="current-password"
                       required>
            </div>

            <button class="full-btn" type="submit">
                Entrar
            </button>
        </form>

        <p>
            Ainda não tem uma conta?
            <a href="UsuarioController.php?acao=formulario">
                Cadastre-se
            </a>
        </p>
    </div>

</main>
</body>
</html>
