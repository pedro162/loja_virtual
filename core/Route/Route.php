<?php

namespace Core\Route;

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


    private function run()
    {
        $url = $this->getUrl();
        var_dump($url);
    }

}