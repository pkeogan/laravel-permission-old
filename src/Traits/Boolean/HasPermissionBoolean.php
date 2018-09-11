<?php

namespace Pkeogan\Permission\Traits\Boolean;

use Illuminate\Support\Collection;
use Pkeogan\Permission\Contracts\Role;
use Illuminate\Database\Eloquent\Builder;
use Pkeogan\Permission\Models\Permission;

trait HasPermissionBoolean
{
      /**
         * Determine if the model may perform the given permission.
         *
         * @param string|Permission $permission
         * @param string|null $guardName
         *
         * @return bool
         */
        public function hasPermission($permission, $guardName = null): bool
        {
            if (is_string($permission)) {
                $permission = app(Permission::class)->findOrCreate(
                    $permission,
                    $guardName ?? $this->getDefaultGuardName()
                );
            }

            if (is_int($permission)) {
                $permission = app(Permission::class)->findByIdOrCreate($permission, $this->getDefaultGuardName());
            }

            return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
        }
  
        /**
         * Determine if the model may perform the given permission.
         *
         * @param string|Permission $permission
         * @param string|null $guardName
         *
         * @return bool
         */
        public function hasPermissionTo($permission, $guardName = null): bool
        {
             return $this->hasPermission($permission, $guardName);
        }
  
    

        /**
         * Determine if the model has any of the given permissions.
         *
         * @param array ...$permissions
         *
         * @return bool
         */
        public function hasAnyPermission(...$permissions): bool
        {
            if (is_array($permissions[0])) {
                $permissions = $permissions[0];
            }

            foreach ($permissions as $permission) {
                if ($this->hasPermissionTo($permission)) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Determine if the model has, via roles, the given permission.
         *
         * @param Permission $permission
         *
         * @return bool
         */
        protected function hasPermissionViaRole(Permission $permission): bool
        {
            if($this instanceof Role){return false;}
            return $this->hasRole($permission->roles);
        }

        /**
         * Determine if the model has the given permission directly.
         *
         * @param string|Permission $permission
         *
         * @return bool
         */
        public function hasDirectPermission($permission): bool
        {
            if (is_string($permission)) {
                $permission = app(Permission::class)->findByName($permission, $this->getDefaultGuardName());
                if (! $permission) {
                    return false;
                }
            }

            if (is_int($permission)) {
                $permission = app(Permission::class)->findById($permission, $this->getDefaultGuardName());
                if (! $permission) {
                    return false;
                }
            }

            return $this->permissions->contains('id', $permission->id);
        }
    
    /**
     *    Check if model has the given permisson or the given permissions parents.
     *
     * @param string|id|Permission $permission
     * @param string $guardName
     *
     * @return return
     */
    public function hasPermissionWithParents($permission, $guardName = null) : bool
    {        
        return $this->hasAnyPermission(Permission::getPermissionIdsWithParents($permission, $guardName));
    }
}
