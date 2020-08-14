<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Marca;

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
        try{

           Transaction::startTransaction('connection');
           
            $marca = new Marca();
            $result = $marca->save($request['post']['marca']);

            $this->view->result = json_encode($result);
            $this->render('produtos/ajaxPainelAdmin', false);

            Transaction::close();
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('marca/ajax', false);
        }
    }
}