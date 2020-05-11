<?php
$routes = [];
$routes[] = ['/', 'HomeController@index'];
$routes[] = ['/home/login', 'HomeController@login'];
$routes[] = ['/home/cadastro', 'HomeController@cadastro'];
$routes[] = ['/home/admin', 'HomeController@painel'];
$routes[] = ['/home/teste', 'HomeController@teste'];
$routes[] = ['/home/menu', 'HomeController@menu'];

$routes[] = ['/produto/all', 'ProdutoController@all'];
$routes[] = ['/produto/show', 'ProdutoController@show'];
$routes[] = ['/produto/detals', 'ProdutoController@detals'];
$routes[] = ['/produto/cadastrar', 'ProdutoController@cadastrar'];
$routes[] = ['/produto/salvar', 'ProdutoController@salvar'];
$routes[] = ['/produto/more', 'ProdutoController@more'];
$routes[] = ['/produto/filtro', 'ProdutoController@filtro'];
$routes[] = ['/produto/editar', 'ProdutoController@editarProduto'];

$routes[] = ['/estoque/lancar', 'FornecimentoController@lancarEstoque'];
$routes[] = ['/estoque/salvar', 'FornecimentoController@salvar'];

$routes[] = ['/marca/cadastrar', 'MarcaController@cadastrar'];

$routes[] = ['/venda/carrinho', 'VendaController@addCarrinho'];
$routes[] = ['/venda/iniciar', 'VendaController@iniciarCompra'];
$routes[] = ['/venda/nova', 'VendaController@nova'];

//$routes[] = ['/financeriro/receber', 'VendaController@iniciarCompra'];
//$routes[] = ['/financeriro/pagar', 'VendaController@iniciarCompra'];

$routes[] = ['/pagar/seguro', 'PagarController@pagar'];
$routes[] = ['/pagar/finesh', 'PagarController@finesh'];

return $routes;