<?php

namespace Core\Database;

use \Core\Database\Connection;
use \Core\Database\Transaction;
use Exception;
use InvalidArgumentException;
use \PDO;

abstract class Commit extends Transaction
{
	private static $conn;
	private static $table;

	protected static function open()
    {
        if(empty(self::$conn))
        {
            self::$conn = Connection::open('connection');
        }
    }

    public static function start()
    {
    	self::open();
    }

	public static function countItens($table)
    {
        $sql = "SELECT COUNT(id".$table.") totItens FROM ".$table;
        $consulta = self::$conn->query($sql);

        $result = $consulta->fetchAll();
        if($result){
            return $result[0]->totItens;
        }else{
            return "Nenhum resultado encontrado<br/>\n";
        }


    }


    //ao informar o filtro, o operador tambem deve ser invormado
    public static function select(String $table, array $elementos, array $filtro = [], $operador = '=',
     $ordem = 'asc', $litmitInit = null, $limitEnd = null, $std = null):array
    {

        $sql = "SELECT ";
        foreach($elementos as $key => $value)
        {
                    $sql .= $value.', ';
        }

        $sql = substr($sql, 0, -2);

        $sql .= ' FROM '.$table;

        if(count($filtro) > 0)
        {
            $sql .= " where ";

            $result = self::satinizar($filtro);
            foreach($result as $key => $value)
            {
                $sql.= $key.' '.$operador.' '.$value.' AND ';
            }
            $sql = substr($sql, 0, -4);
            
        }

        $sql .= ' ORDER BY id'.$table.' '.$ordem;

        if(!(is_null($litmitInit) && is_null($limitEnd))){
            $sql .= ' LIMIT '.$litmitInit.','. $limitEnd;
        }

        $result = self::$conn->query($sql);


        $arrayObj = null;

        if($std == null){

            $arrayObj = $result->fetchAll();

        }else{
            $arrayObj = $result->fetchAll(PDO::FETCH_CLASS, $std);
        }

        if($arrayObj)
        {
            return $arrayObj;
        }

        throw new Exception("Elemento não encontrado<br/>\n");


    }


    public static function delete(String $table, Int $id, Int $limit = 1):bool
    {
        $sql = 'DELETE FROM '.$table.' WHERE id'.$table.'='.$id.' limit '.$limit;

        $result = self::$conn->query($sql);
        if($result)
        {
            return true;
        }

        throw new Exception("Falha ao excluir registro<br/>\n");
    }


    public static function insert(String $table, array $elementos)
    {
        $sql = "INSERT INTO {$table} (";

        $keys = '';
        $values = '';

        foreach (self::satinizar($elementos) as $key => $value)
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




    public static function update(String $table, array $elementos, int $id)
    {
        //update nome table set campo = valor and novocampo = novovalor
        $sql = "UPDATE {$table} SET ";

        foreach (self::satinizar($elementos) as $key => $value)
        {
           $sql .= $key.'='.$value.", ";
        }

        $sql = substr($sql, 0, -2);

        $sql .= " where id{$table}={$id}";

        $result = self::$conn->query($sql);

        if($result)
        {
            return true;
        }

        throw new Exception("Falha ao atualizar registro<br/>\n");

    }


    public static function satinizar($elemento)
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


    public static function persolizaConsulta(String $sql, $clasRetorno = false):array
    {	
        $result = self::$conn->query($sql);

        if($clasRetorno == false){
			$arrayObj = $result->fetchAll();
        }else{
        	$arrayObj = $result->fetchAll(PDO::FETCH_CLASS, $clasRetorno);
        }
        

        if(count($arrayObj) ==0)
        {
            throw new Exception("Elemento não encontrado<br/>\n");
        }
        
        return $arrayObj;
    }

}