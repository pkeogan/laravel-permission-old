<?php

namespace Pkeogan\Permission\Traits\Collections;

use Illuminate\Support\Collection;
use Pkeogan\Permission\Models\Permission;

trait HasPermissionCollections {
  
      /**
     * @param string|array|\Pkeogan\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return \Pkeogan\Permission\Contracts\Permission|\Pkeogan\Permission\Contracts\Permission[]|\Illuminate\Support\Collection
     */
    protected function getStoredPermission($permissions)
    {
        if (is_numeric($permissions)) {
            return app(Permission::class)->findById($permissions, $this->getDefaultGuardName());
        }

        if (is_string($permissions)) {
            return app(Permission::class)->findByName($permissions, $this->getDefaultGuardName());
        }

        if (is_array($permissions)) {
            return app(Permission::class)
                ->whereIn('name', $permissions)
                ->whereIn('guard_name', $this->getGuardNames())
                ->get();
        }

        return $permissions;
    }
  
    /**
     * Return all permissions the directory coupled to the model.
     */
    public function getDirectPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * Return all the permissions the model has via roles.
     */
    public function getPermissionsViaRoles(): Collection
    {
        return $this->load('roles', 'roles.permissions')
            ->roles->flatMap(function ($role) {
                return $role->permissions;
            })->sort()->values();
    }

    /**
     * Return all the permissions the model has, both directly and via roles.
     */
    public function getAllPermissions(): Collection
    {
        return $this->permissions
            ->merge($this->getPermissionsViaRoles())
            ->sort()
            ->values();
    }
  
    /**
     * Get all the permissions of this model
     */
    public function getPermissions(): Collection
    {
        return $this->getAllPermissions();
    }
  
}
