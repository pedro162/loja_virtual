<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\Produto;
use App\Models\Fabricante;
use App\Models\Cliente;

class ProdutoController extends BaseController
{
    public function show()
    {
        $cliente = new Cliente();
        //$string = $cliente->select(['nomeCliente', 'dtNascimento','cpf'], ['nomeCliente'=>'Pedro aguiar Ferreira'], '=');
        //echo $string.'<br/>';

        //$delete = $cliente->delete(1, 1);
        //echo $delete.'<br/>';

        //$insert = $cliente->insert(['nomeCliente' => 'Pedro aguiar Ferreira', 'dtNascimento'=> '1996-03-02', 'cpf'=>'61224450370']);
        //echo "Inserido com sucesso<br/>";
        //$update = $cliente->update(['nomeCliente' => 'Jose Pedro aguiar Ferreira', 'dtNascimento'=> '2020-03-02'], 3);
       /* if($update === true)
        {
        	echo "Atualizou<br/>\n";
        }*/
        /*
        	echo "<pre>";
        	var_dump($string);
        	echo "</pre>";*/
        

        /*
        echo "<pre>";
        var_dump($insert);
        echo "</pre>";
        */

        //echo $insert.'<br/>';

        //$update = $cliente->update(['nome' => 'Pedro aguiar Ferreira', 'nascimento'=> '2020-03-02'], 50);
        //echo $update."<br/>";
        //$this->render('produtos/produto', true);
    }
}