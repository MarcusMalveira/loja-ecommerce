<?php

require_once __DIR__ . '/../config/sessao.php';

require_once __DIR__ . '/../model/dao/CarrinhoDAO.php';
require_once __DIR__ . '/../model/dao/ProdutoDAO.php';
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/dao/UsuarioDAO.php';

$usuarioDAO = new UsuarioDAO($pdo);
$carrinhoDAO = new CarrinhoDAO($pdo);
$produtoDAO = new ProdutoDAO($pdo);
$acao = $_GET['acao'] ?? 'formulario';

// Token de proteção dos formulários
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validarCsrf(): void {
    $recebido = $_POST['csrf_token'] ?? '';

    if (!is_string($recebido) ||
        !hash_equals($_SESSION['csrf_token'], $recebido)) {
        http_response_code(403);
        exit('Requisição inválida. Atualize a página.');
    }
}

switch ($acao) {

    case 'formulario':
        require __DIR__ . '/../view/cadastroUsuario.php';
        break;

    case 'formLogin':
        require __DIR__ . '/../view/login.php';
        break;

    case 'cadastrar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método não permitido.');
        }

        validarCsrf();

        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if (
            $nome === '' ||
            mb_strlen($nome) > 150 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 255 ||
            strlen($senha) < 8
        ) {
            http_response_code(400);
            exit('Dados de cadastro inválidos.');
        }

        try {
            if ($usuarioDAO->buscarPorEmail($email)) {
                http_response_code(409);
                exit('Este e-mail já está cadastrado.');
            }

            $usuarioDAO->cadastrar($nome, $email, $senha);

            header('Location: UsuarioController.php?acao=formLogin');
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            exit('Não foi possível cadastrar o usuário.');
        }

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método não permitido.');
        }

        validarCsrf();

        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)
            || !is_string($senha)) {
            http_response_code(400);
            exit('Credenciais inválidas.');
        }

        try {
            $usuario = $usuarioDAO->buscarPorEmail($email);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            exit('Erro ao consultar usuário.');
        }

        if (!$usuario ||
            !password_verify($senha, $usuario['senha_hash'])) {
            http_response_code(401);
            exit('E-mail ou senha incorretos.');
        }
        // Preserva os produtos adicionados antes do login.
                $carrinhoVisitante = $_SESSION['carrinho'] ?? [];
                $usuarioId = (int) $usuario['id'];

                try {
                    $pdo->beginTransaction();

                    // Busca os produtos que já estavam salvos na conta.
                    $itensSalvos = $carrinhoDAO->buscarPorUsuario($usuarioId);

                    $quantidades = [];

                    // Primeiro, carrega as quantidades do banco.
                    foreach ($itensSalvos as $item) {
                        $produtoId = (int) $item['id'];

                        $quantidades[$produtoId] =
                            (int) $item['quantidade'];
                    }

                    // Depois, combina com os produtos do visitante.
                    foreach ($carrinhoVisitante as $item) {
                        $produtoId = (int) ($item['id'] ?? 0);
                        $quantidade = (int) ($item['quantidade'] ?? 0);

                        if ($produtoId <= 0 || $quantidade <= 0) {
                            continue;
                        }

                        $quantidades[$produtoId] =
                            ($quantidades[$produtoId] ?? 0) + $quantidade;
                    }

                    $carrinhoAtualizado = [];

                    foreach ($quantidades as $produtoId => $quantidade) {
                        $produto = $produtoDAO->buscarPorId($produtoId);

                        // Remove itens indisponíveis.
                        if (!$produto || $produto->getQuantidade() <= 0) {
                            $carrinhoDAO->removerItem(
                                $usuarioId,
                                $produtoId
                            );

                            continue;
                        }

                        // Não permite quantidade maior que o estoque.
                        $quantidadeFinal = min(
                            $quantidade,
                            (int) $produto->getQuantidade()
                        );

                        // Atualiza o carrinho no MySQL.
                        $carrinhoDAO->salvarItem(
                            $usuarioId,
                            $produtoId,
                            $quantidadeFinal
                        );

                        // Monta o carrinho utilizado pela sessão.
                        $carrinhoAtualizado[] = [
                            'id' => $produtoId,
                            'nome' => $produto->getNome(),
                            'valor' => $produto->getValor(),
                            'quantidade' => $quantidadeFinal
                        ];
                    }

                    $pdo->commit();

                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }

                    error_log($e->getMessage());

                    http_response_code(500);
                    exit('Erro ao recuperar o carrinho.');
                }

                // Renova a sessão após autenticação.
                session_regenerate_id(true);

                $_SESSION['usuario'] = [
                    'id' => $usuarioId,
                    'nome' => $usuario['nome'],
                    'tipo' => $usuario['tipo']
                ];

                $_SESSION['carrinho'] = $carrinhoAtualizado;

                // Grava as alterações da sessão antes de redirecionar.
                session_write_close();

                header('Location: ProdutoController.php?acao=listar');
                exit;


    case 'sair':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método não permitido.');
        }

        validarCsrf();

        $_SESSION = [];
        session_destroy();

        header('Location: ProdutoController.php?acao=listar');
        exit;

    default:
        http_response_code(404);
        exit('Página não encontrada.');
}
