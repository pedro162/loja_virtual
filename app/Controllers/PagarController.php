<?php

namespace App\Controllers;

use Core\PagSeguro\PagSeguro;
use App\Controllers\BaseController;
use \Core\Database\Transaction;

class PagarController extends BaseController
{
    public function pagar()
    {
        $dados = [];
        $dados['email'] = 'email@test.com.br';
        $dados['token'] = 'token';
        $dados['currency'] = 'BRL';
        $dados['itemId1'] = '1';
        $dados['itemDescription1'] = 'descricao do produto';
        $dados['itemAmount1'] = 'valor do produto sem virgula ex:1000.52';
        $dados['itemQuantity1'] = 'quantidade do iten';
        $dados['reference'] = 'numero de referencia qualquer';
        $dados['senderName'] = 'nome cliente';
        $dados['senderAreaCode'] = 'codigo da area do telefone cliente';
        $dados['senderPhone'] = 'numero do telefone cliente';
        $dados['senderEmail'] = 'email do cliente';
        $dados['shippingAddressRequired'] = true;
        $dados['extraAmount'] = '0.00';

        $pgSeg = new PagSeguro();
        $pgSeg->obterAltorizacao($dados, false);

        $this->render('pagamentos/pagamento', true);
    }

    public function finesh()
    {
       
        $this->render('pagamentos/sessaoPagseguro', false);
        
    }
    /*curl -X POST \
  'https://ws.pagseguro.uol.com.br/v2/checkout?email=email@email.com&token=token' \
  -H 'Content-Type: application/x-www-form-urlencoded' \
  -d 'email=email%40email.com&token=token&currency=BRL&itemId1=001&itemDescription1=Item%201&itemAmount1=169.90&itemQuantity1=1&reference=124665c23f7896adff508377925&senderName=Natalie%20Green&senderAreaCode=51&senderPhone=988888888&senderEmail=emaildocomprador@pagseguro.com.br&shippingAddressRequired=true&extraAmount=0.00*/

}