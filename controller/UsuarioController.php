
<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/dao/UsuarioDAO.php';

$usuarioDAO = new UsuarioDAO($pdo);
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

        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id' => (int) $usuario['id'],
            'nome' => $usuario['nome'],
            'tipo' => $usuario['tipo']
        ];

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
