<?php

namespace App\Models;

use \Exception;
use InvalidArgumentException;

class Produto extends BaseModel
{
    private $nomeProduto;
    private $textoPromorcional;
    private $preco;
    private $fabricante;
    private $caracteristicas;

    protected $table = 'Produto';

    public function __construct()
    {
        self::open();
    }



    public function addProduto()
    {
        # code...
    }


    public function salvarProduto($resquest)//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
    {
        # code...
    }


    public function listarProdutos():array
    {
        $resultSelect = $this->select(['nomeProduto','textoPromorcional', 'idProduto', 'preco']);

        $gridProdutos = [];

        if((count($resultSelect) % 2) ==0)
        {
           for ($i=0; !($i == count($resultSelect)); $i+=6) {

                $subArray = [];

                $sentinela = 0;
                while (!($sentinela == 6)) {
                    $subArray[] = $resultSelect[$i + $sentinela];
                    $sentinela ++;
                }
                $gridProdutos[] = $subArray;
            } 
        }
        else{
           for ($i=0; !($i == count($resultSelect)); $i+=3) {

                $subArray = [];

                $sentinela = 0;
                while (!($sentinela == 3)) {
                    $subArray[] = $resultSelect[$i + $sentinela];
                    $sentinela ++;
                }
                $gridProdutos[] = $subArray;
            } 
        }
        
        return $gridProdutos;
    }


    public function setNomeProduto(string $nomeProduto):bool
    {
        if(!((is_string($nomeProduto)) && (strlen($nomeProduto) >= 4)))
        {
            throw new Exception("Descricao inválida<br/>\n");
        }

        $this->nomeProduto = $nomeProduto;

        return true;
    }



    public function getNomeProduto():string
    {
        if(empty($this->nomeProduto))
        {
            throw new InvalidArgumentException("Descrição não definida<br/>");
        }

        return $this->nomeProduto;
    }


    public function setTextoPromorcional(String $texto):bool
    {
       if(strlen($texto < 6))
       {
            throw new Exception("Texto promorcional muto curto<br/>\n");
       }

       $this->textoPromorcional = $texto;
       return true;
    }


    public function getTextoPromorcional():string
    {
        if(empty($this->textoPromorcional))
        {
            throw new InvalidArgumentException("Descrição não definida<br/>");
        }

        return $this->textoPromorcional;
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

