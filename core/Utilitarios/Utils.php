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


    public function calFreteCorreios(array $dadosProduto)
    {   
                
        $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?&';
        $url .= http_build_query($dadosProduto);

        $xml = simplexml_load_file($url);
        return $xml;
        
    }

    public static function validaCpf(String $cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if(strlen($cpf) != 11){
            return false;
        }

        $digitoUm = 0;
        $digitoDois = 0;

        for ($i=0, $x=1; !($i == 9 ); $i++, $x ++) { 
            $digitoUm += $cpf[$i] * $x;
        }

        for ($i=0, $x=0; !($i == 10 ); $i++, $x ++) { 
            if(str_repeat($i, 11) == $cpf){
                return false;
            }

            $digitoDois += $cpf[$i] * $x;
        }

        $calculoUm = (($digitoUm % 11)  == 10) ? 0 : ($digitoUm % 11);
        $calculoDois = (($digitoDois % 11) == 10) ? 0 : ($digitoDois % 11);

        if(($calculoUm != $cpf[9]) || ($calculoDois != $cpf[10])){

            return false;
        }

        
        return true;

    }
    

}