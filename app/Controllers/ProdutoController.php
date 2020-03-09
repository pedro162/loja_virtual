<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Produto;
use App\Models\Fabricante;

class ProdutoController extends BaseController
{
    public function show($request)
    {
       
        $this->render('produtos/produto', true);
    }
}