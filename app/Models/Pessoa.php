<?php

namespace App\Models;

abstract class Pessoa extends BaseModel
{
    protected $nome;
    protected $telefone;
    protected $celular;
    protected $email;

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