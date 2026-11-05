
<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/dao/UsuarioDAO.php';

$usuarioDAO = new UsuarioDAO($pdo);

$acao = $_GET['acao'] ?? 'formulario';

switch ($acao) {

    case 'formulario':
        require __DIR__ . '/../view/cadastroUsuario.php';
        break;

    case 'cadastrar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método não permitido.');
        }

        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        // Validação dos campos
        if (
            $nome === '' ||
            mb_strlen($nome) > 150 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 255 ||
            strlen($senha) < 8
        ) {
            http_response_code(400);
            exit('Dados inválidos. Confira nome, e-mail e senha (mínimo de 8 caracteres).');
        }

        // Verificar se o e-mail já existe
        if ($usuarioDAO->buscarPorEmail($email)) {
            http_response_code(409);
            exit('Este e-mail já está cadastrado.');
        }

        try {
            $usuarioDAO->cadastrar($nome, $email, $senha);

            // Depois vamos direcionar para o login
            echo 'Usuário cadastrado com sucesso!';

        } catch (PDOException $e) {
            // Não expor informações internas do banco
            error_log($e->getMessage());
            http_response_code(500);
            echo 'Não foi possível concluir o cadastro.';
        }
        break;

    default:
        http_response_code(404);
        echo 'Página não encontrada.';
}
