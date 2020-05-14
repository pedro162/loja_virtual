<?php

namespace App\Models;

use \Core\Database\Connection;
use \Core\Database\Transaction;
use Exception;
use InvalidArgumentException;
use \PDO;

abstract class BaseModel
{
    protected static $conn;
    protected static $database = 'connection';

    public function start()
    {
        Commit::start();

    }

    protected static function getDatabase()
    {
        return self::$database;
    }

    

    protected static function getConn()
    {
        if(empty(self::$conn))
        {
            throw new Exception("Não existe conexão aberta<br/>");
        }
        return self::$conn;
    }

    public function countItens():int
    {

        $sql = "SELECT COUNT(id{$this->table}) totItens FROM {$this->table}";
        $conn = Transaction::get();

        $consulta = $conn->query($sql);

        $result = $consulta->fetchAll();

        return $result[0]->totItens;
        
    }


    public function paginador(array $campos, Int $itensPorPagina, Int $paginas, $class =  null)
    {   

        $inicio = ($itensPorPagina * $paginas) - $itensPorPagina;


        $result = $this->select($campos, [],'=','asc', $inicio, $itensPorPagina, $class);
        
        return $result;
       
    }



    //ao informar o filtro, o operador tambem deve ser invormado
    public function select(array $elementos, array $filtro = [], $operador = '=',
     $ordem = 'asc', $litmitInit = null, $limitEnd = null, $std = null)
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

        if(!(is_null($litmitInit) && is_null($limitEnd))){
            $sql .= ' LIMIT '.$litmitInit.','. $limitEnd;
        }

        $conn = Transaction::get();

        $result = $conn->query($sql);


        $arrayObj = null;

        if($std != null){
            $arrayObj = $result->fetchAll(PDO::FETCH_CLASS, get_class($this));
           // Transaction::close();

        }else{
            $arrayObj = $result->fetchAll();
            //Transaction::close();
        }

        if($arrayObj)
        {
            return $arrayObj;
        }

        return false;


    }


    public function delete(String $where , String $comparador ,Int $id, Int $limit = null):bool
    {
        
        $sql = 'DELETE FROM '.$this->table.' WHERE '.$where.$comparador.$id;
        if($limit != null){
            $sql .= ' limit '.$limit;
        }

        $conn = Transaction::get();

        $result = $conn->exec($sql);
        if($result > 0)
        {
            return true;
        }

        throw new Exception("Erro ao excluir registro<br/>\n");
        
    }


    public function insert(array $elementos):bool
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

        $conn = Transaction::get();

        $result = $conn->query($sql);
        //$result = self::$conn->query($sql);

        if($result)
        {
            return true;
        }

        return false;


    }




    public function update(array $elementos, int $id):bool
    {

        //return Commit::update($this->table, $elementos, $id);
        //update nome table set campo = valor and novocampo = novovalor
        $sql = "UPDATE {$this->table} SET ";

        foreach ($this->satinizar($elementos) as $key => $value)
        {
           $sql .= $key.'='.$value.", ";
        }

        $sql = substr($sql, 0, -2);

        $sql .= " where id{$this->table}={$id}";

        $conn = Transaction::get();
        $result = $conn->exec($sql);

        if($result > 0)
        {
            return true;
        }
        return false;

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

                $conn = Transaction::get();

                $value = $conn->quote($value);

                $value = strtr($value, ['_'=>'\_', '%'=> '\%']);
                
                $newElemento[$key] = $value;

                
            }
        }
        else
        {
                $elemento = trim($elemento);
                $elemento = htmlspecialchars($elemento);

                $conn = Transaction::get();
                $elemento = $conn->quote($elemento);
                //$elemento = self::$conn->quote($elemento);
                $elemento = strtr($elemento, ['_'=>'\_', '%'=> '\%']);

                $newElemento = $elemento;
        }

        if($newElemento != null)
        {
            return $newElemento;
        }
        return false;
    }



    protected function persolizaConsulta(String $sql, $clasRetorno = false)
    {
        $conn = Transaction::get();
        $result = $conn->query($sql);

        $arrayObj = $result->fetchAll();


        if(count($arrayObj) == 0)
        {
            return false;
        }
        
        return $arrayObj;
    }


    /*protected function parseStdClass($obj)
    {
        $teste = new \ReflectionClass(get_class($this));
        $methods = $teste->getMethods();
        $propriedades = $teste->getProperties();

        $stdclass = new \stdclass();

        for ($i=0; !($i == count($methods)) ; $i++) { 
            if(substr($methods[$i], 0, 2) == 'get'){
                $stdclass->methods[$i];
            }
            
        }
        var_dump();
    }*/


    protected function parseRequestAjax(array $dados){
        $superArray = [];

        for ($i=0; !($i == count($dados)); $i++) { 
            $resultado = null;

            $chave = null;

            for ($j=0; !($j == count($dados[$i])); $j++) {

                $chave = $dados[$i][0];
                if($j != 0){
                    $resultado[] = $dados[$i][$j];
                }
                
            }

            $superArray[$chave] = $resultado;
            
        }

        return $superArray;
    }


    protected function maxId():int
    {
        $id = 'id'.ucfirst($this->table);
        $sql = "SELECT MAX({$id}) as maxId from {$this->table}";

        $conn = Transaction::get();

        $consulta = $conn->query($sql);

        $result = $consulta->fetchObject();
        if($result){
            return $result->maxId;
        }

    }


    abstract protected function parseCommit();

    abstract protected function clear(array $dados);

    abstract public function save(array $dados);

    abstract public function modify(array $dados);




}

