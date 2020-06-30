<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Produto;
use \Exception;
use \InvalidArgumentException;

class Imagem extends BaseModel
{
    private $data = [];
    
    const TABLENAME = 'Imagem';
    
	private $ProdutoIdProduto;
	private $url;
	private $dataUpload;
	private $idUsuario;
    private $idImagem;


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
        //falta implementar corretamente
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        foreach ($dados as $key => $value) {
           
            switch ($key) {
                case 'usuario':
                   $this->setIdUsuario((int) $value);
                   break;

                case 'produto':
                	$this->setProdutoIdProduto((int) $value);
                    
                break;
                case 'url':
                    $this->setUrl($value);
                break;
                case 'tipo':
                    $this->setTipo($value);
                break;
            }

        }

        return true;
    }
    public function save(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $resultSelect = $this->select(['url'], ['url' => $this->getUrl()], '=','asc', null, null, true);

        if($resultSelect != false){

            return ['msg','warning','Atenção: Esta imagem já existe!'];
        }

        $resultInsert = $this->insert($result);
        if($resultInsert == true){
            return true;
        }

        return false;
    }

    public function modify(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();
        $resultUpdate = $this->update($result, $this->getIdImagem());

        if($resultUpdate != false){

            return true;
        }
        
    }

    public function setTipo(String $tipo)
    {
        $tipo = trim($tipo);

        if(isset($tipo) && (strlen($tipo) > 0)){
            $this->data['tipo'] = $tipo;
            return true;
            
        }
        throw new Exception('Parametro inválido<br/>'.PHP_EOL);
    }


    public function getProdutoIdProduto()
    {
    	if(($this->ProdutoIdProduto <= 0) || (!isset($this->ProdutoIdProduto))){
    		if(isset($this->data['ProdutoIdProduto']) && ($this->data['ProdutoIdProduto'] > 0)){
    			return $this->data['ProdutoIdProduto'];
    		}

    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->ProdutoIdProduto;
    }

    public function setProdutoIdProduto(Int $id)
    {
        if(isset($id) && ($id > 0)){
        	$produto = new Produto();
        	$result = $produto->select(['idProduto'], ['idProduto' => $id], '=','asc', null, null, true, false);

        	if($result != false){
        		$idProduto = $result[0]->getIdProduto();
        		$this->data['ProdutoIdProduto'] = $idProduto;
        		return true;
        	}
            
        }
        throw new Exception('Parametro inválido<br/>'.PHP_EOL);
    }


	public function setUrl(String $url)
    {

        if(isset($url) && (!empty($url))){
    		$this->data['url'] = $url;
    		return true;
        	
        }
        throw new Exception('Parametro inválido<br/>'.PHP_EOL);
    }
    

    public function getUrl()
    {
    	if(empty($this->url) || (!isset($this->url))){
    		if(isset($this->data['url']) && (!empty($this->data['url']))) {
    			return $this->data['url'];
    		}

    		throw new Exception("Pripriedade não defindida<br/>");
    	}

    	return $this->url;
    }


    public function setIdUsuario(Int $id)
    {
    	if(isset($id) && ($id > 0)){
    		$this->data['idUsuario'] = $id;
    		return true;
        	           
        }
        throw new Exception('Parametro inválido<br/>'.PHP_EOL);
    }
 

    public function setIdImagem(Int $id)
    {
        if((isset($id))&&($id > 0)){
            $this->data['idImagem'] = $id;
            return true;
        }

        throw new Exception('Parametro inválido<br/>'.PHP_EOL);
    }

    public function getIdImagem()
    {
        if((!isset($this->idImagem)) || ($this->idImagem <= 0)){

            if(isset($this->data['idImagem']) && ($this->data['idImagem'] > 0)){
                return $this->data['idImagem'];
            }
           
            throw new Exception('Parametro inválido<br/>'.PHP_EOL);
        }
        return $this->idImagem;

    }




}
