<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Cliente;

class ProdutoController extends BaseController
{
    public function show()
    {
        $cliente = new Cliente();
        die();
        $string = $cliente->select(['nome', 'nascimento','cpf']);
        $this->render('produtos/produto', true);
    }
}