<?php 

namespace Core\Containner;

use Exception;

class Sessoes
{
    public  static function iniciarSessao()
    {
        session_start();

    }

    public  static function removeItemSessao($item)
    {
        session_start();
        unset($_SESSION[$item]);
    }

    public static function addItemSessao($identificador ,$item)
    {
        if((empty($item)) || (empty($identificador)))
        {
            throw new Exception("Item inválido<br/>\n");
        }

        $_SESSION[$identificador] = $item;
    }

}