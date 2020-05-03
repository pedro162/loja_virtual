<?php

namespace App\Controllers;
use App\Controllers\BaseController;
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
    }

    public function cadastrar()
    {
        $categoria = new Categoria();
        $marca = new Marca();

        $this->view->categorias = $categoria->listaCategoria();
        $this->view->marcas = $marca->listaMarca();

        $this->render('produtos/cadastrar', false);
    }

    public function all($request)
    {

        $pagina = 1;
        $itensPorPagina = 10;

        if(isset($request['get'], $request['get']['pagina'])){
            $pagina = $request['get']['pagina'];
        }

         $produto = new Produto();
         $totItens = $produto->countItens();

         $campos = ['nomeProduto','textoPromorcional', 'idProduto', 'preco', 'estoque','codigo'];


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


    public function detals($request)
    {
       /* echo"<pre>";
        var_dump($request);
        echo "</pre>";*/
    }

    public function editarProduto($request)
    {   
        if(!isset($request['get'], $request['get']['id'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['get']['id'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $produto = new Produto();
        $categoria = new Categoria();
        $marca = new Marca();

        $this->view->categorias = $categoria->listaCategoria();
        $this->view->marcas = $marca->listaMarca();

        $result = $produto->select(
            ['nomeProduto','textoPromorcional','idMarca' , 'idProduto', 'preco', 'estoque', 'codigo'],
            ['idProduto'=>$request['get']['id']], '=', 'asc', null, null, true

        )[0];
        
        $this->view->categoriaProduto = $result->getCategoria();
        
        $this->view->result = $result;
        $this->render('produtos/editar', false);
    }

    public function filtro($request)
    {
        $produto = new Produto();

        $result = $produto->listarConsultaPersonalizada($request);

        $this->view->result = $result;
        $this->render('produtos/ajax', false);
    }

    public function more($request)
    {
        
        $produto = new Produto();
        $result = $produto->detalheProduto($request['get']['id']);
        $this->view->result = $result;
        $this->render('produtos/ajax', false);
    }


    public function salvar($request)
    {
        /*
    	set_time_limit(0);

    	$fiile = new File($request['file']['imgProduto']['name'], $request['file']['imgProduto']['size'], $request['file']['imgProduto']['tmp_name']);
    	if($fiile->salvar('imagens') == true)
    	{
    		echo "Imagem salva com sucesso<br/>";
    	}*/


        $produto = new Produto();
        $produto->commit($request['post']['produto']);
        return false;
    }
}