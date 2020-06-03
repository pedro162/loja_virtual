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

class PedidoController extends BaseController
{
    

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

        if($resultSelect != false){
            $this->view->pessoa = $resultSelect->getNomePessoa();
            $this->view->idCliente = $idCliente;

            $resultLogPessoa =  $resultSelect->getLogradouro();

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

        if(!isset($request['post']['pedidoPanelVenda'])
            || !isset($request['post']['cliente'])
            || !isset($request['post']['entrega'])){

            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['post']['pedidoPanelVenda']) ||
           empty($request['post']['cliente']) ||
           empty($request['post']['entrega'])){

            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $pessoa = new Pessoa();
        $resultFindPessoa = $pessoa->findPessoa((int)$request['post']['cliente']);

        $logradouroPessoa = new LogradouroPessoa();
        $logradouro = $logradouroPessoa->findLogPessoa((int)$request['post']['entrega'], true);

        $pedido = new Pedido();
        
        $arrEstoque = [];
        for ($i=0, $dados = $request['post']['pedidoPanelVenda']; !($i == count($dados)) ; $i++) { 

            $item = explode(',', $dados[$i]);

            $estoque = new Fornecimento();
            $result = $estoque->listarConsultaPersonalizada('P.idProduto = '.$item[0], NULL, NULL, true);
            
            //$arrEstoque[] = $result;
            $detalhesPedido = new DetalhesPedido();

            $resultQtd       = $detalhesPedido->setQtd($result[0], $item[1]);
            $resultVab       = $detalhesPedido->setValBruto($result[0], $item[2]);
            $resultDesCunit  = $detalhesPedido->setVlDescontoUnit($result[0], $item[3]);
            $resultTotDesc   = $detalhesPedido->setTotalDesconto((float)$item[4]);
            $resultPrecoUnit = $detalhesPedido->setPrecoUnitPratic((float)$item[5]);
            $resultEstoqueId = $detalhesPedido->setIdEstoque((int)$result[0]->getIdFornecimento());

            $resultUsuario   = $detalhesPedido->setUsuarioIdUsuario(1);//falta implementar corretamente
            $resultFornec    = $detalhesPedido->setFornecimentoIdFornecimento($result[0]->getIdFornecimento());

            $pedido->addItem($detalhesPedido);
        
        }
        $pedido->setQtdParcelas(1);
        $pedido->setCliente((int)$request['post']['cliente']);
        $pedido->setLogradouroIdLogradouro((int)$logradouro[0]->getIdLogradouroPessoa());
        $pedido->setUsuario(1);

        $result = $pedido->save([]);
        if($result){
            $this->view->result = json_encode($result);
            $this->render('pedido/ajax', false);
        }

        Transaction::close();
    }

}