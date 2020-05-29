<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Fabricante;
use App\Models\ProdutoCategoria;
use App\Models\Venda;
use App\Models\Categoria;
use App\Models\Fornecimento;

class HomeController extends BaseController
{

    public function index()
    {
        try{

            Transaction::startTransaction('connection');
            $this->setMenu();
            $this->setFooter();

           
            $categsProduct = new ProdutoCategoria();
            $fornecimento = new Fornecimento();
            $categoriaFornecimento = $fornecimento->listarCategoriaFornecimento(null, null, true);

            $produotosAndCategorias = $fornecimento->getProdutoEndCategoria(null, null, true);

            $arrCategProducts = [];

            for ($j=0; !($j == count($produotosAndCategorias)) ; $j++) { 

                $idProduto = $produotosAndCategorias[$j]->getProdutoIdProduto();
                $urlImg = $produotosAndCategorias[$j]->getUrl();
                $result = $categsProduct->getCategoria($idProduto);
                if($result){
                    $arrCategProducts[$urlImg] = $result;
                }
            }

            
            $min = (int) $fornecimento->minId();
            $max = (int) $fornecimento->maxId();
            $tot = $fornecimento->countItens();

            $arrProdIndexCat = [];

            for ($i=0; !($i == count($categoriaFornecimento) ); $i++) { 
                
                foreach ($arrCategProducts as $key => $value) {
                    for ($j=0; !($j == count($value) ); $j++) {

                        $result = $categoriaFornecimento[$i]->getIdCategoria() == $value[$j]->getIdCategoria();

                        if($result != false){
                            if(!array_key_exists($categoriaFornecimento[$i]->getNomeCategoria(), $arrProdIndexCat)){
                                $arrProdIndexCat[$categoriaFornecimento[$i]->getNomeCategoria()][$key] = $value;
                            }else{
                                $arrProdIndexCat[$categoriaFornecimento[$i]->getNomeCategoria()][$key] = $value;
                            }
                        }   
                    }
                 
                 } 


                
            }

            /*$supperArrProdutos = [];

            $totItens = 0;

            while (!($totItens == $tot)) {

                $idRand = rand($min, $max);

                if(array_key_exists($idRand, $supperArrProdutos) == false){
                    $result =  $fornecimento->listarConsultaPersonalizada('F.idFornecimento = '.$idRand, null, null, true);
                    if($result != false){
                        $supperArrProdutos[$idRand] = $result[0];
                        $totItens ++;
                    }
                    
                }
                

            }*/

            $grid = [4,2,4,1];

            $soma = 0;

            for ($i=0; !($i == count($grid)) ; $i++) { 
                $soma += $grid[$i];
            }

            //determina a quantidade de interaçoes
            $qtdGrid = intval(count($categoriaFornecimento) /$soma);

            $supperArrayCategorias = [];

            $indiceCategoria = 0;
            $sentinela = 0;


            while (!($qtdGrid == $sentinela)) {
                
                $supArrayCategorias = [];
                for ($linha=0; !($linha == count($grid)) ; $linha++) { 
                    $coluna = (int) $grid[$linha];

                    $subArrCateg = [];
                    
                    while (!(count($subArrCateg) == $coluna)) {

                       if(array_key_exists($indiceCategoria, $categoriaFornecimento)){

                            $subArrCateg[] = $categoriaFornecimento[$indiceCategoria]->getNomeCategoria();
                        }
                        
                        $indiceCategoria ++;
                    }
                    
                    $supArrayCategorias[] = $subArrCateg;
                }

                $supperArrayCategorias[] = $supArrayCategorias;

                $sentinela ++;
            }
            
            $this->view->categorias = $supperArrayCategorias;
            $this->view->arrProdIndexCat = $arrProdIndexCat;

            $this->view->qtd = Venda::qtdItensVenda();// insere o total de itens do carrinho
            $this->render('home/home', true);

        } catch (\Exception $e) {
            
            Transaction::rollback();

            //falta ajustar
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->fornecimento = json_encode($erro);
            $this->render('home/home', true);
        }
    }

    public function login()
    {
        $this->setMenu();
        $this->render('login/login', true);
    }

    public function cadastro()
    {
        $this->setMenu();
        $this->setFooter('footer');
        $this->render('login/cadastro', true);
    }

    public function painel()
    {   
        $this->setMenu('adminMenu');
        $this->setFooter('footer');
        $this->render('produtos/cadastrar', true, 'layoutAdmin');
    }

    public function teste($request){
        echo "<pre>";
        var_dump($request);
        echo "</pre>";
    }

    public function menu()
    {
        $this->render('layout/menuOpcoesAdmin', false);
    }
}
