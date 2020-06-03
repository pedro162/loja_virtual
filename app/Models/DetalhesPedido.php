<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\ProdutoCategoria;
use App\Models\Fornecimento;
use \Exception;
use \InvalidArgumentException;

class DetalhesPedido extends BaseModel
{
    private $data = [];
    
    const TABLENAME = 'DetalhesPedido';
    const PERCENTDESC = 2;
	const MARGEMERROR = 0.002;
    
	private $PedidoIdPedido;//ok
	private $idDetalhesPedito;
	private $qtd;//ok
	private $precoUnit;//ok
	private $dataHoraPedito;
	private $UsuarioIdUsuario;
	private $vlDescontoUnit;//ok

	private $valBruto;//ok
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
    public function save(array $dados)//falta implementar corretamente
    {

        $result = $this->parseCommit();

        $resultInsert = $this->insert($result);
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

		if($desc == 0){
			$this->data['vlDescontoUnit'] = $desc;
			return true;
		}


		$preco = $Fornecimento->getVlVenda();
		$descontoPermit = ($preco * (self::PERCENTDESC/100));

		if($descontoPermit >= $desc){
			$this->data['vlDescontoUnit'] = $desc;
			return true;
		}
		throw new Exception('Desconto inválido/'.$descontoPermit.'/ para valor: '.abs($descontoPermit - $desc).' enviado '.$desc.PHP_EOL);

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


	public function getPrecoUnitPratic()
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

	public function setFornecimentoIdFornecimento(Int $id)
	{
		if($id > 0){
			$this->data['FornecimentoIdFornecimento'] = $id;
			return true;
		}

		throw new Exception("Fornecimento inválido\n");
		
	}

	public function getData()
	{
		if(count($this->data) > 0){
			return $this->data;
		}
		throw new Exception("Propriedade indefinida\n");
		
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
