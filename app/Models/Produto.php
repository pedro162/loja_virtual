<?php

namespace App\Models;

use \Exception;
use InvalidArgumentException;

class Produto extends BaseModel
{
    private $descricao;
    private $estoque;
    private $preco;
    private $fabricante;
    private $caracteristicas;

    protected $table = 'Produto';

    public function __construct()
    {
        self::open();
        //$this->setPreco($preco);
        //$this->setEstoque($estoque);
        //$this->setDescricao($nome);
    }


    public function setDescricao(string $descricao):bool
    {
        if(!((is_string($descricao)) && (strlen($descricao) >= 4)))
        {
            throw new Exception("Descricao inválida<br/>\n");
        }

        $this->descricao = $descricao;

        return true;
    }



    public function getDescricao():string
    {
        if(empty($this->descricao))
        {
            throw new InvalidArgumentException("Descrição não definida<br/>");
        }

        return $this->descricao;
    }


    public function setEstoque(int $estoque):bool
    {
        if(!(is_integer($estoque) && ($estoque > 0)))
        {
            throw new Exception("Estoque inválido<br/>\n");
        }

        $this->estoque = $estoque;
        return true;
    }

    public function getEstoque():int
    {
        if(empty($this->estoque))
        {
            throw new InvalidArgumentException("Estoque indefinido<br/>");
        }

        return $this->estoque;
    }   


    public function setPreco(float $preco):bool
    {
        if(is_float($preco) && ($preco > 0))
        {
            $this->preco = $preco;
            return true;
        }

        throw new Exception("Preço inválido<br/>\n");

    }

    public function getPreco():float
    {
        if(empty($this->preco))
        {
            throw new Exception("Preço indefinido<br/>\n");
        }

        return $this->preco;
    }


    public function setFabricante(Fabricante $newFabricante)
    {
        $this->fabricante = $newFabricante;
    }

    public function getFabricante():Fabricante
    {
        if(empty($this->fabricante))
        {
            throw new InvalidArgumentException("Fabricante indefinido<br/>\n");
        }

        return $this->fabricante;
    }

    public function addCaracteristica(String $nome, String $valor):bool
    {
        $this->caracteristicas[] = new Caracteristica($nome, $valor);
        
        return true;
    }

    public function getCaracteristicas():array
    {
        if(empty($this->caracteristicas))
        {
            throw new Exception("Caracteristicas indefinidas<br/>\n");
        }

        return $this->caracteristicas;

    }


}

