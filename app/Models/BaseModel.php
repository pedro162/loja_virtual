<?php

namespace App\Models;

use \Core\Database\Connection;
use \Core\Database\Transaction;
use Exception;
use InvalidArgumentException;
use \PDO;

abstract class BaseModel extends Transaction
{
    protected static $conn;

    protected static function open()
    {
        if(empty(self::$conn))
        {
            self::$conn = Connection::open('connection');
        }
    }
    
    protected static function getConn()
    {
        if(empty(self::$conn))
        {
            throw new Exception("Não existe conexão aberta<br/>");
        }
        return self::$conn;
    }

    //ao informar o filtro, o operador tambem deve ser invormado
    public function select(array $elementos, array $filtro = [], $operador = '=', $ordem = 'asc'):array
    {

        $sql = "SELECT ";
        foreach($elementos as $key => $value)
        {
                    $sql .= $value.', ';
        }

        $sql = substr($sql, 0, -2);

        $sql .= ' FROM '.$this->table;

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

        $sql .= ' ORDER BY id'.$this->table.' '.$ordem;

        $result = self::$conn->query($sql);
        if($result)
        {
            $arrayObj = $result->fetchAll(PDO::FETCH_CLASS, get_class($this));
            return $arrayObj;
        }

        throw new Exception("Falha ao excluir registro<br/>\n");


    }


    public function delete(Int $id, Int $limit = 1):bool
    {
        $sql = 'DELETE FROM '.$this->table.' WHERE id'.$this->table.'='.$id.' limit '.$limit;

        $result = self::$conn->query($sql);
        if($result)
        {
            return true;
        }

        throw new Exception("Falha ao excluir registro<br/>\n");
    }


    public function insert(array $elementos)
    {
        $sql = "INSERT INTO {$this->table} (";

        $keys = '';
        $values = '';

        foreach ($this->satinizar($elementos) as $key => $value)
        {
            $keys .= $key.', ';
            $values .= $value.', ';
        }


        $keys = substr($keys, 0, -2);
        $values  = substr($values, 0, -2);

        $sql .="{$keys}) VALUES ({$values})";

        $result = self::$conn->query($sql);

        if($result)
        {
            return true;
        }

        throw new Exception("Falha ao cadastrar registro<br/>\n");


    }




    public function update(array $elementos, int $id)
    {
        //update nome table set campo = valor and novocampo = novovalor
        $sql = "UPDATE {$this->table} SET ";

        foreach ($this->satinizar($elementos) as $key => $value)
        {
           $sql .= $key.'='.$value.", ";
        }

        $sql = substr($sql, 0, -2);

        $sql .= " where id{$this->table}={$id}";

        $result = self::$conn->query($sql);

        if($result)
        {
            return true;
        }

        throw new Exception("Falha ao atualizar registro<br/>\n");

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
            if(count($elemento) == 0)
            {
                throw new Exception("Parametro inválido!<br/>\n");
            }

            $newElemento = [];
            foreach($elemento as $key => $value)
            {
                $key = trim($key);
                $key = htmlspecialchars($key);

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



    protected function persolizaConsulta(String $sql)
    {
        $result = self::$conn->query($sql);

        $arrayObj = $result->fetchAll();

        if(count($arrayObj) ==0)
        {
            return false;
        }
        
        return $arrayObj;
    }
}

