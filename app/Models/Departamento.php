<?php

namespace App\Models;

use App\Models\Models;
use \Exception;
use \InvalidArgumentException;

class Departamento extends BaseModel
{
	protected $table = 'Departamento';

    public function __construct()
    {
        self::open();
    }

}