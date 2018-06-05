<?php

namespace Pkeogan\Permission\Models;

use Pkeogan\Permission\Guard;
use Illuminate\Database\Eloquent\Model;
use Pkeogan\Permission\Traits\HasPermissions;
use Pkeogan\Permission\Exceptions\RoleDoesNotExist;
use Pkeogan\Permission\Exceptions\GuardDoesNotMatch;
use Pkeogan\Permission\Exceptions\RoleAlreadyExists;
use Pkeogan\Permission\Contracts\Role as RoleContract;
use Pkeogan\Permission\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Pkeogan\Permission\Traits\Collections\RoleCollections;
use Pkeogan\Permission\Traits\Methods\RoleRelationshipMethods;

class Role extends Model implements RoleContract
{
    use HasPermissions;
    use RoleCollections;
    use RoleRelationshipMethods;
    use RefreshesPermissionCache;

    public $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->setTable(config('permission.table_names.roles'));
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        if (static::where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->first()) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        if (isNotLumen() && app()::VERSION < '5.4') {
            return parent::create($attributes);
        }

        return static::query()->create($attributes);
    }
    
    
// |--------------------------------------------------------------------------
// |  Relationships
// |--------------------------------------------------------------------------  

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions')
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            'model_id'
        );
    }
    
// |--------------------------------------------------------------------------
// |  Collections
// |--------------------------------------------------------------------------  

    /**
     * Find a role by its name and guard name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Pkeogan\Permission\Contracts\Role|\Pkeogan\Permission\Models\Role
     *
     * @throws \Pkeogan\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::where('name', $name)->where('guard_name', $guardName)->first();

        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }
    
    /**
     * Find a role by its ID and guard name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Pkeogan\Permission\Contracts\Role|\Pkeogan\Permission\Models\Role
     *
     * @throws \Pkeogan\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findById(int $id, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::where('id', $id)->where('guard_name', $guardName)->first();

        if (! $role) {
            throw RoleDoesNotExist::withId($id);
        }

        return $role;
    }
    


    /**
     * Determine if the user may perform the given permission.
     *
     * @param string|Permission $permission
     *
     * @return bool
     *
     * @throws \Pkeogan\Permission\Exceptions\GuardDoesNotMatch
     */
    public function hasPermissionTo($permission): bool
    {
        if (is_string($permission)) {
            $permission = app(Permission::class)->findByName($permission, $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = app(Permission::class)->findById($permission, $this->getDefaultGuardName());
        }

        if (! $this->getGuardNames()->contains($permission->guard_name)) {
            throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
        }

        return $this->permissions->contains('id', $permission->id);
    }
}
