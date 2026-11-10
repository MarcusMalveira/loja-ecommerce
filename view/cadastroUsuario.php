<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Criar conta | TechLoja</title>

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
            <a href="UsuarioController.php?acao=formulario">
                Criar conta
            </a>
        </div>
    </nav>
</header>

<main class="container inner-page">

    <div class="page-heading">
        <div>
            <span class="eyebrow-dark">
                Bem-vindo à TechLoja
            </span>

            <h1>Criar minha conta</h1>

            <p>
                Cadastre-se para realizar suas
                compras demonstrativas.
            </p>
        </div>
    </div>

    <div class="form-panel">

        <div class="form-panel-title">
            <div>
                <h2>Cadastro de cliente</h2>
                <p>Preencha seus dados abaixo.</p>
            </div>
        </div>

        <form
            class="product-form"
            method="POST"
            action="UsuarioController.php?acao=cadastrar"
        >

               
            <input type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $_SESSION['csrf_token'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">
            


            <div class="field">
                <label for="nome">Nome completo</label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="150"
                    autocomplete="name"
                    required
                >
            </div>

            <div class="field">
                <label for="email">E-mail</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="255"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="field">
                <label for="senha">Senha</label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

                <small>
                    A senha deve conter pelo menos
                    8 caracteres.
                </small>
            </div>

            <p style="font-size:0.88rem">Ao criar sua conta, seus dados serão usados para autenticação e testes acadêmicos. Saiba mais na <a href="<?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/controller/') ? '../privacidade.php' : 'privacidade.php' ?>">política de privacidade</a>.</p>

            <div class="form-actions">

                <a
                    class="outline-btn"
                    href="ProdutoController.php?acao=listar"
                >
                    Voltar
                </a>

                <button
                    type="submit"
                    class="full-btn"
                >
                    Criar conta
                </button>

            </div>

        </form>

    </div>

</main>

</body>
</html>
