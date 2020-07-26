<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class LogradouroPessoa extends BaseModel
{
	private $complemento;
	private $endereco;
	private $idLogradouro;
    private $idLogradouroPessoa;
    private $LogradouroIdLogradouro;
    private $PessoaIdPessoa;
    private $idUsuario;

    const TABLENAME = 'LogradouroPessoa';

    private $data = [];


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
                case '':
                   break;
            }

        }
    }

    protected function parseCommit():array
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


    public function save(array $dados)
    {

        $info = $this->parseCommit();

        $response = $this->insert($info);

        if($response == false){
            throw new Exception("Erro ao salvar registro\n");
            
        }

        return true;
    }

    public function modify(array $dados)
    {
        
    }


    public function listarConsultaPersonalizada(String $where = null, Int $limitInt = NULL, Int $limtEnd = NULL, $clasRetorno = false)
    {

        $sql = "SELECT L.endereco, L.complemento, L.idLogradouro, P.nomePessoa
				FROM 
				LogradouroPessoa LP inner join Logradouro L on LP.LogradouroIdLogradouro = L.idLogradouro
				INNER JOIN Pessoa P on LP.PessoaIdPessoa = P.idPessoa ";

        if($where != null)
        {
            $sql .= ' WHERE '.$where;
        }

        if(($limitInt != NULL) && ($limtEnd != NULL)){

            if(($limitInt >= 0) && ($limtEnd >= 0)){
                $sql .= ' LIMIT '.$limitInt.','. $limtEnd; 
            }
        } 
        $result = $this->persolizaConsulta($sql, $clasRetorno);

        return $result;
    }


    public function findLogPessoa(Int $id, $clasRetorno = false)
    {
        $sql = "SELECT LP.idLogradouroPessoa
                FROM 
                LogradouroPessoa LP inner join Logradouro L on LP.LogradouroIdLogradouro = L.idLogradouro
                INNER JOIN Pessoa P on LP.PessoaIdPessoa = P.idPessoa WHERE L.idLogradouro=".$id;
                
        $result = $this->persolizaConsulta($sql, $clasRetorno);

        return $result;

    }

    public function getLogradouro()
    {
        $logradouro  = new Logradouro();
        $result = $logradouro->select(['*'], ['idLogradouro' => $this->LogradouroIdLogradouro], '=', 'asc', null, null, true);

        if($result != false){
            return $result;
        }

        throw new Exception("Registro não encontrado\n");
    }

    public function getComplemento()
    {
    	if(isset($this->complemento) && (!empty($this->complemento))){
    		return $this->complemento;
    	}
    	throw new \Exception("Proriedade indefinida");
    	
    }

    public function getEndereco()
    {
    	if(isset($this->endereco) && (!empty($this->endereco))){
    		return $this->endereco;
    	}

    	throw new \Exception("Proriedade indefinida");
    }

    public function getIdLogradouro()
    {
    	if(isset($this->idLogradouro) && (!empty($this->idLogradouro))){
    		return $this->idLogradouro;
    	}

    	throw new \Exception("Proriedade indefinida");
    }

    public function setIdUsuario(Int $id):bool
    {
        if((!isset($id)) || ($id <=0)){
            throw new Exception("Parãmetro inválido\n");
           
        }

        $this->data['idUsuario'] = $id;
        return true;
    }

    public function setPessoaIdPessoa(Int $id):bool
    {
        //falta validar
        if((!isset($id)) || ($id <=0)){
            throw new Exception("Parãmetro inválido\n");
            
        }

        $this->data['PessoaIdPessoa'] = $id;
        return true;
    }

    public function getIdLogradouroPessoa()
    {
        //falta validar
        if((!isset($this->idLogradouroPessoa)) || ($this->idLogradouroPessoa <=0)){
            if(isset($this->idLogradouroPessoa) && ($this->idLogradouroPessoa > 0)){
                return $this->data['idLogradouroPessoa'];
            }
        }

        return $this->idLogradouroPessoa;
    }

    public function setIdLogradouroPessoa(Int $id):bool
    {
        //falta validar
        if((!isset($id)) || ($id <=0)){
            throw new Exception("Parãmetro inválido\n");
            
        }

        $this->data['idLogradouroPessoa'] = $id;
        return true;
    }

    public function setLogradouroIdLogradouro(Int $id):bool
    {
        //falta validar
        if((!isset($id)) || ($id <=0)){
            throw new Exception("Parãmetro inválido\n");
            
        }

        $this->data['LogradouroIdLogradouro'] = $id;
        return true;
    }

}
