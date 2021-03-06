<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Marca extends BaseModel
{
    const TABLENAME = 'Marca';

	private $nomeMarca;
	private $idMarca;

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
                case 'marca':
                   $this->setNomeMarca($subArray[1]);
                   break;
            }

        }
    }

    protected function parseCommit():array
    {
         //falta implemetar
        $this->data['nomeMarca']= $this->getNomeMarca();

        return $this->data;
    }


    public function save(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $resultSelect = $this->select(['nomeMarca'], ['nomeMarca' => $this->getNomeMarca()], '=','asc', null, null, true);

        if($resultSelect != false){
            return ['msg','warning','Atenção: Esta marca já existe!'];
        }

        $resultInsert = $this->insert($result);
        if($resultInsert == true){
            return ['msg','success','Marca cadastrada com sucesso!'];
        }

        return ['msg','warning','Falha ao cadastrar marca!'];
    }


    public function modify(array $dados)
    {
        
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

    public function setNomeMarca(String $nome):bool
    {
        if(isset($nome) && ((strlen($nome) >= 4) && (strlen($nome) <= 20)))
        {
            $this->nomeMarca = $nome;
            return true;
        }
        throw new Exception("Parâmetro inválido<br/>\n");
    }

    public function getIdMarca()
    {
    	if(empty($this->idMarca)){
    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->idMarca;
    }


    public function loadMarcaBasedCateProduct(Int $idCateg)
    {
        if((! isset($idCateg)) || ($idCateg <= 0)){
                throw new Exception('Parâmetro inválido');
        }


        $sql = 'select distinct M.idMarca, M.nomeMarca
                from Marca M
                inner join Produto P on P.idMarca = M.idMarca
                inner join ProdutoCategoria PC on PC.ProdutoIdProduto = P.idProduto
                where PC.CategoriaIdCategoria = '.$idCateg;

        $result = $this->persolizaConsulta($sql, true);

        return $result;
    }


}
