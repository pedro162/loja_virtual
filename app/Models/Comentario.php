<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use App\Models\Produto;

class Comentario extends BaseModel
{
    private $idComentario;
    private $ProdutoIdProduto;
    private $textoComentario;
    private $UsuarioIdUsuario;
    private $dtComentario;

    private $img;
    private $nomePessoa;

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    const TABLENAME = 'Comentario';


    protected function clear(array $dados):bool
    {
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        foreach ($dados as $key => $value) {
           
            switch ($key) {
                case 'nome':
                   
                	break;
            }

        }

        return true;
    }

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

    public function save(array $dados):bool
    {

        $result = $this->parseCommit(); //retorna os dados já filtrados 
        $resultInsertComent = $this->insert($result);//salva o comentario
        
        if($resultInsertComent == false){

        	throw new Exception("Erroa cadastrar comentário\n");
        	
        }
        return true;

    }

    //ajustar method de update
    public function modify(array $dados)
    {
        //$this->clear($dados);

       // $result = $this->parseCommit();

        //$resultUpdate = $this->update($result, $this->getIdProduto());

        
    }

    public function getIdCometario()
    {
    	if((!isset($this->idComentario)) || ($this->idComentario <=0)){
    		if(isset($this->data['idComentario']) && ($this->data['idComentario'] > 0)){
    			return $this->data['idComentario'];
    		}
    		throw new Exception("Elemento não definido");
    		
    	}

    	return $this->idComentario;
    }

    public function getProdutoIdProduto()
    {
    	if((!isset($this->ProdutoIdProduto)) || ($this->ProdutoIdProduto <=0)){
    		if(isset($this->data['ProdutoIdProduto']) && ($this->data['ProdutoIdProduto'] > 0)){
    			return $this->data['ProdutoIdProduto'];
    		}
    		throw new Exception("Elemento não definido");
    		
    	}

    	return $this->ProdutoIdProduto;
    }

    public function getUsuarioIdUsuario()
    {
        if((!isset($this->UsuarioIdUsuario)) || ($this->UsuarioIdUsuario <= 0)){

            if(isset($this->data['UsuarioIdUsuario']) && ((int)$this->data['UsuarioIdUsuario'] > 0)){

                return (int)$this->data['UsuarioIdUsuario'];
            }
            
            throw new Exception("Parâmetro inváido");
            
        }
        return (Int) $this->UsuarioIdUsuario;

        
        return true;
    }


	public function getTextoComentario()
    {
    	if((!isset($this->textoComentario)) || (strlen($this->textoComentario) == 0)){
    		if(isset($this->data['textoComentario']) && ($this->data['textoComentario'] > 0)){
    			return $this->data['textoComentario'];
    		}
    		throw new Exception("Elemento não definido");
    		
    	}

    	return $this->textoComentario;
    }

    public function setTextoComentario(String $texto)
    {
    	if((!isset($texto)) || (strlen($texto) == 0)){
    		throw new Exception("Parâmetro inválido\n");
    		
    	}

    	$this->data['textoComentario'] = $texto;

    	return true;
    }

    public function setUsuarioIdUsuario(Int $id)
    {
    	if((!isset($id)) || ($id <= 0)){
    		throw new Exception("Parâmetro inváido");
    		
    	}

    	$this->data['UsuarioIdUsuario'] = $id;
    	return true;
    }

    public function setProdutoIdProduto(Int $id)
    {
    	if((!isset($id)) || ($id <=0)){
    		
    		throw new Exception("Elemento não definido");
    	}

    	$this->data['ProdutoIdProduto'] = $id;
    	return true;
    }

    public function getDtComentario()
    {
    	if(!isset($this->dtComentario)){
    		if(isset($this->data['dtComentario'])){
    			return $this->data['dtComentario'];
    		}
    		throw new Exception("Elemento não definido");
    		
    	}

    	return $this->dtComentario;
    }

    public function getImg()
    {
    	if(!isset($this->img)){
    		if(isset($this->data['img']) && ($this->data['img'] > 0)){
    			return $this->data['img'];
    		}
    		throw new Exception("Elemento não definido");
    		
    	}

    	return $this->img;
    }


    public function getNomePessoa()
    {
    	if(!isset($this->nomePessoa)){
    		if(isset($this->data['nomePessoa']) && ($this->data['nomePessoa'] > 0)){
    			return $this->data['nomePessoa'];
    		}
    		throw new Exception("Elemento não definido");
    		
    	}

    	return $this->nomePessoa;
    }

    public function findForId(Int $id)
    {
        $result = $this->select(['idComentario','ProdutoIdProduto', 'textoComentario', 'dtComentario'], ['idComentario'=>$id], '=','asc', null, null,true);
        if($result == false){
            throw new Exception('Produto não encontrado');
            
        }

        return $result[0];
    }

    public function Produto()
    {
        $produto = new Produto();
        $result = $produto->loadProdutoForId((int) $this->getProdutoIdProduto());
        if($result == false){
        	throw new Exception("Propriedade indefinida\n");
        }

        return $result;
    }
    public function listarConsultaPersonalizada(String $where = null, Int $limitInt = NULL, Int $limtEnd = NULL, $clasRetorno = false)
    {

        $sqlPersonalizada = "SELECT C.idComentario, C.ProdutoIdProduto, C.textoComentario, C.dtComentario, C.UsuarioIdUsuario, PS.img, PS.nomePessoa";
        $sqlPersonalizada .= " FROM  Comentario C inner join Produto P on P.idProduto = C.ProdutoIdProduto";
        $sqlPersonalizada .= " inner join Pessoa PS  on PS.idPessoa = C.UsuarioIdUsuario";

        if($where != null)
        {
            $sqlPersonalizada .= ' WHERE '.$where.' order by C.idComentario DESC';
        }

        if(($limitInt != NULL) && ($limtEnd != NULL)){

            if(($limitInt >= 0) && ($limtEnd >= 0)){
                $sqlPersonalizada .= ' LIMIT '.$limitInt.','. $limtEnd; 
            }
        } 
        $result = $this->persolizaConsulta($sqlPersonalizada, $clasRetorno);

        return $result;
    }
   
    public function __get($prop)
    {
        if(method_exists($this, 'get'.ucfirst($prop))){

            return call_user_func([$this,'get'.ucfirst($prop)]);
        }
    }

    public function __set($prop, $value)
    {   
        if(method_exists($this, 'set'.ucfirst($prop))){ 
            return call_user_func([$this,'set'.ucfirst($prop)], $value);
        }
    }
    


}

