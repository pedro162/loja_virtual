<?php
$routes = [];
$routes[] = ['/', 'HomeController@index'];
$routes[] = ['/home/login', 'HomeController@login'];
$routes[] = ['/home/cadastro', 'HomeController@cadastro'];
$routes[] = ['/produtos/show', 'ProdutoController@show'];
$routes[] = ['/produto/detals', 'ProdutoController@detals'];

return $routes;