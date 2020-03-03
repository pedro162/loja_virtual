<?php

namespace Core\Database;

use Exception;

final class Connection
{   
    public function __construct($file)
    {

        if(!file_exists($file.'.ini'))
        {
            throw new Exception("Arquivo {$file}.ini não encontrado<br/>\n");
            
        }
        
        $file_connection = parse_ini_file("{$file}.ini");
        
    }

    
}