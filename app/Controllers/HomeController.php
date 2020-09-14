<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Fabricante;
use App\Models\ProdutoCategoria;
use App\Models\Venda;
use App\Models\Categoria;
use App\Models\Fornecimento;
use App\Models\Pessoa;
use Core\Utilitarios\Sessoes;

class HomeController extends BaseController
{

    public function index()
    {
        try{
            
            Transaction::startTransaction('connection');

            //inicia a cessao para o carrinho de compras
            if(!isset(Sessoes::sessionReturnElements()['produto'])){
                Sessoes::sessionInit();
            }

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            $carrinho = Sessoes::sessionReturnElements()['produto'];
            $qtdItens = 0;
            for ($i=0; !($i == count($carrinho)) ; $i++) { 
                $qtdItens += (int) $carrinho[$i][1];
            }

            $this->view->qtdItensCarrinho = $qtdItens;

            $this->setMenu();
            $this->setFooter();

           
            $categsProduct = new ProdutoCategoria();
            $fornecimento = new Fornecimento();
            $categoriaFornecimento = $fornecimento->listarCategoriaFornecimento(null, null, true);

            $produotosAndCategorias = $fornecimento->getProdutoEndCategoria(null, null, true);

            

            $arrCategProducts = [];

            for ($j=0; !($j == count($produotosAndCategorias)) ; $j++) { 

                $idProduto = $produotosAndCategorias[$j]->getProdutoIdProduto();

                $estoque = $produotosAndCategorias[$j]->getQtdFornecida()- $produotosAndCategorias[$j]->getQtdVendida();

                $urlImg = $produotosAndCategorias[$j]->getUrl().'-'.$idProduto.'-'.$estoque;
                
                $arrCategProducts[$urlImg] = $produotosAndCategorias[$j]->getNomeCategoria();
                


            }

            
            $min = (int) $fornecimento->minId();
            $max = (int) $fornecimento->maxId();
            $tot = $fornecimento->countItens();

            $arrProdIndexCat = [];
            $arrCaegId = [];
            $cateOfProds = [];
            for ($i=0; !($i == count($categoriaFornecimento) ); $i++) { 
                
                foreach ($arrCategProducts as $key => $value) {

                    $result = $value == $categoriaFornecimento[$i]->getNomeCategoria();

                    if($result != false){
                        if(!array_key_exists($categoriaFornecimento[$i]->getNomeCategoria(), $arrProdIndexCat)){

                            $idCateg = $categoriaFornecimento[$i]->getIdCategoria();
                            $arrCaegId[$categoriaFornecimento[$i]->getNomeCategoria()] = $idCateg;

                            $arrProdIndexCat[$categoriaFornecimento[$i]->getNomeCategoria()][] = $key;

                            //aciona as categorias num array para gerar o grid
                            if(! in_array($categoriaFornecimento[$i]->getNomeCategoria(), $cateOfProds)){

                                $cateOfProds[] = $categoriaFornecimento[$i]->getNomeCategoria();
                            }
                        }else{
                            $arrProdIndexCat[$categoriaFornecimento[$i]->getNomeCategoria()][] = $key;

                            //aciona as categorias num array para gerar o grid
                            if(! in_array($categoriaFornecimento[$i]->getNomeCategoria(), $cateOfProds)){

                                $cateOfProds[] = $categoriaFornecimento[$i]->getNomeCategoria();
                            }
                        }
                    }   
                 } 


                
            }
            
            //grid
            $grid = [4,2,4,1];

            $soma = 0;

            for ($i=0; !($i == count($grid)) ; $i++) { 
                $soma += $grid[$i];
            }

            //determina a quantidade de interaçoes
            $qtdGrid = intval(count($cateOfProds) /$soma);

            $indiceCategoria = 0;
            $sentinela = 0;

            $historic = [];

            $supArrayCategorias = [];
            while (!($qtdGrid == $sentinela)) {
                
                
                for ($linha=0; !($linha == count($grid)) ; $linha++) { 
                    $coluna = $grid[$linha];

                    $subArrCateg = [];
                    
                    while (!(count($subArrCateg) == $coluna)) {

                        $key = rand(0,count($cateOfProds));
                       if(array_key_exists($key, $cateOfProds)){

                            if(!in_array($key, $historic)){
                               $subArrCateg[] = $cateOfProds[$key];
                                $historic[] = $key; 
                            }
                            
                        }
                        
                        //$indiceCategoria ++;
                    }
                    
                    $supArrayCategorias[] = $subArrCateg;
                }


                $sentinela ++;
            }

            $this->view->usuario            = $usuario;
            $this->view->categorias         = $supArrayCategorias;
            $this->view->arrProdIndexCat    = $arrProdIndexCat;
            $this->view->arrCaegId          = $arrCaegId;

            $this->view->qtd = Venda::qtdItensVenda();// insere o total de itens do carrinho
            $this->render('home/home', true);

            Transaction::close();

        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
            
            Transaction::rollback();

            //falta ajustar
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->fornecimento = json_encode($erro);
            $this->render('home/home', true);
        }
    }

    public function indexLogin()
    {
        try {
            Transaction::startTransaction('connection');
            $this->setMenu();
            $this->setFooter();

            $this->render('home/login', false);

            Transaction::close();

        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            
            Transaction::rollback();
        }
        
    }

    public function init()
    {
        try {
            
            Transaction::startTransaction('connection');
            
            $this->render('home/loginInit', false);

            Transaction::close();
            
            Sessoes::sessionEnde();


        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            
            Transaction::rollback();
        }
        
    }
    
    public function loginUser($request)
    {
        try {
            Transaction::startTransaction('connection');

            if((!isset($request['post']['usuario'])) || (!isset($request['post']['senha']))) {
                throw new Exception("Dados inálidos\n");
            }

            $pessoa = new Pessoa();
            $result = $pessoa->findLoginForUserPass($request['post']['usuario'], $request['post']['senha']);

            Sessoes::usuarioInit($result);
            
            header('Location:/');
            //$this->view->result= json_encode([1]);
            //$this->render('home/ajax', false);
            Transaction::close();

        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            Sessoes::sendMessage($erro);
            header('Location:/home/init');
        }
        
        
    }



    public function logoutUser()
    {
        if(Sessoes::sessionEnde()){
            header('Location:/home/init');
        }

    }

    public function cadastro()
    {
        $this->setMenu();
        $this->setFooter('footer');
        $this->render('login/cadastro', true);
    }

    public function painel()
    {   
        try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            Transaction::startTransaction('connection');

            $fornecimento = new Fornecimento();
            $resultMonitEst = $fornecimento->monitoraEstoque();

            $this->view->resultMonitEst = $resultMonitEst;
            $this->setMenu('adminMenu');
            $this->setFooter('footer');
            $this->render('admin/home', true, 'layoutAdmin');

            Transaction::close();

        } catch (\PDOException $e) {

            Transaction::rollback();

        }catch (\Exception $e) {
            Transaction::rollback();
            echo $e->getMessage().'-'.$e>getFile();
        }
        
        
    }

    public function inicoPainel()
    {
        try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            Transaction::startTransaction('connection');

            $fornecimento = new Fornecimento();
            $resultMonitEst = $fornecimento->monitoraEstoque();

            $this->view->resultMonitEst = $resultMonitEst;
            $this->setMenu('adminMenu');
            $this->setFooter('footer');
            $this->render('admin/home', false);

            Transaction::close();

        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
            Transaction::rollback();
            echo $e->getMessage().'-'.$e>getFile();
        }

    }

    

    public function menu()
    {
        $this->render('layout/menuOpcoesAdmin', false);
    }



    
}
