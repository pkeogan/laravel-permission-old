<?php

namespace Pkeogan\Permission\Traits\Collections;

use App\Models\Auth\User;
use Illuminate\Support\Collection;
use Pkeogan\Permission\Models\Permission;


trait PermissionCollections
{
       /**
      * Returns a permission object after looking it up via name or int, then checking if its a valid permission object
      *
      * @param mixed $permission
      * @param string $guard
      *
      * @return Permission
      */
      public static function getPermissionModel($permission, $guardName = null)
      {
            if(is_numeric($permission)){
                return Permission::findById($permission, $guardName);
            } 
            if(is_string($permission)){
                return Permission::findByName($permission, $guardName);
            } 
            if(is_a($permission, 'Permission')) {
                return $permission;
            } else {
                throw ModelIsNot::permission();
            }
      }
    
     /**
      * Returns a permission object after looking it up via name or int, if not found returns null. If Permisison object is given, it checks if it is a permission object
      *
      * @param mixed $permission
      * @param string $guard
      *
      * @return Permission
      */
      public static function getPermissionModelOrNull($permission, $gaurdname = null)
      {
            if(is_numeric($permission)){
                return Permission::findByIdOrNull($permission, $guardName);
            } elseif(is_string($permission)){
                return Permission::findByNameOrNull($permission, $guardName);
            } elseif(is_a($permission, 'Permission')) {
                return $permission;
            } else {
                return null;
            }
      }
    
     /**
      * Returns a collection of the permission being looked for and all of its parents
      *
      * @param mixed $permission
      * @param string $guard
      *
      * @return Permission
      */
      public static function getPermissionModelWithParents($permission, $gaurdname = null) 
      {
        $perm = static::getPermissionModel($permission, $gaurdname);
          if($perm->parents == null){return collect($perm);}
        return $perm->parents->push($perm);
      }
    
     /**
      * Returns a collection of the permission being looked for and all of its parents
      *
      * @param mixed $permission
      * @param string $guard
      *
      * @return Permission
      */
      public static function getPermissionModelWithParentsOrNull($permission, $gaurdname = null)
      {
        $perm = static::getPermissionModelOrNull($permission, $gaurdname);
          
        if(! $perm){return null;}
          
        return $perm->parents->push($perm);
      }
    
     /**
      * Returns a collection of the permission being looked for and all of its parents
      *
      * @param mixed $permission
      * @param string $guard
      *
      * @return Permission
      */
      public static function getPermissionIdsWithParents($permission, $gaurdname = null) 
      {
        $perm = static::getPermissionModel($permission, $gaurdname);
          return $perm->idWithParents;
      }
    
     /**
      * Gets all of the permissions a collections of users has. Grabs the user's own permissions, and the users roles's permissions.
      *
      * @param Collection $collection (Collection of Users)
      *
      * @return Collection (Collection of Permissions)
      */
      public static function getPermissionsFromUsers($collection)
      {
        return Permission::whereHas('roles.users', function ($query) use ($collection) {
                  $query->whereIn('id', $collection->pluck('id')->toArray() );
              })->orWhereHas('users', function ($query) use ($collection) {
                  $query->whereIn('id', $collection->pluck('id')->toArray() );
              })->get();
      }
  
     /**
      * Gets all of the users a collections of permissions has.
      *
      * @param Collection $collection (Collection of Users)
      *
      * @return Collection (Collection of Permissions)
      */
      public static function getUsersFromPermissions($collection)
      {
        return User::whereHas('roles.permissions', function ($query) use ($collection) {
                  $query->whereIn('id', $collection->pluck('id')->toArray() );
              })->orWhereHas('permissions', function ($query) use ($collection) {
                  $query->whereIn('id', $collection->pluck('id')->toArray() );
              })->get();
      }
	
	  /**
      * Gets all of the users a collections of permissions has.
      */
      public static function getUsersFromPermission($permission)
      {
		$permission = Permission::findByNameOrNull($permission);
		  		 if($permission == null){return collect(['permission does not exist']);}

		  $c = $permission->users;
		  foreach($permission->roles as $role)
		  {
			  $c = $c->merge($role->users);
		  }
		  return $c;
      }
    
    /**
      * Gets all of the permissions a collections of models. Grabs the models's own permissions, and the model's roles's permissions. then returns them.
      *
      * @param Collection $collection (Collection of Mixed Objects)
      *
      * @return Collection (Collection of Permissions)
      */
      public static function getPermissionsFromModels($collection)
      {
        $c = collect();
        foreach($collection as $item)
        {
          $c->merge();
        }
        return Permission::whereHas('roles.'.$item->getTable(), function ($query) use ($collection) {
                  $query->whereIn('id', $collection->pluck('id')->toArray() );
              })->orWhereHas('users', function ($query) use ($collection) {
                  $query->whereIn('id', $collection->pluck('id')->toArray() );
              })->get();

      }
    
    /**
     *    Get a collection of permissions from a array of permissions name with a unique name that preappenns each permissions
     *
     * @param parameter
     *
     * @return return
     */
    public static function getPermissionsFromArrayWithUniqueName($permissions, $unique, $gaurdname = null)
    {
        $c = collect();
        foreach($permissions as $perm)
        {
            $c->push(static::findByName($unique . $perm));
        }
        return $c;
    }
    
        /**
     *    Get a collection of permissions from a array of permissions name with a unique name that preappenns each permissions
     *
     * @param parameter
     *
     * @return return
     */
    public static function getPermissionsFromArray($permissions, $unique, $gaurdname = null)
    {
        $c = collect();
        foreach($permissions as $perm)
        {
            $c->push(static::findRole($unique . $perm));
        }
        return $c;
    }
    
}
