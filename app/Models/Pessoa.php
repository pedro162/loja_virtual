<?php

namespace App\Models;

abstract class Pessoa extends BaseModel
{
    protected $nome;
    protected $telefone;
    protected $celular;
    protected $email;
    protected $grupo;

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    protected $table = 'Pessoa';




   protected function clear(array $dados)//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
    {
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        for ($i=0; !($i == count($dados)) ; $i++) { 

            $subArray = explode('=', $dados[$i]);
           
            switch ($subArray[0]) {
                case 'nome':
                   $this->setNomeProduto($subArray[1]);
                   break;
                case 'telefone':
                   $this->setTextoPromorcional($subArray[1]);
                   break;

                case 'email':
                   $this->setEstoque($subArray[1]);
                   break;

                case 'celular':
                    $idMarca = (int) $subArray[1];
                   $this->setIdMarca($idMarca);
                   break;

                case 'grupo':
                   $this->setNf($subArray[1]);
                   break;

            }

        }
    }

    protected function parseCommit()
    {
        $this->data['nome']        	= $this->getNome();
        $this->data['telefone']     = $this->getElefone();
        $this->data['email']   		= $this->getEmail();

        return $this->data;
    }


    public function commit(array $dados)
    {

        $this->clear($dados);

        $result = $this->parseCommit();


        //Transaction::startTransaction(self::getDatabase());
        
        $this->insert($result);

        //Transaction::close();
    }
} 