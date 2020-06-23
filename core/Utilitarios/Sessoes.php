<?php

namespace Core\Utilitarios;
use App\Models\Pessoa;
use \Exception;

class Sessoes
{
	private function __construct(){}

	public static function sessionInit()
	{
		if(session_status() != PHP_SESSION_ACTIVE){
			session_start();
		}

	}

	public static function sessionAddElement($id, $qtd, $remove = false):bool //array associativo
	{
		self::sessionInit();

		if ((!isset($id)) || (!isset($qtd)))
		{
			throw new \Exception("Propriedade indefinida<br/>\n");
		}

		if ((empty($id)) || (empty($qtd)))
		{
			throw new \Exception("Valor indefinido<br/>\n");
		}

		if(isset($_SESSION['produto'])){

			$sentina = false;
			for ($i=0; !($i == count($_SESSION['produto'])); $i++) { 
				if($_SESSION['produto'][$i][0] == $id){

					if($remove != false){
						$_SESSION['produto'][$i][1] -= (int)$qtd;
					}else{
						$_SESSION['produto'][$i][1] += (int)$qtd;
					}
					
					$sentina = true;
					break;
				}
			}

			if($sentina == false){

				$_SESSION['produto'][] = [$id, $qtd];
				
			}
			
		}else{
			$_SESSION['produto'][] = [$id, $qtd];
		}

		return true;

		
	}

	public static function usuarioInit($user, $key = 'usuario')
	{
		if(isset($user) && (!empty($user)) && (isset($key)) && (!empty($key))){

			self::sessionInit();
			$_SESSION[$key] = serialize($user);
			return true;
		}

		throw new \Exception("Parâmetro inválido\n");
	}

	public static function usuarioLoad($key = 'usuario')
	{
		if((!isset($key)) || (empty($key))){
			throw new Exception("Parâmetro inválido\n");
			
		}

		self::sessionInit();

		if(isset($_SESSION[$key]) && (!empty($_SESSION[$key]))){
			return unserialize($_SESSION[$key]);
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

    public static function sessionReturnElements()
    {
    	
    	if ((!isset($_SESSION)))
    	{
    		return 0;
    	}
    	return $_SESSION;
    }
}