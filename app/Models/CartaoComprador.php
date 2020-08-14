<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use App\Models\Pessoa;
use App\Models\Cartao;

class CartaoComprador extends BaseModel
{
	private $data = [];
    
    const TABLENAME = 'CartaoComprador';
    
    private $idCartaoComprador;
	private $idCartao;
	private $idPessoa;
	private $bandeira;
	private $dtRegistro;
    private $ativo;

    protected function parseCommit()
    {
        $arrayPase = [];
        for ($i=0; !($i == count($this->columns())) ; $i++) { 
            $chave = $this->columns()[$i]->Field;
            if(array_key_exists($chave, $this->data)){
                $arrayPase[$chave] = $this->data[$chave];
            }
        }
        return $arrayPase;
    }

    protected function clear(array $dados)
    {
        
    }
    public function save(array $dados)
    {
        $result = $this->parseCommit();

        $errors = $this->getError();
        if(strlen($errors) > 0){
            throw new Exception($errors);
            
        }
        
        $resultInsert = $this->insert($result);
        return $resultInsert;
    }

    public function modify(array $dados)
    {
        $result = $this->parseCommit();

        $resultInsert = $this->update($result, $this->idCartaoComprador);
        return $resultInsert;
    }

    public function findCategoriaFromId(Int $id)
    {
        $result = $this->select(['idCategoria','nomeCategoria'], ['idCategoria' => $id], '=',
        'asc', null, null, true);

        if($result != false){
            return $result[0];
        }
        throw new Exception("Categoria não encontrada\n");
        
    }


    public function getIdCartaoComprador()
    {
    	if((! isset($this->data['idCartaoComprador'])) || ($this->data['idCartaoComprador'] <= 0)){

    		if(isset($this->idCartaoComprador) && ($this->idCartaoComprador > 0)){
    			return $this->idCartaoComprador;
    		}

    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->data['idCartaoComprador'];
    }

    public function setIdCartaoComprador(Int $id)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->addError("Pripriedade não defindida");
            return false;
        }

        $this->data['idCartaoComprador'] = $id;
        return true;
    }

    public function getIdCartao()
    {
    	if((! isset($this->data['idCartao'])) || ($this->data['idCartao'] <= 0)){

    		if(isset($this->idCartao) && ($this->idCartao > 0)){
    			return $this->idCartao;
    		}

    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->data['idCartao'];
    }

    public function setIdCartao(Int $id)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->addError("Pripriedade não defindida");
            return false;
        }

        $this->data['idCartao'] = $id;
        return true;
    }

    public function getIdPessoa()
    {
    	if((! isset($this->data['idPessoa'])) || ($this->data['idPessoa'] <= 0)){

    		if(isset($this->idPessoa) && ($this->idPessoa > 0)){
    			return $this->idPessoa;
    		}

    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->data['idPessoa'];
    }

    public function setIdPessoa(Int $id)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->addError("Pripriedade não defindida");
            return false;
        }

        $this->data['idPessoa'] = $id;
        return true;
    }

    public function getDtRegistro()
    {
    	if((! isset($this->data['dtRegistro'])) || (strlen($this->data['dtRegistro']) == 0)){

    		if(isset($this->dtRegistro) && (strlen($this->dtRegistro) > 0)){
    			return $this->dtRegistro;
    		}

    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->data['dtRegistro'];
    }

    public function setDtRegistro(String $dtRegistro)
    {
        if((! isset($dtRegistro)) || (strlen($dtRegistro) == 0)){

            $this->addError("Pripriedade não defindida");
            return false;
        }

        $this->data['dtRegistro'] = $dtRegistro;
        return true;
    }


    public function getAtivo()
    {
        if((! isset($this->data['ativo'])) || (strlen($this->data['ativo']) == 0)){

            if(isset($this->ativo) && (strlen($this->ativo) > 0)){
                return $this->ativo;
            }

            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->data['ativo'];
    }

    public function setAtivo(String $ativo)
    {
       if((! isset($ativo)) || (strlen($ativo) == 0)){

            $this->addError("Pripriedade não defindida");
            return false;
        }

        $this->data['ativo'] = $ativo;
        return true;
    }


    public function getPessoa()
    {
    	$pessoa = new Pessoa();

    	$result = $pessoa->selectNew(['*'],
    	 [
    	 	['key'=>'idPessoa', 'val'=> $this->idPessoa, 'comparator'=> '=']
    	 ],

    	 [
    	 	['key'=>'idPessoa', 'order'=>'asc']
    	 ],

    	  1, null, true, false
    	);

        if($result == false){
            throw new Exception("Nenhum registro encontrado\n");
            
        }

    	return $result;
    }


    public function getCartao()
    {
    	$pessoa = new Cartao();

    	$result = $pessoa->selectNew(['*'],
    	 [
    	 	['key'=>'idCartao', 'val'=> $this->idCartao, 'comparator'=> '=']
    	 ],

    	 [
    	 	['key'=>'idCartao', 'order'=>'asc']
    	 ],

    	  1, null, true, false
    	);

        if($result == false){
            throw new Exception("Nenhum registro encontrado\n");
            
        }

    	return $result;
    }
 
}	