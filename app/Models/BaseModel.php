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
    }

    //ao informar o filtro, o operador tambem deve ser invormado
    public function select(array $elementos, array $filtro = [], $operador = '=', $ordem = 'asc'):string //$filtro array assiciativo ['key'=>'value']
    {
        $sql = "SELECT ";
        foreach($this->satinizar($elementos) as $key => $value)
        {
            $sql .= $value.', ';
        }

        $sql = substr($sql, 0, -2);

        if(count($filtro) > 0)
        {
            $sql .= " where ";

            $result = $this->satinizar($filtro);
            foreach($result as $key => $value)
            {
                $sql.= $key.' '.$operador.' '.$value.' AND ';
            }
            $sql = substr($sql, 0, -4);
            
        }

        $sql .= ' FROM '.$this->table.' ORDER BY id'.$this->table.' '.$ordem;
        return $sql;

    }


    protected function delete(Int $id, Int $limit = 1)
    {
        $sql = 'DELETE FROM '.$this->table.' where
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