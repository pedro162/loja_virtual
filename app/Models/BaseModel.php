<?php

namespace App\Models;

use Core\Database\Connection;
use InvalidArgumentException;
use \PDO;

abstract class BaseModel
{
    private static $conn;

    public static function open()
    {
        if(empty(self::$conn))
        {
            self::$conn = Connection::open('connection');
        }

        return self::$conn;
    }

    
}