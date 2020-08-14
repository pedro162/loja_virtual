<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Models\Pessoa;
use \Core\Database\Transaction;
use Core\Utilitarios\Sessoes;
use \Exception;

/**
 * 
 */
class UserController extends BaseController
{
	public function index()
	{
		try {
			
			Sessoes::sessionEnde();

			Transaction::startTransaction('connection');
				
			$this->render('usuario/index', false);

        	Transaction::close();

		}catch (\PDOException $e) {

            Transaction::rollback();

        }catch (Exception $e) {
			
			Transaction::rollback();
		}
		
	}
	
    
    public function loginAdmin($request)
    {
    	try {
			Transaction::startTransaction('connection');

			
			Sessoes::sessionInit();//inicia a sessao
            


			if((!isset($request['post']['usuario'])) || (!isset($request['post']['senha']))) {
				throw new Exception("Dados inálidos\n");
			}

			$usuario = new Usuario();
			$result = $usuario->findLoginForUserPass($request['post']['usuario'], $request['post']['senha']);

			Sessoes::usuarioInit($result, 'user_admin');//grava o usuario na sessao

			header('location:/home/admin');

        	Transaction::close();

		}catch (\PDOException $e) {

            Transaction::rollback();

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