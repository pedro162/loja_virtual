<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use App\Models\Departamento;
use App\Models\Produto;

class ProdutoCategoria extends BaseModel
{   
    protected $ProdutoIdProduto;
    protected $idProdutoCategoria;
    protected $CategoriaIdCategoria;

    const TABLENAME = 'ProdutoCategoria';
    protected $data = [];

    protected function parseCommit()
    {   
        $this->data['ProdutoIdProduto']          = $this->ProdutoIdProduto;
        $this->data['CategoriaIdCategoria']      = $this->CategoriaIdCategoria;

        return $this->data;

    }
    protected function clear(array $dados)
    {
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        foreach ($dados as $key => $value) {
            
            switch ($key) {
                case 'idProduto':
                   $this->setIdProduto($value);
                   break;
                case 'idCategoria':
                   $this->setIdCategoria($value);
                   break;

            }

        }
    }

    public function setIdCategoria(Int $id):bool
    {   
        if($id > 0){
            $this->CategoriaIdCategoria = $id;//faltar validar
            return true;
        }
        throw new Exception("Propriedade inválida<br/>\\n");
        
    }

    public function modify(array $dados)
    {
        //method abstract
    }
    

    public function setIdProduto(Int $id):bool
    {
        if($id > 0){
            $this->ProdutoIdProduto = $id;//faltar validar
            return true;
        }
        throw new Exception("Propriedade inválida<br/>\\n");

    }


    public function save(array $dados):bool
    {
        $this->clear($dados);

        $result = $this->parseCommit();
        
        return $this->insert($result);
    }


    public function getCategoria(Int $idProduto)
    {

    	$sql = "select DISTINCT C.nomeCategoria, C.idCategoria ";
		$sql .=	"from ProdutoCategoria PG inner join Categoria C ";
		$sql .=	"on C.idCategoria = PG.CategoriaIdCategoria ";
		$sql .=	"inner join Produto P ";
		$sql .=	"on P.idProduto = PG.ProdutoIdProduto ";
		$sql .=	"where P.idProduto = ".$idProduto;

    	$restult = $this->persolizaConsulta($sql, get_class($this));

    	return $restult;
    }


    public function getData(){
    	return $this->data;
    }

    public function getProduto(Int $idCategoria)
    {

        $sql = "select DISTINCT C.nomeCategoria, C.idCategoria ";
        $sql .= "from ProdutoCategoria PG inner join Categoria C ";
        $sql .= "on C.idCategoria = PG.CategoriaIdCategoria ";
        $sql .= "inner join Produto P ";
        $sql .= "on P.idProduto = PG.ProdutoIdProduto ";
        $sql .= "where P.idProduto = ".$idCategoria;

        $restult = $this->persolizaConsulta($sql, get_class($this));

        return $restult;
    }

    public function setNomeCategoria($prop, $value){
    	$this->data[$prop] = $value;
    }


   	



}
