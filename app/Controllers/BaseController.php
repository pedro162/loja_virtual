<?php 

namespace App\Controllers;

class BaseController
{
    protected $view;
    protected $action;

    public function __construct()
    {
        $this->view = new \stdClass();
    }


    public function render($action, $layoute = true)
    {
        $this->action = $action;
        if(($layoute == true) && (file_exists('../app/Views/layout.phtml')))
        {
            include_once '../app/Views/layout.phtml';
        }
        else
        {
            $this->content();
        }
        
    }


    public function content()
    {
        $atual = get_class($this);
        $singleClassName = strtolower(str_replace("App\\Controllers\\", "", $atual));
        include_once '../app/Views/'.$singleClassName.'/'.$this->action.'.phtml';
    }

}