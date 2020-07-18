<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use App\Models\Produto;

class Cidade extends BaseModel
{
    private $idCidade;
    private $nome;
    private $UfIdUf;
    private $UsuarioIdUsuario;

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    const TABLENAME = 'Cidade';


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

   	public function getNome()
    {
        if((!isset($this->nome)) || (strlen($this->nome) ==0 )){
            if(isset($this->data['nome']) && (strlen($this->data['nome']) > 0)){
                return $this->data['nome'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->nome;
    }
   	
   	public function getIdCidade():int
    {
        if((!isset($this->idCidade)) || ($this->idCidade <= 0)){

            if(isset($this->data['idCidade']) && ($this->data['idCidade'] > 0)){
                return $this->data['idCidade'];
            }
            throw new Exception("Propriedade não definida.");
        }

        return $this->idCidade;
        
        
    }

    public function getUfIdUf():int
    {
        if((!isset($this->UfIdUf)) || ($this->UfIdUf <= 0)){

            if(isset($this->data['UfIdUf']) && ($this->data['UfIdUf'] > 0)){
                return $this->data['UfIdUf'];
            }
            throw new Exception("Propriedade não definida.");
        }

        return $this->UfIdUf;
        
        
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

