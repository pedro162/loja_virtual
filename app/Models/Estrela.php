<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\ProdutoCategoria;
use \Exception;
use \InvalidArgumentException;

class Estrela extends BaseModel
{
    private $data = [];
    
    const TABLENAME = 'Estrela';
    
	private $idEstrela;
	private $dtEstrela;
	private $ProdutoIdProduto;
	private $numEstrela;
    private $UsuarioIdUsuario;

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
                case 'produto':
                   $this->setProdutoIdProduto((int)$value);
                   break;
                case 'estrela':
                   $this->setNumEstrela((int)$value);
                   break;
                case 'user':
                   $this->setUsuarioIdUsuario((int)$value);
                   break;
                
            }

        }
    }
    public function save(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $resultInsert = $this->insert($result);
        if($resultInsert == true){
            return true;
        }

        throw new Exception("Falha ao registra o voto");
        
    }

    public function modify(array $dados)
    {
        
    }


    public function lodaEstrelaForId(Int $id, $classEstrela = true)
    {   
        if(!isset($id) || ($id <= 0)){

            throw new Exception("Parametro inválido\n");
            
        }

        $result = $this->select(['idEstrela', 'dtEstrela', 'ProdutoIdProduto'
        						, 'UsuarioIdUsuario', 'numEstrela'], ['idEstrela'=> $id], '=', 'asc', 1, 10, $classEstrela, true);
        
        if($result == false){
        	return 0;
        	
        }
		return $result[0];
    }

    public function Produto()
    {
        $produto = new Produto();
        $result = $produto->select(['idProduto','nomeProduto'], ['idProduto'=>$this->ProdutoIdProduto], '=','asc', null, null,true);

        if($result != false){
            return $result[0];
        }

        throw new Exception('Produto não encontrado');
    }


    public function setNumEstrela(Int $num)
    {
    	if(!isset($num) || ($num <= 0) || ($num > 5)){
    		throw new Exception("Parâmetro inváldio\n");
    	}

    	$this->data['numEstrela'] = $num;
    	return true;
    }

    public function getNumEstrela()
    {
    	if(!isset($this->numEstrela) || ($this->numEstrela <= 0)){

    		if(isset($this->data['numEstrela']) && ($this->data['numEstrela'] > 0)){
    			return (int)$this->data['numEstrela'];
    		}
    		throw new Exception("Elemtno não definido\n");
    	}

    	return (int) $this->numEstrela;
    }

    public function setProdutoIdProduto(Int $id)
 	{
 		if(!isset($id) || ($id <= 0)){

    		throw new Exception("Parâmetro inválido\n");
    		
    	}

		$this->data['ProdutoIdProduto'] = $id;

		return true;
 	}
 	
 	public function getProdutoIdProduto()
 	{
 		if(!isset($this->ProdutoIdProduto) || ($this->ProdutoIdProduto <= 0)){

    		if(isset($this->data['ProdutoIdProduto']) && ($this->data['ProdutoIdProduto'] > 0)){
    			return $this->data['ProdutoIdProduto'];
    		}
    		throw new Exception("Elemtno não definido\n");
    	}

    	return (int)$this->ProdutoIdProduto;
 	}

    public function setUsuarioIdUsuario(Int $id)
    {
        if(!isset($id) || ($id <= 0)){

            throw new Exception("Parâmetro inválido\n");
            
        }

        $this->data['UsuarioIdUsuario'] = $id;

        return true;
    }

}
