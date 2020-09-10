<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Categoria;
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
    private $nomeCategoria;
    private $idCategoria;
    private $classificCateg;

    const TABLENAME = 'ProdutoCategoria';
    protected $data = [];

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
                case 'classific':
                   $this->setClassificCateg($value);
                   break;

            }

        }
    }

    public function categoria()
    {
        $categoria = new Categoria();
        $result = $categoria->selectNew(['*'], 
        [
            ['key'=>'idCategoria', 'val'=> $this->CategoriaIdCategoria, 'comparator'=>'=']
        ], null, 1, null, true, false);

        return $result;
    }

    public function setIdCategoria(Int $id):bool
    {   
        if($id > 0){
            $this->data['CategoriaIdCategoria'] = $id;//faltar validar
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
            $this->data['ProdutoIdProduto'] = $id;//faltar validar
            return true;
        }
        throw new Exception("Propriedade inválida<br/>\\n");

    }


    public function getClassificCateg()
    {
        if(isset($this->classificCateg) && (strlen($this->classificCateg) > 0)){
            return $this->classificCateg;
        }
        throw new Exception('Propriedade indefinida');
    }


    public function save(array $dados):bool
    {
        $this->clear($dados);

        $result = $this->parseCommit();
        var_dump($result);
        
        return $this->insert($result);
    }


    public function getCategoria(Int $idProduto)
    {

    	$sql = "select DISTINCT C.nomeCategoria, C.idCategoria, PG.classificCateg ";
		$sql .=	"from ProdutoCategoria PG inner join Categoria C ";
		$sql .=	"on C.idCategoria = PG.CategoriaIdCategoria ";
		$sql .=	"inner join Produto P ";
		$sql .=	"on P.idProduto = PG.ProdutoIdProduto ";
		$sql .=	"where P.idProduto = ".$idProduto;

    	$restult = $this->persolizaConsulta($sql, get_class($this));

    	return $restult;
    }

    public function getIdCategoria()
    {
        if(($this->idCategoria > 0) && (isset($this->idCategoria))){
             
            return $this->idCategoria;
        }
        throw new Exception("Propriedade não definida<br/>\\n");
    }

    public function getIdProdutoCategoria()
    {
        if((isset($this->idProdutoCategoria)) && ($this->idProdutoCategoria > 0)){
             
            return $this->idProdutoCategoria;
        }
        throw new Exception("Propriedade não definida<br/>\\n");
    }

    public function getNomeCategoria()
    {
        if(isset($this->nomeCategoria)){
             
            return $this->nomeCategoria;
        }
        throw new Exception("Propriedade não definida<br/>\\n");
    }

    public function getData(){
    	return $this->data;
    }

    public function getProduto(Int $idCategoria)
    {

        $sql = "select DISTINCT P.nomeProduto, P.idProduto ";
        $sql .= "from ProdutoCategoria PG inner join Produto P ";
        $sql .= "on P.idProduto = PG.ProdutoIdProduto ";
        $sql .= "inner join Categoria C ";
        $sql .= "on C.idCategoria = PG.CategoriaIdCategoria ";
        $sql .= "where C.idCategoria = ".$idCategoria;

        $restult = $this->persolizaConsulta($sql, get_class($this));

        return $restult;
    }


    public function listProductFromCategoria()
    {
        $sql = 'select distinct P.idProduto, P.nomeProduto, P.preco';
        $sql .= 'from ProdutoCategoria as PC right JOIN Produto as P on PC.ProdutoIdProduto = P.idProduto';
        $sql .= 'INNER JOIn Categoria as C on PC.CategoriaIdCategoria = C.idCategoria';

        $restult = $this->persolizaConsulta($sql, get_class($this));

        return $restult;

    }


    
    public function setNomeCategoria($prop, $value){
    	$this->data[$prop] = $value;
    }


   	public function setClassificCateg(String $classific)
    {
        if(strlen($classific) == 0){
            throw new Exception("Parametro inválidos\n");
        }

        $this->data['classificCateg'] = $classific;
    }



}
