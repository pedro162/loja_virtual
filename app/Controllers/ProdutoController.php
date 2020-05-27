<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Cliente;
use Core\Containner\File;
use App\Models\Venda;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Fornecimento;
use App\Models\Imagem;


class ProdutoController extends BaseController
{
    public function show($request)
    {  
        try{

            Transaction::startTransaction('connection');

            $pagina = 1;
            $itensPorPagina = 2;

            if(isset($request['get'], $request['get']['pagina'])){
                $pagina = $request['get']['pagina'];
            }

            $produto = new Produto();

            $fornecimento = new Fornecimento();

            $totItens = (int) $fornecimento->countItens();

            $inicio = (int) ($itensPorPagina * $pagina) - $itensPorPagina;
            $inicio = ($inicio == 0) ? 1: $inicio;

            $result = $fornecimento->listarConsultaPersonalizada(null, $inicio, $itensPorPagina, true);
            
            $this->view->fornecimento = $result; 
            
            $this->view->pagina = $pagina;
            $this->view->itensPorPagina = $itensPorPagina;
            $this->view->totPaginas = ceil($totItens / $itensPorPagina);
            
            $this->view->qtd = Venda::qtdItensVenda(); // insere o total de itens do carrinho
            $this->view->optionsLeft = $produto->getFiltros();

            $this->setMenu();
            $this->setFooter('footer');
            $this->render('produtos/relacionados', true);

            Transaction::close();
        } catch (\Exception $e) {
            
            Transaction::rollback();

            //falta ajustar
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->fornecimento = json_encode($erro);
            $this->render('produtos/relacionados', true);
        }
    }

    public function cadastrar()
    {
        try{
            Transaction::startTransaction('connection');

            $categoria = new Categoria();
            $marca = new Marca();

            $totItensCategoria = $categoria->countItens();
            $totItensMarca = $marca->countItens();

            if(($totItensCategoria > 0) && ($totItensMarca > 0)){

                $this->view->categorias = $categoria->listaCategoria();
                $this->view->marcas = $marca->listaMarca();

                $this->render('produtos/cadastrar', false);

            }else{


                $this->view->categorias = $totItensCategoria;
                $this->view->marcas = $totItensMarca;

                $this->render('produtos/cadastrar', false);
                
            }

            Transaction::close();
        } catch (\Exception $e) {
            
            Transaction::rollback();

            //falta implementar corretamente
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->categorias  = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }
    }

    public function all($request)
    {
        try{

            Transaction::startTransaction('connection');

            $pagina = 1;
            $itensPorPagina = 10;

            if(isset($request['get'], $request['get']['pagina'])){
                $pagina = $request['get']['pagina'];
            }

            $produto = new Produto();
            $totItens = $produto->countItens();

            if($totItens == 0){

                $this->view->tableProdutos = $totItens;
                $this->render('produtos/tabelaProdutos', false);

            }else{

                $campos = ['nomeProduto','textoPromorcional', 'idProduto'];

                $this->view->pagina = $pagina;
                $this->view->itensPorPagina = $itensPorPagina;
                $this->view->totPaginas = ceil($totItens / $itensPorPagina);

                $result = $produto->paginador($campos, $itensPorPagina, $pagina, true);
                
                $stdPaginacao = new \stdClass();
                $stdPaginacao->pagina = $this->view->pagina;
                $stdPaginacao->itensPorPagina = $this->view->itensPorPagina;
                $stdPaginacao->totPaginas = $this->view->totPaginas ;

                $this->view->tableProdutos = $result;
                $this->render('produtos/tabelaProdutos', false);

            }

            Transaction::close();
        } catch (\Exception $e) {
            
            Transaction::rollback();

            //falta implementar corretamente
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->tableProdutos = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }
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
        try{ 
            Transaction::startTransaction('connection');

            if(!isset($request['get'], $request['get']['id'])){
                throw new \Exception("Propriedade indefinida<br/>");
                
            }
            if(empty($request['get']['id'])){
                throw new \Exception("Propriedade indefinida<br/>");
                
            }

            $produto    = new Produto();
            $categoria  = new Categoria();
            $marca      = new Marca();

            $this->view->categorias = $categoria->listaCategoria();
            $this->view->marcas = $marca->listaMarca();


            $result = $produto->select(
                ['nomeProduto','textoPromorcional','idMarca' , 'idProduto'],
                ['idProduto'=>$request['get']['id']], '=', 'asc', null, null, true

            )[0];
            
            $this->view->categoriaProduto = $result->getCategoria();
            
            $this->view->result = $result;
            $this->render('produtos/editar', false);

            Transaction::close();
        } catch (\Exception $e) {
            
            Transaction::rollback();

            //falta implementar corretamente
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }
    }

    public function filtro($request)
    {
        try{

            Transaction::startTransaction('connection');

            $produto = new Produto();

            $result = $produto->listarConsultaPersonalizada($request);

            $this->view->result = json_encode($result);
            $this->render('produtos/ajax', false);

            Transaction::close();
        } catch (\Exception $e) {
            
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }
    }

    public function more($request)
    {
        try {

            Transaction::startTransaction('connection');

            $produto = new Produto();
            $result = $produto->detalheProduto($request['get']['id']);
            $this->view->result = $result;
            $this->render('produtos/ajax', false);

            Transaction::close();

        } catch (\Exception $e) {
            
            Transaction::rollback();

            //falta implementar corretamente
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }
    }


    public function salvar($request)
    {
        try {
            set_time_limit(0);
        
            Transaction::startTransaction('connection');

            //prepara o arquivo de imagem para salvar
            $extenImg = explode('/', $request['file']['imgProduto']['type'])[1];
            $nameImg = $request['post']['nome'].'.'.$extenImg;
            $nameImg = strtolower((str_replace([' ','_'], ['', ''], $nameImg)));
            $fiile = new File($nameImg, $request['file']['imgProduto']['size'], $request['file']['imgProduto']['tmp_name']);

            $produto = new Produto();
            $request['post']['img'] = $nameImg;
            $resultPoduto = $produto->save($request['post']);

            if($resultPoduto != false){
                $fiile->salvar('imagens', true);
            }
            
            $this->view->result = json_encode($resultPoduto);
            $this->render('produtos/ajaxPainelAdmin', false);

            Transaction::close();

        } catch (\Exception $e) {

            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }
        
        
    }

    public function atualizar($request)
    {
        try{

            set_time_limit(0);
            
            Transaction::startTransaction('connection');
            
            $extenImg = explode('/', $request['file']['imgProduto']['type'])[1];
            $nameImg = $request['post']['nome'].'.'.$extenImg;
            $nameImg = strtolower((str_replace([' ','_'], ['', ''], $nameImg)));
            $fiile = new File($nameImg, $request['file']['imgProduto']['size'], $request['file']['imgProduto']['tmp_name']);

            $produto = new Produto();
            $request['post']['img'] = $nameImg;
            $result = $produto->modify($request['post']);

            if($result != false){
                $fiile->salvar('imagens', true);
            }

            $this->view->result = json_encode($result);

            $this->render('produtos/ajaxPainelAdmin', false);

            Transaction::close();

        } catch (\Exception $e) {

            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }

    }


    public function lancarEstoque()
    {
        try{

            Transaction::startTransaction('connection');

            $produto    = new Produto();
            $result = $produto->select(
                ['nomeProduto', 'idProduto'],
                [], '=', 'asc', null, null, true);

            $this->view->produtos = $result;
            $this->render('produtos/estoqueLancar', false);

            Transaction::close();

        } catch (\Exception $e) {

            Transaction::rollback();


            //falta implementar corretamente
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->produtos = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }
    }

}