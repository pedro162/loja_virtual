<?php

namespace App\Controllers;

use Core\PagSeguro\PagSeguro;
use App\Controllers\BaseController;

class PagarController extends BaseController
{
    private static $email;
    private static $token;
    private static $url;
    private static $script;

    public function pagar()
    {
        $this->setScript(false);
        $this->render('pagamentos/pagamento', true);
    }

    public function finesh()
    {
        echo "Pagar";
        self::startPagSeguro(false);
    }

    public static function startPagSeguro(bool $producao = false)
    {
        self::$email = 'phedroclooney@gmail.com';
        self::$token = 'b9ad5e73-b3af-483a-9e57-2302a14a40c815c453714f228ba4f41ce8ca69ff2c518a93-98e8-44d1-be50-8ed2d4cf6a55';
        self::$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/';
        if($producao == true)
        {
            self::$email = 'phedroclooney@gmail.com';
            self::$token = 'b9ad5e73-b3af-483a-9e57-2302a14a40c815c453714f228ba4f41ce8ca69ff2c518a93-98e8-44d1-be50-8ed2d4cf6a55';
            self::$url = 'https://ws.pagseguro.uol.com.br/v2/';
            
        }
        
        $pagSeguro = new PagSeguro(self::$email, self::$token, self::$url, self::$script);
    }

    public static function getScript()
    {
        return self::$script;
    }

    private function setScript($producao = false)
    {
        self::$script = 'https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';

        if($producao == true)
        {
            self::$script = 'https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';
        }
        
    }
}