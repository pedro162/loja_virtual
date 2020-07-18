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
use \App\Models\Usuario;
use \App\Models\ProdutoCategoria;
use \App\Models\Comentario;
use \App\Models\FormPgto;
use \App\Models\PedidoFormPgto;
use \App\Models\ContaPagarReceber;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;
use \Exception;

class PessoaController extends BaseController
{
    
	public function pedidos($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

    		if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

    			throw new Exception('Parâmetro inválido');
    			
    		}

    		Transaction::startTransaction('connection');

    		$pessoa = new Pessoa();

    		$idPessoa = (int) $request['get']['cliente'];
    		$resultPessoa = $pessoa->findPessoa($idPessoa);

    		//busca todos os pedidos com status de venda
    		$this->view->pedidos = $resultPessoa->infoPedidoComplete();
    		$this->view->pessoa = $resultPessoa;
    		$this->render('pessoa/pedido/index', false);
    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function prevendas($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

                throw new Exception('Parâmetro inválido');
                
            }

            //abre a conexao com o banco de dados
    		Transaction::startTransaction('connection');

            $pessoa = new Pessoa();

            $idPessoa = (int) $request['get']['cliente'];
            $resultPessoa = $pessoa->findPessoa($idPessoa);

            //busca todos os pedidos com status de venda
            $tipo = 'prevenda';
            $this->view->tipo = $tipo;
            $this->view->pedidos = $resultPessoa->infoPedidoComplete([], $tipo);
            $this->view->pessoa = $resultPessoa;
            $this->render('pessoa/pedido/index', false);

            //fax o commit e fecha a conexao com o banco
    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }
	public function orcamentos($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

                throw new Exception('Parâmetro inválido');
                
            }

            //abre a conexao com o banco de dados
            Transaction::startTransaction('connection');

            $pessoa = new Pessoa();

            $idPessoa = (int) $request['get']['cliente'];
            $resultPessoa = $pessoa->findPessoa($idPessoa);

            //busca todos os pedidos com status de venda
            $tipo = 'orcamento';
            $this->view->tipo = $tipo;
            $this->view->pedidos = $resultPessoa->infoPedidoComplete([], $tipo);
            $this->view->pessoa = $resultPessoa;
            $this->render('pessoa/pedidos', false);

            //fax o commit e fecha a conexao com o banco
            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function cadastro()
    {
    	try {
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }


    		Transaction::startTransaction('connection');
            
            $this->view->pessoa = $usuario;
            $this->render('pessoa/cadastro/index', false);

    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function endereco()
    {
        try {
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');

            $this->view->pessoa = $usuario;
            $this->render('pessoa/endereco/index', false);
            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function compras()
    {
    	try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

    		Transaction::startTransaction('connection');

            //busca todos os pedidos com status de venda
            $this->view->pedidos = $usuario->infoPedidoComplete();
            $this->view->pessoa = $usuario;

            $this->render('pessoa/pedido/index', false);

    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function pagamento()
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');

            //busca todos os pedidos com status de venda
            $this->view->pedidos = $usuario->infoPedidoComplete();
            $this->view->pessoa = $usuario;

            $this->render('pessoa/pagamento/index', false);

            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function nfs()
    {
    	try {

    		Transaction::startTransaction('connection');

    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    

}