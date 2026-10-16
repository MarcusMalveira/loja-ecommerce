<?php

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/dao/ProdutoDAO.php';
require_once __DIR__ . '/../model/dto/ProdutoDTO.php';

$produtoDAO = new ProdutoDAO($pdo);
$acao = $_GET['acao'] ?? 'listar';

switch ($acao) {
    case 'listar':
        $produtos = $produtoDAO->listarTodos();
        require __DIR__ . '/../view/loja.php';
        break;

    case 'formulario':
        require __DIR__ . '/../view/formularioProdutos.php';
        break;

    case 'cadastrar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ProdutoController.php?acao=formulario');
            exit;
        }

        $produto = new ProdutoDTO();
        $produto->setNome($_POST['nome']);
        $produto->setValor($_POST['valor']);
        $produto->setQuantidade($_POST['quantidade']);
        $produto->setValidade($_POST['validade']);

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die('Requisição inválida');
        }

        $produto = new ProdutoDTO();
        $produto->setId($_POST['id']);
        $produto->setNome($_POST['nome']);
        $produto->setValor($_POST['valor']);
        $produto->setQuantidade($_POST['quantidade']);
        $produto->setValidade($_POST['validade']);

        $produtoDAO->atualizar($produto);

        header('Location: ProdutoController.php?acao=listar');
        exit;

    case 'excluir':
        if (!isset($_GET['id'])) {
            die('ID do produto não encontrado');
        }

        $produtoDAO->excluir((int) $_GET['id']);

        header('Location: ProdutoController.php?acao=listar');
        exit;

    default:
        echo "Ação inválida.";
        break;
}