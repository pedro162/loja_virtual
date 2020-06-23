<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Usuario extends BaseModel
{
    const TABLENAME = 'Usuario';

	private  $login;
	private  $senha;
	private  $idUsuario;
    private $img;

    private static $dataUserSection = [];

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

    public function getIdUsuario()
    {
    	if(empty($this->idUsuario)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return (int) $this->idUsuario;
    }


    public function getLogin()
    {
    	if(empty($this->login)){
    		throw new Exception("Pripriedade não defindida jj<br/>");
    	}

    	return $this->login;
    }


    public function getSenha()
    {
    	if(empty($this->senha)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->senha;
    }

    public function getImg()
    {
        if(empty($this->img)){
            throw new Exception("Pripriedade não defindida<br/>");
        }

        return $this->img;
    }


    public function findLoginForUserPass($user, $pass)
    {
        if((!isset($user) )|| (!isset($pass))){
            throw new Exception("Parâmetro inválido\n");
            
        }

        $user = trim($user);
        $pass = trim($pass);
        if((strlen($user) == 0)|| (strlen($pass) == 0)){
            throw new Exception("Parâmetro inválido\n");
            
        }


        $result = $this->select(['idUsuario', 'login', 'senha', 'img'], ['login' => $user], '=', 'asc', null, null, true, false);

        if($result == false){
            throw new Exception("Usuario ou senha inválidos\n");
        }

        if (($result[0]->getLogin() === $user) && ($result[0]->getSenha() === $pass)) {

            return $result[0];
        }

        throw new Exception("Usuario ou senha inválidos\n");

        
    }
}
