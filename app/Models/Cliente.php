<?php

namespace App\Models;
use \App\Models\BaseModel;

class Cliente extends BaseModel
{
    private $table = 'Cliente';

    public function __construct()
    {
       var_dump(self::open());
    }
}
