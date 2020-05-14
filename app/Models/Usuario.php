<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Usuario extends BaseModel
{
	protected $table = 'Usuario';

	private $login;
	private $password;
	private $idUsuario;

    private $data = [];


    protected function clear(array $dados)//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
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
         //falta implemetar
        //$this->data['idUsuario']      = $this->getIdUser();
        //$this->data['login']          = $this->getLogin();
        //$this->data['senha']          = $this->getPassword();

        return $this->data;
    }


    public function save(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $this->insert($result);
    }

    public function modify(array $dados)
    {
        
    }


    public function listaMarca():array
    {
    	$result = $this->select(['idMarca','nomeMarca'], [], '=','asc', null, null, true);
    	return $result;
    }


    public function getIdUser()
    {
    	if(empty($this->idUsuario)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->idUsuario;
    }


    public function getLogin()
    {
    	if(empty($this->login)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->login;
    }


    public function getPassword()
    {
    	if(empty($this->password)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->password;
    }




}
