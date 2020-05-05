<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Categoria extends BaseModel
{
	protected $table = 'Categoria';
   

	private $nomeCategoria;
	private $idCategoria;


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

    public function listaCategoria():array
    {
    	$result = $this->select(['idCategoria','nomeCategoria'], [], '=',
     'asc', null, null, true);
    	return $result;
    }


    public function getCategoria()
    {
    	if(empty($this->nomeCategoria)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->nomeCategoria;
    }

    public function getIdCategoria()
    {
    	if(empty($this->idCategoria)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->idCategoria;
    }

    
 




}
