<?php

namespace Pkeogan\Permission\Traits\Collections;

use Illuminate\Support\Collection;
use Pkeogan\Permission\Models\Role;

trait HasRoleCollections
{
   public function getRoleNames(): Collection
    {
        return $this->roles->pluck('name');
    }

    protected function getStoredRole($role): Role
    {
        if (is_numeric($role)) {
            return app(Role::class)->findById($role, $this->getDefaultGuardName());
        }

        if (is_string($role)) {
            return app(Role::class)->findByName($role, $this->getDefaultGuardName());
        }

        return $role;
    }
}
