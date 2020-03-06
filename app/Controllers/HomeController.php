<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        echo "Instanciei meu controller e chamei meu metodo<br/>\n";
    }
}
