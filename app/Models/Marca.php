<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Marca extends BaseModel
{
	protected $table = 'Marca';

	private $nomeMarca;
	private $idMarca;

    private $data = [];

    public function __construct()
    {
        //self::open();
        $this->start();
    }


    protected function clear(array $dados)//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
    {
        /*falta implementar corretamente
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
                case 'texto':
                   $this->setTextoPromorcional($subArray[1]);
                   break;

                case 'quantidade':
                   $this->setEstoque($subArray[1]);
                   break;

                case 'marca':
                    $idMarca = (int) $subArray[1];
                   $this->setIdMarca($idMarca);
                   break;

                case 'nf':
                   $this->setNf($subArray[1]);
                   break;

                case 'codigo':
                   $this->setCodigoProduto($subArray[1]);
                   break;

                case 'preco':
                   $this->setPreco($subArray[1]);
                   break;
            }

        }*/
    }

    protected function parseCommit()
    {
        /* //falta implemetar
        $this->data['nomeProduto']          = $this->getNomeMarca();
        $this->data['IdMarca']              = $this->getIdMarca();
        $this->data['preco']                = $this->getPreco();
        $this->data['codigo']               = $this->getCodigoProduto();
        $this->data['nf']                   = $this->getNf();
        $this->data['estoque']              = $this->getEstoque();
        $this->data['textoPromorcional']    = $this->getTextoPromorcional();*/

        return $this->data;
    }


    public function commit(array $dados)
    {
       /* $this->clear($dados);

        $result = $this->parseCommit();

        $this->insert($result);
        var_dump($result);*/
    }


    public function listaMarca():array
    {
    	$result = $this->select(['idMarca','nomeMarca'], [], '=','asc', null, null, true);
    	return $result;
    }


    public function getNomeMarca()
    {
    	if(empty($this->nomeMarca)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->nomeMarca;
    }

    public function getIdMarca()
    {
    	if(empty($this->idMarca)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->idMarca;
    }




}
