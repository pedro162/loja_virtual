<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class Cliente extends BaseModel
{
	private $data = [];
	protected $table = 'Cliente';

	private $nomeCliente;
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
                case 'loadCliente':
                	return $this->loadCliente($subArray[1]);
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

    public function loadCliente($dados, $classCliente = true, $like = true)
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
    			$filtro = ['cpf', $dados];
    			break;
            default:
                $filtro = ['nomeCliente', '%'.$dados.'%'];
                break;
    	}

    	if($filtro != null){

            $result =false;

            if($like){
                $result = $this->select(['idCliente', 'nomeCliente', 'cpf'], [$filtro[0] => $filtro[1]], 'like', 'asc', null, null, $classCliente, true);
            }else{
                $result = $this->select(['idCliente', 'nomeCliente', 'cpf'], [$filtro[0] => $filtro[1]], '=', 'asc', 1, 10, $classCliente, true);
            }

    		return $result;
    		
    	}
    	throw new Exception('Parâmetro inválido<br/>'.PHP_EOL);
    }

	public function findCliente($dados)
	{
		$result = $this->clear($dados);
		if($result){
			return true;
		}
		return false;

	}

    

    
 




}
