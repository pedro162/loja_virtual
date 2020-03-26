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
        
    }

    public function cadastrar()
    {
    	$this->setMenu('adminMenu');
        $this->render('produtos/cadastrar');
    }
}