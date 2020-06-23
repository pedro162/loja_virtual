<?php
namespace App\Models;

use App\Models\BaseModel;
use App\Models\PedidoFormPgto;
use \Exception;

/**
 * Classe para as formas de pagamento
 */
class FormPgto extends BaseModel
{
	const TABLENAME = 'FormPgto';

    private $data = [];
    private $idFormPgto;
    private $tipo;

    protected function parseCommit()
    {

    }

    protected function clear(array $dados)
    {

    }

    public function save(array $dados)
    {

    }

    public function modify(array $dados)
    {
    	
    }

	public function getTipo()
	{
		if((!isset($this->tipo)) || (strlen($this->tipo) == 0)){
			if(isset($this->data['tipo']) && (strlen($this->data['tipo']) == 0)){
				return $this->data['tipo'];
			}
			throw new Exception('Propriedade não definida');
			
		}

		return $this->tipo;
	}

	public function findFormPgtoForTipo(String $tipo)
	{
		if((!isset($tipo)) || (strlen($tipo) == 0)){
			throw new Exception('Parâmetro inválido');
			
		}

		$result = $this->select(['tipo', 'idFormPgto'], ['tipo' => $tipo], '=', 'asc', null, null, true, false);
		if($result == false){
			throw new Exception("Erro ao carregar elemento\n");
			
		}
		return $result[0];
	}


	public function getIdFormPgto()
	{
		if((!isset($this->idFormPgto)) || ($this->idFormPgto <= 0)){
			if(isset($this->data['idFormPgto']) && (strlen($this->data['idFormPgto']) == 0)){
				return $this->data['idFormPgto'];
			}
			throw new Exception('Propriedade não definida');
			
		}

		return $this->idFormPgto;
	}


	public function getPedidoFormPgto()
	{
		$PedidoFormPgto = new PedidoFormPgto();
		$result = $PedidoFormPgto->select(
			['idPedidoFormPgto', 'PedidoIdPedido', 'FormPgtoIdFormPgto', 'qtdParcelas', 'vlParcela', 'dtOperacao']
			, [(int)$this->idFormPgto =>'FormPgtoIdFormPgto']
    		, '=', 'asc', null, null, true, false
		);

		if($result == false){
    		throw new Exception("Erro ao carregar elemento");
    		
    	}
    	return $result;

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