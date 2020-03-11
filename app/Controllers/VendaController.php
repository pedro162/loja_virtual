<?php 

namespace App\Controllers;

use App\Models\Cliente;
use App\Models\Produto;

class VendaController
{
    private $carrinho;

    public function __construct()
    {
        $this->carrinho = [];
    }

    public function iniciarCompra()
    {
        $cliente = new Cliente
    }

    public function cancelarCompra()
    {
        # code...
    }

    public function finalizarCompra()
    {
        # code...
    }


    public function addProduto(Produto $newProduto)
    {
        $this->carrinho[] = $newProduto;
    }



}