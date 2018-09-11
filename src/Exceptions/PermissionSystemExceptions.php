<?php

namespace Pkeogan\Permission\Exceptions;

use InvalidArgumentException;

class PermissionSystemExceptions extends InvalidArgumentException
{
    public static function command()
    {
        return new static("Permission System Error: First command not given");
    }
  
    public static function doesError($string)
    {
        return new static("Permission System Error: " . $string . " Is not a valid middle verb for does(), use all or any only. ");
    }
  
    public static function objectAlreadySet($string)
    {
        return new static("Permission System Error: Object: " . $string . " has already been set. your not allowed to check things of itself. ");
    }
  
    public static function render($string)
    {
        return new static("Permission System Error: Rendering Error: ");
    }
  
    public static function modelsMustBeCollection($string)
    {
        return new static("Permission System Error: IF Models are given, they must be a collection ");
    }
  
    public static function unkObjects($string)
    {
        return new static("Permission System Error: unkObjects ");
    }

}
