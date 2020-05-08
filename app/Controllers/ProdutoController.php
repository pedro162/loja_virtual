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


class ProdutoController extends BaseController
{
    public function show($request)
    {   
        Transaction::startTransaction('connection');

        $pagina = 1;
        $itensPorPagina = 18;

        if(isset($request['get'], $request['get']['pagina'])){
            $pagina = $request['get']['pagina'];
        }

        $produto = new Produto();

        $totItens = $produto->countItens();

        $campos = ['nomeProduto','textoPromorcional', 'idProduto', 'preco'];

        $result = $produto->paginador($campos, $itensPorPagina, $pagina, true);
        
        $this->view->produtos = $produto->listarProdutos($result);
        
        $this->view->pagina = $pagina;
        $this->view->itensPorPagina = $itensPorPagina;
        $this->view->totPaginas = ceil($totItens / $itensPorPagina);
        
        $this->view->qtd = Venda::qtdItensVenda(); // insere o total de itens do carrinho
        $this->view->optionsLeft = $produto->getFiltros();

        $this->setMenu();
        $this->setFooter('footer');
        $this->render('produtos/relacionados', true);

        Transaction::close();
    }

    public function cadastrar()
    {
        Transaction::startTransaction('connection');

        $categoria = new Categoria();
        $marca = new Marca();

        $this->view->categorias = $categoria->listaCategoria();
        $this->view->marcas = $marca->listaMarca();

        $this->render('produtos/cadastrar', false);

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

         $produto = new Produto();
         $totItens = $produto->countItens();

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

        Transaction::close();

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
    }

    public function filtro($request)
    {
        Transaction::startTransaction('connection');

        $produto = new Produto();

        $result = $produto->listarConsultaPersonalizada($request);

        $this->view->result = $result;
        $this->render('produtos/ajax', false);

        Transaction::close();
    }

    public function more($request)
    {
        Transaction::startTransaction('connection');

        $produto = new Produto();
        $result = $produto->detalheProduto($request['get']['id']);
        $this->view->result = $result;
        $this->render('produtos/ajax', false);

        Transaction::close();
    }


    public function salvar($request)
    {
        Transaction::startTransaction('connection');

        /*
    	set_time_limit(0);

    	$fiile = new File($request['file']['imgProduto']['name'], $request['file']['imgProduto']['size'], $request['file']['imgProduto']['tmp_name']);
    	if($fiile->salvar('imagens') == true)
    	{
    		echo "Imagem salva com sucesso<br/>";
    	}*/


        $produto = new Produto();
        $result = $produto->commit($request['post']['produto']);

        $this->view->result = json_encode($result);
        $this->render('produtos/ajaxPainelAdmin', false);

        Transaction::close();
        
    }
}