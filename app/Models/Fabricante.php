<?php

namespace App\Models;

use Exception;

class Fabricante
{
    private $nome;
    private $endereco;
    private $documento;

    public function __construct(string $nome, string $endereco, int $document)
    {
        $this->nome = $nome;
        $this->endereco = $endereco;
        $this->documento = $document;
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