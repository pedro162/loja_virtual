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
        if(count($request['post']) == 0){

            $this->view->result = 'Consulta inválida';
            $this->render('produtos/ajax', false);
            //throw new \Exception("Consulta inválida<br/>\n");
            return false;
        }

        $superArray = [];

        for ($i=0; !($i == count($request['post']['produtos'])); $i++) { 
            $resultado = null;

            $chave = null;

            for ($j=0; !($j == count($request['post']['produtos'][$i])); $j++) {

                $chave = $request['post']['produtos'][$i][0];
                if($j != 0){
                    $resultado[] = $request['post']['produtos'][$i][$j];
                }
                
            }

            $superArray[$chave] = $resultado;
            
        }

        //$this->view->result = json_encode($superArray);
       // $this->render('produtos/ajax', false);
       // die();

        
        $produto = new Produto();

        $result = $produto->listarConsultaPersonalizada($superArray);

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