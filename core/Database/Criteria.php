<?php

namespace Core\Database;

use \Core\Database\Connection;
use \Core\Database\Transaction;
use Exception;
use InvalidArgumentException;
use \PDO;

class Criteria
{ 	
	private $filters;

	public function __construct()
	{
		$this->filters = [];
	}


	public function add($variable, $compareOperator, $value, $logicOperator = 'and')
	{
		if(empty($this->filters)){
			$logicOperator = null;
		}
		$this->filters[] = [$variable, $compareOperator, $satinizar()];
	}


    protected function satinizar($elemento, $like =false)
    {
        if(empty($elemento) || (!isset($elemento)))
        {
            throw new Exception("Parametro inválido<br/>\n");
        }

        $result = null;

        if(is_array($elemento))
        {
            if(count($elemento) == 0)
            {
                throw new Exception("Parametro inválido!<br/>\n");
            }

            $newElemento = [];
            foreach($elemento as $value)
            {
            	if(is_integer($value)){

            		$newElemento[] = $value;

            	}else if(is_string($value)){

            		$value = trim($value);
	                $value = htmlspecialchars($value);

	                $conn = Transaction::get();

	                $value = $conn->quote($value);
	                $value = strtr($value, ['_'=>'\_', '%'=> '\%']);

	                $newElemento[] = $value;

                
            	}
        	}

        	$result = '('.implode(', ',  $newElemento).')';

        }else if(is_integer($value)){

        	$result= $value;

    	}else if(is_string($value)){

    		$value = trim($value);
            $value = htmlspecialchars($value);

            $conn = Transaction::get();

            $value = $conn->quote($value);
            $value = strtr($value, ['_'=>'\_', '%'=> '\%']);

            $result = $value;

        
    	}


        if($result != null)
        {
            return $result;
        }
        return false;
    }


    public function dump()
    {
    	if(is_array($this->filters) && (count($this->filters) > 0)){
    		$result = '';

    		foreach ($this->filters as $value) {
    			$result .= $value[3].' '.$value[0].' '.$value[1].' '.$value[2].' ';
    		}

    		$result = trim($result);
    		return "({$result})";
    	}
    	throw new Exception('Propriedade indefinida<br/>'.PHP_EOL);

    }




}

