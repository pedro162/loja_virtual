<?php

namespace App\Controllers;

class ProdutoController
{
    public function show($request)
    {
        $this->view->msg =  "estou no produto controler, metodo show nome: ".$request['get']['nome'].'<br/>';
        echo $this->view->msg;
    }
}