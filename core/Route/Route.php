<?php

namespace Core\Route;

use Exception;
use Core\Containner\Start;

class Route
{
    private $routes;

    public function __construct(array $newRoutes)
    {
        $this->routes = $newRoutes;
        $this->run();
    }

    private function getUrl()
    {
        return parse_url($_SERVER['REQUEST_URI'] , PHP_URL_PATH);
    }

    private function request()
    {
        $array_request = [];
        if($_SERVER['REQUEST_METHOD'] ==  'POST')
        {
            $array_request['post'] = $_POST;
        }

        if($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $array_request['get'] = $_GET;
        }

        if(count($array_request) > 0)
        {
            return $array_request;
        }
        return false;
    } 


    private function run()
    {
        $url = $this->getUrl();
        
        $array_file_url = $this->routes;


        $array_class_method = null;

        $sentinala_rota = false;

        foreach($array_file_url as $array_route)
        {
            for($i =0; !($i == count($array_route)); $i++)
            {
                if($array_route[0] == $url)
                {
                    $array_class_method = explode('@',$array_route[1]);
                    $sentinala_rota = true;
                break;
                }

            }
        }

        if($sentinala_rota == false)
        {
            throw new Exception("Rota inváldida<br/>\n");
        }
        else
        {   
            if(($request = $this->request()) != false)
            {
                Start::initController($array_class_method[0], $array_class_method[1], $request);
            }
            else
            {
                Start::initController($array_class_method[0], $array_class_method[1]);
            }
        }
    }

}