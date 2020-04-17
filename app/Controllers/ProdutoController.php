<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Cliente;
use Core\Containner\File;
use App\Models\Venda;

class ProdutoController extends BaseController
{
    public function show()
    {
        $produto = new Produto();

        $this->view->produtos = $produto->listarProdutos();
        $this->setMenu();
        $this->view->qtd = Venda::qtdItensVenda(); // insere o total de itens do carrinho

        $this->view->optionsLeft =
        [
            'Departamento'=>[e,3], 'Preco'=>[e,3],
            'Mais procurados'=>[e,3],
            'Cama, Mesa e Banho'=>[e,3],
            'Condições'=> [e,3]
        ];

        $this->render('produtos/relacionados', true);
    }

    public function cadastrar()
    {
    	$this->setMenu('adminMenu');
        $this->render('produtos/cadastrar');
    }


    public function detals($request)
    {
       /* echo"<pre>";
        var_dump($request);
        echo "</pre>";*/
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