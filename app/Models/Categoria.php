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
    }

    public function listaCategoria():array
    {
    	$result = $this->select(['idCategoria','nomeCategoria']);
    	return $result;
    }


    public function getCategoria()
    {
    	if(empty($this->idCategoria)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->idCategori;
    }

    public function getIdCategoria()
    {
    	if(empty($this->idCategoria)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->idCategoria;
    }


 




}
