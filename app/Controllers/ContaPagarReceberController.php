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
use \Core\Utilitarios\Sessoes;
use \App\Models\Usuario;


/**
 * Classe para gestão financeira
 */
class ContaPagarReceberController extends BaseController
{
	
	public function painel()
	{
		try {

			Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

			Transaction::startTransaction('connection');

			$this->render('contas/index', false);

			Transaction::close();

		}catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
			
			Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);

		}
	}
}