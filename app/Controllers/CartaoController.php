<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Cliente;
use Core\Containner\File;
use App\Models\Venda;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Fornecimento;
use App\Models\Imagem;
use App\Models\Estrela;
use App\Models\CartaoComprador;
use App\Models\Cartao;
use \Core\Utilitarios\Sessoes;
use \Exception;
use \PDOException;

class CartaoController extends BaseController
{
	public function salvar($request)
    {
        try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
            }

            Transaction::startTransaction('connection');

            if(! isset($request['post'])){
                throw new Exception("Requisição inválida\n");
                
            }

            $cartao = new Cartao();
            
            $cartao->setBandeira($request['post']['bandeira']);
            $cartao->setNumero($request['post']['numero']);
            $cartao->setCvv($request['post']['cvv']);
            $cartao->setMesValidade($request['post']['validade_mes']);
            $cartao->setCpfTitular($request['post']['cpf_cartao']);
            $cartao->setAnoValidade($request['post']['ano_validade']);
            $cartao->setDtRegistro(date('Y-m-d H:i:s'));

            $result = $cartao->save([]);
            if($result == false){
                throw new Exception("Erro ao adiconar cartão\n");
                
            }

            $idCartao = $cartao->maxId();

            $cartaoComprador = new CartaoComprador();

            $cartaoComprador->setIdCartao($idCartao);
            $cartaoComprador->setIdPessoa($usuario->getIdPessoa());
            $cartaoComprador->setDtRegistro(date('Y-m-d H:i:s'));
            $cartaoComprador->setAtivo('sim');
            $cartaoComprador->save([]);

            $success = ['msg','success', 'Cartão adiconado com sucesso!'];
            $this->view->result = json_encode($success);
            $this->render('cartao/ajax', false);

            Transaction::close();

        }catch(\PDOException $e){

        	Transaction::rollback();

        }catch(\Exception $e){
        	
        	Transaction::rollback();


            //falta implementar corretamente
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('cartao/ajax', false);
        }
    }

    public function editar($request)
    {
        try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
            }

            Transaction::startTransaction('connection');


            Transaction::close();

        }catch(\PDOException $e){

        	Transaction::rollback();

        }catch(\Exception $e){
        	
        	Transaction::rollback();


            //falta implementar corretamente
            $this->view->result = '<h4>'.$e->getMessage().'</h4>';
            $this->render('cartao/ajax', false);
        }
    }


    public function atualizar($request)
    {
        try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
            }

            Transaction::startTransaction('connection');



            Transaction::close();

        }catch(\PDOException $e){

        	Transaction::rollback();

        }catch(\Exception $e){
        	
        	Transaction::rollback();

            $this->view->result = '<h4>'.$e->getMessage().'</h4>';
            $this->render('cartao/ajax', false);
        }
    }


    public function deletar($request)
    {
        try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
            }

            if(! isset($request['post']['cc'], $request['post']['cp'])){
            	throw new Exception("Parâmetro inválido\n");
            	
            }

            if( ($request['post']['cc'] <= 0) || ($request['post']['cp'] <= 0)){

            	throw new Exception("Parâmetro inválido\n");
            	
            }

            Transaction::startTransaction('connection');

            $cartaoComprador = new CartaoComprador();
            $idCartao = (int) $request['post']['cc'];
            $idPessoa = (int) $usuario->getIdPessoa();

            $cartaoCompradorLoaded = $cartaoComprador->selectNew(
            	['*'],
            	[
            		['key'=>'idCartao', 'val'=>$idCartao, 'comparator' => '=', 'operator'=> 'and'],
            		['key'=>'idPessoa', 'val'=>$idPessoa, 'comparator' => '=']
            	] , null, 1, null, true, false
        	);

            if($cartaoCompradorLoaded == false){
            	throw new Exception("Registro não encontrado\n");
            	
            }
            $cartaoCompradorLoaded[0]->setAtivo('nao');
            $result = $cartaoCompradorLoaded[0]->modify([]);

            if($result == true){

            	$this->view->result = json_encode(['msg','success', 'Registro deletado com sucesso.']);
            	$this->render('cartao/ajax', false);
            }

            Transaction::close();

        }catch(\PDOException $e){

        	Transaction::rollback();

        }catch(\Exception $e){
        	
        	Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('cartao/ajax', false);
        }
    }


    public function cadastrar($request)
    {
        try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
            }

            Transaction::startTransaction('connection');

            if(! isset($request['post'])){
                throw new Exception("Requisição inválida\n");
                
            }

            $this->render('cartao/cadastrar', false);

            Transaction::close();

        }catch(\PDOException $e){

        	Transaction::rollback();

        }catch(\Exception $e){
        	
        	Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];

        	$this->view->result = json_encode($erro);
            $this->render('cartao/ajax', false);
        }
    }

}