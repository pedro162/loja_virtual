<?php
$routes = [];
$routes[] = ['/', 'HomeController@index'];
$routes[] = ['/home/login', 'HomeController@login'];
$routes[] = ['/home/cadastro', 'HomeController@cadastro'];
$routes[] = ['/produto/show', 'ProdutoController@show'];
$routes[] = ['/produto/detals', 'ProdutoController@detals'];
$routes[] = ['/produto/cadastrar', 'ProdutoController@cadastrar'];
$routes[] = ['/produto/salvar', 'ProdutoController@salvar'];
$routes[] = ['/produto/more', 'ProdutoController@more'];
$routes[] = ['/produto/filtro', 'ProdutoController@filtro'];

$routes[] = ['/venda/carrinho', 'VendaController@addCarrinho'];
$routes[] = ['/venda/iniciar', 'VendaController@iniciarCompra'];

$routes[] = ['/pagar/seguro', 'PagarController@pagar'];
$routes[] = ['/pagar/finesh', 'PagarController@finesh'];

return $routes;