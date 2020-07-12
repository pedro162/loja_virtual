<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Pedido;
use App\Models\Chate;
use App\Models\ConversaChate;
use \Exception;
use \InvalidArgumentException;

class Pessoa extends BaseModel
{
	private $data = [];
    const TABLENAME = 'Pessoa';
    
	private $nomePessoa;
	private $idPessoa;
    private $login;
    private $senha;
    private $nomeFantasia;
    private $documento;
    private $documentoComplementar;
    private $img;

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
                case 'loadPessoa':
                	//return $this->loadPessoa($subArray[1]);
                   	break;
            }

        }
    }
    public function save(array $dados)
    {
       /* $this->clear($dados);

        $result = $this->parseCommit();

        $resultSelect = $this->select(['nomeCategoria'], ['nomeCategoria' => $this->getCategoria()], '=','asc', null, null, true);

        if($resultSelect != false){

            return ['msg','warning','Atenção: Esta categoria já existe!'];
        }

        $resultInsert = $this->insert($result);
        if($resultInsert == true){
            return ['msg','success','Categoria cadastrada com sucesso!'];
        }

        return ['msg','warning','Falha ao cadastrar categoria!'];*/
    }

    public function modify(array $dados)
    {

    }

    public function infoPedidoComplete(Array $where = [], String $tipo = 'venda', Int $init = 0, Int $end = 10)
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
            )as vendedor
        from Pedido P 

        INNER join DetalhesPedido D on D.PedidoIdPedido = P.idPedido
        INNER JOIN Usuario U on U.idUsuario = P.UsuarioIdUsuario
        WHERE P.tipo = '".$tipo."' and P.PessoaIdPessoa = ".$this->idPessoa;

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


    public function getPedido(String $tipo = 'venda')
    {
        if(!(isset($tipo) && (strlen($tipo) > 0))){

            throw new Exception("Parâmetro inválido\n");
            
        }

        $pedido = new Pedido();

        $result = $pedido->select(['idPedido', 'dtPedido','dtEnvio', 'dtEntrega',
            'via', 'frete', 'LogradouroIdLogradouro'
            ], ['PessoaIdPessoa' => $this->idPessoa, 'tipo' => $tipo], '=', 'desc', 0, 10, true, false);

        return $result;
    }

    public function loadPessoa($dados, $classPessoa = true, $like = true)
    {	
        if(is_array($dados)){
    	   if($dados[0]=='cod'){
                $dados = $dados[1];
            }else{
                $dados = $dados[0];//falta terminar de implementar
            }
        }

    	$length =(int) strlen($dados);

    	$filtro = null;

    	switch ($length) {
    		case 11:
    			$filtro = ['documento', $dados];
    			break;
            default:
                $filtro = ['nomePessoa', '%'.$dados.'%'];
                break;
    	}

    	if($filtro != null){

            $result =false;

            if($like){
                $result = $this->select(['idPessoa', 'nomePessoa', 'documento'], [$filtro[0] => $filtro[1]], 'like', 'asc', null, null, $classPessoa, true);
            }else{
                $result = $this->select(['idPessoa', 'nomePessoa', 'documento'], [$filtro[0] => $filtro[1]], '=', 'asc', 1, 10, $classPessoa, true);
            }

    		return $result;
    		
    	}
    	throw new Exception('Parâmetro inválido<br/>'.PHP_EOL);
    }

	public function findPessoa(Int $id)
	{
		$result = $this->select(['idPessoa', 'nomePessoa', 'nomeFantasia','documentoComplementar','tipo','documento'], ['idPessoa' => $id], '=', 'asc', null, null, true, false);
		if($result){
			return $result[0];
		}
		throw new Exception("Cliente inválido\n");
        

	}


    public function getConversation()
    {
        $conversation = new ConversaChate();
        $result = $conversation->loadConversationForPersonId($this->idPessoa);
        return $result;
    }


    public function getNomePessoa()
    {
        if(isset($this->nomePessoa) && (!empty($this->nomePessoa))){
            return $this->nomePessoa;
        }
        throw new Exception("Propriedade não definida.");
        
    }

    public function getIdPessoa():int
    {
        if((!isset($this->idPessoa)) || ($this->idPessoa <= 0)){

            if(isset($this->data['idPessoa']) && ($this->data['idPessoa'] > 0)){
                return $this->data['idPessoa'];
            }
            throw new Exception("Propriedade não definida.");
        }

        return $this->idPessoa;
        
        
    }

    public function getLogradouro()
    {
         $logradPessoa = new LogradouroPessoa();
         $resultLogPessoa =  $logradPessoa->listarConsultaPersonalizada('P.idPessoa = '.$this->getIdPessoa(), null, null, true);
         if($resultLogPessoa != false){
            return $resultLogPessoa;
         }
         throw new Exception("Logradouro não encontrado\n");
         
    }

    public function getLogin()
    {
        if((!isset($this->login)) || (strlen($this->login) ==0 )){
            if(isset($this->data['login']) && (strlen($this->data['login']) > 0)){
                return $this->data['login'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->login;
    }

    public function getImg()
    {
        if((!isset($this->img)) || (strlen($this->img) ==0 )){
            if(isset($this->data['img']) && (strlen($this->data['img']) > 0)){
                return $this->data['img'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->img;
    }

    public function getDocumento()
    {
        if((!isset($this->documento)) || (strlen($this->documento) ==0 )){
            if(isset($this->data['documento']) && (strlen($this->data['documento']) > 0)){
                return $this->data['documento'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->documento;
    }

    public function getDocumentoComplementar()
    {
        if((!isset($this->documentoComplementar)) || (strlen($this->documentoComplementar) ==0 )){
            if(isset($this->data['documentoComplementar']) && (strlen($this->data['documentoComplementar']) > 0)){
                return $this->data['documentoComplementar'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->documentoComplementar;
    }


    public function getNomeFantasia()
    {
        if((!isset($this->nomeFantasia)) || (strlen($this->nomeFantasia) ==0 )){
            if(isset($this->data['nomeFantasia']) && (strlen($this->data['nomeFantasia']) > 0)){
                return $this->data['nomeFantasia'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->nomeFantasia;
    }


     public function getSenha()
    {
        if((!isset($this->senha)) || (strlen($this->senha) ==0 )){
            if(isset($this->data['senha']) && (strlen($this->data['senha']) > 0)){
                return $this->data['senha'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->senha;
    }

    public function findLoginForUserPass($user, $pass)
    {
        if((!isset($user) )|| (!isset($pass))){
            throw new Exception("Parâmetro inválido\n");
            
        }

        $user = trim($user);
        $pass = trim($pass);
        if((strlen($user) == 0)|| (strlen($pass) == 0)){
            throw new Exception("Parâmetro inválido\n");
            
        }


        $result = $this->select(['idPessoa', 'nomePessoa', 'login', 'senha', 'img'], ['login' => $user], '=', 'asc', null, null, true, false);

        if($result == false){
            throw new Exception("Usuario o senha inválidos\n");
        }

        if (($result[0]->getLogin() === $user) && ($result[0]->getSenha() === $pass)) {
            return $result[0];
        }

        throw new Exception("Usuario o senha inválidos\n");

        
    }
    

    public function loadChate()
    {
        $chate = new Chate();
        $result = $chate->getChateOfLucutor($this->idPessoa);

        return $result;
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
