<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;

class Fornecedor extends BaseModel
{	
	private $nomeFornecedor;
    private $cnpj;
    private $ie;
    private $idFornecedor

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    private $table = 'Fornecedor';

    public function __construct()
    {
        self::open();
    }
    
    protected function parseCommit()
    {
    	$this->data['nomeFornecedor']       = $this->getNomeProduto();
        $this->data['cnpj']              	= $this->getMarca()->getIdMarca();
        $this->data['ie']                	= $this->getPreco();

        return $this->data;

    }
	protected function clear(array $dados)
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
            }

        }
	}

	public function commit(array $dados)
	{
		$this->clear($dados);

        $result = $this->parseCommit();


        //Transaction::startTransaction(self::getDatabase());
        
        $this->insert($result);

        //Transaction::close();
	}

	public function setNomeFornecedor(String $nome)
	{
		if(isset($nome) && (strlen($nome) >= 4) && (strlen($nome) <= 20)){
			$this->nomeFornecedor = $nome;
			return true;
		}

		throw new Exception("Valor inválido<br>\n");

	}

	public function getIdFornecedor()
	{
		if(isset($this->idFornecedor) && ($this->idFornecedor > 0))
		{
			return $this->idFornecedor;
		}
		throw new Exception("Propriedade não definida<br/>\n");
	}

	
}