<?php

namespace App\Controllers;

use App\Controllers\BaseController;


class MarcaController extends BaseController
{
    public function show($request)
    {   
        
        
    }

    public function cadastrar()
    {
        $this->render('marca/cadastrar', false);
    }

    public function all($request)
    {

        

    }


    public function buscar()
    {
        
    }

    public function detals($request)
    {
       /* echo"<pre>";
        var_dump($request);
        echo "</pre>";*/
    }

    public function editarProduto($request)
    {   
        
    }

    public function filtro($request)
    {
        
    }

    public function more($request)
    {
        
    }


    public function salvar($request)
    {
       
    }
}