<?php

namespace Pkeogan\Permission\Traits\Methods;

trait RoleRelationshipMethods
{
	
	 /**
      * Give a role a collection of permissions
      *
      * @param Role $role 
      * @param Collection $permissions
      */
      public static function assignRoleThesePermissions($role, $permissions)
      {
		  if($role == null)
		  {
		  	return;
		  } else {
			static::getRoleModel($role)->givePermissionTo($permissions);			  
		  }
      }
	
		/**
      * Give a role a collection of permissions
      *
      * @param Role $role 
      * @param Collection $permissions
      */
      public static function syncRolesWithPermissions($roles, $permissions)
      {
		  foreach($roles as $role)
		  {
			  static::getRoleModel($role)->syncPermissions($permissions);
		  }
      }
	
	/**
      * Give a role a collection of permissions
      *
      * @param Role $role 
      * @param Collection $permissions
      */
      public static function assignRolesThesePermissions($roles, $permissions)
      {
		  if($roles == null)
		  {
			  return;
		  }
		  if(! is_array($roles))
		  {
			return static::getRoleModel($roles)->givePermissionTo($permissions);
		  }
		  foreach($roles as $role)
		  {
			  static::getRoleModel($role)->givePermissionTo($permissions);
		  }
      }
	
	  public static function assignRolesThesePermissionsWithUniqueName($roles, $permissions, $unique)
      {		  
		  foreach($roles as $role)
		  {  
		  	foreach($permissions as $perm)
			  {
				  static::getRoleModel($role)->givePermissionTo($unique . $perm);
			  }
		  }
      }
}
