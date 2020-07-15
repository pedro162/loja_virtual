<?php

namespace App\Models;


use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use \App\Models\Fornecimento;
use \App\Models\DetalhesPedido;
use \App\Models\PedidoFormPgto;
use \App\Models\Pessoa;

/**
 * 
 */
class Pedido extends BaseModel
{
    const TABLENAME = 'Pedido';

    private $data = [];
    private $carrinho;

    private $qtdParcelas;
    private $PessoaIdPessoa;
    private $UsuarioIdUsuario;
    private $dtPedido;
    private $dtEnvio;
    private $dtEntrega;
    private $via;
    private $frete;
    private $LogradouroIdLogradouro;
    private $idPedido;

    private $nomeDestinatario;
   	private $endereco;
    private $complemento;
    private $nomePessoa;
    private $tipo;


    public function __construct()
    {
        $this->carrinho = [];
    }

	protected function parseCommit()
	{
		$arrayPase = [];
        for ($i=0; !($i == count($this->columns())) ; $i++) { 
            $chave = $this->columns()[$i]->Field;
            if(array_key_exists($chave, $this->data)){
                $arrayPase[$chave] = $this->data[$chave];
            }
        }
        return $arrayPase;
	}
	protected function clear(array $dados)
	{

	}
	public function save(array $dados)
	{
		$result = $this->parseCommit(); //retorna os dados já filtrados

		$resultInsertPedido = $this->insert($result);//salva o pedito

		if($resultInsertPedido != false){
			
			$idPedido = $this->maxId();

			$ajustarEstoque = false;
			if($this->getTipo() != 'orcamento'){
				$ajustarEstoque  = true;
			}

			for ($i=0; !($i == count($this->loatItem()) ); $i++) {

				$this->loatItem()[$i]->setPedidoIdPedido((int)$idPedido);

				$result = $this->loatItem()[$i]->save([], $ajustarEstoque); // salva os itens do pedido e ajusta o estoque
				if($result == false){
					throw new Exception("Erro ao salvar o pedido\n");
				}
				
			}
			return ['msg','success','Pedido gerado com sucesso!'];
		}
		throw new Exception("Erro ao salvar o pedido\n");
		
	}

	public function modify(array $dados)
    {
        
    }

	public static function qtdItensVenda()
	{
		Sessoes::sessionInit();

	
	}

	public function addItem(DetalhesPedido $newItem)
    {
        $this->carrinho[] = $newItem;
    }

    public function loatItem()
    {
        if(isset($this->carrinho) && (count($this->carrinho) > 0)){
            return $this->carrinho;
        }

        throw new Exception("Propriedade não definida\n");
        
    }

	public function setTipo(Int $tipo)
	{
		if((!isset($tipo)) || ($tipo < 1) || ($tipo > 3)){
			throw new Exception("Parâmetro inválido\n");
			
		}

		if($tipo == 1){
			$this->data['tipo'] = 'orcamento';
		}else if($tipo == 2){
			$this->data['tipo'] = 'prevenda';
		}else if($tipo == 3){
			$this->data['tipo'] = 'venda';
		}
	}

	public function getTipo()
	{
		if((! isset($this->tipo)) || (strlen($this->tipo) == 0)){
			if(isset($this->data['tipo']) && (strlen($this->data['tipo']))){
				return $this->data['tipo'];
			}

			throw new Exception("Propriedade não definida\n");
		}

		return $this->tipo;
	}

	public function getDtPedido()
	{
		if((! isset($this->dtPedido)) || (strlen($this->dtPedido) == 0)){
			if(isset($this->data['dtPedido']) && (strlen($this->data['dtPedido']))){
				return $this->data['dtPedido'];
			}

			throw new Exception("Propriedade não definida\n");
		}

		return $this->dtPedido;
		
	}

	public function setUsuarioIdUsuario(Int $id)
	{
		if(isset($id) && ($id > 0)){
			$this->data['UsuarioIdUsuario'] = $id;
			return true;
		}
	}

	public function setCliente(Int $id)
	{
		if(isset($id) && ($id > 0)){
			$this->data['PessoaIdPessoa'] = $id;
			return true;
		}

	}

