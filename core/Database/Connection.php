<?php

namespace Core\Database;

use \Exception;
use \PDO;

final class Connection
{   
    private function __construct(){}

    public static function open($file)
    {
        if(!file_exists(__DIR__.'/../../'.$file.'.php'))
        {
            throw new Exception("Arquivo {$file}.ini não encontrado<br/>\n");
            
        }
        
        $file_connection = require(__DIR__."/../../{$file}.php");
        

        $user = isset($file_connection['user'])     ? $file_connection['user']      : null;
        $pass = isset($file_connection['password']) ? $file_connection['password']  : null;
        $name = isset($file_connection['dbname'])   ? $file_connection['dbname']    : null;
        $host = isset($file_connection['host'])     ? $file_connection['host']      : null;
        $type = isset($file_connection['type'])     ? $file_connection['type']      : null;
        $port = isset($file_connection['port'])     ? $file_connection['port']      : null;
        
        $conn = null;
        switch($type)
        {
            case "mysql":
                $port = $port ? $port : '3306';
                $conn = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8", $user, $pass);
            break;
            case 'pgsql':
                $port = $port ? $port : '5432';
                $conn = new PDO("pgsql:dbname={$name}; user={$user}; password={$pass}; host={$host}; port={$port}");
            break;
            default:
                throw new Exception("Driver inválido<br/>\n");
            break;

        }

        if($conn == null)
        {
            throw new Exception("Falha na Conexao <br/>\n");
        }

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $conn;


    }

    
}