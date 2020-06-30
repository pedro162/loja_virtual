<?php
$routes = [];
$routes[] = ['/', 'HomeController@index'];
$routes[] = ['/home/login', 'HomeController@indexLogin'];
$routes[] = ['/home/login/logar', 'HomeController@loginUser'];
$routes[] = ['/home/login/logout', 'HomeController@logoutUser'];
$routes[] = ['/home/cadastro', 'HomeController@cadastro'];
$routes[] = ['/home/admin', 'HomeController@painel'];
$routes[] = ['/home/admin/inicio', 'HomeController@inicoPainel'];
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
$routes[] = ['/produto/voto', 'ProdutoController@votar'];

$routes[] = ['/produto/comentar', 'ComentarioController@comentar'];
$routes[] = ['/produto/load/comentarios', 'ComentarioController@loadComentFormProduto'];

$routes[] = ['/estoque/lancar', 'FornecimentoController@lancarEstoque'];
$routes[] = ['/estoque/salvar', 'FornecimentoController@salvar'];
$routes[] = ['/estoque/all', 'FornecimentoController@all'];
$routes[] = ['/estoque/editar', 'FornecimentoController@editar'];
$routes[] = ['/estoque/atualizar', 'FornecimentoController@atualizar'];

$routes[] = ['/marca/cadastrar', 'MarcaController@cadastrar'];
$routes[] = ['/marca/salvar', 'MarcaController@salvar'];

$routes[] = ['/categoria/cadastrar', 'CategoriaController@cadastrar'];
$routes[] = ['/categoria/salvar', 'CategoriaController@salvar'];
$routes[] = ['/categoria/load', 'CategoriaController@loadCategoria'];

$routes[] = ['/pedido/carrinho', 'PedidoController@addCarrinho'];
$routes[] = ['/pedido/carrinho/finsh', 'PedidoController@fineshPedido'];
$routes[] = ['/pedido/iniciar', 'PedidoController@iniciarCompra'];
$routes[] = ['/pedido/novo', 'PedidoController@novo'];
$routes[] = ['/pedido/painel', 'PedidoController@painel'];
$routes[] = ['/pedido/load/pessoa', 'PedidoController@loadPessoa'];
$routes[] = ['/pedido/load/estoque', 'PedidoController@loadEstoque'];
$routes[] = ['/pedido/save/pedido', 'PedidoController@savePedido'];
$routes[] = ['/pedido/produto/detalhes', 'PedidoController@detalhesOfProduto'];
$routes[] = ['/pedido/produto/frete', 'PedidoController@calcFrete'];
$routes[] = ['/pedido/produto/vermais', 'PedidoController@viewMore'];
$routes[] = ['/pedido/pagamento', 'PedidoController@pedidoPagar'];
$routes[] = ['/pedido/save/loja', 'PedidoController@savePedidoLoja'];

$routes[] = ['/usuario/index', 'UserController@index'];
$routes[] = ['/usuario/login', 'UserController@loginAdmin'];

//$routes[] = ['/financeriro/receber', 'VendaController@iniciarCompra'];
//$routes[] = ['/financeriro/pagar', 'VendaController@iniciarCompra'];

$routes[] = ['/pagar/seguro', 'PagarController@pagar'];
$routes[] = ['/pagar/finesh', 'PagarController@finesh'];

return $routes;