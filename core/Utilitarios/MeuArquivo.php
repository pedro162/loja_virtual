<?php

namespace Core\Utilitarios;

class MeuArquivo
{
    private $path;
    public function __construct($path)
    {
        $this->path = $path;
    }


    public function getContentes()
    {
        return \file_get_contents($this->path);
    }

    public function getExtensions()
    {
        return \pathinfo($this->path, PATHINFO_EXTENSION);
    }


    public function getFileName()
    {
        return \basename($this->path);
    }

    public function getSize()
    {
        return \filesize($this->path);
    }


}