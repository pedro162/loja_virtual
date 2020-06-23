<?php 

namespace App\Controllers;

use Exception;
use \Core\Database\Transaction;
use Core\Utilitarios\Sessoes;

class BaseController
{
    protected $view;
    protected $menu;
    protected $footer;

    public function __construct()
    {
        $this->view = new \stdClass();
               
    }



    public function render($view_path, $layoute = true, $fileLayout = 'layout')
    {
        if(($layoute == true) && (file_exists('../app/Views/layout/'.$fileLayout.'.phtml')))
        {
            require_once  '../app/Views/layout/'.$fileLayout.'.phtml';
            
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
        }else{
            die('Não existe mennu');
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
            $this->footer = '../app/Views/layout/'.$path.'.phtml';
        }
    }

    public function loadFooter()
    {
       require_once $this->footer;
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