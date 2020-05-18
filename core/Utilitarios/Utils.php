<?php

namespace Core\Utilitarios;

class Utils
{
	public static function validaData(String $data)
	{
		if(isset($data) && (strlen($data) > 0)){
            $arrayData = explode('-', $data);
            if(count($arrayData) == 3){
                if(checkdate($arrayData[1], $arrayData[2], $arrayData[0])){
                	
                    return [$arrayData[0], $arrayData[1], $arrayData[2]];
                }

            }else{

                return false;
            }

        }

        return false;
	}

    public static function formataDateBr(String $date):String
    {
        $dtRe = substr($date, 0, 10);
        $dtRe = explode('-', $date);
        return $dtRe[2].'/'.$dtRe[1].'/'.$dtRe[0];
    }
    

}