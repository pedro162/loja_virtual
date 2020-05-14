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
        $result = $fornecimento->save($request['post']['estoque']);
        
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


    public function all($request)
    {
        Transaction::startTransaction('connection');

        $pagina = 1;
        $itensPorPagina = 10;

        if(isset($request['get'], $request['get']['pagina'])){
            $pagina = $request['get']['pagina'];
        }

        $fornecimento = new Fornecimento();
        $totItens = $fornecimento->countItens();

        $this->view->totItens = $totItens;
        if($totItens == 0){
            
           $this->render('fornecimento/tabelaEstoque', false);

        }else{

            $campos = ['idFornecimento' ,'ProdutoIdProduto','dtValidade', 'dtRecebimento', 'qtdFornecida', 'qtdVendida'];


            $this->view->pagina = $pagina;
            $this->view->itensPorPagina = $itensPorPagina;
            $this->view->totPaginas = ceil($totItens / $itensPorPagina);

            $result = $fornecimento->paginador($campos, $itensPorPagina, $pagina, true);

            for ($i=0; !($i == count($result)) ; $i++) { 
                $result[$i]->setProduto($result[$i]->ProdutoIdProduto);
            }

            $stdPaginacao = new \stdClass();
            $stdPaginacao->pagina = $this->view->pagina;
            $stdPaginacao->itensPorPagina = $this->view->itensPorPagina;
            $stdPaginacao->totPaginas = $this->view->totPaginas ;

            $this->view->tableFornecimento = $result;

            $this->render('fornecimento/tabelaEstoque', false);
        }
        Transaction::close();

    }


}