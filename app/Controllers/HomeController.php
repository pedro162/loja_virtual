<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Fabricante;

class HomeController extends BaseController
{
    public function index()
    {
        $this->render('home/home', true);
    }

    public function login()
    {
        $this->render('login/login', true);
    }

    public function cadastro()
    {
        $this->render('login/cadastro', true);
    }
}
