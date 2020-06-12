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

    public function loadCategoria($request)
    {   
        try {
            Transaction::startTransaction('connection');

            if(!isset($request['post']['loadCategoria']) || (empty($request['post']['loadCategoria']))){

                throw new \Exception("Parametro inválido");
            }

            $categoria = new Categoria();
            $result = $categoria->loadCategoria($request['post']['loadCategoria'], true, true);

            if($result != false){

                $newResult = [];

                for ($i=0; !($i == count($result)); $i++) {
                    $newResult[] = [$result[$i]->getIdCategoria(), $result[$i]->getCategoria()];
                   
                }
                $this->view->result = json_encode($newResult);
                $this->render('pedido/ajax', false);

            }else{
                
            $this->view->result = json_encode($result);
            $this->render('categoria/ajax', false);
            }

            Transaction::close();
            
        } catch (Exception $e) {

             Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('categoria/ajax', false);
        }
        
    }
}