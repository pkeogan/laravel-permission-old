<?php

namespace Pkeogan\Permission\Traits\Collections;

use Pkeogan\Permission\Guard;
use Illuminate\Support\Collection;
use Pkeogan\Permission\Models\Role;


trait RoleCollections
{
	
	  public static function getUsersFromName($role)
      {
		$role = Role::findByNameOrNull($role);
	 	if($role == null){return collect(['Role does not exist']);}
		  
		  return $role->users();
      }
	
    /**
     * Find all the roles of the given permissions, then return an array of the roles that have the given permissions.
     *
     * @param array $permissions
     * @param string|null $guardName
     *
     * @throws \Pkeogan\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return Array of Roles
     */
    public static function getAllRolesFromPermissions($permissions, $guardName = null)
    {
        $allRoles = Role::pluck('name', 'id')->all(); //Get all of the current roles, this array will be returned at the end
        $guardName = $guardName ?? Guard::getDefaultName(static::class);  //set the guard we are workong on
        foreach($permissions as $key=>$permission) // Loop through all the permissions given
        {
          //Check and see if the given permission is a number, it it is, we are calling it by the ID, if not, we are calling it by the name
          if(is_numeric($permission))
          {
              $permission = self::findById($permission, $guardName);
         
          } else {
              $permission = self::findByName($permission, $guardName);
          }
          //Now that we have a valid permisson, lets loop through our list of roles left
          foreach($allRoles as $roleID=>$roleName)
          {
            // Check and see if the role belongs 
            if(! array_key_exists($roleID, $permission->roles->pluck('id')->all()))
            { 
              unset($allRoles[$roleID]);
            }
          }
        }
      return $allRoles;
    }
     /* Returns a Role object after looking it up via name or int, then checking if its a valid Role Object
      *
      * @param mixed $permission
      * @param string $guard
      *
      * @return Permission
      */
      public static function getRoleModel($role, $gaurdname = null)
      {
                  $guardName = $guardName ?? Guard::getDefaultName(static::class);  //set the guard we are workong on
          
            if(is_numeric($role)){
                return static::findById($role, $guardName);
            } 
            if(is_string($role)){
                return static::findByName($role, $guardName);
            } 
            if(is_a($role, 'Role')) {
                return $role;
            } else {
                throw ModelIsNot::role();
            }
      }
    
    /**
     * Get a collection of roles from a array of ids
     *
     * @param parameter
     *
     * @return return
     */
    public static function getRolesFromArray($roles, $gaurdname = null)
    {   
        $c = collet();
        foreach($roles as $role)
        {
            $c->push(static::getRoleModel($role, $gaurdname));
        }
        return $c;
    }
}