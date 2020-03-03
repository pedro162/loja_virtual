<?php

namespace Core\Utilitarios;

abstract class Sessoes
{
    public static function sessionEnde()
    {
        session_start();
        $_SESSION = array();
        \session_destroy();
        header("Location: login.html");
    }
}