<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\Fornecimento;

class VendaController extends BaseController
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

        $cliente = new Cliente();
        $resultSelect = $cliente->findCliente($request['post']['cliente']);
        if($resultSelect != false)
            $this->render('venda/painel', false);

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
            $this->render('venda/ajax', false);
        }
        

    }


    public function nova()
    {
        $this->render('venda/novaVenda', false);
    }


    public function loadCliente($request)
    {   
        Transaction::startTransaction('connection');

        if(!isset($request['post']['loadCliente'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['post']['loadCliente'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $cliente = new Cliente();
        $result = $cliente->loadCliente($request['post']['loadCliente'], false, true);

        if($result != false){

            $newResult = [];

            for ($i=0; !($i == count($result)); $i++) { 
                $newResult[] = [$result[$i]->idCliente, $result[$i]->nomeCliente, $result[$i]->cpf];
               // $newResult[] = $result[$i]->cpf;
            }
            $this->view->result = json_encode($newResult);
            $this->render('venda/ajax', false);

        }else{
            $this->view->result = json_encode($result);
            $this->render('venda/ajax', false);
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
                $newResult[] = [$result[$i]->idProduto, $result[$i]->nomeProduto, $result[$i]->qtdF, $result[$i]->vlVenda];
               // $newResult[] = $result[$i]->cpf;
            }
            $this->view->result = json_encode($newResult);
            $this->render('venda/ajax', false);

        }else{
            $this->view->result = json_encode($result);
            $this->render('venda/ajax', false);
        }
        
        Transaction::close();
    }

}