<?php

namespace App\Models;
use \App\Models\BaseModel;

class Cliente extends BaseModel
{
    protected $table = 'Cliente';

    public function __construct()
    {
       self::open();
    }

    
}
