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
        //$dados['email'] = 'email@test.com.br';
        //$dados['token'] = 'token';
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
        $this->view->result = json_encode($pgSeg->getSession());
        $this->render('pagamentos/ajax', true);
    }

    public function finesh()
    {
        $TokenCard = $_POST['TokenCard'];
        $HashCard = $_POST['HashCard'];
        $QtdParcelas = $_POST['QtdParcelas'];
        $ValorParcelas = $_POST['ValorParcelas'];

        $dados = [];
        $dados['currency'] = 'BRL';
        $dados['itemId1'] = '1';
        $dados['itemDescription1'] = 'descricao do produto';
        $dados['itemAmount1'] = '500.00';//valor do produto sem virgula ex:1000.52 
        $dados['itemQuantity1'] = 'quantidade do iten';
        $dados['reference'] = 'numero de referencia qualquer';
        $dados['senderName'] = 'nome cliente';
        $dados['senderAreaCode'] = 'codigo da area do telefone cliente';
        $dados['senderPhone'] = 'numero do telefone cliente';
        $dados['senderEmail'] = 'email do cliente';
        $dados['shippingAddressRequired'] = true;
        $dados['extraAmount'] = '0.00';


        $dados['paymentMethod'] = 'creditCard';
        $dados['currency'] = 'BRL';
        $dados['extraAmount'] = '1.00';
        $dados['itemId1'] = '1';
        $dados['itemDescription1'] = 'Notebook Prata';
        $dados['itemAmount1'] = '24300.00';
        $dados['itemQuantity1'] = '1';
        $dados['notificationURL'] = '/url/para/emu/site';
        $dados['reference'] = 'REF1234';
        $dados['senderName'] = 'Jose Comprador';
        $dados['senderCPF'] = '22111944785';
        $dados['senderAreaCode'] = '98';
        $dados['senderPhone'] = '56273440';
        $dados['senderEmail'] = 'comprador@uol.com.br' ;
        $dados['senderHash'] = $HashCard ;
        $dados['shippingAddressRequired'] = 'true' ;
        $dados['shippingAddressStreet'] = 'Av. Brig. Faria Lima' ;
        $dados['shippingAddressNumber'] = '1384' ;
        $dados['shippingAddressComplement'] = '5o andar' ;
        $dados['shippingAddressDistrict'] = 'Jardim Paulistano' ;
        $dados['shippingAddressPostalCode'] = '01452002' ;
        $dados['shippingAddressCity'] = 'Sao Luis' ;
        $dados['shippingAddressState'] = 'MA' ;
        $dados['shippingAddressCountry'] = 'BRA' ;
        $dados['shippingType'] = '1' ; //dedex ; pact /ouros
        $dados['shippingCost'] = '0.00' ;
        $dados['creditCardToken'] = $TokenCard ;
        $dados['installmentQuantity'] = $QtdParcelas ;
        $dados['installmentValue'] = $ValorParcelas ;
        $dados['noInterestInstallmentQuantity'] = '2' ; // quantidade de parcelas sem juros para o cliente
        $dados['creditCardHolderName'] = 'Jose Comprador' ;
        $dados['creditCardHolderCPF'] = '22111944785' ;
        $dados['creditCardHolderBirthDate'] = '27/10/1987' ;
        $dados['creditCardHolderAreaCode'] = '11' ;
        $dados['creditCardHolderPhone'] = '56273440' ;
        $dados['billingAddressStreet'] = 'Av. Brig. Faria Lima' ;
        $dados['billingAddressNumber'] = '1384' ;
        $dados['billingAddressComplement'] = '5o andar' ;
        $dados['billingAddressDistrict'] = 'Jardim Paulistano' ;
        $dados['billingAddressPostalCode'] = '01452002' ;
        $dados['billingAddressCity'] = 'Sao Luis' ;
        $dados['billingAddressState'] = 'MA' ;
        $dados['billingAddressCountry'] = 'BRA' ;

        $pgSeg = new PagSeguro();
        $pgSeg->executarTransaction($dados, false);
        $this->view->result = json_encode($pgSeg->getSession());
        $this->render('pagamentos/ajax', true);
    }

}