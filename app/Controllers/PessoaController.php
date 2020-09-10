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
use \App\Models\Cartao;
use \App\Models\CartaoComprador;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;
use \Exception;

class PessoaController extends BaseController
{
    
	public function pedidos($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

    		if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

    			throw new Exception('Parâmetro inválido');
    			
    		}

    		Transaction::startTransaction('connection');

    		$pessoa = new Pessoa();

    		$idPessoa = (int) $request['get']['cliente'];
    		$resultPessoa = $pessoa->findPessoa($idPessoa);

    		//busca todos os pedidos com status de venda
    		$this->view->pedidos = $resultPessoa->infoPedidoComplete();
    		$this->view->pessoa = $resultPessoa;
    		$this->render('pessoa/pedido/index', false);
    		Transaction::close();
    		
    	}catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function prevendas($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

                throw new Exception('Parâmetro inválido');
                
            }

            //abre a conexao com o banco de dados
    		Transaction::startTransaction('connection');

            $pessoa = new Pessoa();

            $idPessoa = (int) $request['get']['cliente'];
            $resultPessoa = $pessoa->findPessoa($idPessoa);

            //busca todos os pedidos com status de venda
            $tipo = 'prevenda';
            $this->view->tipo = $tipo;
            $this->view->pedidos = $resultPessoa->infoPedidoComplete([], $tipo);
            $this->view->pessoa = $resultPessoa;
            $this->render('pessoa/pedido/index', false);

            //fax o commit e fecha a conexao com o banco
    		Transaction::close();
    		
    	}catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }
	public function orcamentos($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

                throw new Exception('Parâmetro inválido');
                
            }

            //abre a conexao com o banco de dados
            Transaction::startTransaction('connection');

            $pessoa = new Pessoa();

            $idPessoa = (int) $request['get']['cliente'];
            $resultPessoa = $pessoa->findPessoa($idPessoa);

            //busca todos os pedidos com status de venda
            $tipo = 'orcamento';
            $this->view->tipo = $tipo;
            $this->view->pedidos = $resultPessoa->infoPedidoComplete([], $tipo);
            $this->view->pessoa = $resultPessoa;
            $this->render('pessoa/pedidos', false);

            //fax o commit e fecha a conexao com o banco
            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function cadastro($riquest)
    {
    	try {
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

    		Transaction::startTransaction('connection');

            if(! isset($riquest['post'])){
                throw new Exception("Requisição inválida\n");
                
            }

            $pessoa = new Pessoa();

            $this->view->pessoa = $pessoa->findPessoa($usuario->getIdPessoa());
            $this->render('pessoa/cadastro/index', false);

    		Transaction::close();
    		
    	} catch (\PDOException $e) {

            Transaction::rollback();

        }catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function endereco($riquest)
    {
        try {
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');

            if(! isset($riquest['post'])){
                throw new Exception("Requisição inválida\n");
                
            }

            $this->view->pessoa = $usuario;
            $this->render('pessoa/endereco/index', false);
            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function compras($request)
    {
    	try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }


    		Transaction::startTransaction('connection');

            if(! isset($request['post'])){
                throw new Exception("Requisição inválida\n");
                
            }

            
            $pagina = 1;
            $itensPorPagina = 10;

            if(isset($request['post']['pagina'])){
                $pagina = (int) $request['post']['pagina'];
            }

            $filtroOptionSelected = 0;
            $search = null;

            if(isset($request['post']['filtro'])){
                $filtroOptionSelected = (int)$request['post']['filtro'];

                switch ((int)$request['post']['filtro']) {
                    case 1:
                        $search = 'cancelado';
                        break;
                    
                    case 2:
                        $search = 'andamento';
                        break;
                    case 3:
                        $search = 'entregue';
                        break;
                }
            }

            $pedido = new Pedido();

            $inicio = ($itensPorPagina * $pagina) - $itensPorPagina;

            if($search != null){
                $totItens = $pedido->countItens('PessoaIdPessoa = '.$usuario->getIdPessoa().' and tipo = \'venda\' and status = \''.$search.'\'');

                $this->view->pedidos = $usuario->infoPedidoComplete(
                    [['key'=>'status','val'=>$search,'comparator'=> '=','operator'=> 'and']],
                    'venda', $inicio, $itensPorPagina
                );

            }else if((isset($request['post']['pedido']) )&& ($request['post']['pedido'] > 0)){

                $totItens = $pedido->countItens('PessoaIdPessoa = '.$usuario->getIdPessoa().' and idPedido = '.(int) $request['post']['pedido']);

                $this->view->pedidos = $usuario->infoPedidoComplete([
                    [
                        'key'=>'P.idPedido', 'val'=>(int) $request['post']['pedido'],
                        'comparator' => '=',
                        'operator' => 'and'
                    ]
                ],'venda', $inicio, $itensPorPagina);

                $this->view->pedido = (int) $request['post']['pedido'];

            }else{
                $totItens = $pedido->countItens('PessoaIdPessoa = '.$usuario->getIdPessoa().' and tipo = \'venda\'');

                $this->view->pedidos = $usuario->infoPedidoComplete([],'venda', $inicio, $itensPorPagina);
            }

            
            $this->view->pessoa = $usuario;
            $this->view->pagina = $pagina;
            $this->view->itensPorPagina = $itensPorPagina;
            $this->view->totPaginas = ceil($totItens / $itensPorPagina);
            $this->view->filtro = $filtroOptionSelected;
            $this->render('pessoa/pedido/index', false);

    		Transaction::close();
    		
    	}catch (\PDOException $e) {
            $erro = ['msg','warning', 'Algo errado aconteceu na requisição'];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);

        } catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function pagamento($riquest)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');

            if(! isset($riquest['post'])){
                throw new Exception("Requisição inválida\n");
                
            }

            $registros = $usuario->getCartaoComprador();

            $cartoes = [];

            for ($i=0; !($i == count($registros) ); $i++) { 
                $cartoes[] = $registros[$i]->getCartao()[0];
            }

            //busca todos os pedidos com status de venda
            $this->view->registros = $registros;
            $this->view->pessoa = $usuario;

            $this->render('pessoa/pagamento/index', false);

            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
            Transaction::rollback();

            $this->view->result = '<h4>'.$e->getMessage().'</h4><p><a href="/cartao/cadastrar" class="btn btn-md btn-warning add-card-cred">Adicionar cartão</a></p>';
            $this->render('pessoa/ajax', false);
        }
    }


    public function cadastrar()
    {
        $this->render('pessoa/cadastro/cadastrar', false);
    }

    public function salvar($request)
    {
        try {

            if((!isset($request['post'])) || (empty($request['post']))){

                throw new Exception('Parâmetro inválido');
                
            }

            $dados = $request['post'];

            Transaction::startTransaction('connection');
            $pessoa = new Pessoa();
            $pessoa->setNomePessoa($dados['nome']);
            $pessoa->setLogin($dados['login']);
            $pessoa->setDocumento(Utils::clearMask($dados['documento']));
            $pessoa->setDocumentoComplementar(Utils::clearMask($dados['documento_complementar']));
            $pessoa->setNomeComplementar($dados['nome_complementar']);
            $pessoa->setSenha($dados['senha']);

            if(isset($dados['img']) && (strlen($dados['img']) > 0)){
                $pessoa->setImg($dados['img']);
            }else{
                $pessoa->setImg('avatar.png');
            }

            if(isset($dados['grupo']) && (strlen($dados['grupo']) > 0)){
                $pessoa->setGrupo($dados['grupo']);
            }else{
                $pessoa->setGrupo('Cliente');
            }

            if(isset($dados['tipo']) && (strlen($dados['tipo']) > 0)){
                $pessoa->setTipo($dados['tipo']);
            }else{
                $pessoa->setTipo('F');
            }

            if(isset($dados['sexo']) && (strlen($dados['sexo']) > 0)){
                $pessoa->setSexo($dados['sexo']);
            }else{
                $pessoa->setSexo('N');
            }

            $pessoa->save([]);

            $this->view->result = json_encode(['msg', 'success', '<h3>Cadastro efetuado com sucesso</h3> <p> Obs: confirme seu emil através do código enviado.</p>
                <p><a href= "#" class="btn btn-sm btn-primary" >Validar codigo</a> <a href= "/" class="btn btn-sm btn-secondary" >Voltar</a></p>
             ']);
            $this->render('pessoa/ajax', false);

            Transaction::close();
            
        }catch (\PDOException $e) {
            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

    public function editar($riquest)
    {   
        try {
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');

            if(! isset($riquest['post'])){
                throw new Exception("Requisição inválida\n");
                
            }

            $pessoa = new Pessoa();

            $registros = $pessoa->findPessoa($usuario->getIdPessoa());

            $this->view->registros = $registros;
            $this->render('pessoa/cadastro/editar', false);

            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }

        
    }


    public function atualizar($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }


            Transaction::startTransaction('connection');

             if((!isset($request['post'])) || (empty($request['post']))){

                throw new Exception('Parâmetro inválido');
                
            }


            $dados = $request['post'];
            
            $pessoa = new Pessoa();
            $resultPessoa = $pessoa->findPessoa($usuario->getIdPessoa());

            $resultPessoa->setNomePessoa($dados['nome']);
            $resultPessoa->setLogin($dados['login']);
            $resultPessoa->setDocumento(Utils::clearMask($dados['documento']));
            $resultPessoa->setDocumentoComplementar(Utils::clearMask($dados['documento_complementar']));
            $resultPessoa->setNomeComplementar($dados['nome_complementar']);

            if(isset($dados['senha']) && (strlen($dados['senha']) > 0)){
               $resultPessoa->setSenha($dados['senha']);
            }

            if(isset($dados['img']) && (strlen($dados['img']) > 0)){
                $resultPessoa->setImg($dados['img']);
            }else{
                $resultPessoa->setImg('avatar.png');
            }

            if(isset($dados['grupo'])){
                $resultPessoa->setGrupo($dados['grupo']);
            }else{
                $resultPessoa->setGrupo('Cliente');
            }

            if(isset($dados['tipo'])){
                $resultPessoa->setTipo($dados['tipo']);
            }else{
                $resultPessoa->setTipo('F');
            }

            if(isset($dados['sexo'])){
                $resultPessoa->setSexo($dados['sexo']);
            }else{
                $resultPessoa->setSexo('N');
            }

            $result = $resultPessoa->modify([]);

            if($result == true){
                $this->view->result = json_encode(['msg', 'success', '<h3>Cadastro atualizado com sucesso</h3> ']);
            }else{
                $this->view->result = json_encode(['msg', 'warning', '<h3>Nenhuma modificação foi efetuada!</h3> ']);
            }

            $this->render('pessoa/ajax', false);

            Transaction::close();
            
        }catch (\PDOException $e) {

            Transaction::rollback();

        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function nfs()
    {
    	try {

    		Transaction::startTransaction('connection');

    		Transaction::close();
    		
    	}catch (\PDOException $e) {

            Transaction::rollback();

        } catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage().' - '.$e->getLine()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    

}