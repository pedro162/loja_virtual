<?php 

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;

class VendaController extends BaseController
{
    private $carrinho;

    public function __construct()
    {
        $this->carrinho = [];
    }

    public function iniciarCompra()
    {
        
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
            $this->view->carrinho = json_encode(Venda::qtdItensVenda());
            $this->render('venda/ajax', false);
        }
        

    }


    public function nova()
    {
        
        $this->render('venda/novaVenda', false);
    }


}