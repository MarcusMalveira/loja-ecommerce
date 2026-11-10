<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacidade e cookies | TechLoja</title>
    <link rel="stylesheet" href="<?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/api/') ? '/style.css' : 'public/style.css' ?>">
    <?php require __DIR__ . '/analytics.php'; ?>
</head>
<body>
<header class="topbar"><nav class="nav" aria-label="Navegação principal">
    <a class="brand" href="<?= str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/api/') ? 'ProdutoController.php?acao=listar' : 'controller/ProdutoController.php?acao=listar' ?>">Tech<span>Loja</span></a>
</nav></header>
<main class="container inner-page">
    <section class="form-panel" style="max-width:850px;margin:0 auto">
        <h1>Privacidade e cookies</h1>
        <p><strong>Última atualização:</strong> 10 de novembro de 2026.</p>
        <p>A TechLoja é uma loja virtual acadêmica e demonstrativa. Nenhuma cobrança ou entrega real acontece neste projeto.</p>
        <h2>Quais dados são tratados?</h2>
        <p>Para criar e acessar a conta, o sistema recebe nome, e-mail e senha. A senha é armazenada como hash, não como texto original. O sistema também associa itens do carrinho ao identificador da conta e utiliza cookies de sessão para autenticação e segurança.</p>
        <h2>Para que utilizamos os dados?</h2>
        <p>Para autenticar participantes, demonstrar o carrinho e o checkout, proteger operações e avaliar o funcionamento técnico do projeto. O sistema não processa pagamentos reais nem solicita dados bancários.</p>
        <h2>Cookies necessários e análises opcionais</h2>
        <p>O cookie PHPSESSID é usado para manter a sessão e executar funções da aplicação. Com sua escolha, o Google Analytics 4 poderá utilizar cookies como _ga para produzir estatísticas de navegação e eventos de comércio eletrônico simulados (por exemplo: adicionar ao carrinho e finalizar compra).</p>
        <p>O GA4 só é carregado após a escolha de aceitar análises. Recusar não impede utilizar o site. A escolha fica registrada no armazenamento local do navegador e pode ser alterada abaixo.</p>
        <div class="tl-privacy-actions">
            <p><strong>Sua escolha atual:</strong> <span id="tl-analytics-status">Verificando...</span></p>
            <button class="tl-cookie-accept" type="button" onclick="techlojaSetAnalyticsChoice('accepted')">Aceitar análises</button>
            <button class="tl-cookie-reject" type="button" onclick="techlojaSetAnalyticsChoice('rejected')">Recusar análises</button>
        </div>
        <h2>Serviços envolvidos</h2>
        <p>O projeto utiliza Vercel para hospedagem, Aiven para o banco de dados e Google Analytics 4 para medições opcionais. Esses serviços podem tratar dados técnicos necessários à prestação de seus serviços e operar em outros países, conforme suas próprias políticas.</p>
        <h2>Armazenamento e solicitações</h2>
        <p>Não existe exclusão automática de contas ou prazo definitivo de retenção implementado no sistema. Os dados de teste devem ser revisados ao término da avaliação acadêmica e eliminados quando não forem mais necessários, respeitadas eventuais obrigações aplicáveis.</p>
        <p>Para solicitar informações, correção ou exclusão de seus dados, contate o responsável pelo projeto pelo mesmo canal privado no qual recebeu o convite para testar a TechLoja. Não publique nome, e-mail ou outros dados pessoais em issues públicas do GitHub.</p>
        <p>A remoção de cookies no navegador não exclui automaticamente dados de uma conta no banco ou informações anteriormente recebidas pelo Google.</p>
        <h2>Sobre os seus direitos</h2>
        <p>A Lei Geral de Proteção de Dados (Lei nº 13.709/2018) prevê direitos como confirmação de tratamento, acesso, correção e, nas hipóteses aplicáveis, eliminação de dados. Esta página é um aviso para o projeto acadêmico e não substitui a avaliação de conformidade legal.</p>
        <p><a href="https://www.gov.br/anpd/pt-br" target="_blank" rel="noopener noreferrer">Conheça a Autoridade Nacional de Proteção de Dados (ANPD)</a>.</p>
    </section>
</main>
</body>
</html>
