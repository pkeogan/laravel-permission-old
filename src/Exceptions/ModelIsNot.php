<?php

namespace Pkeogan\Permission\Exceptions;

use InvalidArgumentException;

class ModelIsNot extends InvalidArgumentException
{
    public static function permission()
    {
        return new static("The given Model is not a Permission!");
    }

    public static function role()
    {
        return new static("The given Model is not a Role!");
    }
  
    public static function user()
    {
        return new static("The given Model is not a User!");
    }
}
