<?php

namespace Pkeogan\Permission\Traits\Methods;


trait PermissionSystemConvertMethods
{
       /**
      * Converts and Array of Permission IDs and Names to a collection of permission objects
      *
      * @param Array &$permissionsInput (Array that contains IDs (Int) and Names (String) of Valid Permissions)
      *
      * @return Collection (Collection of Permissions)
      */
      public function permissionsArrayToCollection(&$permissionsInput)
      {
        $c = collect(); //start a collection
        foreach($permissionsInput as $permission){ //go through permissions
          $c->add($this->getPermissionModel($permission, $guard)); //add the found valid permission to the collection
        }
        return $c; //return the collection
      }
  
     /**
      * Converts and Array of Roles IDs and Names to a collection of role objects
      *
      * @param Array &$rolesInput (Array that contains IDs (Int) and Names (String) of Valid Roles)
      *
      * @return Collection (Collection of Roles)
      */
      public function rolesArrayToCollection(&$rolesInput)
      {
        $c = collect(); //start a collection
        foreach($rolesInput as $role){ //go through permissions
          $c->add($this->getRoleModel($role, $guard)); //add the found valid permission to the collection
        }
        return $c; //return the collection
      }
 
     /**
      * Converts and Array of Users IDs to a collection of user objects
      *
      * @param Array &$usersInput (Array of IDs (INT))
      *
      * @return Collection (Collection of Roles)
      */
      public function usersArrayToCollection(&$usersInput)
      {
        $c = collect(); //start a collection
        foreach($usersInput as $user){ //go through permissions
          $c->add(User::find($user)); //add the found valid permission to the collection
        }
        return $c; //return the collection
      }  
}
