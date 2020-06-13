<?php

namespace Core\Utilitarios;
use App\Models\Pessoa;
use \Exception;

class Sessoes
{
	private function __construct(){}

	public static function sessionInit()
	{
		session_start();

	}

	public static function sessionAddElement($key, $value):bool //array associativo
	{
		self::sessionInit();

		if ((!isset($key)) || (!isset($value)))
		{
			throw new \Exception("Propriedade indefinida<br/>\n");
		}

		if ((empty($key)) || (empty($value)))
		{
			throw new \Exception("Valor indefinido<br/>\n");
		}


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

	public static function usuarioInit(Pessoa $pess)
	{
		if(isset($pess) && (!empty($pess))){

			self::sessionInit();
			$_SESSION['usuario'] = serialize($pess);
			return true;
		}

		throw new \Exception("Parâmetro inválido\n");
	}

	public static function usuarioLoad()
	{

		self::sessionInit();

		if(isset($_SESSION['usuario']) && (!empty($_SESSION['usuario']))){
			return unserialize($_SESSION['usuario']);
		}
		return false;
		
	}

    public static function sessionEnde()
    {
       self::sessionInit();

       $_SESSION = array();

       $result = session_destroy();
       
       return $result;
    }

    public static function sessionReturnElements():array
    {
    	//self::sessionInit();

    	if (!count($_SESSION) > 0)
    	{
    		throw new \Exception("Adicione proprieades<br/>\n");
    	}
    	return $_SESSION;
    }
}