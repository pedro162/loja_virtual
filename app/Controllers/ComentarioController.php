<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Pessoa;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\Fornecimento;
use App\Models\LogradouroPessoa;
use App\Models\Pedido;
use App\Models\DetalhesPedido;
use \App\Models\ProdutoCategoria;
use \App\Models\Comentario;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;

class ComentarioController extends BaseController
{

	public function comentar($request)
    {
    	try {

    		Transaction::startTransaction('connection');

    		$usuario = Sessoes::usuarioLoad();

    		if($usuario == false){
    			throw new \Exception("Faça o login para comentar.");
    			
    		}

	        if((!isset($request['post']['produto']))|| (!isset($request['post']['comentario'])) ){
	            throw new \Exception("Propriedade indefinida<br/>");
	            
	        }
	        if(empty($request['post']['produto']) || (empty($request['post']['comentario']))){
	            throw new \Exception("Propriedade indefinida<br/>");
	            
	        }

	        $produto = new Produto();
	        $result = $produto->loadProdutoForId($request['post']['produto'], true);

	        if($result != false){

	        	$idUsuario = $usuario->getIdPessoa();

	        	$comentario = new Comentario();
	        	$comentario->setTextoComentario($request['post']['comentario']);
	        	$comentario->setUsuarioIdUsuario($idUsuario);//obs alterar para iplementarção correta do usuario
	        	$comentario->setProdutoIdProduto((int)$result->getIdProduto());

	        	$resultContario = $comentario->save([]);

	        	if($resultContario == true){
	      
		            $this->view->result = json_encode([(int)$result->getIdProduto()]);
		            
	        	}else{
	        		 $this->view->result = json_encode([0]);
	        	}
	        	$this->render('comentario/ajax', false);

	        }else{
	            throw new Exception("Parêmetro inválido\n");
	            
	        }
	        
	        Transaction::close();

    	} catch (\Exception $e) {
    		Transaction::rollback();
			
			$erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);

		    $this->render('comentario/ajax', false);
    	}

    }


    //carrega os comentarios
    public function loadComentFormProduto($request)
    {
    	try {

    		Transaction::startTransaction('connection');

	        if(!isset($request['post']['produto'])){
	            throw new \Exception("Propriedade indefinida<br/>");
	            
	        }

	        $id = (int)$request['post']['produto'];
	        if($id <= 0){
	            throw new \Exception("Propriedade indefinida<br/>");
	            
	        }

	        $produto = new Produto();
	        $result = $produto->loadProdutoForId($request['post']['produto'], true);

	        if($result != false){
	        	$comentario = new Comentario();

	        	$comentarios = $comentario->listarConsultaPersonalizada('C.ProdutoIdProduto ='.(int)$result->getIdProduto(), NULL, NULL, true);

	        	if($comentarios){
	        		$this->view->usuario = Sessoes::usuarioLoad();//pega o usuario se estiver logado
	        		$this->view->comentarios = $comentarios;;
		            $this->render('comentario/comentarios', false);
	        	}

	        }else{
	            throw new Exception("Parêmetro inválido\n");
	            
	        }
	        
	        Transaction::close();

    	} catch (Exception $e) {
    		Transaction::rollback();
			
			$erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
		    $this->render('comentario/ajax', false);
    	}


    }
    
}