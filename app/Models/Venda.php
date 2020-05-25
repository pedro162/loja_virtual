<?php

namespace App\Models;


use App\Models\BaseModel;
use Core\Utilitarios\Sessoes;
/**
 * 
 */
class Venda extends BaseModel
{
	public static function carrinho()
	{
		Sessoes::sessionInit();

		if (isset($_SESSION['carrinho']))
		{
			return $_SESSION['carrinho'];
		}
		throw new \Exception("Carrinho vazio<br/>\n");
		
	}

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

		$key = 'carrinho';

		$total = 0;

		if (isset($_SESSION[$key]))
		{
			if (count($_SESSION[$key]) > 0)
			for ($i=0; !($i == count($_SESSION[$key])) ; $i++)
			{ 
				if (isset($_SESSION[$key][$i]['quantidade'])) {
					$total += $_SESSION[$key][$i]['quantidade'];
				}
				
			}
		}
		return $total;
	}

	public static function addToCarrinho(int $value)
	{
		Sessoes::sessionInit();

		if (!isset($value))
		{
			throw new \Exception("Propriedade indefinida<br/>\n");
		}

		if (empty($value))
		{
			throw new \Exception("Valor indefinido<br/>\n");
		}

		$key = 'carrinho';

		$sentinela = false;
		if (isset($_SESSION[$key]))
		{
			for ($i=0; !($i == count($_SESSION[$key])) ; $i++)
			{ 
				if($_SESSION[$key][$i]['produto'] == $value)
				{
					$_SESSION[$key][$i]['quantidade'] += 1;

					$sentinela = true;
					break;
				}
				
			}
		}

		if($sentinela == false)
		{
			$_SESSION[$key][] = ['produto'=>$value, 'quantidade' => 1];
		}
		
		return true;
		
	}

	public function desistirVenda()
	{
		//obs falta configurar direito
		return Sessoes::sessionEnde();
	}

	public function __get($prop)
	{
		if(method_exists($this, 'get'.ucfirst($prop))){
			 return call_user_func([$this,'get'.ucfirst($prop)]);
		}
	}



}