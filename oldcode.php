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

/**
 * Checks if any of the given models have any of the given permissions.
 *
 * @param mixed  $models 
 * @param mixed  $permissions 
 * @param string $guard
 *
 * @return boolean
 */
function anyModelsHaveAnyPermissions($models, $permissions, $gaurdname = null)
{
    $guardName = $guardName ?? Guard::getDefaultName(static::class);
  
    foreach($models as $model) // Loop through all the given models
        {
          
          foreach($permissions as $permission) // loop through all of the permissions
          {
             if(is_numeric($permission)){$permission = Permission::findById($permission, $guardName);} //find permission by ID if given a number
             elseif(is_string($permission)){$permission = Permission::findByName($permission, $guardName);} //find permission by Name if given a string
            // Check and see if the role belongs 
            if(! array_key_exists($roleID, $permission->roles->pluck('id')->all()))
            { 
              unset($allRoles[$roleID]);
            }
          }
        }
      return $allRoles;
}


  public function camelCaseToArray(&$string)
  {
    return $pieces = preg_split('/(?=[A-Z])/', $string);
  }