<?php

namespace App\Controllers;

use App\Controllers\BaseController;

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
}
