<?php

namespace App\Models;


use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use \App\Models\Fornecimento;
use \App\Models\DetalhesPedido;

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
    private $idUsuario = 1;
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

			for ($i=0; !($i == count($this->loatItem()) ); $i++) {

				$this->loatItem()[$i]->setPedidoIdPedido((int)$idPedido);

				$result = $this->loatItem()[$i]->save([]); // salva os itens do pedido
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

	public function desistirVenda()
	{

	}

	public function setUsuario(Int $id)
	{
		if(isset($id) && ($id > 0)){
			$this->data['idUsuario'] = $id;
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


	public function previewPedido(Int $idPedido, $clasRetorno = false)
	{
		$sql = "SELECT P.dtPedido, P.nomeDestinatario, P.idPedido, P.frete, L.endereco, L.complemento, PS.nomePessoa
				FROM Pedido as P inner join LogradouroPessoa as LP
				on LP.LogradouroIdLogradouro = P.LogradouroIdLogradouro
				INNER JOIN Logradouro L on L.idLogradouro = LP.LogradouroIdLogradouro
				INNER join Pessoa as PS on PS.idPessoa = LP.PessoaIdPessoa WHERE P.idPedido = ".$idPedido;


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