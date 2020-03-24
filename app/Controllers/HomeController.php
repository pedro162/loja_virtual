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
        

        $resultSelect = $produto->select(['nomeProduto','textoPromorcional', 'idProduto']);
        $gridProdutos = [];

        if((count($resultSelect) % 2) ==0)
        {
           for ($i=0; !($i == count($resultSelect)); $i+=4) {

                $subArray = [];

                $sentinela = 0;
                while (!($sentinela == 4)) {
                    $subArray[] = $resultSelect[$i + $sentinela];
                    $sentinela ++;
                }
                $gridProdutos[] = $subArray;
            } 
        }
        else{
           for ($i=0; !($i == count($resultSelect)); $i+=3) {

                $subArray = [];

                $sentinela = 0;
                while (!($sentinela == 3)) {
                    $subArray[] = $resultSelect[$i + $sentinela];
                    $sentinela ++;
                }
                $gridProdutos[] = $subArray;
            } 
        }
        

       // echo "<pre>";
       // var_dump($gridProdutos);
       // echo "</pre>";
        $this->view->produtos = $gridProdutos;
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
