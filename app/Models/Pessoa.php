<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Pedido;
use App\Models\Chate;
use App\Models\ConversaChate;
use App\Models\CartaoComprador;
use \Core\Utilitarios\Utils;
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
    private $nomeComplementar;
    private $documento;
    private $documentoComplementar;
    private $img;
    private $sexo;
    private $tipo;
    private $grupo;

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
        $result = $this->parseCommit();

        $this->validaPessoa();

        $resultInsert = $this->insert($result);

        if($resultInsert == true){
            return true;
        }

        return false;
    }

    public function modify(array $dados)
    {
        $result = $this->parseCommit();
        
        $this->validaPessoa(true);

        $resultUpdate = $this->update($result, $this->getIdPessoa());

        return $resultUpdate;
    }

    public function validaPessoa($edit = false):bool
    {

        $error = [];

        if($edit == false){
            
            $resultLogin = $this->select(['*'], ['login' => $this->data['login']] , '=','asc', null, null, true);

            if($resultLogin != false){
                $error[] = "Email já cadastrado";
                
            }

            $resultDocument = $this->select(['*'], ['documento' => $this->data['documento']] , '=','asc', null, null, true);

            $resultDocComplement = $this->select(['*'], ['documentoComplementar' => $this->data['documentoComplementar']] , '=','asc', null, null, true);

            if(($resultDocument != false) || ($resultDocComplement != false)){
                $error[] = "Pessoa já está cadastrada\n";
            }

            if($this->data['tipo'] == 'F'){
                if(Utils::validaCpf($this->data['documento']) == false){
                    $error[] = "Cpf inválido\n";
                    
                }
            }

        }else{

            $resultLogin = $this->select(['*'], ['login' => $this->data['login']] , '=','asc', null, null, true);

            if($resultLogin[0]->getIdPessoa() != $this->getIdPessoa()){
                $error[] = "Email já cadastrado";
                
            }

            $resultDocument = $this->select(['*'], ['documento' => $this->data['documento']] , '=','asc', null, null, true);

            $resultDocComplement = $this->select(['*'], ['documentoComplementar' => $this->data['documentoComplementar']] , '=','asc', null, null, true);
            if(($resultDocComplement != false) && ($resultDocument != false)){
                if( 
                    ($resultDocument[0]->getIdPessoa() != $this->getIdPessoa()) 
                    || ($resultDocComplement[0]->getIdPessoa() != $this->getIdPessoa())
                ){
                    $error[] = "Pessoa já está cadastrada\n";
                }
            }
            
            if($this->data['tipo'] == 'F'){
                if(Utils::validaCpf($this->data['documento']) == false){
                    $error[] = "Cpf inválido\n";
                    
                }
            }

        }


        

        if($tot = count($error) > 0){
            $msg = '';

            for ($i=0; !($i == $tot ); $i++) { 
                $msg .= $error[$i].'<br/>'.PHP_EOL;
            }

            throw new Exception($msg);
        }

        return true;
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
                    if(isset($where[$i]['key']) && isset($where[$i]['val']) && isset($where[$i]['comparator'])){

                        $key        = trim($where[$i]['key']);
                        $val        = trim($where[$i]['val']);
                        $comparator   = trim($where[$i]['comparator']);

                        if(!is_numeric($val)){
                            $val = $this->satinizar($val);
                        }

                        if(isset($where[$i]['operator'])){
                            $sql .= ' '.trim($where[$i]['operator']);
                        }

                        $sql .= ' '.$key.' '.$comparator.' '.$val;

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
		$result = $this->select(['*'], ['idPessoa' => $id], '=', 'asc', null, null, true, false);
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

    public function setNomePessoa(String $nome):bool
    {
        if((!isset($nome)) || (strlen(trim($nome)) < 3) || (strlen(trim($nome)) > 200)){
            throw new Exception("O nome deve ter entre 3 e 200 caracteres.");
        }

       $this->data['nomePessoa'] = $nome;

       return true;
        
        
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

    public function setIdPessoa(Int $id):bool
    {
        if((!isset($id)) || ($id <= 0)){
            throw new Exception("Parâmetro inválido\n");
        }

       $this->data['idPessoa'] = $id;

       return true;
        
        
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

    public function logradouro()
    {
        $logradouro = new LogradouroPessoa();
        $result = $logradouro->select(['*'], ['PessoaIdPessoa' => $this->getIdPessoa()], '=','asc', null, null, true);
        return $result;
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

    public function setLogin(String $login):bool
    {
        if((!isset($login)) || (strlen(trim($login)) == 0)){
            throw new Exception("O email deve ter entre 12 e 100 caracteres.");
        }

       $this->data['login'] = $login;

       return true;
        
        
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

    public function setImg(String $img):bool
    {
        if((!isset($img)) || (strlen(trim($img)) == 0)){
            throw new Exception("Parâmetro inválido\n");
        }

       $this->data['img'] = $img;

       return true;
        
        
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

    public function setDocumento(String $documento):bool
    {
        if((!isset($documento)) || (strlen(trim($documento)) != 11)){
            throw new Exception("O CPF deve ter 11 caracteres\n");
        }

       $this->data['documento'] = $documento;

       return true;
        
        
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

    public function setDocumentoComplementar(String $documento):bool
    {
        if((!isset($documento)) || (strlen($documento) != 13)){
            throw new Exception("O RG deve ter 13 caracteres.");
        }

       $this->data['documentoComplementar'] = $documento;

       return true;
        
        
    }


    public function getNomeComplementar()
    {
        if((!isset($this->nomeComplementar)) || (strlen($this->nomeComplementar) ==0 )){
            if(isset($this->data['nomeComplementar']) && (strlen($this->data['nomeComplementar']) > 0)){
                return $this->data['nomeComplementar'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->nomeComplementar;
    }


    public function setNomeComplementar(String $nomeComplementar):bool
    {
        if((!isset($nomeComplementar)) || (strlen(trim($nomeComplementar)) < 3) || (strlen(trim($nomeComplementar)) > 200)){
            throw new Exception("Sobrenome deve ter entre 3 e 200 caracteres\n");
        }

       $this->data['nomeComplementar'] = $nomeComplementar;

       return true;
        
        
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

    public function setSenha(String $senha):bool
    {
        if((!isset($senha)) || (strlen(trim($senha)) < 9) || (strlen(trim($senha)) > 12)){
            throw new Exception("Senha deve ter entrre 9 e 12 caracteres\n");
        }

       $this->data['senha'] = $senha;

       return true;
        
        
    }

    public function getTipo()
    {
        if((!isset($this->tipo)) || (strlen($this->tipo) ==0 )){
            if(isset($this->data['tipo']) && (strlen($this->data['tipo']) > 0)){
                return $this->data['tipo'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->tipo;
    }


    public function setTipo(String $tipo):bool
    {
        if((!isset($tipo)) || (strlen($tipo) <= 0)){
            throw new Exception("Parâmetro inválido\n");
        }

       $this->data['tipo'] = $tipo;

       return true;
        
        
    }

    public function getGrupo()
    {
        if((!isset($this->grupo)) || (strlen($this->grupo) ==0 )){
            if(isset($this->data['grupo']) && (strlen($this->data['grupo']) > 0)){
                return $this->data['grupo'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->grupo;
    }

    public function setGrupo(String $grupo):bool
    {
        if((!isset($grupo)) || (strlen($grupo) <= 6)){
            throw new Exception("Parâmetro inválido\n");
        }

       $this->data['grupo'] = $grupo;

       return true;
        
        
    }

    public function getSexo()
    {
        if((!isset($this->sexo)) || (strlen($this->sexo) ==0 )){
            if(isset($this->data['sexo']) && (strlen($this->data['sexo']) > 0)){
                return $this->data['sexo'];
            }

            throw new Exception("Propriedade não definida\n");
        }

        return $this->sexo;
    }

    public function setSexo(String $sexo):bool
    {   
        $sexo = trim($sexo);
        
        $sex = ['M', 'F', 'N'];

        if((!isset($sexo)) || (strlen($sexo) <= 0)){
            throw new Exception("Parâmetro inválido\n");
        }

        if(! in_array($sexo, $sex)){
           throw new Exception("Parâmetro inválido\n");
        }

        $this->data['sexo'] = $sexo;

        return true;
        
        
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


        $result = $this->select(['*'], ['login' => $user], '=', 'asc', null, null, true, false);

        if($result == false){
            throw new Exception("Usuario o senha inválidos\n");
        }

        if (($result[0]->getLogin() === $user) && ($result[0]->getSenha() === $pass)) {
            return $result[0];
        }

        throw new Exception("Usuario o senha inválidos\n");

        
    }

    public function getCartaoComprador()
    {
        $cartao = new CartaoComprador();

        $result = $cartao->selectNew(['*'],
         [
            ['key'=>'idPessoa', 'val'=> $this->idPessoa, 'comparator'=> '=', 'operator' => 'and'],
            ['key'=>'ativo', 'val'=> 'sim', 'comparator'=> '=']
         ],

         [
            ['key'=>'idPessoa', 'order'=>'asc']
         ],

          null, null, true, false
        );

        if($result == false){
            throw new Exception("Nenhum registro encontrado\n");
            
        }
        return $result;
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
