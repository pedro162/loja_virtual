<?php

namespace Core\PagSeguro;

class PagSeguro
{
    private $email;
    private $token;
    private $url;

    public function __construct($email, $token, $url)
    {
        $this->email = $email;
        $this->token = $token;
        $this->url = $url;
    }

    public function obterAltorizacao()
    {
        $url = $this->url."sessions?email=".$this->email."&token=".$this->token;
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded; charset=UTF-8"));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $retorno = curl_exec($curl);

        curl_close($curl);
        
        $xml = simplexml_load_string($retorno);
        
        return json_encode($xml);
        
        
    }

    public function pagar()
    {
        $url = $this->url."?email=".$this->email."&token=".$this->token;
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $retorno = curl_exec($curl);

        curl_close($curl);

        $xml = simplexml_load_string($retorno);

        return json_encode($retorno);
    }
}