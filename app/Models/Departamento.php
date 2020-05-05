<?php

namespace App\Models;

use App\Models\Models;
use \Exception;
use \InvalidArgumentException;

class Departamento extends BaseModel
{
	protected $table = 'Departamento';

    public function __construct()
    {
        self::open();
        //$this->start();
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