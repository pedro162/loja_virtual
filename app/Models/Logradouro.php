<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use App\Models\Produto;

class Logradouro extends BaseModel
{
    private $idLogradouro;
    private $tipo;
    private $cep;
    private $endereco;
    private $complemento;
    private $CidadeIdCidade;
    private $bairro;
    private $UsuarioIdUsuario;

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    const TABLENAME = 'Logradouro';


    protected function clear(array $dados):bool
    {
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        foreach ($dados as $key => $value) {
           
            switch ($key) {
                case 'nome':
                   
                	break;
            }

        }

        return true;
    }

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

    public function save(array $dados):bool
    {

        $result = $this->parseCommit(); //retorna os dados já filtrados 
        $resultInsertComent = $this->insert($result);//salva o comentario
        
        if($resultInsertComent == false){

        	throw new Exception("Erroa cadastrar comentário\n");
        	
        }
        return true;

    }

    //ajustar method de update
    public function modify(array $dados)
    {
        //$this->clear($dados);

       // $result = $this->parseCommit();

        //$resultUpdate = $this->update($result, $this->getIdProduto());

        
    }

   public function getComplemento()
    {
        if((!isset($this->complemento)) || (strlen($this->complemento) ==0 )){
            if(isset($this->data['complemento']) && (strlen($this->data['complemento']) > 0)){
                return $this->data['complemento'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->complemento;
    }
   	
   	public function getIdLogradouro():int
    {
        if((!isset($this->idLogradouro)) || ($this->idLogradouro <= 0)){

            if(isset($this->data['idLogradouro']) && ($this->data['idLogradouro'] > 0)){
                return $this->data['idLogradouro'];
            }
            throw new Exception("Propriedade não definida.");
        }

        return $this->idLogradouro;
        
        
    }

	public function getCidadeidCidade():int
    {
        if((!isset($this->CidadeIdCidade)) || ($this->CidadeIdCidade <= 0)){

            if(isset($this->data['CidadeIdCidade']) && ($this->data['CidadeIdCidade'] > 0)){
                return $this->data['CidadeIdCidade'];
            }
            throw new Exception("Propriedade não definida.");
        }

        return $this->CidadeIdCidade;
        
        
    }

    public function getCidade()
    {
    	$cidade  = new Cidade();
    	$result = $cidade->select(['*'], ['idCidade' => $this->getCidadeidCidade()], '=', 'asc', null, null, true);

    	if($result != false){
    		return $result[0];
    	}

    	throw new Exception("Registro não encontrado\n");
    }

    public function getUsuarioIdUsuario():int
    {
        if((!isset($this->UsuarioIdUsuario)) || ($this->UsuarioIdUsuario <= 0)){

            if(isset($this->data['UsuarioIdUsuario']) && ($this->data['UsuarioIdUsuario'] > 0)){
                return $this->data['UsuarioIdUsuario'];
            }
            throw new Exception("Propriedade não definida.");
        }

        return $this->UsuarioIdUsuario;
        
        
    }

    public function getCep()
    {
        if((!isset($this->cep)) || (strlen($this->cep) ==0 )){
            if(isset($this->data['cep']) && (strlen($this->data['cep']) > 0)){
                return $this->data['cep'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->cep;
    }

    public function getBairro()
    {
        if((!isset($this->bairro)) || (strlen($this->bairro) ==0 )){
            if(isset($this->data['bairro']) && (strlen($this->data['bairro']) > 0)){
                return $this->data['bairro'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->bairro;
    }

    public function getTipo()
    {
        if((!isset($this->tipo)) || (strlen($this->tipo) ==0 )){
            if(isset($this->data['tipo']) && (strlen($this->data['tipo']) > 0)){
                return $this->data['tipo'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->cipo;
    }

    public function getEndereco()
    {
        if((!isset($this->endereco)) || (strlen($this->endereco) ==0 )){
            if(isset($this->data['endereco']) && (strlen($this->data['endereco']) > 0)){
                return $this->data['endereco'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->endereco;
    }

    public function __get($prop)
    {
        if(method_exists($this, 'get'.ucfirst($prop))){

            return call_user_func([$this,'get'.ucfirst($prop)]);
        }
    }

    public function __set($prop, $value)
    {   
        if(method_exists($this, 'set'.ucfirst($prop))){ 
            return call_user_func([$this,'set'.ucfirst($prop)], $value);
        }
    }
    


}


