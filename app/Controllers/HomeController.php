<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Fabricante;
use App\Models\Produto;

class HomeController extends BaseController
{
    public function index()
    {
        $produto = new Produto();
        
        $this->view->produtos = $produto->select(['nomeProduto','textoPromorcional']);
        $this->render('home/home', true);
    }

    public function login()
    {
        $this->render('login/login', true);
    }

    public function cadastro()
    {
        $this->render('login/cadastro', true);
    }
}
