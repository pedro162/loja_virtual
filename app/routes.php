<?php
$routes = [];
$routes[] = ['/', 'HomeController@index'];
$routes[] = ['/produtos/show', 'ProdutoController@show'];
$routes[] = ['/produto/detals', 'ProdutoController@detals'];

return $routes;