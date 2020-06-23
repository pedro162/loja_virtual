<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Pedido;
use App\Models\FormPgto;
use \Exception;

/**
 * Classe para as formas de pagamento
 * relacionada com o pedido
 */
class PedidoFormPgto extends BaseModel
{
	const TABLENAME = 'PedidoFormPgto';
	const MAXPARCELA = 3;

    private $data = [];
    private $idPedidoFormPgto;
    private $PedidoIdPedido;
   	private $FormPgtoIdFormPgto;
    private $qtdParcelas;
    private $vlParcela;
    private $UsuarioIdUsuario;

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

    public function save(array $dados):bool
    {
    	$dadosPgto = $this->parseCommit();
    	$result = $this->insert($dadosPgto);//salva forma de pagamento

    	if($result == false){
    		throw new Exception("Erro ao registrar forma de pagamento\n");
    		
    	}
    	return true;
    }

    public function modify(array $dados)
    {
    	
    }


	public function setQtdParcelas(Int $qtd)
	{
		if((!isset($qtd)) || ($qtd <= 0)){
			
			throw new Exception('Parâmetro inválido');
			
		}

		if(self::MAXPARCELA < $qtd){
			throw new Exception('Total de parcelas acima do permitido '.$qtd);
		}

		$this->data['qtdParcelas'] = $qtd;
		
		return true;
	}



	public function setFormPgtoIdFormPgto(Int $id)
	{
		if((!isset($id)) || ($id <= 0)){
			
			throw new Exception('Parâmetro inválido');
			
		}

		$this->data['FormPgtoIdFormPgto'] = $id;
		
		return true;
	}


    public function setVlParcela(Float $val)
	{
		if((!isset($val)) || ($val <= 0)){
			
			throw new Exception('Parâmetro inválido');
			
		}

		$this->data['vlParcela'] = $val;

		return true;
	}



	public function getVlParcela()
	{
		if((!isset($this->vlParcela)) || ($this->vlParcela < 0)){
			if(isset($this->data['vlParcela']) && ($this->data['vlParcela'] >= 0)){
				return $this->data['vlParcela'];
			}
			throw new Exception('Propriedade não definida');
			
		}

		return $this->vlParcela;
	}


	public function getIdPedidoFormPgto()
	{
		if((!isset($this->idPedidoFormPgto)) || ($this->idPedidoFormPgto <= 0)){
			if(isset($this->data['idPedidoFormPgto']) && ($this->data['idPedidoFormPgto'] > 0)){
				return $this->data['idPedidoFormPgto'];
			}
			throw new Exception('Propriedade não definida');
			
		}

		return $this->idPedidoFormPgto;
	}

	public function setIdPedidoFormPgto(Int $id):bool
	{
		if((!isset($id)) || ($id <= 0)){
			
			throw new Exception('Parâmetro inválido');
		}

		$this->data['idPedidoFormPgto'] = $id;

		return true;

	}

	public function getPedidoIdPedido()
	{
		if((!isset($this->PedidoIdPedido)) || ($this->PedidoIdPedido <= 0)){
			if(isset($this->data['PedidoIdPedido']) && ($this->data['PedidoIdPedido'] > 0)){
				return $this->data['PedidoIdPedido'];
			}
			throw new Exception('Propriedade não definida');
			
		}

		return $this->PedidoIdPedido;
	}

	public function setPedidoIdPedido(Int $id):bool
	{
		if((!isset($id)) || ($id <= 0)){
			
			throw new Exception('Parâmetro inválido');
		}

		$this->data['PedidoIdPedido'] = $id;

		return true;

	}


	public function getFormPgtoIdFormPgto()
	{
		if((!isset($this->FormPgtoIdFormPgto)) || ($this->FormPgtoIdFormPgto <= 0)){
			if(isset($this->data['FormPgtoIdFormPgto']) && ($this->data['FormPgtoIdFormPgto'] > 0)){
				return $this->data['FormPgtoIdFormPgto'];
			}
			throw new Exception('Propriedade não definida');
			
		}

		return $this->FormPgtoIdFormPgto;
	}

	public function getQtdParcelas()
	{
		if((!isset($this->qtdParcelas)) || ($this->qtdParcelas <= 0)){
			if(isset($this->data['qtdParcelas']) && ($this->data['qtdParcelas'] > 0)){
				return $this->data['qtdParcelas'];
			}
			throw new Exception('Propriedade não definida');
			
		}

		return $this->qtdParcelas;
	}


	public function getPedido()
	{
		$pedido = new Pedido();

    	$restult = $pedido->select(
    		['idPedido', 'PessoaIdPessoa', 'idUsuario',
    		 'dtPedido', 'dtEnvio', 'dtEntrega', 'via', 'frete',
    		 'nomeDestinatario', 'LogradouroIdLogradouro', 'tipo']
    		, [$this->PedidoIdPedido =>'idPedido']
    		, '=', 'asc', null, null, true, false);

    	if($restult == false){
    		throw new Exception("Erro ao carregar elemento");
    		
    	}
    	return $restult;
	}


	public function getFormPgto()
	{
		$pedido = new FormPgto();

    	$restult = $pedido->select(
    		['idFormPgto', 'tipo']
    		, [$this->FormPgtoIdFormPgto =>'idFormPgto']
    		, '=', 'asc', null, null, true, false);

    	if($restult == false){
    		throw new Exception("Erro ao carregar elemento");
    		
    	}
    	return $restult;
	}

	public function setUsuarioIdUsuario(Int $id)
	{
		if($id <= 0){
			throw new Exception('Parâmetro inválido');
			
		}

		$this->data['UsuarioIdUsuario'] = $id;
		return true;
	}

}