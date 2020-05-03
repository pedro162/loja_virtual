<?php

namespace App\Models;

use Exception;

class Fabricante extends Pessoa
{
    
    public function __construct()
    {
        
    }


    public function getNome():string
    {
        if(empty($this->nome))
        {
            throw new Exception("Nome indefinido<br/>\n");
        }
        return $this->nome;
    }
    
    protected function parseCommit()
    {

    }
    protected function clear(array $dados)
    {

    }
    public function commit(array $dados)
    {
        
    }



}