	public function setQtdParcelas(Int $qtdParcelas)
	{
		if(isset($qtdParcelas) && ($qtdParcelas > 0)){
			$this->data['qtdParcelas'] = $qtdParcelas;
			return true;
		}

	}

	public function setLogradouroIdLogradouro(Int $id)
	{
		if($id > 0){
			$this->data['LogradouroIdLogradouro'] = $id;
			return true;
		}
		throw new Exception("Parâmetro inválido $id \n");
		
	}

	public function getPedidoForId(Int $idPedido)
	{
		if($idPedido <= 0){

			throw new Exception("Parãmetro inválido\n");
    		
		}

		$restult = $this->select(
    		['idPedido', 'PessoaIdPessoa', 'UsuarioIdUsuario',
    		 'dtPedido', 'dtEnvio', 'dtEntrega', 'via', 'frete',
    		 'nomeDestinatario', 'LogradouroIdLogradouro', 'tipo']
    		, ['idPedido' =>$idPedido]
    		, '=', 'asc', null, null, true, false);

    	if($restult == false){
    		throw new Exception("Erro ao carregar elemento");
    		
    	}
    	return $restult[0];

	}


	public function getPedidoFormPgto()
	{
		$pedidoFormPgto = new PedidoFormPgto();
		$result = $pedidoFormPgto->select(
			['idPedidoFormPgto', 'PedidoIdPedido', 'FormPgtoIdFormPgto', 'qtdParcelas', 'vlParcela', 'dtOperacao']
			, ['PedidoIdPedido' =>$this->idPedido]
    		, '=', 'asc', null, null, true, false
		);

		if($result == false){
    		throw new Exception("Erro ao carregar elemento\n");
    		
    	}
    	return $result;

	}

	public function getDetalhesPedido()
	{
		$detalhesPedido = new DetalhesPedido();
		$result = $detalhesPedido->select(
			['idDetalhesPedido', 'PedidoIdPedido', 'qtd','precoUnitPratic','dataHoraPedido',
			'vlDescontoUnit', 'FornecimentoIdFornecimento', 'UsuarioIdUsuario']
			, ['PedidoIdPedido' => (int)$this->idPedido]
    		, '=', 'asc', null, null, true, false
		);

		if($result == false){
    		throw new Exception("Erro ao carregar elemento");
    		
    	}
    	return $result;

	}

	public function getPessoa()
	{
		$pessoa = new Pessoa();
		$result = $pessoa->select([
			 'idPessoa', 'nomePessoa', 'nomeFantasia', 'documento', 'documentoComplementar'
			],['idPessoa' => $this->PessoaIdPessoa], '=', 'asc', null, null, true, false
		);

		if($result == false){
			throw new Exception('Erro ao carregar elemento');
			
		}

		return $result;
	}


	public function getIdPedido()
	{
		if((!isset($this->idPedido)) || ($this->idPedido <= 0)){
			if(isset($this->data['idPedido']) && ($this->data['idPedido'] > 0)){
				return $this->data['idPedido'];
			}

			throw new Exception("Propriedade não definida\n");
		}

		return $this->idPedido;
	}

	public function infoPedidoAll(Array $where = [], String $tipo = 'venda', Int $init = 0, Int $end = 10)
    {
        $sql = "SELECT P.idPedido, P.dtPedido, P.frete, P.tipo, (
            SELECT sum(D.precoUnitPratic * D.qtd) total from DetalhesPedido D 
            where D.PedidoIdPedido = P.idPedido
        )as total,
            (
                SELECT sum(D.vlDescontoUnit * D.qtd) total from DetalhesPedido D 
                where D.PedidoIdPedido = P.idPedido
            ) as totalDesconto,
            (
                SELECT nomePessoa FROM Pessoa WHERE idPessoa = U.PessoaIdPessoa 
            )as vendedor,
            (
                SELECT nomePessoa FROM Pessoa WHERE idPessoa = P.PessoaIdPessoa 
            )as cliente
        from Pedido P 

        INNER join DetalhesPedido D on D.PedidoIdPedido = P.idPedido
        INNER JOIN Usuario U on U.idUsuario = P.UsuarioIdUsuario
        WHERE P.tipo = '".$tipo."' ";

