<?php

namespace App\Models;

use \Core\Database\Connection;
use Exception;
use InvalidArgumentException;
use \PDO;

abstract class BaseModel
{
    protected static $conn;

    protected static function open()
    {
        if(empty(self::$conn))
        {
            self::$conn = Connection::open('connection');
        }
        return self::$conn;
    }

    protected function select(array $elementos, String $filtro = '', $ordem = 'asc'):string
    {
        # code...
        //select ite[0], item[1]
        $sql = "SELECT ";
        foreach($this->satinizar($elementos) as $key => $value)
        {
            $sql .= $value.', ';
        }

        $sql = substr($sql, 0, -2);
        return $sql;

    }


    protected function satinizar($elemento)
    {
        if(empty($elemento) || (!isset($elemento)))
        {
            throw new Exception("Parametro inválido<br/>\n");
        }

        $newElemento = null;

        if(is_array($elemento))
        {
            $newElemento = [];
            foreach($elemento as $key => $value)
            {
                $key = trim($key);
                $key = htmlspecialchars($key);
                $key = self::$conn->quote($key);
                $key = strtr($key, ['_'=>'\_', '%'=> '\%']);

                $value = trim($value);
                $value = htmlspecialchars($value);
                $value = self::$conn->quote($value);
                $value = strtr($value, ['_'=>'\_', '%'=> '\%']);
                
                $newElemento[$key] = $value;

                
            }
        }
        else
        {
                $elemento = trim($elemento);
                $elemento = htmlspecialchars($elemento);
                $elemento = self::$conn->quote($elemento);
                $elemento = strtr($elemento, ['_'=>'\_', '%'=> '\%']);

                $newElemento = $elemento;
        }

        if($newElemento != null)
        {
            return $newElemento;
        }
        return false;
    }
}