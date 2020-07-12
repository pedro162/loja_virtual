<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Pessoa;
use \App\Models\Usuario;
use \App\Models\ConversaChate;
use \App\Models\Chate;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;
use \Exception;

class ChateController extends BaseController
{
    
    public function init()
    {
    	try {

    		Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/usuario/index');
                return false;
            }

    		Transaction::startTransaction('connection');

            $result = $usuario->getConversation();

            $this->view->usuario = $usuario;
            $this->view->conversation = $result;

    		$this->render('loja/chate/index', false);
    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('loja/ajax', false);
    	}
    }


    public function saveMessage($request)
    {
        try {
            
            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/usuario/index');
                return false;
            }

            if((!isset($request['post'])) || (!is_array($request['post']))){
                throw new Exception("Parâmetro inválido\n");
                
            }
            Transaction::startTransaction('connection'); //arbre a conexao com o banco

            $conversaChate = new ConversaChate();
            
            $conversaChate->setTextChat($request['post']['msg']);
            $conversaChate->setSender($usuario->getIdPessoa());
            $conversaChate->setReciever($request['post']['reciever']);

            $result = $conversaChate->save([]);
            
            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('loja/ajax', false);
        }
    }

	
}