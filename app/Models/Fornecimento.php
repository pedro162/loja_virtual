<?php

namespace App\Models;

use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
use App\Models\Produto;
use App\Models\Usuario;
use \Core\Utilitarios\Utils;

/**
 * 
 */
class Fornecimento extends BaseModel
{	
	private $idFornecimento;
    private $Produto;
    private $idFornecedor;
    private $dtFornecimento;
    private $dtRecebimento;
    private $dtValidade;
    private $qtdFornecida;
    private $qtdVendida;
    private $vlCompra;
    private $vlVenda;
    private $ativo;
    private $quantidade;
    private $Usuario; 
    private $nf;

    protected $data = []; //armazena chaves e valores filtrados por setters  para pessistencia no banco

    protected $table = 'Fornecimento';

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
                case 'produto':

                    $this->setProduto($subArray[1]);
                    break;

                case 'fornecedor':

                    //$this->setIdFornecedor($subArray[1]);
                    break;

                case 'dtValidade':

                    $this->setDataValidade($subArray[1]);
                    break;

                case 'dtFornecimento':

                    $this->setDataFornecimento($subArray[1]);
                    break;

                case 'dtRecebimento':

                    $this->setDataRecebimento($subArray[1]);
                    break;

                case 'qtd':

                    $this->setQtdFornecida($subArray[1]);
                    break;

                case 'vlCompra':

                    $this->setValCompra($subArray[1]);
                    break;

                case 'vlVenda':

                    $this->setValVenda($subArray[1]);
                    break;

                case 'margem':

                    $this->setMargem($subArray[1]);
                    break;

                case 'fornecimento': // falta criar o metodo ideal
                    $this->setIdFornecimento($subArray[1]);
                    break;

