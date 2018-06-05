<?php

namespace Pkeogan\Permission\Traits\Boolean;



trait HasRoleBoolean
{
    /**
     * Determine if the model has (one of) the given role(s).
     *
     * @param string|array|\Pkeogan\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    /**
     * Determine if the model has any of the given role(s).
     *
     * @param string|array|\Pkeogan\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasAnyRole($roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * Determine if the model has all of the given role(s).
     *
     * @param string|\Pkeogan\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasAllRoles($roles): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        $roles = collect()->make($roles)->map(function ($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $roles->intersect($this->roles->pluck('name')) == $roles;
    }
    
}
