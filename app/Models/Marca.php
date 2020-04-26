<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Marca extends BaseModel
{
	protected $table = 'Marca';

	private $nomeMarca;
	private $idMarca;


    public function __construct()
    {
        self::open();
    }

    public function listaMarca():array
    {
    	$result = $this->select(['idMarca','nomeMarca']);
    	return $result;
    }


    public function getMarca()
    {
    	if(empty($this->nomeMarca)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->nomeMarca;
    }

    public function getIdMarca()
    {
    	if(empty($this->idMarca)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->idMarca;
    }




}
