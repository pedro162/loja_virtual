<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Exception;
use \InvalidArgumentException;
use App\Models\Departamento;

class ProdutoCategoria extends BaseModel
{
    protected $table = 'ProdutoCategoria';
    private $data = [];

    public function __construct()
    {
        //self::open();
        $this->start();
    }


    public function getCategoria(Int $id)
    {
    	$sql = "select DISTINCT C.nomeCategoria, C.idCategoria ";
		$sql .=	"from ProdutoCategoria PG inner join Categoria C ";
		$sql .=	"on C.idCategoria = PG.CategoriaIdCategoria ";
		$sql .=	"inner join Produto P ";
		$sql .=	"on P.idProduto = PG.ProdutoIdProduto ";
		$sql .=	"where P.idProduto = ".$id;

    	$restult = $this->persolizaConsulta($sql, get_class($this));

    	return $restult;
    }


    public function getData(){
    	return $this->data;
    }

    public function getProduto()
    {

    }

    public function setNomeCategoria($prop, $value){
    	$this->data[$prop] = $value;
    }


   	public function __get($prop)
    {
    	
    	if(!array_key_exists($prop, $this->data)){
    		unset($prop);
    		throw new Exception('Propriedade indefiida<br/>'.PHP_EOL);
    	}

		//suposto method
		$method = 'set'.ucfirst($prop);

		//Se o suposto method existir, ele tem prioridade de executar
		if(method_exists($this, $method)){
			return $this->$method();
		}else{
			return $this->data[$prop];
    	}

    }

    public function __set($prop, $value)
    {
    	if((!isset($value)) || empty($value)){
    		unset($prop);
    		unset($value);
    	}else{

    		//suposto method
    		$method = 'set'.ucfirst($prop);

    		//Se o suposto method existir, ele tem prioridade de executar
    		if(method_exists($this, $method)){
    			$this->$method($prop, $value);
    		}else{
    			$this->data[$prop] = $value;
    		}
    	}
    }

    public function __isset($prop)
    {
    	return isset($this->data[$prop]);
    }



}
