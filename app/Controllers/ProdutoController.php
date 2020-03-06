<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Cesta;
use App\Models\Produto;
use App\Models\Fabricante;

class ProdutoController extends BaseController
{
    public function show($request)
    {
        $cesta = new Cesta();

        $fabricante = new Fabricante("pedro", "rua sao sebastiao", 141413131);

        $produto = new Produto("arroz", 12, 12.00);
        $produto->setFabricante($fabricante);
        $produto->addCaracteristica("cor", "branco");
        $produto->addCaracteristica("peso", '2.6 kg');
        $produto->addCaracteristica("sabor", "adocicado");

        $othe_product = new Produto("Feijão", 12, 12.2);
        $othe_product->setFabricante($fabricante);
        $othe_product->addCaracteristica('cor', 'preto');
        $othe_product->addCaracteristica('peoso', '2.4');
        $othe_product->addCaracteristica('grao', 'médio');

        $cesta->addItem($produto);
        $cesta->addItem($othe_product);

        
        $this->view->cesta = $cesta;

        $this->view->produto = $produto->getFabricante()->getNome();
        $this->view->produto_descricao = $produto->getDescricao();
        $this->view->caracteristicas = $produto->getCaracteristicas();
        $this->view->msg =  "estou no produto controler, metodo show nome: ".$request['get']['nome'].'<br/>';
       
        $this->render('produtos/produto', true);
    }
}