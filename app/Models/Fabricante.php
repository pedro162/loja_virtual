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
    



}