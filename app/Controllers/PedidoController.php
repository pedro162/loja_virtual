<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Pessoa;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\Fornecimento;
use App\Models\LogradouroPessoa;
use App\Models\Pedido;
use App\Models\DetalhesPedido;
use \App\Models\Usuario;
use \App\Models\ProdutoCategoria;
use \App\Models\Comentario;
use \App\Models\FormPgto;
use \App\Models\PedidoFormPgto;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;
use \Exception;

class PedidoController extends BaseController
{
    
    const CEP_EMPRESA = 65061220;

    public function painel($request)
    { 
        try {
            Transaction::startTransaction('connection');

            if(!isset($request['post'], $request['post']['cliente'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
            }
            if(empty($request['post']['cliente'])){
                throw new \Exception("Propriedade indefinida<br/>");
                
            }
            $idCliente = (int) explode('=', $request['post']['cliente'][0])[1];

            $pessoa = new Pessoa();
            $resultSelect = $pessoa->findPessoa($idCliente);

            if($resultSelect != false){
                $this->view->pessoa = $resultSelect->getNomePessoa();
                $this->view->idCliente = $idCliente;

                $resultLogPessoa =  $resultSelect->getLogradouro();

                if($resultLogPessoa){
                    $this->view->resultLogPessoa = $resultLogPessoa;

                    $this->render('pedido/painel', false);
                }else{
                    var_dump('Dados pendentes no cadastro do cliente');
                }
                
            }

            Transaction::close();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
        }
        
        
    }

    public function cancelarCompra()
    {
        # code...
    }

    public function finalizarCompra()
    {
        return Venda::carrinho();
    }


    public function addCarrinho($request)
    {
        try {
            Transaction::startTransaction('connection');

            if(
                (!isset($request['get']['cd'])) || ($request['get']['cd'] <= 0)
                || (!isset($request['get']['qtd'])) || (($request['get']['qtd'] <= 0))
            ){

                throw new Exception("Parametro inválido");
                
            }
            $id = (int) $request['get']['cd'];
            $qtd = (int) $request['get']['qtd'];
            $remove = false;

            if(isset($request['get']['rem']) && ($request['get']['rem'] == 1)){
                $remove = true;
            }

            Sessoes::sessionAddElement($id, $qtd, $remove);

            $carrinho = Sessoes::sessionReturnElements()['produto'];
            $qtdItens = 0;
            for ($i=0; !($i == count($carrinho)) ; $i++) { 
                $qtdItens += (int) $carrinho[$i][1];
            }

            $this->view->result = json_encode([$qtdItens]);
            $this->render('pedido/ajax', false);

            Transaction::close();
        } catch (\Exception $e) {

            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
        }
         
    }

    public function fineshPedido()
    {   
        try {
            Transaction::startTransaction('connection');

            //inicia a cessao para o carrinho de compras
            if(!isset(Sessoes::sessionReturnElements()['produto'])){
                Sessoes::sessionInit();
            }
            
            $carrinho = Sessoes::sessionReturnElements()['produto'];


            $allProducts = [];

            $categorias = [];

            $fornecimento = new Fornecimento();
            if(count($carrinho) > 0){
            
                for ($i=0; !($i == count($carrinho)); $i++) { 

                    $product =  $fornecimento->loadFornecimentoForIdProduto((int)$carrinho[$i][0] , true);
                    $allProducts[] = ['produto'=>$product, 'qtd'=> $carrinho[$i][1]];

                    if(!in_array($product->getIdCategoria(),  $categorias)){
                        $categorias[] = $product->getIdCategoria();
                    }

                }
            }

            
            $this->view->allProducts = $allProducts;
            $this->view->moreOptions = $fornecimento->loadFornecimentoForIdCategoria($categorias, true, null, 1,20);
            $this->render('pedido/carrinho', false);

            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
        }
    }

    public function novo()
    {
        $this->render('pedido/novoPedido', false);
    }


    public function loadPessoa($request)
    {   
        Transaction::startTransaction('connection');

        if(!isset($request['post']['loadPessoa'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['post']['loadPessoa'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $pessoa = new Pessoa();
        $result = $pessoa->loadPessoa($request['post']['loadPessoa'], false, true);

        if($result != false){

            $newResult = [];

            for ($i=0; !($i == count($result)); $i++) { 
                $newResult[] = [$result[$i]->idPessoa, $result[$i]->nomePessoa, $result[$i]->documento];
            }
            $this->view->result = json_encode($newResult);
            $this->render('pedido/ajax', false);

        }else{
            $this->view->result = json_encode($result);
            $this->render('pedido/ajax', false);
        }
        
        Transaction::close();
    }


    public function loadEstoque($request)
    {
        Transaction::startTransaction('connection');

        if(!isset($request['post']['loadEstoque'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }
        if(empty($request['post']['loadEstoque'])){
            throw new \Exception("Propriedade indefinida<br/>");
            
        }

        $estoque = new Fornecimento();
        $result = $estoque->loadFornecimento($request['post']['loadEstoque'], true);

        if($result != false){

            $newResult = [];

            for ($i=0; !($i == count($result)); $i++) {
                $newResult[] = [$result[$i]->getProdutoIdProduto(), $result[$i]->getProdutoNome(), ($result[$i]->getQtdFornecida() - $result[$i]->getQtdVendida()), $result[$i]->getVlVenda()];
               // $newResult[] = $result[$i]->cpf;
            }
            $this->view->result = json_encode($newResult);
            $this->render('pedido/ajax', false);

        }else{
            $this->view->result = json_encode($result);
            $this->render('pedido/ajax', false);
        }
        
        Transaction::close();
    }

    /**
     * Recebe os dados do pedido e solicita 
     * que seja salvo no banco.
     */
    public function savePedido($request)
    {
        
        try {

            Transaction::startTransaction('connection');

            Sessoes::sessionInit();//inicia a sessao

            if(!isset($request['post']['pedidoPanelVenda'])
                || !isset($request['post']['cliente'])
                || !isset($request['post']['entrega'])){

                throw new \Exception("Propriedade indefinida<br/>");
                
            }
            if(empty($request['post']['pedidoPanelVenda']) ||
               empty($request['post']['cliente']) ||
               empty($request['post']['entrega'])){

                throw new \Exception("Propriedade indefinida<br/>");
                
            }

            $usuario = Sessoes::usuarioLoad('user_admin');

            $pessoa = new Pessoa();
            $resultFindPessoa = $pessoa->findPessoa((int)$request['post']['cliente']);

            $logradouroPessoa = new LogradouroPessoa();
            $logradouro = $logradouroPessoa->findLogPessoa((int)$request['post']['entrega'], true);

            $pedido = new Pedido();
            $detalhesPedido = new DetalhesPedido();
            $fornecimento = new Fornecimento();

            
            $arrEstoque = [];
            for ($i=0, $dados = $request['post']['pedidoPanelVenda']; !($i == count($dados)) ; $i++) { 

                $item = explode(',', $dados[$i]);

                $estoque = new Fornecimento();
                $result = $estoque->listarConsultaPersonalizada('F.ativo = 1 and (F.qtdFornecida - F.qtdVendida > 0) and P.idProduto = '.$item[0], NULL, NULL, true);

                //$arrEstoque[] = $result;
                $detalhesPedido = new DetalhesPedido();

                $resultQtd       = $detalhesPedido->setQtd($result[0], $item[1]);
                $resultVab       = $detalhesPedido->setValBruto($result[0], $item[2]);
                $resultDesCunit  = $detalhesPedido->setVlDescontoUnit($result[0], $item[3]);
                $resultTotDesc   = $detalhesPedido->setTotalDesconto((float)$item[4]);
                $resultPrecoUnit = $detalhesPedido->setPrecoUnitPratic((float)$item[5]);
                $resultEstoqueId = $detalhesPedido->setIdEstoque((int)$result[0]->getIdFornecimento());

                $resultUsuario   = $detalhesPedido->setUsuarioIdUsuario($usuario->getIdUsuario());

                $resultFornec    = $detalhesPedido->setFornecimentoIdFornecimento($result[0]->getIdFornecimento());

                $pedido->addItem($detalhesPedido);
            
            }
            $pedido->setQtdParcelas(1);
            $pedido->setCliente((int)$request['post']['cliente']);
            $pedido->setLogradouroIdLogradouro((int)$logradouro[0]->getIdLogradouroPessoa());
            $pedido->setUsuarioIdUsuario($usuario->getIdUsuario());
            $pedido->setTipo((int)$request['post']['tipo']);

            $result = $pedido->save([]);
            if($result){

                //busca os detalhes do pedido gravado
                $pedidoNow = $pedido->getPedidoForId((int)$pedido->maxId());
                $detalhePedido = $pedidoNow->getDetalhesPedido();

                //recupera o total do pedido
                $totPedido = 0;

                for ($i=0; !($i == count($detalhePedido)) ; $i++) { 
                    $tot += (float)$detalhePedido[$i]->getPrecoUnitPratic();
                }

                $totParcelas = 0;

                for ($i=0, $dados = $request['post']['PgtoPanelVenda']; !($i == count($dados)); $i++) { 
                    $pgto = explode(',', $dados[$i]);

                    $formPgto = new FormPgto();
                    $result = $formPgto->findFormPgtoForTipo($pgto[0]);
                    $idFormPgto = (int)$result->getIdFormPgto();

                    $pedidoPgto = new PedidoFormPgto();
                    $pedidoPgto->setFormPgtoIdFormPgto($idFormPgto);
                    $pedidoPgto->setPedidoIdPedido($pedido->maxId());
                    $pedidoPgto->setQtdParcelas($pgto[2]);
                    $pedidoPgto->setUsuarioIdUsuario($usuario->getIdUsuario());

                    $pedidoPgto->setVlParcela((float)$pgto[1]);
                    $totParcelas += (float) $pgto[1];

                    $pedidoPgto->save([]);

                }


                if(($totParcelas - $tot) > 0.002){
                    throw new \Exception("Valor das parcelas não condiz com o valor do pedido\n".$pgto[1].' -> '.$tot);
                    
                }

                $this->view->dataCliente = $pedido->previewPedido($pedido->maxId(), false);
                $this->view->dataItens = $pedido->getItensPedido($pedido->maxId(), false);
                $this->render('pedido/pedido', false);
            }


            Transaction::close();

        } catch (\Exception $e) {
            
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
        }
        
    }



     public function detalhesOfProduto($request)
    {
        try {
            Transaction::startTransaction('connection');
            $this->setMenu();
            $this->setFooter();

            if(!isset($request['get']['cd'])){
                throw new Exception("Parametro inválido\n");
                
            }
            $idProduto = intval($request['get']['cd']);

            $produto = new Produto();
            $resultProduto = $produto->loadProdutoForId($idProduto);

            $imagensProduto = $resultProduto->getImagem();

            $fornecimento = new Fornecimento();
            $resultFornce = $fornecimento->loadFornecimentoForIdProduto($idProduto, true);

            $categorias = $resultProduto->produtoCategoria();
            
            $arrIdCategPrim = [];
            $arrIdCategSeg = [];
            for ($i=0; !($i == count($categorias)) ; $i++) { 

                if($categorias[$i]->getClassificCateg() == 'primaria'){
                    $idCateg = $categorias[$i]->getIdCategoria();
                    $arrIdCategPrim[] = (int) $idCateg;
                }else if($categorias[$i]->getClassificCateg() == 'secundaria'){
                    $idCateg = $categorias[$i]->getIdCategoria();
                    $arrIdCategSeg[] = (int) $idCateg;
                }
               
            }
            $othesFornecimentosPrim = $resultFornce->loadFornecimentoForIdCategoria($arrIdCategPrim, true,(int) $idProduto,1,4);
            $othesFornecimentosSec = $resultFornce->loadFornecimentoForIdCategoria($arrIdCategSeg, true,(int) $idProduto, 1,4);


            //busca os comentatios do produto
            $comentario = new Comentario();
            $comentarios = $comentario->listarConsultaPersonalizada('C.ProdutoIdProduto ='.$idProduto, NULL, NULL, true);
            
            $estrelas = $resultProduto->Estrela(); //busca as estelas do produto

            //conta o total dos likes
            $gostie = 0;
            $totEstrelas = 0;

            if($estrelas){

                for ($i=0; !($i == count($estrelas)) ; $i++) { 
                    $gostie += $estrelas[$i]->getNumEstrela();
                }

                $totEstrelas = count($estrelas);

            }

            //calcula a media
            if($totEstrelas > 0 && $totEstrelas > 0){

                $media = round($gostie / $totEstrelas, 1);

            }else{
                $media = 0;
            }
            
            $usuario = Sessoes::usuarioLoad();//pega o usuario se estiver logado

            $this->view->usuario = $usuario;
            $this->view->percent = ($media * 20).'%';//determina a porcentagem da estrela de like
            $this->view->idProduto = $idProduto;
            $this->view->comentarios = $comentarios;
            $this->view->imagensProduto = $imagensProduto;
            $this->view->fornecimento = $resultFornce;
            $this->view->produto = $resultProduto;
            $this->view->categoriasProduto = $categorias;
            $this->view->othesFornecimentosPrim = $othesFornecimentosPrim;
            $this->view->othesFornecimentosSec = $othesFornecimentosSec;
            $this->render('produtos/detalhes', false);
            
            Transaction::close();
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
            
        }
    }

    public function viewMore($request)
    {
        try {
            Transaction::startTransaction('connection');
            $this->setMenu();
            $this->setFooter();

            if((!isset($request['get']['cd']))|| ($request['get']['cd'] <= 0)){
                throw new \Exception("Parâmetro inválido");
                
            }

            $idCateg = (int) $request['get']['cd'];

            $fornecimento = new Fornecimento();

            $result = $fornecimento->loadFornecimentoForIdCategoria([$idCateg], true, null, 1, 20);

            if($result != false){
                $this->view->result = $result;
                $this->render('produtos/produtosRelacionados', false);;
            }else{
                throw new \Exception("Não existem produtos relaionados\n");
                
            }

            Transaction::close();

        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
            
        }
    }

    public function calcFrete($request)
    {
        try {

            Transaction::startTransaction('connection');
            $this->setMenu();
            $this->setFooter();

            if((!isset($request['get']['cep'])) || (!isset($request['get']['prod']))){
                throw new Exception('Parâmetro inválido');
                
            }

            $cep = preg_replace('/[^0-9]/', '',trim($request['get']['cep']));
            $idProd = (int) trim($request['get']['prod']);

            if(!($cep && $idProd)){
                throw new Exception("Parâmetro inválido\n");
                
            }

            $fornce = new Fornecimento();

            $fornProduct = $fornce->loadFornecimentoForIdProduto($idProd, true);

            /*----------------------------- calcua o frete ------------*/
            
            $dadosProduto['nCdEmpresa'] = '';
            $dadosProduto['sDsSenha'] = '';
            $dadosProduto['nCdServico'] = 41106;
            $dadosProduto['sCepOrigem'] = self::CEP_EMPRESA;
            $dadosProduto['sCepDestino'] = $cep;
            $dadosProduto['nVlPeso'] = 2;
            $dadosProduto['nCdFormato'] = 1;
            $dadosProduto['nVlComprimento'] = $fornProduct->getComprimento(); //20;
            $dadosProduto['nVlAltura'] = $fornProduct->getAltura();//6;
            $dadosProduto['nVlLargura'] = $fornProduct->getLargura();//21;
            $dadosProduto['nVlDiametro'] = 11;
            $dadosProduto['sCdMaoPropria'] = 'n';
            $dadosProduto['nVlValorDeclarado'] =  $fornProduct->getVlVenda();
            $dadosProduto['sCdAvisoRecebimento'] = 'N';
            $dadosProduto['StrRetorno'] = 'xml';
            $dadosProduto['nIndicaCalculo'] = 3;

            $frete = new Utils();
            $result = $frete->calFreteCorreios($dadosProduto);

            $frete = $result->cServico;

            $response = 'Total frete R$ '.$frete->Valor.'<br/>Entrega em até '.$frete->PrazoEntrega.' dias';

            $this->view->result = $response;
            
            $this->render('pedido/ajax', false);

            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
        }

    }


}