                case 'nf': // falta criar o metodo ideal
                    $this->setNotaFiscal($subArray[1]);
                    break;
            }

        }
    }

    protected function parseCommit()
    {	
    	if(!empty($this->idFornecimento)){
    		$this->data['idFornecimento'] = $this->getIdFornecimento();
    	}

        $dtRece = new \DateTime($this->getDataRecebimento());
        $dtForne = new \DateTime($this->getDataFornecimento());
		
        if($dtRece < $dtForne){
            throw new Exception('Falha ao no cadastro de fornecimento!<br/>');
        }

		$this->data['ProdutoIdProduto'] 			= $this->getProduto()->getIdProduto();
		$this->data['FornecedorIdFornecedor']		=  1; //apenas para teste
		$this->data['dtFornecimento']				= $this->getDataFornecimento();
		$this->data['dtRecebimento']				= $this->getDataRecebimento();	
		$this->data['dtValidade']					= $this->getDataValidade();
		$this->data['qtdFornecida']					= $this->getQtdFornecida();
		$this->data['vlCompra']						= $this->getValCompra();
		$this->data['vlVenda']						= $this->getValVenda();
        $this->data['nf']                           = $this->getNotaFiscal();
		$this->data['ativo']						= '1';
		$this->data['idUsuario']					= '1';

        return $this->data;
    }


    public function save(array $dados)
    {

        $this->clear($dados);

        $resultParse = $this->parseCommit();
        
        $result = $this->insert($resultParse);

        if($result == true){
            return ['msg', 'success', 'Estoque lançado com sucesso'];
        }
        return ['msg',' warning ','Produto cadastrado com sucesso!'];
    }


    public function modify(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $resultUpdate = $this->update($result, $this->getIdFornecimento());

        if($resultUpdate == false){

            return ['msg','warning','Estoque não pôde ser atualizado!'];
        }

        return ['msg','success','Estoque atualizado com sucesso!'];
    }

    public function setProduto(Int $id):bool
    {
        if($id >0){

            $produto = new Produto();

            $result = $produto->select(['idProduto','nomeProduto'], ['idProduto'=>$id], '=','asc', null, null,true);
            if($result[0] != false){
                $this->Produto = $result[0];
                return true;
            }else{
                throw new Exception('Parametro invalido<br/>'.PHP_EOL);
            }
        }

        throw new Exception('Parametro invalido<br/>'.PHP_EOL);
    }

    public function getProduto():Produto
    {
        if(isset($this->Produto) && (!empty($this->Produto))){
            return $this->Produto;
        }
        throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
    }

    public function setUsuario(int $id):bool
    {
        if($id > 0){

            $user = new User();

            $result = $user->select(['idUsuario','nomeUsuario'], ['idUsuario'=>$id], '=','asc', null, null,true);
            if($result[0] != false){
                $this->Usuario = $result[0];
                return true;
            }else{
                throw new Exception('Parametro invalido<br/>'.PHP_EOL);
            }
        }

        throw new Exception('Parametro invalido<br/>'.PHP_EOL);
    }

    public function getUsuario():int
    {
        if(isset($this->Usuario) && (!empty($this->Usuario))){
            return $this->Usuario;
        }
        throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
    }

    public function setIdFornecedor(int $id):bool
    {
        if($id > 0){

            $fornecedor = new Fornecedor();

            $result = $fornecedor->select(['idFornecedor','nomeFornecedor'], ['idFornecedor'=>$id], '=','asc', null, null,true);
            if($result[0] != false){
                $this->idFornecedor = $result[0]->getIdFornecedor();
                return true;
            }else{
                throw new Exception('Parametro invalido<br/>'.PHP_EOL);
            }
        }

        throw new Exception('Parametro invalido<br/>'.PHP_EOL);
    }

    public function getIdFornecedor():int
    {
        if(isset($this->idFornecedor) && (!empty($this->idFornecedor))){
            return $this->idFornecedor;
        }
        throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
    }


    public function setNotaFiscal(String $nota)
    {
        if(isset($nota) && (strlen($nota) >=4) && (strlen($nota) <= 40)){
            $this->nf = $nota;
            return true;
        }
        throw new Exception('Parãmetro inválido<br/>'.PHP_EOL);
    }


    public function getNotaFiscal()
    {
        if(isset($this->nf) && (!empty($this->nf))){

            return $this->nf;
        }

        throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
    }


    public function getIdFornecimento():int
    {
        if(isset($this->idFornecimento) && (!empty($this->idFornecimento))){
            return $this->idFornecimento;
        }
        throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
    }

    public function setIdFornecimento(Int $id):bool
    {
        if(isset($id) && ($id > 0)){
            $fornecimento = $this->select(['idFornecimento'], ['idFornecimento'=>$id], '=','asc', null, null,true);
            if($fornecimento != false){
                $this->idFornecimento = $id;
                return true;
            }else{
                throw new Exception('Propriedade inválida<br/>'.PHP_EOL);
            }
        }
        throw new Exception('Propriedade não definida<br/>'.PHP_EOL);
    }

    public function getDataFornecimento()
    {
    	if(!empty($this->dtFornecimento)){

    		return $this->dtFornecimento;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function setDataFornecimento(String $data)
    {
        if(isset($data) && (strlen($data) > 0)){

            $result = Utils::validaData($data);
            if($result != false){

                $fornecimento = new \DateTime($result[0].'-'.$result[1].'-'.$result[2]);

                $date = new \DateTime();
                
                $today = new \DateTime($date->format('Y-m-d'));

                if($fornecimento <= $today){

                    $this->dtFornecimento = $fornecimento->format('Y-m-d H:i:s');

                    return true;
                }
               
                throw new Exception('Data inválida<br/>');
            }
        }

        
        throw new Exception('Data inválida<br/>');
    }


    public function getDataRecebimento()
    {
    	if(!empty($this->dtRecebimento)){

    		return $this->dtRecebimento;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }

    public function setDataRecebimento(string $data)
    {
        if(isset($data) && (strlen($data) > 0)){

            $result = Utils::validaData($data);
            if($result != false){

                $receb = new \DateTime($result[0].'-'.$result[1].'-'.$result[2]);

                $date = new \DateTime();
                
                $today = new \DateTime($date->format('Y-m-d'));

                if($receb <= $today){
                    $this->dtRecebimento = $receb->format('Y-m-d H:i:s');
                    return true;
                }
               
                throw new Exception('Data inválida<br/>');
            }
        }

        
        throw new Exception('Parametro inváldio<br/>');
    }


    public function getDataValidade()
    {
    	if(!empty($this->dtValidade)){

    		return $this->dtValidade;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function setDataValidade(String $data)
    {
        if(isset($data) && (strlen($data) > 0)){

            $result = Utils::validaData($data);
            if($result != false){

                $validade = new \DateTime($result[0].'-'.$result[1].'-'.$result[2]);

                $date = new \DateTime();
                
                $today = new \DateTime($date->format('Y-m-d'));

                if($validade >= $today){

                    $this->dtValidade = $validade->format('Y-m-d H:i:s');

                    return true;
                }
               
               throw new Exception('Data inválida<br/>');
            }
        }

        
        throw new Exception('Parãmetro inválido<br/>');
    }

    public function getQtdVendida():int
    {
        if(isset($this->qtdVendida) && ($this->qtdVendida >=0)){

            return $this->qtdVendida;
        }

        throw new Exception('Propriedade não definida<br/>');
    }

    public function getQtdFornecida():int
    {

        if(!empty($this->qtdFornecida)){

            return $this->qtdFornecida;
        }

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function setQtdFornecida(Int $qtd)
    {
        if($qtd > 0)
        {
            $this->qtdFornecida = $qtd;
            return true;
        }

        throw new Exception('Parametro inválido<br/>');

    }


    public function getValCompra()
    {
    	
    	if(!empty($this->vlCompra)){

    		return $this->vlCompra;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }

    public function setValCompra(float $val)
    {
        
        if(isset($val) && ($val > 0)){
            
            $this->vlCompra = $val;

            return true;
        }

        throw new Exception('Parametro inválido<br/>');
    }


    public function getValVenda()
    {
    	
    	if(!empty($this->vlVenda)){

    		return $this->vlVenda;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function setValVenda(float $val)
    {
        if(isset($val) && ($val > 0)){
            
            $this->vlVenda = $val;
            
            return true;
        }

        throw new Exception('Parametro inválido<br/>');
    }

    public function getAtivo()
    {
    	
    	if(!empty($this->ativo)){

    		return $this->ativo;
    	}

    	throw new Exception('Propriedade não definida<br/>');
    }


    public function setAtivo(int $ativo = 1)
    {
        
        if(isset($ativo) && ($ativo >=0) && ($ativo <= 1)){

            $this->ativo = $ativo;

            return true;
        }

        throw new Exception('Parametro inválido<br/>');
    }

    public function setMargem(float $margem)
    {
        if(isset($margem) && ($margem >= 0)){

            $this->margem = $margem;

            return true;
        }

        throw new Exception('Parametro inválido<br/>');
    }


    public function getMargem():float
    {
        if(isset($this->margem) && ($this->margem > 0)){

            return $this->margem; 
        }
        
        throw new Exception('Propriedade não definida<br/>');
    }


    public function listarConsultaPersonalizada(String $where = null):array
    {

        $sqlPersonalizada = "SELECT distinct P.nomeProduto, F.dtValidade, F.qtdFornecida, F.qtdVendida ";
        $sqlPersonalizada .= " FROM  Fornecimento F inner join Produto P on P.idProduto = F.ProdutoIdProduto";

        if($where != null)
        {
            $sqlPersonalizada .= $where;
        }

        $result = $this->persolizaConsulta($sqlPersonalizada);

        return $result;
    }
    
    

}