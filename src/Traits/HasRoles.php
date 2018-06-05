<?php

namespace Pkeogan\Permission\Traits;

use Illuminate\Support\Collection;
use Pkeogan\Permission\Contracts\Role;
use Illuminate\Database\Eloquent\Builder;
use Pkeogan\Permission\Contracts\Permission;
use Pkeogan\Permission\Traits\Boolean\HasRoleBoolean;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Pkeogan\Permission\Traits\Exceptions\HasRoleExceptions;
use Pkeogan\Permission\Traits\Attributes\HasRoleAttributes;
use Pkeogan\Permission\Traits\Methods\HasRoleConvertMethods;
use Pkeogan\Permission\Traits\Collections\HasRoleCollections;
use Pkeogan\Permission\Traits\Methods\HasRoleRelationshipMethods;


trait HasRoles
{
    use HasPermissions;
    use HasRoleBoolean;
    use HasRoleExceptions;
    use HasRoleCollections;
    use HasRoleConvertMethods;
    use HasRoleRelationshipMethods;
    

    public static function bootHasRoles()
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->roles()->detach();
            $model->permissions()->detach();
        });
    }

    /**
     * A model may have multiple roles.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            'model_id',
            'role_id'
        );
    }

    /**
     * A model may have multiple direct permissions.
     */
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            'model_id',
            'permission_id'
        );
    }

    /**
     * Scope the model query to certain roles only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array|\Pkeogan\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole(Builder $query, $roles): Builder
    {
        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }

        if (! is_array($roles)) {
            $roles = [$roles];
        }

        $roles = array_map(function ($role) {
            if ($role instanceof Role) {
                return $role;
            }

            return app(Role::class)->findByName($role, $this->getDefaultGuardName());
        }, $roles);

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere(config('permission.table_names.roles').'.id', $role->id);
                }
            });
        });
    }

    

    /**
     * Scope the model query to certain permissions only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array|\Pkeogan\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePermission(Builder $query, $permissions): Builder
    {
        $permissions = $this->convertToPermissionModels($permissions);

        $rolesWithPermissions = array_unique(array_reduce($permissions, function ($result, $permission) {
            return array_merge($result, $permission->roles->all());
        }, []));

        return $query->
            where(function ($query) use ($permissions, $rolesWithPermissions) {
                $query->whereHas('permissions', function ($query) use ($permissions) {
                    $query->where(function ($query) use ($permissions) {
                        foreach ($permissions as $permission) {
                            $query->orWhere(config('permission.table_names.permissions').'.id', $permission->id);
                        }
                    });
                });
                if (count($rolesWithPermissions) > 0) {
                    $query->orWhereHas('roles', function ($query) use ($rolesWithPermissions) {
                        $query->where(function ($query) use ($rolesWithPermissions) {
                            foreach ($rolesWithPermissions as $role) {
                                $query->orWhere(config('permission.table_names.roles').'.id', $role->id);
                            }
                        });
                    });
                }
            });
    }

    protected function convertPipeToArray(string $pipeString)
    {
        $pipeString = trim($pipeString);

        if (strlen($pipeString) <= 2) {
            return $pipeString;
        }

        $quoteCharacter = substr($pipeString, 0, 1);
        $endCharacter = substr($quoteCharacter, -1, 1);

        if ($quoteCharacter !== $endCharacter) {
            return explode('|', $pipeString);
        }

        if (! in_array($quoteCharacter, ["'", '"'])) {
            return explode('|', $pipeString);
        }

        return explode('|', trim($pipeString, $quoteCharacter));
    }
}
