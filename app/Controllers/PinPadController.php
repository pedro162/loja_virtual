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
use \App\Models\FormPgto;
use \App\Models\PedidoFormPgto;
use \App\Models\ContaPagarReceber;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;
use \Exception;

/**
 * Classe para faturamentos de fneas, pagamentos, etc..
 */
class PinPadController extends BaseController
{
	public function index()
    {
        try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            //abre a conexao com o banco de dados
            Transaction::startTransaction('connection');

            $pedido = new Pedido();

            //busca todos os pedidos com status de prevenda
            $tipo = 'prevenda';
            $this->view->tipo = $tipo;
            $this->view->prevendas = $pedido->infoPedidoAll(
            	[	
            		//o id do do vendedor deve ser diferente do da loja virtual
            		['key'=>'U.idUsuario', 'val'=>1, 'comparator' => '<>', 'operator' => 'and']
            	], $tipo);

            //busca todos os pedidos com status de venda e pago
            $tipo = 'prevenda';
            $this->view->tipo = $tipo;
            $this->view->prevendas = $pedido->infoPedidoAll(
            	[	
            		//o id do do vendedor deve ser diferente do da loja virtual
            		['key'=>'U.idUsuario', 'val'=>1, 'comparator' => '<>', 'operator' => 'and']
            	], $tipo);

            $this->render('pinpad/index', false);

            //fax o commit e fecha a conexao com o banco
            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pinpad/ajax', false);
        }
    }

	
}
