<?php


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validarRequisicaoAdmin(): void
{
    // Exige o método POST.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Método não permitido.');
    }

    // Recupera os tokens.
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    $tokenSessao = $_SESSION['csrf_token'] ?? '';

    // Verifica se ambos existem e são iguais.
    if (
        !is_string($tokenEnviado) ||
        !is_string($tokenSessao) ||
        $tokenSessao === '' ||
        !hash_equals($tokenSessao, $tokenEnviado)
    ) {
        http_response_code(403);
        exit('Token CSRF inválido.');
    }
}


function validarDadosProduto(bool $edicao = false): array
{
    $nome = $_POST['nome'] ?? null;
    $valor = $_POST['valor'] ?? null;
    $quantidade = $_POST['quantidade'] ?? null;

    // Nome obrigatório e limitado a 255 caracteres
    if (!is_string($nome)) {
        http_response_code(400);
        exit('Nome do produto inválido.');
    }

    $nome = trim($nome);

    if ($nome === '' || mb_strlen($nome) > 255) {
        http_response_code(400);
        exit('O nome deve ter entre 1 e 255 caracteres.');
    }

    // Preço não negativo e com até 2 casas decimais
    if (
        !is_string($valor) ||
        !preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $valor)
    ) {
        http_response_code(400);
        exit('Preço inválido. Utilize um valor positivo ou zero, com até 2 casas decimais.');
    }

    // Quantidade deve ser um número inteiro
    $quantidadeValidada = filter_var(
        $quantidade,
        FILTER_VALIDATE_INT,
        [
            'options' => [
                'min_range' => $edicao ? 0 : 1,
                'max_range' => 2147483647
            ]
        ]
    );

    if ($quantidadeValidada === false) {
        http_response_code(400);
        exit('Quantidade inválida.');
    }

    return [
        'nome' => $nome,
        'valor' => $valor,
        'quantidade' => $quantidadeValidada
    ];
}



require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/dao/ProdutoDAO.php';
require_once __DIR__ . '/../model/dto/ProdutoDTO.php';

$produtoDAO = new ProdutoDAO($pdo);
$acao = $_GET['acao'] ?? 'listar';


// Operações exclusivas de administradores
$acoesAdmin = [
    'formulario',
    'cadastrar',
    'editar',
    'atualizar',
    'excluir'
];

if (in_array($acao, $acoesAdmin, true)) {
    if (($_SESSION['usuario']['tipo'] ?? null) !== 'admin') {
        http_response_code(403);
        exit('Acesso negado. Área exclusiva do administrador.');
    }
}


switch ($acao) {
    case 'listar':
        $produtos = $produtoDAO->listarTodos();
        require __DIR__ . '/../view/loja.php';
        break;

    case 'formulario':
        require __DIR__ . '/../view/formularioProdutos.php';
        break;

    
    case 'cadastrar':

        validarRequisicaoAdmin();

        $dados = validarDadosProduto();

        $produto = new ProdutoDTO();
        $produto->setNome($dados['nome']);
        $produto->setValor($dados['valor']);
        $produto->setQuantidade($dados['quantidade']);

        $produtoDAO->cadastrarOuSomar($produto);

        header('Location: ProdutoController.php?acao=listar');
        exit;

    case 'editar':
        if (!isset($_GET['id'])) {
            die('ID do produto não encontrado');
        }

        $produto = $produtoDAO->buscarPorId((int) $_GET['id']);

        if (!$produto) {
            die('Produto não encontrado');
        }

        require __DIR__ . '/../view/editar.php';
        break;

    
    case 'atualizar':

        validarRequisicaoAdmin();

        $id = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        if ($id === false || $id === null || $id <= 0) {
            http_response_code(400);
            exit('ID do produto inválido.');
        }

        // Confirma se o produto existe
        if (!$produtoDAO->buscarPorId($id)) {
            http_response_code(404);
            exit('Produto não encontrado.');
        }

        $dados = validarDadosProduto(true);

        $produto = new ProdutoDTO();
        $produto->setId($id);
        $produto->setNome($dados['nome']);
        $produto->setValor($dados['valor']);
        $produto->setQuantidade($dados['quantidade']);

        $produtoDAO->atualizar($produto);

        header('Location: ProdutoController.php?acao=listar');
        exit;


    
        
    case 'excluir':

        // Verifica método POST e token CSRF.
        validarRequisicaoAdmin();

        // Valida o ID enviado pelo formulário.
        $id = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        if ($id === false || $id === null || $id <= 0) {
            http_response_code(400);
            exit('ID do produto inválido.');
        }

        try {
            $produtoDAO->excluir($id);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(409);
            exit('Não foi possível excluir o produto. Verifique se ele está associado a algum carrinho.');
        }

        header('Location: ProdutoController.php?acao=listar');
        exit;

}