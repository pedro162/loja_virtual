<?php

namespace App\Models;

use Exception;

class Caracteristica
{
    private $nome;
    private $valor;

    public function __construct(String $nome, string $valor)
    {
        $this->nome = $nome;
        $this->valor = $valor;
    }


    public function getNome():string
    {
        if(empty($this->nome))
        {
            throw new Exception("Nome indefinido para característica<br/>\n");
            
        }

        return $this->nome;

    }


    public function getValor():string
    {
        if(empty($this->valor))
        {
            throw new Exception("Valor indefinido para características<br/>\n");
        }

        return $this->valor;
    }



}