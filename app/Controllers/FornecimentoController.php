<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use \App\Models\Produto;
use \App\Models\Fabricante;
use \App\Models\Fornecimento;
use \App\Models\Pessoa;
use \Core\Utilitarios\Utils;
use \App\Models\ProdutoCategoria;
use \App\Models\Venda;

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
        $totItens = (int) $fornecimento->countItens();

        $this->view->totItens = $totItens;

        $inicio = (int) ($itensPorPagina * $pagina) - $itensPorPagina;
        $inicio = ($inicio == 0) ? 1: $inicio;


        $result = $fornecimento->listarConsultaPersonalizada(null, $inicio, $itensPorPagina, true);
       
        $this->view->pagina = $pagina;
        $this->view->itensPorPagina = $itensPorPagina;
        $this->view->totPaginas = ceil($totItens / $itensPorPagina);

        $stdPaginacao = new \stdClass();
        $stdPaginacao->pagina = $this->view->pagina;
        $stdPaginacao->itensPorPagina = $this->view->itensPorPagina;
        $stdPaginacao->totPaginas = $this->view->totPaginas ;

        $this->view->tableFornecimento = $result;

        $this->render('fornecimento/tabelaEstoque', false);
        
        Transaction::close();

    }


    public function editar($request)
    {   
         Transaction::startTransaction('connection');

        if(!isset($request['get'], $request['get']['id'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['get']['id'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $fornecimento  = new Fornecimento();

        $result = $fornecimento->select(
            ['idFornecimento','ProdutoIdProduto','nf' , 'FornecedorIdFornecedor',
             'dtFornecimento', 'dtRecebimento', 'dtValidade','qtdFornecida', 'vlCompra', 'vlVenda'
            ],
            ['idFornecimento'=>$request['get']['id']], '=', 'asc', null, null, true

        )[0];

        $produto    = new Produto();
        $resultProduto = $produto->select(
            ['nomeProduto', 'idProduto'],
            [], '=', 'asc', null, null, true);
        $this->view->resultProduto = $resultProduto;

        //$this->view->fornecedor = $result->FornecedorIdFornecedor;

        $this->view->result = $result;
        $this->render('fornecimento/editarEstoque', false);

        Transaction::close();
    }


    public function atualizar($request)
    {
        Transaction::startTransaction('connection');

        $fornecimento = new Fornecimento();
        $result = $fornecimento->modify($request['post']['estoque']);
        
        $this->view->result = json_encode($result);
        $this->render('produtos/ajaxPainelAdmin', false);

        Transaction::close();

    }



    public function detalhes($request)
    {
        try {
            Transaction::startTransaction('connection');
            $this->setMenu();
            $this->setFooter();

            if(!isset($request['get']['cd'])){
                var_dump('não e');
            }
            $idProduto = intval($request['get']['cd']);

            

            $produto = new Produto();
            $resultProduto = $produto->loadProdutoForId($idProduto);

            $imagensProduto = $resultProduto->getImagem();

            $fornecimento = new Fornecimento();
            $resultFornce = $fornecimento->loadFornecimentoForIdProduto($idProduto, true);

            $categorias = $resultProduto->produtoCategoria();
            
            $arrIdCateg = [];
            for ($i=0; !($i == count($categorias)) ; $i++) { 

                if($categorias[$i]->getClassificCateg() == 'primaria'){
                    $idCateg = $categorias[$i]->getIdCategoria();
                    $arrIdCateg[] = (int) $idCateg;
                }
               
            }
            $othesFornecimentos = $resultFornce->loadFornecimentoForIdCategoria($arrIdCateg, true,(int) $idProduto);
            
            $this->view->imagensProduto = $imagensProduto;
            $this->view->fornecimento = $resultFornce;
            $this->view->produto = $resultProduto;
            $this->view->categoriasProduto = $categorias;
            $this->view->othesFornecimentos = $othesFornecimentos;
            $this->render('produtos/detalhes', false);
            
            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            var_dump($e->getMessage());
            
        }
    }

}