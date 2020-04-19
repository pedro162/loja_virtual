<?php 

namespace App\Controllers;

use Exception;

class BaseController
{
    protected $view;
    protected $menu;
    protected $footer;

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

    public function setMenu(String $path = 'standardMenu')
    {
        if((!empty($path)) && (file_exists('../app/Views/layout/'.$path.'.phtml')))
        {
            $this->menu = '../app/Views/layout/'.$path.'.phtml';
        }
    }

    public function loadMenu()
    {
        require_once $this->menu;
    }

    public function setFooter(String $path = 'standardFooter')
    {
        if((!empty($path)) && (file_exists('../app/Views/layout/'.$path.'.phtml')))
        {
            $this->menu = '../app/Views/layout/'.$path.'.phtml';
        }
    }

    public function loadFooter($value='')
    {
       require_once $this->menu;
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