<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\ProdutoCategoria;
use App\Models\Fornecimento;
use App\Models\Pedido;
use \Exception;
use \InvalidArgumentException;

class DetalhesPedido extends BaseModel
{
    private $data = [];
    
    const TABLENAME = 'DetalhesPedido';
    const PERCENTDESC = 2;
	const MARGEMERROR = 0.005;
    
	private $PedidoIdPedido;
	private $idDetalhesPedido;
	private $qtd;
	private $precoUnitPratic;
	private $dataHoraPedito;
	private $UsuarioIdUsuario;
	private $vlDescontoUnit;
	private $FornecimentoIdFornecimento;
	private $dataHoraPedido;

	private $valBruto;
	private $idEstoque;
	private $totalDesconto;
	private $subTotal;
	


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
        //falta implementar corretamente
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        for ($i=0; !($i == count($dados)) ; $i++) { 

            $subArray = explode('=', $dados[$i]);
           
            switch ($subArray[0]) {
                case '':
                   
                   break;
            }

        }
    }
    public function save(array $dados, $orcamento = false)
    {

        $result = $this->parseCommit();

        $resultInsert = $this->insert($result);

        //se for orçamento, não faz o update na quantidade vendida
        if($orcamento != false){

	        $fornecimento = new Fornecimento();
	        $idForn = $this->getFornecimentoIdFornecimento();

	        $resultFindForn = $fornecimento->findFornecimentoForId((int)$idForn);

	        $saldoEstoque = (int)$resultFindForn->getQtdFornecida() - (int)$resultFindForn->getQtdVendida() ;

	        if($saldoEstoque < (int)$this->getQtd()){
	        	throw new Exception("Estoque insuficiente para o produto \"{$resultFindForn->getProdutoNome()}\"");
	        	
	        }

	        $resultFindForn->modify(['qtdVend='.((int)$resultFindForn->getQtdVendida() + $this->getQtd())]);


        }

        if($resultInsert == true){
            return true;
        }
        throw new Exception("Falha ao salvar o pedido");
        
    }

    public function modify(array $dados)
    {
        
    }

    public function setVlDescontoUnit(Fornecimento $Fornecimento,Float $desc)
	{
		if($desc < 0){
			throw new Exception('Desconto inválido'.PHP_EOL);
		}

		if(($desc == 0) || ($desc == 0.00)){
			$this->data['vlDescontoUnit'] = $desc;
			return true;
		}


		$preco = $Fornecimento->getVlVenda();
		$descontoPermit = ($preco * (self::PERCENTDESC/100));

		if($desc > $descontoPermit){
			
			if(abs($descontoPermit - $desc) <= self::MARGEMERROR){
				$this->data['vlDescontoUnit'] = $desc;
				return true;
			}

			throw new Exception('Desconto inválido/'.$descontoPermit.'/ para valor: '.abs($descontoPermit - $desc).' enviado '.$desc.' -> margem '.self::MARGEMERROR.PHP_EOL);
		}else{

			$this->data['vlDescontoUnit'] = $desc;
			return true;
		}
		

	}

	public function getDataHoraPedido()
	{
		if((!isset($this->dataHoraPedido)) || (strlen($this->dataHoraPedido) == 0)){
			if(isset($this->data['dataHoraPedido']) && (strlen($this->dataHoraPedido))){
				return $this->data['dataHoraPedido'];
			}
			throw new Exception('Propriedade não definida');
			
		}

		return $this->dataHoraPedido;
	}

	public function setIdEstoque(Int $id)
	{
		if($id <=0){
			throw new Exception("Parametro inváldio");
			
		}
		$this->data['idEstoque'] = $id; //Obs falta validar
	}
	


	public function setValBruto(Fornecimento $Fornecimento,Float $valBruto)
	{
		if($valBruto <= 0){
			throw new Exception('Parametro inválido'.PHP_EOL);
		}


		$valBrutoForn = $Fornecimento->getVlVenda();
		$dif = abs($valBrutoForn - $valBruto);

		if($dif <= self::MARGEMERROR){

			$this->data['valBruto'] = $valBruto;
			return true;
			
		}

		throw new Exception("Errror: Val Estoque: {$valBrutoForn} -> Valbruto inform : {$valBruto} Dif: {$dif}.\n");
	}

	public function getValBruto()
	{
		if((!isset($this->valBruto)) || ($this->valBruto <= 0)){
			if(isset($this->data['valBruto']) && ($this->data['valBruto'] > 0)){
				return $this->data['valBruto'];
			}

			throw new Exception("Propriedade não definida\n");
			
		}

		return $this->valBruto;
	}


	public function setQtd(Fornecimento $Fornecimento,Int $qtd)
	{
		if($qtd <= 0){
			throw new Exception('Parametro inválido'.PHP_EOL);
		}


		$estoque = $Fornecimento->getQtdFornecida() - $Fornecimento->getQtdVendida();

		if($qtd <= $estoque){

			$this->data['qtd'] = $qtd;
			return true;
			
		}

		throw new Exception("Errror: qtd Estoque: {$estoque} -> qtd inform : {$qtd}\n");
	}

	public function getQtd()
	{
		if((!isset($this->qtd)) || ($this->qtd <= 0)){
			if(isset($this->data['qtd']) && ($this->data['qtd'] > 0)){
				return $this->data['qtd'];
			}
			
		}

		return $this->qtd;
	}

	public function getVlDescontoUnit()
	{
		if((!isset($this->vlDescontoUnit)) || ($this->vlDescontoUnit < 0)){
			if(isset($this->data['vlDescontoUnit']) && ($this->data['vlDescontoUnit'] >=0)){
				return $this->data['vlDescontoUnit'];
			}

			throw new Exception("Propriedade não definida");
			
		}

		return $this->vlDescontoUnit;

	}


	public function setTotalDesconto(Float $totDesc)
	{
		if($totDesc < 0){
			throw new Exception('Parametro inválido'.PHP_EOL);
		}

		$totDescCalc = $this->getQtd() * $this->getVlDescontoUnit();
		if(abs($totDescCalc - $totDesc) <= self::MARGEMERROR){
			$this->data['totalDesconto'] = $totDesc;

			return true;
		}

		throw new Exception("Errror: totDesc estoque : {$totDescCalc} -> totDesc enviado : {$totDesc}".PHP_EOL);
	}

	public function getTotalDesconto()
	{
		if((!isset($this->totalDesconto)) || ($this->totalDesconto < 0)){
			if(isset($this->data['totalDesconto']) && ($this->data['totalDesconto'] >=0)){
				return $this->data['totalDesconto'];
			}
			throw new Exception("Propriedade não definida");
		}

		return $this->totalDesconto;
	}


	public function setPrecoUnitPratic(Float $preco)
	{

		if($preco <= 0){
			throw new Exception("Parametro inválido\n");
			
		}

		$valBruto = $this->getValBruto();
		$vlDescontoUnit = $this->getVlDescontoUnit();
		$result = $valBruto - $vlDescontoUnit;

		if(abs($result - $preco) <= self::MARGEMERROR){
			$this->data['precoUnitPratic'] = $preco;
			return true;
		}

		throw new Exception("Parametro inválido\n");
		
	}


	public function getPrecoUnitPratic():float
	{
		if((!isset($this->precoUnitPratic)) || ($this->precoUnitPratic <= 0)){
			if(isset($this->data['precoUnitPratic']) && ($this->data['precoUnitPratic'] >0)){
				return $this->data['precoUnitPratic'];
			}
		}

		return $this->precoUnitPratic;
	}


	public function setPedidoIdPedido(Int $idPedido)
	{
		if($idPedido <= 0){
			throw new Exception('Parametro inválido');
			
		}

		$this->data['PedidoIdPedido'] = $idPedido;//falta validar se existe
	}

	public function getPedidoIdPedido()
	{
		if((!isset($this->PedidoIdPedido)) || ($this->PedidoIdPedido <= 0)){
			if(isset($this->PedidoIdPedido) && ($this->PedidoIdPedido > 0)){
				return $this->data['PedidoIdPedido'];
			}
			throw new Exception('Propriedade não definida');
		}
		return $this->PedidoIdPedido;

	}

	public function setSubTotal(Float $value)
	{
		if($value <=0){
			throw new Exception("Parametro inválido\n");
			
		}

		$subTotal =($this->getQtd() * $this->getValBruto()) - $this->getTotalDesconto();

		if(abs($subTotal - $value) <= self::MARGEMERROR){
			$this->data['subTotal'] = $value;
		}

		throw new Exception("Parametro inválido\n");
		
	}

	public function setUsuarioIdUsuario(Int $id)
	{
		if($id > 0){
			$this->data['UsuarioIdUsuario'] = $id;
			return true;
		}

		throw new Exception("Usuario inválido\n");
		
	}


	public function getUsuarioIdUsuario()
	{
		if((!isset($this->UsuarioIdUsuario)) || ($this->UsuarioIdUsuario <= 0)){
			if(isset($this->UsuarioIdUsuario) && ($this->UsuarioIdUsuario > 0)){
				return $this->data['UsuarioIdUsuario'];
			}
			throw new Exception('Propriedade não definida');
		}
		return $this->UsuarioIdUsuario;
	}

	public function setFornecimentoIdFornecimento(Int $id)
	{
		if($id > 0){
			$this->data['FornecimentoIdFornecimento'] = $id;
			return true;
		}

		throw new Exception("Fornecimento inválido\n");
		
	}

	public function getFornecimentoIdFornecimento()
	{
		if((!isset($this->FornecimentoIdFornecimento)) || ($this->FornecimentoIdFornecimento <= 0)){
			if(isset($this->data['FornecimentoIdFornecimento']) && ($this->data['FornecimentoIdFornecimento'] > 0)){
				return $this->data['FornecimentoIdFornecimento'];
			}
		}

		return $this->FornecimentoIdFornecimento;

	}

	public function getData()
	{
		if(count($this->data) > 0){
			return $this->data;
		}
		throw new Exception("Propriedade indefinida\n");
		
	}

	public function gePedido()
	{
		$pedido = new Pedido();

    	$restult = $pedido->select(
    		['idPedido', 'PessoaIdPessoa', 'idUsuario',
    		 'dtPedido', 'dtEnvio', 'dtEntrega', 'via', 'frete',
    		 'nomeDestinatario', 'LogradouroIdLogradouro', 'tipo']
    		, ['idPedido' => $this->PedidoIdPedido]
    		, '=', 'asc', null, null, true, false);

    	if($restult == false){
    		throw new Exception("Erro ao carregar elemento");
    		
    	}
    	return $restult;

	}

	public function getFornecimento()
	{
		$fornecimento = new Fornecimento();

    	$restult = $fornecimento->select(
    		['idFornecimento', 'ProdutoIdProduto', 'nf',
    		 'FornecedorIdFornecedor', 'dtFornecimento', 'dtRecebimento', 'dtValidade', 'dtInsert',
    		 'qtdFornecida', 'qtdVendida', 'vlCompra', 'vlVenda', 'ativo', 'UsuarioIdUsuario']
    		, ['idFornecimento' => $this->FornecimentoIdFornecimento]
    		, '=', 'asc', null, null, true, false);

    	if($restult == false){
    		throw new Exception("Erro ao carregar elemento");
    		
    	}
    	return $restult;
		
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
