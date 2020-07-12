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
use App\Models\Estrela;
use \Core\Utilitarios\Sessoes;
use \App\Models\Usuario;

class ProdutoController extends BaseController
{
    const IMG = ['primaria', 'secundaria', 'ternaria','quatenaria'];

    public function show($request)
    {  
        try{
            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection'); //arbre a conexao com o banco

            $pagina = 1;
            $itensPorPagina = 18;

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
            
            $this->view->usuario = $usuario;
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

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            Transaction::startTransaction('connection');

            $categoria = new Categoria();
            $marca = new Marca();

            $totItensCategoria = $categoria->countItens();
            $totItensMarca = $marca->countItens();

            if(($totItensCategoria > 0) && ($totItensMarca > 0)){

                $this->view->categorias = $categoria->listaCategoria();
                $this->view->marcas = $marca->listaMarca();
                $this->view->img = self::IMG;

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

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

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

    public function detalhes($request)
    {
        try {
            Transaction::startTransaction('connection');
           
            
            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();
            var_dump($e);
            
        }
    }

    public function editarProduto($request)
    {   

        try{ 

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

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
            $this->view->img = self::IMG;

            $this->view->categorias = $categoria->listaCategoria();
            $this->view->marcas = $marca->listaMarca();


            $result = $produto->select(
                ['nomeProduto','textoPromorcional','idMarca' , 'idProduto'],
                ['idProduto'=>$request['get']['id']], '=', 'asc', null, null, true

            )[0];
            
            $imagem = __DIR__.'/../../public/files/imagens/'.$result->getImagem()[0]->getUrl();
            if(file_exists($imagem)){
                $this->view->imgProduto = '<img style="width: 253px; height: 232px" id="img" src="../files/imagens/'.$result->getImagem()[0]->getUrl().'" />';
            }else{
                $this->view->imgProduto = '<img style="width: 253px; height: 232px" id="img" src=""/>';
            }

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
            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

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

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

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

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            set_time_limit(0);
        
            Transaction::startTransaction('connection');

            if(!isset($request['post'])){
                throw new \Exception("Preencha o formulario corretament\n");
                
            }


            $extenImg = null;
            $nameImg = null;
            $beforeExtension = null;

            //verifica se as imagens foram enviadas
            if(isset($request['file']) && (count($request['file']) == 4) ){
                foreach ($request['file'] as $key => $value) {
                    if(isset($value['name']) && (strlen($value['name']) > 0)){

                        $arrNameExtension = explode('/', $value['type']);
                        $beforeExtension = $arrNameExtension[0]; 

                        $extenImg = strtolower($arrNameExtension[1]);

                        if((!isset($request['post']['nome'])) || (strlen($request['post']['nome']) == 0)){
                            throw new \Exception("Preencha o formulario corretamente\n");
                            
                        }

                        $nameImg = strtolower($request['post']['nome']).'.'.$extenImg;
                        $nameImg = str_replace([' ','_'], ['', ''], $nameImg);

                        $request['post']['img'][$key] = $nameImg;
                    }else{
                        throw new \Exception("Adicione as imagens\n");
                        
                    }
                }
            }else{
                throw new \Exception("Adicione as imagens\n");
            }


            $produto = new Produto();

            $produto->setUsuarioIdUsuario($usuario->getIdUsuario());
            $resultPoduto = $produto->save($request['post']);

            if($resultPoduto != false){
                foreach ($request['file'] as $key => $value) {

                    $tipo = null;
                    switch (trim($key)) {
                        case 'imgProduto-2':
                           $tipo = 'secundaria';
                            break;
                        case 'imgProduto-3':
                            $tipo = 'ternaria';
                            break;
                        case 'imgProduto-4':
                            $tipo = 'quartenaria';
                            break;
                        default:
                             $tipo = 'primaria';
                            break;
                    }

                    $newNameImg = $tipo.'-'.$nameImg;

                    if($tipo != null){
                        $fiile = new File($newNameImg, $value['size'], $value['tmp_name']);

                        $fiile->salvar('imagens', true);
                    }
                    
                }
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


            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            set_time_limit(0);
            
            Transaction::startTransaction('connection');
            $produto = new Produto();

            $imagem = new Imagem();
            $urlImagem = $imagem->select(['url'], ['ProdutoIdProduto'=>$request['post']['prod']], '=','asc', null, null,true);
            if($urlImagem == false){
                die();//falta implemtar testando
            }

            $extenImg = null;
            $nameImg = null;

            $beforeExtension = null;

            if(isset($request['file']) && (count($request['file']) == 4) ){
                foreach ($request['file'] as $key => $value) {
                    if(isset($value['name']) && (strlen($value['name']) > 0)){

                        $arrNameExtension = explode('/', $value['type']);
                        $beforeExtension = $arrNameExtension[0]; 

                        $extenImg = strtolower($arrNameExtension[1]);
                        $nameImg = strtolower($request['post']['nome']).'.'.$extenImg;
                        $nameImg = str_replace([' ','_'], ['', ''], $nameImg);

                        $request['post']['img'][$key] = $nameImg;
                    }else{
                        throw new \Exception("Adicione as imagens\n");
                        
                    }
                }
            }else{
                throw new \Exception("Adicione as imagens\n");
            }

            foreach ($request['file'] as $key => $value) {

                    $tipo = null;
                    switch (trim($key)) {
                        case 'imgProduto-2':
                           $tipo = 'secundaria';
                            break;
                        case 'imgProduto-3':
                            $tipo = 'ternaria';
                            break;
                        case 'imgProduto-4':
                            $tipo = 'quartenaria';
                            break;
                        default:
                             $tipo = 'primaria';
                            break;
                    }


                    if($tipo != null){


                        $fiile = new File($tipo.'_'.$nameImg, $value['size'], $value['tmp_name']);

                        $resultUnlink = unlink($nameImg);

                        $fiile->salvar('imagens', true);
                    }
                
            }

            
            /*$fiile = new File($tipo.'_'.$nameImg, $value['size'], $value['tmp_name']);

                        $resultUnlink = unlink($nameImg);

                        $fiile->salvar('imagens', true);*/


            $this->view->result = json_encode($result);

            $this->render('produtos/ajaxPainelAdmin', false);

            Transaction::close();

        } catch (\Exception $e) {

            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage().' - linha: '.$e->getLine().' - arquivo: '.$e->getFile()];
            $this->view->result = json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }

    }


    public function lancarEstoque()
    {
        try{


            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

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


    public function votar($request)
    {
        try{

            Sessoes::sessionInit();//inicia a sessao
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');

            if((!isset($request['get']['cd'])) || (!isset($request['get']['pt']))){
                throw new \Exception("Parâmetro inváldio");
                
            }
            $produto = new Produto();

            $pontos = (int)$request['get']['pt']; //pontos voto
            $id = (int) $request['get']['cd'];  //produto votado

            //busca o  produto
            $resultFind = $produto->loadProdutoForId((int)$id); //busca o produto 
            
            $estrelas = $resultFind->Estrela(); //busca as estelas do produto

            //conta o total dos likes
            $gostie = 0;
            $totEstrelas = 0;

            if($estrelas){
                for ($i=0; !($i == count($estrelas)) ; $i++) { 
                    $gostie += $estrelas[$i]->getNumEstrela();
                }

                $totEstrelas = count($estrelas);
            }

            $estrela = new Estrela();
            $resultVoto = $estrela->save(['produto'=>(int) $id, 'estrela'=> (int)$pontos, 'user' => $usuario->getIdPessoa()]);

            if($resultVoto == true){

                $totVotos = $totEstrelas + 1;
                $totLikes = $gostie  + $pontos;

                $media = round($totLikes / $totVotos, 1);

                $this->view->result= json_encode([$media, 'Obrigado!']);
                $this->render('produtos/ajaxPainelAdmin', false);
            }
            
            Transaction::close();

        } catch (\Exception $e) {

            Transaction::rollback();


            //falta implementar corretamente
            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result= json_encode($erro);
            $this->render('produtos/ajaxPainelAdmin', false);
        }
    }

}