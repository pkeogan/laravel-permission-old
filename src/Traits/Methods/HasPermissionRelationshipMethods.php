<?php

namespace Pkeogan\Permission\Traits\Methods;



trait HasPermissionRelationshipMethods
{
     /**
     * Batch creates permissions with asscoated parent permissions.
     *
     * @param string|array|\Pkeogan\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return $this
     */
    public function makeModelPermissions($config, $parentUniqueName, $gaurdName = null)
    { 
        foreach(config($config) as $permissionName)
        {
          $parentPermission = Permission::findOrFail(['name' => $parentUniqueName . $permissionName, 'guard_name' => $gaurdName]);
          $permission = Permission::create(['name' => $this->uniqueName . $permissionName, 'guard_name' => $gaurdName, 'parent_id' => $parentPermission->id]);
          $this->permissions()->save($permission);
        }
        
        $this->forgetCachedPermissions();
    }
  
   /**
     * Grant the given permission(s) to a role.
     *
     * @param string|array|\Pkeogan\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return $this
     */
    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                return $this->getStoredPermission($permission);
            })
            ->filter(function ($permission) {
                $this->ensureModelSharesGuard($permission);
                if($this->hasPermission($permission)){return false;}else{return true;}
            })
            ->all();
        

        $this->permissions()->saveMany($permissions);

        $this->forgetCachedPermissions();

        return $this;
    }
  
   /**
     * Remove all current permissions and set the given ones.
     *
     * @param string|array|\Pkeogan\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return $this
     */
    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions);
    }

    /**
     * Revoke the given permission.
     *
     * @param \Pkeogan\Permission\Contracts\Permission|\Pkeogan\Permission\Contracts\Permission[]|string|string[] $permission
     *
     * @return $this
     */
    public function revokePermissionTo($permission)
    {
        $this->permissions()->detach($this->getStoredPermission($permission));

        $this->forgetCachedPermissions();

        return $this;
    }

  
}
