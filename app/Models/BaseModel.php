<?php

namespace App\Models;

use \Core\Database\Commit;
use \App\Models\InterfaceBaseModel;
use Exception;
use InvalidArgumentException;

abstract class BaseModel
{
    protected static $conn;

    public function start()
    {
        Commit::start();

    }

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

    public function countItens()
    {
       /* $sql = "SELECT COUNT(id{$this->table}) totItens FROM {$this->table}";
        $consulta = self::$conn->query($sql);

        $result = $consulta->fetchAll();
        if($result){
            return $result[0]->totItens;
        }else{
            return "Nenhum resultado encontrado<br/>\n";
        }*/
        return Commit::countItens($this->table);


    }


    public function paginador(array $campos, Int $itensPorPagina, Int $paginas, $class =  null):array
    {   
        $inicio = ($itensPorPagina * $paginas) - $itensPorPagina;


        $result = $this->select($campos, [],'=','asc', $inicio, $itensPorPagina, $class);

        return $result;
       
    }



    //ao informar o filtro, o operador tambem deve ser invormado
    public function select(array $elementos, array $filtro = [], $operador = '=',
     $ordem = 'asc', $litmitInit = null, $limitEnd = null, $std = null):array
    {
        if($std != null)
        $std = get_class($this);

        return Commit::select($this->table,$elementos,$filtro, $operador,
     $ordem, $litmitInit, $limitEnd, $std);

        /*$sql = "SELECT ";
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

        $result = self::$conn->query($sql);


        $arrayObj = null;

        if($std != null){

            $arrayObj = $result->fetchAll();

        }else{
            $arrayObj = $result->fetchAll(PDO::FETCH_CLASS, get_class($this));
        }

        if($arrayObj)
        {
            return $arrayObj;
        }

        throw new Exception("Elemento não encontrado<br/>\n");*/


    }


    public function delete(Int $id, Int $limit = 1):bool
    {
         return Commit::delete($this->table, $id, $limit);
        /*
        $sql = 'DELETE FROM '.$this->table.' WHERE id'.$this->table.'='.$id.' limit '.$limit;

        $result = self::$conn->query($sql);
        if($result)
        {
            return true;
        }

        throw new Exception("Falha ao excluir registro<br/>\n");*/
    }


    public function insert(array $elementos)
    {
        return Commit::insert($this->table, $elementos);
       /* $sql = "INSERT INTO {$this->table} (";

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

        throw new Exception("Falha ao cadastrar registro<br/>\n");*/


    }




    public function update(array $elementos, int $id)
    {

        return Commit::update($this->table, $elementos, $id);
        //update nome table set campo = valor and novocampo = novovalor
        /*$sql = "UPDATE {$this->table} SET ";

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

        throw new Exception("Falha ao atualizar registro<br/>\n");*/

    }


    protected function satinizar($elemento)
    {
        return Commit::satinizar($elemento);
        /*
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
        return false;*/
    }



    protected function persolizaConsulta(String $sql, $clasRetorno = false)
    {
        return Commit::persolizaConsulta($sql, $clasRetorno);
       /* $result = self::$conn->query($sql);

        $arrayObj = $result->fetchAll();

        if(count($arrayObj) ==0)
        {
            return false;
        }
        
        return $arrayObj;*/
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


    abstract protected function parseCommit();

    abstract protected function clear(array $dados);

    abstract public function commit(array $dados);




}

