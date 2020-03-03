<?php

namespace Core\Utilitarios;

use \Exception;

abstract class SendEmail
{
    public static function bodyMail($nome, $email, $title, $msg, $replay = "phedroclooney@gmail.com")
    {
        if(isset($nome) && isset($email))
        {
            if(!(empty($nome) && empty($email)))
            {
                $result_send_mail = mail($mail, $title, $msg, "From:phedroclooney@gmail.com", "-r {$replay}");
                if(!$result_send_mail)
                {
                    return true;
                }
                
                throw new Exception("Falha no evio do email<br/>\n");

            }
            throw new Exception("Nome/Email inválidos <br/>\n");
        }
        else
        {
            return false;
        }
    }
}