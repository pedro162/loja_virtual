<?php

    try{

        $routes = __DIR__.'/routes.php';
        if(file_exists($routes))
        {   
            $arrayRoutes = require_once $routes;

            if(is_array($arrayRoutes))
            {
                $routeObject = new \Core\Route\Route($arrayRoutes);

            }
            else
            {
                die("Erro: defina o array de rotas!");
            }

        }
        else
        {
            die("File not fund routes.php");
        }

    }catch(\Exception $e){
        echo"Erro: ". $e->getMessage().'<br/>'.PHP_EOL;
        echo"Arquivo: ". $e->getFile().'<br/>'.PHP_EOL;
        echo"Linha: ". $e->getLine().'<br/>'.PHP_EOL;
    }
