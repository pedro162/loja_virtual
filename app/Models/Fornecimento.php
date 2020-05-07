<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use App\Models\ProdutoCategoria;
use \Exception;
use \InvalidArgumentException;
use App\Models\Departamento;
use App\Models\Marca;
use App\Models\Categoria;
use App\Models\Fornecedor;

/**
 * 
 */
class Fornecimento extends BaseModel
{	
	private $idFornecimento;

	protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    protected $table = 'idFornecimento';  
    protected $table = 'Produto_idProduto';
    protected $table = 'Fornecedor_idFornecedor';
    protected $table = 'dtFornecimento';
    protected $table = 'dtRecebimento';
    protected $table = 'validade';
    protected $table = 'qtdVendida';
    protected $table = 'vlCompra';
    protected $table = 'vlVenda';
    protected $table = 'ativo'; 
    protected $table = 'idUsuario';            


	protected function clear(array $dados)//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
    {
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        for ($i=0; !($i == count($dados)) ; $i++) { 

            $subArray = explode('=', $dados[$i]);
           
            switch ($subArray[0]) {
                case 'nome':
                   $this->setNomeProduto($subArray[1]);
                   break;
                case 'texto':
                   $this->setTextoPromorcional($subArray[1]);
                   break;

                case 'quantidade':
                   $this->setEstoque($subArray[1]);
                   break;

                case 'marca':
                    $idMarca = (int) $subArray[1];
                   $this->setIdMarca($idMarca);
                   break;

                case 'nf':
                   $this->setNf($subArray[1]);
                   break;

                case 'codigo':
                   $this->setCodigoProduto($subArray[1]);
                   break;

                case 'valorCompra':
                   $this->setPreco($subArray[1]);
                   break;

                case 'margem':
                   $this->setPreco($subArray[1]);
                   break;

                case 'categoria':
                   $this->setPreco($subArray[1]);
                   break;
                case 'fornecedor': // falta criar o metodo ideal
                    $idFornecedor = (int) $subArray[1];
                   $this->setFornecedor($idFornecedor);
                   break;
            }

        }
    }

    protected function parseCommit()
    {	
    	if(!empty($this->idFornecimento)){
    		$this->data['idFornecimento'] => $this->getIdFornecimento();
    	}
		
		$this->data['Produto_idProduto'] 			= $this->getProduto()->getIdProduto();
		$this->data['Fornecedor_idFornecedor']		= 1;
		$this->data['dtFornecimento']				= $this->getDataFornecimento();
		$this->data['dtRecebimento']				= $this->getDataRecebimento();	
		$this->data['validade']						= $this->getValidade();
		$this->data['qtdVendida']					= $this->getQtdVendida();
		$this->data['vlCompra']						= $this->getValCompra();
		$this->data['vlVenda']						= $this->getValVenda();
		$this->data['ativo']						= $this->getAtivo();
		$this->data['idUsuario']					= $this->getQtdVendida();

        return $this->data;
    }


    public function commit(array $dados)
    {

        $this->clear($dados);

        $result = $this->parseCommit();


        //Transaction::startTransaction(self::getDatabase());
        
        $this->insert($result);

        //Transaction::close();
    }


    public function getProduto()
    {
    	$produto = new Produto();

    	$result = $produto->select(['idProduto','nomeProduto'], ['idProduto'=>$this->Produto_idProduto], '=','asc', null, null,true);

    	return $result[0];

    }

    public function getUsuario()
    {
    	$produto = new User();

    	$result = $produto->select(['idProduto','nomeProduto'], ['idProduto'=>$this->Produto_idProduto], '=','asc', null, null,true);

    	return $result[0];

    }


    public function getIdFornecimento()
    {
    	if(!empty($this->idFornecimento)){
    		return $this->idFornecimento;
    	}

    	throw new Exception("Propriedade não definida<br/>\n");
    }


    public function getDataFornecimento()
    {
    	if(!empty($this->dtFornecimento)){

    		return $this->dtFornecimento;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function getDataRecebimento()
    {
    	if(!empty($this->dtRecebimento)){

    		return $this->dtRecebimento;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function getValidade()
    {
    	if(!empty($this->validade)){

    		return $this->validade;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function getQtdVendida()
    {
    	if(!empty($this->qtdVendida)){

    		return $this->qtdVendida;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function getValCompra()
    {
    	
    	if(!empty($this->vlCompra)){

    		return $this->vlCompra;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function getValVenda()
    {
    	
    	if(!empty($this->vlVenda)){

    		return $this->vlVenda;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }

    public function getAtivo()
    {
    	
    	if(!empty($this->ativo)){

    		return $this->ativo;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    
    

}