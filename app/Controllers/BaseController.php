<?php 

namespace App\Controllers;

use Exception;

class BaseController
{
    protected $view;

    public function __construct()
    {
        $this->view = new \stdClass();
    }


    public function render($view_path, $layoute = true)
    {
        if(($layoute == true) && (file_exists('../app/Views/layout/layout.phtml')))
        {
            require_once  '../app/Views/layout/layout.phtml';
            
        }
        else
        {
            $this->content($view_path);
        }
        
    }

    public function loadMenu(String $path = 'standardMenu')
    {
        if((!empty($path)) && (file_exists('../app/Views/layout/'.$path.'.phtml')))
        {
            require_once '../app/Views/layout/'.$path.'.phtml';
        }
    }


    public function content($path_view)
    {
        $view = '../app/Views/'.$path_view.'.phtml';
        
        if(file_exists($view))
        {
            require_once $view;
        }
        else
        {
            throw new Exception("View indefinida<br/>\n");
        }
    }

}