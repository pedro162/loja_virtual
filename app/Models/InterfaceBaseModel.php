<?php

namespace App\Models;

interface InterfaceBaseModel{
	public function __get($value);
	public function __set($prop, $value);
}