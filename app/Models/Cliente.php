<?php

namespace App\Models;
use \App\Models\BaseModel;

class Cliente extends BaseModel
{
    protected $table = 'Clientes';

    public function __construct()
    {
       //self::open();
    	$this->start();
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
