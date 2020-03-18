<?php

namespace App\Models;
use \App\Models\BaseModel;

class Cliente extends BaseModel
{
    protected $table = 'Clientes';

    public function __construct()
    {
       self::open();
    }

    
}
