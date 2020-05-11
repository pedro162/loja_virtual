<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Fornecimento;
use App\Models\Pessoa;


class FornecimentoController extends BaseController
{

    public function salvar($request)
    {
        Transaction::startTransaction('connection');

        $fornecimento = new Fornecimento();
        $result = $fornecimento->commit($request['post']['estoque']);
        
        $this->view->result = json_encode($result);
        $this->render('produtos/ajaxPainelAdmin', false);

        Transaction::close();
        
    }


    public function lancarEstoque()
    {
        Transaction::startTransaction('connection');

        $produto    = new Produto();
        $result = $produto->select(
            ['nomeProduto', 'idProduto'],
            [], '=', 'asc', null, null, true);

        $this->view->produtos = $result;
        $this->render('fornecimento/lancarEstoque', false);

        Transaction::close();
    }

}