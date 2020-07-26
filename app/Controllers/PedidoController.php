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
use \App\Models\ContaPagarReceber;
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

            $idCliente = null;

            if(isset($request['post']['cliente']) && (!empty($request['post']['cliente']))){

                $idCliente = (int) explode('=', $request['post']['cliente'][0])[1];
            
            }elseif(isset($request['get']['cliente']) && (!empty($request['get']['cliente']))){
                $idCliente = (int) $request['get']['cliente'];
            }

            if($idCliente == null){

                throw new \Exception("Propriedade indefinida<br/>");
                
            }

            $pessoa = new Pessoa();
            $resultSelect = $pessoa->findPessoa($idCliente);

            if($resultSelect != false){
                $this->view->pessoa = $resultSelect->getNomePessoa();
                $this->view->idCliente = $idCliente;

                $resultLogPessoa =  $resultSelect->getLogradouro();

                if($resultLogPessoa){
                    $this->view->resultLogPessoa = $resultLogPessoa;
                    $this->view->idCliente = $idCliente;
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

            $qtdItens = $this->qtdIntensCar();

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

    public function qtdIntensCar()
    {
        //busca o usuario logado
        $usuario = Sessoes::usuarioLoad();
        if($usuario == false){
            header('Location:/home/init');
            
        }

        $carrinho = Sessoes::sessionReturnElements()['produto'];
        $qtdItens = 0;

        if(is_array($carrinho)){

            for ($i=0; !($i == count($carrinho)) ; $i++) {
                if(in_array($carrinho[$i][1], $carrinho[$i])){

                    $qtdItens += (int) $carrinho[$i][1];
                }
            }
        }

        return $qtdItens;
    }

    public function removeFromCarrinho($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            if((!isset($request['get']['cd'])) || ($request['get']['cd'] <= 0)){
                throw new Exception("Parametro inválido");
            }

            Sessoes::removeElement((int)$request['get']['cd']);

            $qtdItens = $this->qtdIntensCar();

            $this->view->result = json_encode([$qtdItens]);
            $this->render('pedido/ajax', false);
            
        } catch (\Exception $e) {
            
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
            if((is_array($carrinho)) && (count($carrinho) > 0)){
            
                for ($i=0; !($i == count($carrinho)); $i++) { 

                    if(in_array($carrinho[$i][0], $carrinho[$i])){
                        $product =  $fornecimento->loadFornecimentoForIdProduto((int)$carrinho[$i][0] , true);
                        $allProducts[] = ['produto'=>$product, 'qtd'=> $carrinho[$i][1]];

                        if(!in_array($product->getIdCategoria(),  $categorias)){
                            $categorias[] = $product->getIdCategoria();
                        }
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

            if( $logradouro == false){
                throw new Exception("Endereço de entrega não definido\n");
                
            }

            $pedido = new Pedido();
            $detalhesPedido = new DetalhesPedido();

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
                    $totPedido += (float) $detalhePedido[$i]->getPrecoUnitPratic() * $detalhePedido[$i]->getQtd();
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

                //verificar o bug na diferença entre o total das parcelas e o total do pedido
                if(abs($totParcelas - $totPedido) > 0.005){
                    throw new \Exception("Valor das parcelas não condiz com o valor do pedido\n".$totParcelas.' -> '.$totPedido);
                    
                }

                $this->printPedido($pedido->maxId());
                
            }


            Transaction::close();

        } catch (\Exception $e) {
            
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
        }
        
    }

    //monta a view do pedido completo
    public function printPedido(Int $id)
    {   
        $pedido = new Pedido();
        
        $this->view->dataCliente = $pedido->previewPedido($id, false);
        $this->view->dataItens = $pedido->getItensPedido($id, false);
        $this->render('pedido/pedido', false);
    }

    public function viewPedidoLoja($request)
    {

        try {
            
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            if((!isset($request['get']['cd'])) || ($request['get']['cd'] <= 0)){
                throw new Exception("Parametro inválido");
            }

            Transaction::startTransaction('connection');

            $pedido = new Pedido();
            $resultPedido = $pedido->getPedidoForId((int) $request['get']['cd']);
            $this->printPedido($resultPedido->getIdPedido());
            Transaction::close();

        } catch (\Exception $e) {
            
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pedido/ajax', false);
        }
       
    }

    public function savePedidoLoja($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }


            if((!isset($request['get']['cd'])) || ($request['get']['cd'] <= 0)){
                throw new Exception("Parametro inválido");
            }

            $idEntrega = (int) $request['get']['cd'];

            Transaction::startTransaction('connection');

            if(!array_key_exists('produto', Sessoes::sessionReturnElements())){

                throw new Exception("Não existem produtos no carrinho\n");
                
            }

            $pedido = new Pedido();
            $detalhesPedido = new DetalhesPedido();
            $fornecimento = new Fornecimento();

            $logradouroPessoa = new LogradouroPessoa();

            $resultLogPess = $usuario->logradouro();
            if($resultLogPess == false){
                throw new Exception("Endereço de entrega não definido\n");
                
            }
            //recebe o id do endereço de entrega.

            $logradouro = $logradouroPessoa->findLogPessoa($idEntrega, true);//ajustar o id do logradouro

            $produtos = Sessoes::sessionReturnElements()['produto'];
            $cliente = $usuario->findPessoa($usuario->getIdPessoa());

            for ($i=0; !($i == count($produtos)) ; $i++) { 

                $item = (int)$produtos[$i][0];
                $qtd = $produtos[$i][1];

                $estoque = new Fornecimento();
                $result = $estoque->listarConsultaPersonalizada('F.ativo = 1 and (F.qtdFornecida - F.qtdVendida > 0) and P.idProduto = '.$item, NULL, NULL, true);

                
                $detalhesPedido = new DetalhesPedido();

                $resultQtd       = $detalhesPedido->setQtd($result[0], $qtd);
                $resultVab       = $detalhesPedido->setValBruto($result[0], $result[0]->getVlVenda());
                $resultDesCunit  = $detalhesPedido->setVlDescontoUnit($result[0], 0);
                $resultTotDesc   = $detalhesPedido->setTotalDesconto(0);
                $resultPrecoUnit = $detalhesPedido->setPrecoUnitPratic((float)$result[0]->getVlVenda());
                $resultEstoqueId = $detalhesPedido->setIdEstoque((int)$result[0]->getIdFornecimento());

                //configura o uruario da operação a própria loja virtual
                $resultUsuario   = $detalhesPedido->setUsuarioIdUsuario(1);

                $resultFornec    = $detalhesPedido->setFornecimentoIdFornecimento($result[0]->getIdFornecimento());

                $pedido->addItem($detalhesPedido);
            
            }

            $pedido->setCliente((int)$cliente->getIdPessoa());
            $pedido->setLogradouroIdLogradouro((int)$logradouro[0]->getIdLogradouroPessoa());

            //configura o uruario da operação a própria loja virtual
            $pedido->setUsuarioIdUsuario(1);
            $pedido->setTipo(3); //tipo 3 configura uma venda

            $result = $pedido->save([]);
            if($result){

                //busca os detalhes do pedido gravado
                $pedidoNow = $pedido->getPedidoForId((int)$pedido->maxId());
                $detalhePedido = $pedidoNow->getDetalhesPedido();

                //recupera o total do pedido
                $totPedido = 0;

                for ($i=0; !($i == count($detalhePedido)) ; $i++) { 
                    $totPedido += (float)$detalhePedido[$i]->getPrecoUnitPratic() * $detalhePedido[$i]->getQtd();
                }

                
                $formPgto = new FormPgto();
                $result = $formPgto->findFormPgtoForTipo('CCR');
                $idFormPgto = (int)$result->getIdFormPgto();
                
                $pedidoPgto = new PedidoFormPgto();
                $pedidoPgto->setFormPgtoIdFormPgto($idFormPgto);
                $pedidoPgto->setPedidoIdPedido($pedido->maxId());
                $pedidoPgto->setQtdParcelas(3);//ajustar para a quantidade de parcelas do cliente

                //configura o uruario da operação a própria loja virtual
                $pedidoPgto->setUsuarioIdUsuario(1);

                $pedidoPgto->setVlParcela($totPedido / $pedidoPgto->getQtdParcelas());//ajustar o calculo da parcela correto
               
                $result = $pedidoPgto->save([]);

                if($result){
                    
                    $formPgto = $pedidoNow->getPedidoFormPgto();

                    for ($i=0; !($i == count($formPgto) ); $i++) { 

                        $qtdParcelas = $formPgto[$i]->getQtdParcelas();

                        for ($j=0; !($j == $qtdParcelas) ; $j++) {

                            $dtVenc = new \DateTime();
                            $dtVenc->modify('+'.($j+1).' month');

                            $contPgtoReceb = new ContaPagarReceber();
                            $contPgtoReceb->setPedFormPgtoIdPedFormPgto($formPgto[$i]->getIdPedidoFormPgto());

                            $contPgtoReceb->setPcDescontoJuros(0);
                            $contPgtoReceb->setDescricao('pgto venda lojavirtual');
                            $contPgtoReceb->setTipo('entrada');
                            $contPgtoReceb->setDtVencimento($dtVenc->format('Y-m-d'));

                            //configura o caixa da operação o da própria loja virtual
                            $contPgtoReceb->setCaixaIdCaixa(3);

                            //configura o uruario da operação a própria loja virtual
                            $contPgtoReceb->setUsuarioIdUsuario(1);
                            $result = $contPgtoReceb->save([]);

                            if($result == false){
                                throw new Exception("Erro ao de processamento\n");
                                
                            }
                            
                            Sessoes::rezetCarrinho();

                            $img_response = 'files/imagens/response/response_success.png';
                            $msg = "
                                <div class='container'>
                                    <div class='row'>
                                        <div class='col-sm-12 col-md-12'>
                                            <div class='row'>
                                                <div class='col-sm-12 col-md-12 alert alert-success'>
                                                    <h4>".ucwords($usuario->getNomePessoa()).", seu pagamento foi realizado com sucesso!</h4>
                                                </div>
                                            </div>
                                            <div class='row'>
                                                <div class='col-sm-12 col-md-12' align='center'>
                                                    <img style='width: 50%;height:100%;' src=".$img_response."/>
                                                </div>
                                            </div>
                                            <div class='row'>
                                                <div class='col-sm-12 col-md-12 alert alert-warning mt-3'>
                                                    <p>
                                                        Você receberá um email com algumas informações adicionais.<br/>
                                                        Fique avontade para entrar em contato através dos nossos canais: chate, email, whatsapp.<br/><br/>
                                                        Obs: vefique sua caixa de spam caso não encontre na caixa de entrada.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ";

                            $this->view->result = json_encode(['msg','Success:',$msg]);
                            $this->render('pedido/ajax', false);
                        }

                    }


                }
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

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

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

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }


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


    public function pedidoPagar()
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }


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

            $logradouro = $usuario->logradouro();

            $this->view->logradouro = $logradouro;
            $this->view->allProducts = $allProducts;
            $this->render('/pedido/pagamento', false);

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

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }


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