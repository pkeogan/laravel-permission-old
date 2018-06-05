<?php
  /*
  |----------------------------------------------------------------------------------------------------
  |      __                               __       
  |     / /  ____ __________ __   _____  / /  
  |    / /  / __ `/ ___/ __ `| | / / _ \/ /  
  |   / /__/ /_/ / /  / /_/ /| |/ /  __/ / 
  |  /_____\__,_/_/   \__,_/ |___/\___/_/  
  |----------------------------------------------------------------------------------------------------
  | Laravel Permission System - By Peter Keogan FORKED from spatie/permission-laravel - Link:https://github.com/pkeogan/laravel-permission
  |----------------------------------------------------------------------------------------------------
  |   Title : Permission System Facade Class
  |   Desc  : Facade Support Class
  |   Useage: Please Refer to readme.md 
  | 
  |
  */
namespace Pkeogan\Permission;

/**
 * @see Pkeogan\LaravelPermission
 */
class PermissionSystemFacade extends \Illuminate\Support\Facades\Facade
{    

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'permissionsystem';
    }
   
  
}