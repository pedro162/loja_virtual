<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Models\Pessoa;
use \Core\Database\Transaction;
use \Exception;

/**
 * 
 */
class UserController extends BaseController
{
	public function index()
	{
		try {
			Transaction::startTransaction('connection');
				
			$this->render('usuario/index', false);

        	Transaction::close();

		} catch (Exception $e) {
			
			Transaction::rollback();
		}
		
	}
	
    
    public function loginAdmin($request)
    {
    	try {
			Transaction::startTransaction('connection');

			if((!isset($request['post']['usuario'])) || (!isset($request['post']['senha']))) {
				throw new Exception("Dados inálidos\n");
			}

			$usuario = new Usuario();
			$result = $usuario->findLoginForUserPass($request['post']['usuario'], $request['post']['senha']);

			header('location:/home/admin');

        	Transaction::close();

		} catch (Exception $e) {
			var_dump($e->getMessage());//falta implentar a msg na sessao para exibir ao ususario
			Transaction::rollback();
			//header('location:/usuario/login');
		}
		
        
    }

    public function logoutAdmin()
    {

    }


}