        if((isset($where)) && (count($where) > 0)){
            for ($i=0; !($i == count($where)); $i++) { 
                if(is_array($where[$i]) && (count($where[$i]) > 0)){
                    if(isset($where[$i]['key']) && isset($where[$i]['val']) && isset($where[$i]['operator']) && isset($where[$i]['comparator'])){
                        $key        = trim($where[$i]['key']);
                        $val        = trim($where[$i]['val']);
                        $comparator   = trim($where[$i]['comparator']);
                        $operator   = trim($where[$i]['operator']);

                        if(!is_numeric($val)){
                            $val = $this->satinizar($val);
                        }

                        $sql .= ' '.$operator.' '.$key.' '.$comparator.' '.$val;
                    }
                }
            }
            
        }

        $sql .= ' GROUP BY P.idPedido ORDER BY P.dtPedido DESC LIMIT '.$init.','.$end;
        
        $result = $this->persolizaConsulta($sql);

        return $result;
    }

	public function previewPedido(Int $idPedido, $clasRetorno = false)
	{
		$sql = "SELECT P.dtPedido, P.nomeDestinatario,P.tipo, P.idPedido, P.frete, L.endereco, L.complemento, PS.nomePessoa
				FROM Pedido as P inner join LogradouroPessoa as LP
				on LP.LogradouroIdLogradouro = P.LogradouroIdLogradouro
				INNER JOIN Logradouro L on L.idLogradouro = LP.LogradouroIdLogradouro
				INNER join Pessoa as PS on PS.idPessoa = P.PessoaIdPessoa WHERE P.idPedido = ".$idPedido;


		$result = $this->persolizaConsulta($sql, $clasRetorno);
	    if($result != false){
	        return $result;
	    }
	     throw new Exception("Erro ao carregar pedido.\n");


	}

	public function getItensPedido(Int $idPedido, $clasRetorno = false)
	{
		$sql = "SELECT P.nomeProduto, P.idProduto, DT.qtd, DT.precoUnitPratic, DT.vlDescontoUnit, DT.dataHoraPedido, DT.FornecimentoIdFornecimento
				FROM DetalhesPedido as DT INNER JOIN Fornecimento as F on F.idFornecimento = DT.FornecimentoIdFornecimento
				INNER JOIN Produto as P on P.idProduto = F.ProdutoIdProduto
				INNER join Pedido as PD on PD.idPedido = DT.PedidoIdPedido
				WHERE PD.idPedido = ".$idPedido;

		$result = $this->persolizaConsulta($sql, $clasRetorno);
	    if($result != false){
	        return $result;
	    }
	     throw new Exception("Erro ao carregar itens.\n");

	}
	
	public function getItensPedidoInListArr(aray $idPedido, $clasRetorno = false)
	{
		if(count($idPedido) == 0){
			throw new Exception("Parãmetro inválido\n");
			
		}

		for ($i=0; !($i == count($in)) ; $i++) { 

			if(!is_integer($in[$i])){
				throw new Exception("Parãmetro com valor inválido\n");
				
			}
		}

		$in = implode(',', $idProduto);

		$sql = "SELECT P.nomeProduto, P.idProduto, DT.qtd, DT.precoUnitPratic, DT.vlDescontoUnit, DT.dataHoraPedido, DT.FornecimentoIdFornecimento
				FROM DetalhesPedido as DT INNER JOIN Fornecimento as F on F.idFornecimento = DT.FornecimentoIdFornecimento
				INNER JOIN Produto as P on P.idProduto = F.ProdutoIdProduto
				INNER join Pedido as PD on PD.idPedido = DT.PedidoIdPedido
				WHERE PD.idPedido in ({$in})";

		$result = $this->persolizaConsulta($sql, $clasRetorno);
	    if($result != false){
	        return $result;
	    }
	     throw new Exception("Erro ao carregar itens.\n");

	}
	

	public function __get($prop)
    {
        if(method_exists($this, 'get'.ucfirst($prop))){

            return call_user_func([$this,'get'.ucfirst($prop)]);
        }
    }

    public function __set($prop, $value)
    {   
        if(method_exists($this, 'set'.ucfirst($prop))){ 
            return call_user_func([$this,'set'.ucfirst($prop)], $value);
        }
    }


    


}