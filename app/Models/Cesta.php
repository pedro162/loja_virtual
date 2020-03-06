<?php

namespace App\Models;

use Exception;

class Cesta
{
    private $time;
    private $itens;
    
    public function __construct()
    {
        $this->time = date('Y-m-d H:i:s');
        $this->itens = [];
    }


    public function addItem(Produto $produto):bool
    {
        if($produto instanceof \App\Models\Produto)
        {
            $this->itens[] = $produto;
            return true;
        }

        throw new Exception("Produto inválido<br/>\n");
    }

    public function getItens()
    {
        if(count($this->itens) == 0)
        {
            throw new Exception("Não existe itens<br/>\n");
        }

        return $this->itens;
    }


    public function getTime()
    {
        return $this->time;
    }


}