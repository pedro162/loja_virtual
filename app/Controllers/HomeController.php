<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Fabricante;
use App\Models\Produto;
use App\Models\Venda;

class HomeController extends BaseController
{

    public function index()
    {
        $this->setMenu();
        $this->setFooter();
        $this->view->qtd = Venda::qtdItensVenda();// insere o total de itens do carrinho
        $this->render('home/home', true);
    }

    public function login()
    {
        $this->setMenu();
        $this->render('login/login', true);
    }

    public function cadastro()
    {
        $this->setMenu();
        $this->setFooter('footer');
        $this->render('login/cadastro', true);
    }

    public function painel()
    {   
        $this->setMenu('adminMenu');
        $this->setFooter('footer');
        $this->render('produtos/cadastrar', true, 'layoutAdmin');
    }

    public function teste($request){
        echo "<pre>";
        var_dump($request);
        echo "</pre>";
    }
}
