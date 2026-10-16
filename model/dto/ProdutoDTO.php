<?php

class ProdutoDTO
{
    private $id;
    private $nome;
    private $valor;
    private $quantidade;
    private $validade;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    public function getQuantidade()
    {
        return $this->quantidade;
    }

    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;
    }

    public function getValidade()
    {
        return $this->validade;
    }

    public function setValidade($validade)
    {
        $this->validade = $validade;
    }
}