<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Pedido;
use App\Models\FormPgto;
use App\Models\DetalhesPedido;
use Core\Utilitarios\Utils;
use \Exception;

/**
 * Classe para gerar contas a pagar e receber
 */
class ContaPagarReceber extends BaseModel
{
	const TABLENAME = 'ContaPagarReceber';
	const MAXPARCELA = 3;

    private $data = [];
    private $idContaReceber;
    private $codBarrTitulo;
   	private $dtVencimento;
    private $dtPagamento;
    private $descricao;
    private $pcDescontoJuros;
    private $UsuarioIdUsuario;
    private $CaixaIdCaixa;
    private $tipo;
    private $status;
    private $dtOperacao;
    private $PedFormPgtoIdPedFormPgto ;

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
    	$dadosCommit = $this->parseCommit();
        $dadosCommit['dtOperacao'] = date('Y-m-d H:i:s');
        
    	$result = $this->insert($dadosCommit);

    	if($result == false){
    		throw new Exception("Erro ao registrar elemento\n");
    		
    	}
    	return true;
    }

    public function modify(array $dados)
    {
    	
    }
    
    public function getPedidoFormPgto()
    {
        $pedidoFormPgto = new PedidoFormPgto();
        $result = $pedidoFormPgto->select(
            ['idPedidoFormPgto', 'PedidoIdPedido', 'FormPgtoIdFormPgto', 'qtdParcelas', 'vlParcela', 'dtOperacao']
            , [$this->idPedido =>'PedidoIdPedido']
            , '=', 'asc', null, null, true, false
        );

        if($result == false){
            throw new Exception("Erro ao carregar elemento\n");
            
        }
        return $result;

    }

	public function setIdContaReceber(Int $id):bool
	{
		if((!isset($id)) || ($id <= 0)){
			
			throw new Exception('Parâmetro inválido');
			
		}

		$this->data['idContaReceber'] = $id;
		
		return true;
	}
    public function setPedFormPgtoIdPedFormPgto(Int $id):bool
    {
        if((!isset($id)) || ($id <= 0)){
            
            throw new Exception('Parâmetro inválido');
            
        }

        $this->data['PedFormPgtoIdPedFormPgto'] = $id;
        
        return true;
    }

    public function setCaixaIdCaixa(Int $id):bool
    {
        if((!isset($id)) || ($id <= 0)){
            
            throw new Exception('Parâmetro inválido');
            
        }

        $this->data['CaixaIdCaixa'] = $id;
        
        return true;
    }


    public function setPcDescontoJuros(float $descJur):bool
    {
        if((!isset($descJur)) || ($descJur < 0)){
            
            throw new Exception('Parâmetro inválido');
            
        }

        $this->data['pcDescontoJuros'] = $descJur;
        
        return true;
    }

	public function setCodBarrTitulo(String $cod):bool
	{
		if((!isset($cod)) || (strlen($cod) == 0)){
			
			throw new Exception('Parâmetro inválido');
			
		}

		$this->data['codBarrTitulo'] = $cod;
		
		return true;
	}

    public function setDescricao(String $desc):bool
    {
        if((!isset($desc)) || (strlen($desc) == 0)){
            
            throw new Exception('Parâmetro inválido');
            
        }

        $this->data['descricao'] = $desc;
        
        return true;
    }

    public function setTipo(String $tipo = 'entrada'):bool
    {
        if((!isset($tipo)) || (strlen($tipo) == 0)){
            
            throw new Exception('Parâmetro inválido');
            
        }

        $this->data['tipo'] = $tipo;
        
        return true;
    }

    public function setStatus(String $status = 'aberto'):bool
    {
        if((!isset($status)) || (strlen($status) == 0)){
            
            throw new Exception('Parâmetro inválido');
            
        }

        $this->data['status'] = $tipo;
        
        return true;
    }

    public function setDtVencimento(String $data):bool
	{
		if(isset($data) && (strlen($data) > 0)){

            $result = Utils::validaData($data);
            if($result != false){

                $dtVencimento = new \DateTime($result[0].'-'.$result[1].'-'.$result[2]);

                $date = new \DateTime();
                
                $today = new \DateTime($date->format('Y-m-d'));

                if($dtVencimento >= $today){

                    $this->data['dtVencimento'] = $dtVencimento->format('Y-m-d');

                    return true;
                }
               
                throw new Exception('Data inválida<br/>');
            }
        }

        
        throw new Exception('Data inválida<br/>');
	}

    public function setDtPagamento(String $data):bool
    {
        if(isset($data) && (strlen($data) > 0)){

            $result = Utils::validaData($data);
            if($result != false){

                $dtPagamento = new \DateTime($result[0].'-'.$result[1].'-'.$result[2]);

                $date = new \DateTime();
                
                $today = new \DateTime($date->format('Y-m-d'));

                if($dtPagamento >= $today){

                    $this->data['dtPagamento'] = $dtPagamento->format('Y-m-d');

                    return true;
                }
               
                throw new Exception('Data inválida<br/>');
            }
        }

        
        throw new Exception('Data inválida<br/>');
    }

	public function setUsuarioIdUsuario(Int $id)
	{
		if($id <= 0){
			throw new Exception('Parâmetro inválido');
			
		}

		$this->data['UsuarioIdUsuario'] = $id;
		return true;
	}

    
    public function exercicoAnual()
    {
        $sql = 'SELECT year(dataHoraPedido) ano ,month(dataHoraPedido) mes,(
                    SELECT SUM(D.precoUnitPratic) FROM DetalhesPedido D 
                    WHERE month(D.dataHoraPedido) = month(DT.dataHoraPedido)
                    and year(D.dataHoraPedido) = year(DT.dataHoraPedido)
                ) totMes
                from DetalhesPedido DT
                group by ano, mes';

        $restult = $this->persolizaConsulta($sql);

        return $restult;
    }


}