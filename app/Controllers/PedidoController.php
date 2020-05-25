<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Pessoa;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\Fornecimento;
use App\Models\LogradouroPessoa;

class PedidoController extends BaseController
{
    private $carrinho;

    public function __construct()
    {
        $this->carrinho = [];
    }

    public function painel($request)
    { 
        
        Transaction::startTransaction('connection');

        if(!isset($request['post'], $request['post']['cliente'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['post']['cliente'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        $idCliente = (int) explode('=', $request['post']['cliente'][0])[1];

        $pessoa = new Pessoa();
        $resultSelect = $pessoa->findPessoa($idCliente);

        $logradPessoa = new LogradouroPessoa();

        if($resultSelect != false){
            $this->view->pessoa = $resultSelect->getNomePessoa();
            $idPessoa = $resultSelect->getIdPessoa();

            $resultLogPessoa =  $logradPessoa->listarConsultaPersonalizada('P.idPessoa = '.$idPessoa, null, null, true);

            if($resultLogPessoa){
                $this->view->resultLogPessoa = $resultLogPessoa;

                $this->render('pedido/painel', false);
            }else{
                var_dump('Dados pendentes no cadastro do cliente');
            }
            
        }

        Transaction::close();
        
    }

    public function cancelarCompra()
    {
        # code...
    }

    public function finalizarCompra()
    {
        return Venda::carrinho();
    }


    public function addProduto(Produto $newProduto)
    {
        $this->carrinho[] = $newProduto;
    }

    public function addCarrinho($request)
    {
        if(Venda::addToCarrinho($request['get']['id']) == true)
        {
            $this->view->result = json_encode(Venda::qtdItensVenda());
            $this->render('pedido/ajax', false);
        }
        

    }


    public function novo()
    {
        $this->render('pedido/novoPedido', false);
    }


    public function loadPessoa($request)
    {   
        Transaction::startTransaction('connection');

        if(!isset($request['post']['loadPessoa'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['post']['loadPessoa'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $pessoa = new Pessoa();
        $result = $pessoa->loadPessoa($request['post']['loadPessoa'], false, true);

        if($result != false){

            $newResult = [];

            for ($i=0; !($i == count($result)); $i++) { 
                $newResult[] = [$result[$i]->idPessoa, $result[$i]->nomePessoa, $result[$i]->documento];
            }
            $this->view->result = json_encode($newResult);
            $this->render('pedido/ajax', false);

        }else{
            $this->view->result = json_encode($result);
            $this->render('pedido/ajax', false);
        }
        
        Transaction::close();
    }


    public function loadEstoque($request)
    {
        Transaction::startTransaction('connection');

        if(!isset($request['post']['loadEstoque'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['post']['loadEstoque'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $estoque = new Fornecimento();
        $result = $estoque->loadFornecimento($request['post']['loadEstoque'], true);

        if($result != false){

            $newResult = [];

            for ($i=0; !($i == count($result)); $i++) {
                $newResult[] = [$result[$i]->getProdutoIdProduto(), $result[$i]->getProdutoNome(), ($result[$i]->getQtdFornecida() - $result[$i]->getQtdVendida()), $result[$i]->getVlVenda()];
               // $newResult[] = $result[$i]->cpf;
            }
            $this->view->result = json_encode($newResult);
            $this->render('pedido/ajax', false);

        }else{
            $this->view->result = json_encode($result);
            $this->render('pedido/ajax', false);
        }
        
        Transaction::close();
    }


    public function savePedido($request)
    {
        Transaction::startTransaction('connection');

        if(!isset($request['post']['pedidoPanelVenda'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['post']['pedidoPanelVenda'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $arrEstoque = [];
        for ($i=0, $dados = $request['post']['pedidoPanelVenda']; !($i == count($dados)) ; $i++) { 
            $estoque = new Fornecimento();
            $result = $estoque->listarConsultaPersonalizada('P.idProduto = '.$dados[$i][0], NULL, NULL, true);
            $arrEstoque[] = $result;
        }

        echo "<pre>";
        var_dump($arrEstoque);
        echo "</pre>";

        Transaction::close();
    }

}