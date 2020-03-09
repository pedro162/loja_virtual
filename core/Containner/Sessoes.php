<?php 

namespace Core\Containner;

use Exception;

class Sessoes
{
    public  static function iniciarSessao()
    {
        session_start();

    }

    public  static function encerrarSessao($item)
    {
        session_start();
        unset($_SESSION[$item]);
    }

    public static function addItemSessao($item)
    {
        if(empty($item))
        {
            throw new Exception("Item inválido<br/>\n");
        }

        $_SESSION[] = $item;
    }

}