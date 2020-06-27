<?php

namespace Core\PagSeguro;

use \Exception;

class PagSeguro
{
    /* --------- PAGSEGURO ----------*/
    const EMAIL_PAGS = 'phedroclooney@gmail.com';
    const TOKEN_PAGS = '5e73-b3af-483a-9e57-2302a14a40c815c453714f228ba4f41ce8ca69ff2c518a93-98e8-44d1-be50-8ed2d4cf6a55';
    const URL_PAGS   = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout/';

    /* --------- SANDBOX ----------*/
    const EMAIL_SANDB  = 'phedroclooney@gmail.com';
    const TOKEN_SANDB  = 'FA3BCBA60FEF46D39C0BEC28A624542F';
    const URL_SANDB    = 'https://ws.pagseguro.uol.com.br/v2/checkout/';

    public function __construct()
    {

    }

    public function obterAltorizacao(array $data, bool $sandbox = false)
    {
        $url = self::URL_PAGS."sessions?email=".self::EMAIL_PAGS."&token=".self::TOKEN_PAGS;

        if((!isset($data)) && (count($data) == 0)){
            throw new Exception("Parâmetro inválido\n");
            
        }

        $data['email'] = self::EMAIL_PAGS;
        $data['token'] = self::TOKEN_PAGS;

        $data = http_build_query($data);//formata para url

        if($sandbox == true){
            $url = self::URL_SANDB."sessions?email=".self::EMAIL_SANDB."&token=".self::TOKEN_SANDB;
            $data['email'] = self::EMAIL_SANDB;
            $data['token'] = self::TOKEN_SANDB;
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded; charset=UTF-8"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $retorno = curl_exec($curl);

        curl_close($curl);
        
        $xml = simplexml_load_string($retorno);
        
        return json_encode($xml);
        
        
    }

    public function extornTransaction(string $transactionCode)
    {

    }

    public function cancelTrasaction(string $transactionCode)
    {
        
    }

    public function consultTransaction()


}