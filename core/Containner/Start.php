<?php

namespace Core\Containner;

use Exception;

class Start
{
    public static function initController($controller, $action, $param = null)
    {
        $clas = "\\App\\Controllers\\".$controller;
        if(!class_exists($clas))
        {
            throw new Exception("Class not fund<br/>");
        }

        if(!method_exists($clas, $action))
        {
            throw new Exception("Method not fund<br/>");
        }

        $obj = new $clas();
        
        if($param != null)
        {
            $obj->$action($param);
        }
        else
        {
            $obj->$action();
        }
        
    }
}