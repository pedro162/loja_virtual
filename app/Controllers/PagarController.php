<?php

namespace App\Controllers;

use Core\PagSeguro\PagSeguro;
use App\Controllers\BaseController;
use \Core\Database\Transaction;

class PagarController extends BaseController
{
    public function pagar($request)
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
        //$pgSeg->obterAltorizacao($dados, false);
        $this->view->result = json_encode($pgSeg->getSession());
        $this->render('pagamentos/ajax', true);
    }

    public function finesh()
    {
       
        $this->render('pagamentos/sessaoPagseguro', false);
        
    }

}