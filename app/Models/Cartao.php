<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use App\Models\CartaoComprador;

class Cartao extends BaseModel
{
	private $data = [];
    
    const TABLENAME = 'Cartao';
    
	private $idCartao;
	private $numero;
	private $cvv;
	private $mesValidade;
	private $anoValidade;
	private $cpfTitular;
	private $dtRegistro;
    private $bandeira;

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
        
    }


    
    public function getBandeira()
    {
        if((! isset($this->data['bandeira'])) || (strlen($this->data['bandeira']) == 0)){

            if(isset($this->bandeira) && (strlen($this->bandeira) > 0)){
                return $this->bandeira;
            }

            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->data['bandeira'];
    }

    public function setBandeira(String $bandeira)
    {
        if((! isset($bandeira)) || (strlen($bandeira) == 0)){

            $this->addError("Banderia do cartao inválida");
            return false;
        }

        $this->data['bandeira'] = $bandeira;

        return true;
    }

    public function getNumero()
    {
        if((! isset($this->data['numero'])) || (strlen($this->data['numero']) == 0)){

            if(isset($this->numero) && (strlen($this->numero) > 0)){
                return $this->numero;
            }

            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->data['numero'];
    }

    public function setNumero(String $numero)
    {
        if((! isset($numero)) || (strlen($numero) == 0)){

            $this->addError("Cartao inválido");
            return false;
        }

        $this->data['numero'] = $numero;

        return true;
    }

    public function getCvv()
    {
        if((! isset($this->data['cvv'])) || (strlen($this->data['cvv']) == 0)){

            if(isset($this->cvv) && (strlen($this->cvv) > 0)){
                return $this->cvv;
            }

            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->data['cvv'];
    }

    public function setCvv(String $cvv)
    {
        if((! isset($cvv)) || (strlen($cvv) == 0)){

            $this->addError("Codigo de verificação inválido");
            return false;
        }

        $this->data['cvv'] = $cvv;

        return true;
    }

    public function getMesValidade()
    {
        if((! isset($this->data['mesValidade'])) || (strlen($this->data['mesValidade']) == 0)){

            if(isset($this->mesValidade) && (strlen($this->mesValidade) > 0)){
                return $this->mesValidade;
            }

            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->data['mesValidade'];
    }

    public function getAnoValidade()
    {
        if((! isset($this->data['anoValidade'])) || (strlen($this->data['anoValidade']) == 0)){

            if(isset($this->anoValidade) && (strlen($this->anoValidade) > 0)){
                return $this->anoValidade;
            }

            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->data['anoValidade'];
    }

    public function setAnoValidade(String $anoValidade)
    {
        if((! isset($anoValidade)) || (strlen($anoValidade) == 0)){

            $this->addError("Ano de validade inválida");
            return false;
        }

        $this->data['anoValidade'] = $anoValidade;

        return true;
    }

    public function setMesValidade(String $mesValidade)
    {
        if((! isset($mesValidade)) || (strlen($mesValidade) == 0)){

            $this->addError("Data de validade inválida");
            return false;
        }

        $this->data['mesValidade'] = $mesValidade;

        return true;
    }

    public function getCpfTitular()
    {
        if((! isset($this->data['cpfTitular'])) || (strlen($this->data['cpfTitular']) == 0)){

            if(isset($this->cpfTitular) && (strlen($this->cpfTitular) > 0)){
                return $this->cpfTitular;
            }

            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->data['cpfTitular'];
    }

    public function setCpfTitular(String $cpfTitular)
    {
        if((! isset($cpfTitular)) || (strlen($cpfTitular) < 11)){

            $this->addError("Cpf inválido");
            return false;
        }

        $this->data['cpfTitular'] = $cpfTitular;

        return true;
    }
    
    public function listartoes():array
    {
    	$result = $this->select(['*'], [], '=',
     'asc', null, null, true);
    	return $result;
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

            $this->addError("Parãmetro inválido");
            return false;
        }

        $this->data['dtRegistro'] = $dtRegistro;

        return true;
    }
    

    public function getIdCartao()
    {
    	if((! isset($this->data['idCartao'])) || (strlen($this->data['idCartao']) == 0)){

            if(isset($this->idCartao) && (strlen($this->idCartao) > 0)){
                return $this->idCartao;
            }

            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->data['idCartao'];
    }

    public function setIdCartao(Int $id)
    {
        if((! isset($id)) || ($id <= 0)){

            $this->addError("Parãmetro inválido");
            return false;
        }

        $this->data['idCartao'] = $id;

        return true;
    }


    public function getCartaoComprador()
    {
    	$cartao = new CartaoComprador();

    	$result = $cartao->selectNew(['*'],
    	 [
    	 	['key'=>'idCartao', 'val'=> $this->idCartao, 'comparator'=> '=']
    	 ],

    	 [
    	 	['key'=>'idCartao', 'order'=>'asc']
    	 ],

    	  null, null, true, false
    	);

    	return $result;
    }

 
}	