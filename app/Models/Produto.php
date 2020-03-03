<?php

namespace App\Models;

use \Exception;

class Produto
{
    private static $conn;
    private $data;

    public function __get($pro)
    {
        if(in_array($prop))
        {
            return $this->data[$prop];
        }

        throw new Exception("Propriedade inválida\n");
    }


    public function __set($pro, $val)
    {
        if((isset($prop)) && (isset($val)))
        {
            if(!empty($val))
            {
                $this->data[$prop] = $val;

                return true;
            }


            return false;
        }
        return false;
    }

    public static function setConnetion(PDO $con)
    {
        self::$con = $con;
    }


    public static function find($id)
    {
        $sql = "select * from produto where id= '$id' ";
        
        print $sql.'<br/>\n';

        $result = self::$con->query($sql);
        return $result->fetchObject(__CLASS__);
    }



    public static function all($filter = '')
    {
        $sql = "SELECT * FROM produtos";

        if($filter)
        {
            $sql .= "where $filter";
        }

        print $sql.'<br/>';
        
        $restult = self::$con->query($sql);

        return $restult->fetchAll(PDO::FETCH_CLASS, __CLASS__);

    }


    public function delete()
    {
        $sql = "DELET FROM produtos where id = '{$this->id}'";
        print $sql;;

        return slef::$conn->query($sql);

   
    }














}

