<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Categoria;

class CategoriaController extends BaseController
{
    public function show($request)
    {   
        
        
    }

    public function cadastrar()
    {
        $this->render('categoria/cadastrar', false);
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
       Transaction::startTransaction('connection');
       
        $categoria = new Categoria();
        $result = $categoria->save($request['post']['categoria']);

        $this->view->result = json_encode($result);
        $this->render('produtos/ajaxPainelAdmin', false);

        Transaction::close();
    }
}