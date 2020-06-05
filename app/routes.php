<?php
$routes = [];
$routes[] = ['/', 'HomeController@index'];
$routes[] = ['/home/login', 'HomeController@login'];
$routes[] = ['/home/cadastro', 'HomeController@cadastro'];
$routes[] = ['/home/admin', 'HomeController@painel'];
$routes[] = ['/home/admin/inicio', 'HomeController@inicoPainel'];
$routes[] = ['/home/teste', 'HomeController@teste'];
$routes[] = ['/home/menu', 'HomeController@menu'];

$routes[] = ['/produto/all', 'ProdutoController@all'];
$routes[] = ['/produto/show', 'ProdutoController@show'];
//$routes[] = ['/produto/detalhes', 'ProdutoController@detalhes'];
$routes[] = ['/produto/cadastrar', 'ProdutoController@cadastrar'];
$routes[] = ['/produto/salvar', 'ProdutoController@salvar'];
$routes[] = ['/produto/more', 'ProdutoController@more'];
$routes[] = ['/produto/filtro', 'ProdutoController@filtro'];
$routes[] = ['/produto/editar', 'ProdutoController@editarProduto'];
$routes[] = ['/produto/atualizar', 'ProdutoController@atualizar'];

$routes[] = ['/estoque/lancar', 'FornecimentoController@lancarEstoque'];
$routes[] = ['/estoque/salvar', 'FornecimentoController@salvar'];
$routes[] = ['/estoque/all', 'FornecimentoController@all'];
$routes[] = ['/estoque/editar', 'FornecimentoController@editar'];
$routes[] = ['/estoque/atualizar', 'FornecimentoController@atualizar'];
$routes[] = ['/estoque/detalhes', 'FornecimentoController@detalhes'];

$routes[] = ['/marca/cadastrar', 'MarcaController@cadastrar'];
$routes[] = ['/marca/salvar', 'MarcaController@salvar'];

$routes[] = ['/categoria/cadastrar', 'CategoriaController@cadastrar'];
$routes[] = ['/categoria/salvar', 'CategoriaController@salvar'];

$routes[] = ['/pedido/carrinho', 'PedidoController@addCarrinho'];
$routes[] = ['/pedido/iniciar', 'PedidoController@iniciarCompra'];
$routes[] = ['/pedido/novo', 'PedidoController@novo'];
$routes[] = ['/pedido/painel', 'PedidoController@painel'];
$routes[] = ['/pedido/load/pessoa', 'PedidoController@loadPessoa'];
$routes[] = ['/pedido/load/estoque', 'PedidoController@loadEstoque'];
$routes[] = ['/pedido/save/pedido', 'PedidoController@savePedido'];

//$routes[] = ['/financeriro/receber', 'VendaController@iniciarCompra'];
//$routes[] = ['/financeriro/pagar', 'VendaController@iniciarCompra'];

$routes[] = ['/pagar/seguro', 'PagarController@pagar'];
$routes[] = ['/pagar/finesh', 'PagarController@finesh'];

return $routes;