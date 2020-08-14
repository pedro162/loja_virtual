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
use \App\Models\Logradouro;
use \Core\Utilitarios\LoadEnderecoApi;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;
use \Exception;

class LogradouroController extends BaseController
{
    public function cadastrar($riquest)
    {	
        try {

            if(! isset($riquest['post'])){

                throw new Exception('Parâmetro inválido');
                
            }


            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            $this->render('logradouro/cadastrar', false);
            
        }catch (\PDOException $e) {


        } catch (Exception $e) {

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('logradouro/ajax', false);
        }
    }

    public function salvar($request)
    {
        try {

        	//busca o usuario logado
	        $usuario = Sessoes::usuarioLoad();
	        if($usuario == false){
	            header('Location:/home/init');
	            
	        }

            Transaction::startTransaction('connection');

            if((!isset($request['post'])) || (empty($request['post']))){

                throw new Exception('Parâmetro inválido');
                
            }

            $dados = $request['post'];

            $logradouro = new Logradouro();
            $logradouro->setComplemento($dados['complemento']);
			$logradouro->setCidadeidCidade(1);//obs: ainda falta configurar 
			$logradouro->setCep($dados['cep']);
			$logradouro->setBairro($dados['bairro']);
			$logradouro->setTipo($dados['tipo']);
			$logradouro->setEndereco($dados['endereco']);

			//configura o usuario como a loja virtual
			$logradouro->setUsuarioIdUsuario(1);

			$resultInsertLog = $logradouro->save([]);
			if($resultInsertLog == true){
				$logPessoa = new LogradouroPessoa();
                $lestId = (int)$logradouro->maxId();

                $logPessoa->setIdUsuario(1);//cofigura o usuairo de registro como loja virtual
                $logPessoa->setPessoaIdPessoa((int)$usuario->getIdPessoa());
                $logPessoa->setLogradouroIdLogradouro($lestId);
                $logPessoa->save([]);
			}
           
            $this->view->result = json_encode(['msg','success', 'Cadastro efetuado com sucesso!']);
            $this->render('logradouro/ajax', false);

            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('logradouro/ajax', false);
        }
    }


    public function atualizar($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            if((!isset($request['post'])) || (empty($request['post']))){

                throw new Exception('Parâmetro inválido');
                
            }

            $dados = $request['post'];

            Transaction::startTransaction('connection');

            $logradouro = new Logradouro();
            $logLoaded = $logradouro->findLogradouro($dados['cod']);

            if($logLoaded == false){
                throw new Exception("Endereço inválido\n");
                
            }

            $logLoaded->setComplemento($dados['complemento']);
            $logLoaded->setCidadeidCidade(1);//obs: ainda falta configurar 
            $logLoaded->setCep($dados['cep']);
            $logLoaded->setBairro($dados['bairro']);
            $logLoaded->setTipo($dados['tipo']);
            $logLoaded->setEndereco($dados['endereco']);

            $resultInsertLog = $logLoaded->modify([]);
            if($resultInsertLog == true){
                
                $this->view->result = json_encode(['msg','success', 'Informações atualizadas com sucesso!']);
                $this->render('logradouro/ajax', false);
            }
           
            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('logradouro/ajax', false);
        }
    }
    
    public function editar($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            
            Transaction::startTransaction('connection');

            $logradouro = new Logradouro();
            $logLoaded = $logradouro->findLogradouro($request['get']['cd']);

            if($logLoaded == false){
                throw new Exception("Endereço inválido\n");
                
            }

            $this->view->logradouro = $logLoaded;
            $this->render('logradouro/editar', false);
            
            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('logradouro/ajax', false);
        }
    }

    public function loadCep($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            
            Transaction::startTransaction('connection');

            $cepApi = new LoadEnderecoApi($request['post']['cep']);
            $resultCepApi = $cepApi->getEndereco();

            $this->view->result = $resultCepApi;
            $this->render('logradouro/ajax', false);

            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('logradouro/ajax', false);
        }
    }
}