<?php

namespace Pkeogan\Permission\Traits\Methods;



trait PermissionSystemRealtionshipMethods
{
      /**
     * Give an array/collection of roles an array/collection of permissions
     *
     * @param array $permissions
     * @param array $roles
     * @param string|null $guardName
     *
     * @return \Pkeogan\Permission\Contracts\Role|\Pkeogan\Permission\Models\Role
     *
     * @throws \Pkeogan\Permission\Exceptions\RoleDoesNotExist
     */
    public static function givePermissionsToRoles($permissionsParameter, $rolesParameter, $guardName = null)
    {
      if(! $permissionsParam instanceof Illuminate\Database\Eloquent\Collection)
      { 
        // Check if 
        if(is_numeric($permissions))
          {
              $permissions = self::findById($permissions, $guardName);
         
          } else {
              $permissions = self::findByName($permissions, $guardName);
          }
        dd('collection fo objects'); 
      }
      if(! $roles instanceof Illuminate\Database\Eloquent\Collection){ dd('collection fo objects'); }
      foreach($roles as $role)
      {
        $role->syncPermissions($permissions);
      }
        
        $this->forgetCachedPermissions();
    }
}
