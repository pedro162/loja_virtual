<?php

namespace App\Models;


use App\Models\BaseModel;
use \Core\Database\Commit;
use \Core\Database\Transaction;
use \Exception;
use \InvalidArgumentException;
/**
 * 
 */
class Pedido extends BaseModel
{
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

	public static function qtdItensVenda()
	{
		Sessoes::sessionInit();

	
	}

	public static function addToCarrinho(int $value)
	{
	
		
	}

	public function desistirVenda()
	{

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