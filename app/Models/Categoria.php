<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Categoria extends BaseModel
{
    private $data = [];
    
    const TABLENAME = 'Categoria';
    
	private $nomeCategoria;
	private $idCategoria;


    protected function parseCommit()
    {
        $this->data['nomeCategoria']= $this->getCategoria();

        return $this->data;
    }
    protected function clear(array $dados)
    {
        //falta implementar corretamente
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        for ($i=0; !($i == count($dados)) ; $i++) { 

            $subArray = explode('=', $dados[$i]);
           
            switch ($subArray[0]) {
                case 'categoria':
                   $this->setCategoria($subArray[1]);
                   break;
            }

        }
    }
    public function save(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $resultSelect = $this->select(['nomeCategoria'], ['nomeCategoria' => $this->getCategoria()], '=','asc', null, null, true);

        if($resultSelect != false){

            return ['msg','warning','Atenção: Esta categoria já existe!'];
        }

        $resultInsert = $this->insert($result);
        if($resultInsert == true){
            return ['msg','success','Categoria cadastrada com sucesso!'];
        }

        return ['msg','warning','Falha ao cadastrar categoria!'];
    }

    public function modify(array $dados)
    {
        
    }

    public function listaCategoria():array
    {
    	$result = $this->select(['idCategoria','nomeCategoria'], [], '=',
     'asc', null, null, true);
    	return $result;
    }

    public function findCategoriaFromId(Int $id)
    {
        $result = $this->select(['idCategoria','nomeCategoria'], ['idCategoria' => $id], '=',
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

    public function setCategoria(String $categoria):bool
    {
        if(isset($categoria) && (strlen($categoria) >= 4) && (strlen($categoria) <= 20)){
            $this->nomeCategoria = $categoria;
            return true;
        }
        throw new Exception('Parametro inválido<br/>'.PHP_EOL);
    }

    public function getIdCategoria()
    {
    	if(empty($this->idCategoria)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->idCategoria;
    }

    
 




}
