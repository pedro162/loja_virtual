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

        $result = $produto->paginador($campos, $itensPorPagina, $pagina);
        
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

        $this->setMenu('adminMenu');
        $this->setFooter('footer');
        $this->render('produtos/cadastrar');
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

         $result = $produto->paginador($campos, $itensPorPagina, $pagina);

        $this->view->pagina = $pagina;
        $this->view->itensPorPagina = $itensPorPagina;
        $this->view->totPaginas = ceil($totItens / $itensPorPagina);


        $this->setMenu('adminMenu');
        $this->setFooter('footer');

         //muda o tipo de objeto para stdClass caso a requisisao seja via ajax
         if(isset($request['get'], $request['get']['rq']) && ($request['get']['rq'] == 'ajax')){
            $arrayObjStdClass = [];
             for ($i=0; !($i == count($result)) ; $i++) { 
                 $obj = new \stdClass();

                 $obj->nomeProduto          = $result[$i]->getNomeProduto();
                 $obj->textoPromorcional    = $result[$i]->getTextoPromorcional();
                 $obj->estoque              = $result[$i]->getEstoque();
                 $obj->idProduto            = $result[$i]->getIdProduto();
                 $obj->preco                = $result[$i]->getPreco();
                 $obj->codigo               = $result[$i]->getCodigoProduto();

                 $arrayObjStdClass[] = $obj;
            }

            $stdPaginacao = new \stdClass();
            $stdPaginacao->pagina = $this->view->pagina;
            $stdPaginacao->itensPorPagina = $this->view->itensPorPagina;
            $stdPaginacao->totPaginas = $this->view->totPaginas ;

            $this->view->result = json_encode([$arrayObjStdClass, $stdPaginacao]);
            $this->render('produtos/ajaxPainelAdmin', false);

         }else{
            $this->view->tableProdutos = $result;
            $this->render('produtos/tabelaProdutos');
         }

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

        $this->view->result = $result = $produto->select(
            ['nomeProduto','textoPromorcional', 'idProduto', 'preco', 'estoque', 'codigo'], ['idProduto'=>$request['get']['id']]
        )[0];
        
        $this->setMenu('adminMenu');
        $this->setFooter('footer');
        $this->render('produtos/editar');
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
    	set_time_limit(0);

    	$fiile = new File($request['file']['imgProduto']['name'], $request['file']['imgProduto']['size'], $request['file']['imgProduto']['tmp_name']);
    	if($fiile->salvar('imagens') == true)
    	{
    		echo "Imagem salva com sucesso<br/>";
    	}
    }
}