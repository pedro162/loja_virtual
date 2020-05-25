<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Pessoa extends BaseModel
{
	private $data = [];
    const TABLENAME = 'Pessoa';
    
	private $nomePessoa;
	private $idCliente;


    protected function parseCommit()
    {
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
		$result = $this->select(['idPessoa', 'nomePessoa', 'documento'], ['idPessoa' => $id], '=', 'asc', null, null, true, false);
		if($result){
			return $result[0];
		}
		return false;

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

    

    
 




}
