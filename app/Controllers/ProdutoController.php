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
    public function show()
    {
        $produto = new Produto();

        $this->view->produtos = $produto->listarProdutos(['nomeProduto','textoPromorcional', 'idProduto', 'preco', 'idDepartamento']);
        $this->setMenu();
        $this->setFooter('footer');
        
        $this->view->qtd = Venda::qtdItensVenda(); // insere o total de itens do carrinho
        $this->view->optionsLeft = $produto->getFiltros();
        $this->render('produtos/relacionados', true);
    }

    public function cadastrar()
    {
    	$this->setMenu('adminMenu');
        $this->setFooter('footer');

        $categoria = new Categoria();
        $marca = new Marca();

        $this->view->categorias = $categoria->listaCategoria();
        $this->view->marcas = $marca->listaMarca();
        $this->render('produtos/cadastrar');
    }


    public function detals($request)
    {
       /* echo"<pre>";
        var_dump($request);
        echo "</pre>";*/
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