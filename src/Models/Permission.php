<?php

namespace Pkeogan\Permission\Models;

use Pkeogan\Permission\Guard;
use Illuminate\Support\Collection;
use Pkeogan\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Pkeogan\Permission\PermissionRegistrar;
use Pkeogan\Permission\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Pkeogan\Permission\Exceptions\PermissionDoesNotExist;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Pkeogan\Permission\Exceptions\PermissionAlreadyExists;
use Pkeogan\Permission\Traits\Attributes\PermissionAttributes;
use Pkeogan\Permission\Traits\Methods\PermissionRelationshipMethods;
use Pkeogan\Permission\Traits\Collections\PermissionCollections;
use Pkeogan\Permission\Contracts\Permission as PermissionContract;

class Permission extends Model implements PermissionContract
{ 
    use HasRoles;
    use PermissionRelationshipMethods;
    use PermissionAttributes;
    use PermissionCollections;
    use RefreshesPermissionCache;

    public $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->setTable(config('permission.table_names.permissions'));
    }
// |--------------------------------------------------------------------------
// |  Creation Methods
// |--------------------------------------------------------------------------
    /**
     * Create a permission.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Pkeogan\Permission\Contracts\Permission
     */
    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        if (static::getPermissions()->where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->first()) {
            throw PermissionAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        if (isNotLumen() && app()::VERSION < '5.4') {
            return parent::create($attributes);
        }
        

        return static::query()->create($attributes);
    }
  
    /**
     * Find or create permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Pkeogan\Permission\Contracts\Permission
     */
    public static function findOrCreate(string $name, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->where('name', $name)->where('guard_name', $guardName)->first();

        if (! $permission) {
            return static::create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $permission;
    }
    
        /**
     * Find or create permission by its id (and optionally guardName).
     *
     * @param string $id
     * @param string|null $guardName
     *
     * @return \Pkeogan\Permission\Contracts\Permission
     */
    public static function findByIdOrCreate(int $id, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->where('id', $id)->where('guard_name', $guardName)->first();

        if (! $permission) {
            return static::create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $permission;
    }
    
    /**
     *  Create permissions from an array then return the created permisisons in a collection
     *
     * @param array $permission
     *
     * @return return
     */
    public static function createFromArray($names, $uniqueName = "", $guardName = null): Collection
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        
        $c = collect();
        
        foreach($names as $name)
        {
            $c->push(static::create(['name' => $uniqueName . $name, 'guard_name' => $guardName]));
        }
        
        return $c;
    }
    
        /**
     *  Create permissions from an array then return the created permisisons in a collection
     *
     * @param array $permission
     *
     * @return return
     */
    public static function createFromArrayWithParent($names, $uniqueName, $parentUniqueName, $guardName = null): Collection
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        
        $c = collect();
        
        foreach($names as $name)
        {
            if(substr($name, -strlen("_all")) == "_all")
            {
                $parent = static::findByNameOrNull($parentUniqueName . $name, $guardName);
            } else
            {
                $parent = static::findByNameOrNull($parentUniqueName . $name . "_all", $guardName);
            }
            if($parent == null)
            {
                $c->push(static::create(['name' => $uniqueName . $name, 'guard_name' => $guardName ]));
            } else {
                $c->push(static::create(['name' => $uniqueName . $name, 'guard_name' => $guardName, 'parent_id' => $parent->id]));
            }
        }
        
        return $c;
    }
  
// |--------------------------------------------------------------------------
// |  Realtionships
// |--------------------------------------------------------------------------  
  
    /**
     * A permission can be applied to roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions')
        );
    }

    /**
     * A permission belongs to some users of the model associated with its guard.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_permissions'),
            'permission_id',
            'model_id'
        );
    }
  
      /**
     *  A permission has a parent
     */
    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id', 'id');
    }
  
    /**
     *  a Permission has children
     */
    public function children()
    {
        return $this->hasMany(Permission::class, 'id', 'parent_id');
    }
    
// |--------------------------------------------------------------------------
// |  Collection Methods
// |--------------------------------------------------------------------------  

    /**
     * Find a permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \Pkeogan\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Pkeogan\Permission\Contracts\Permission
     */
    public static function findByName(string $name, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->where('name', $name)->where('guard_name', $guardName)->first();

        if (! $permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }
  
    /**
     * Find a permission by its name (and optionally guardName).
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \Pkeogan\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Pkeogan\Permission\Contracts\Permission
     */
    public static function findByNameOrNull(string $name, $guardName = null)
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->where('name', $name)->where('guard_name', $guardName)->first();

        if (! $permission) {
            return null;
        }

        return $permission;
    }

    /**
     * Find a permission by its id (and optionally guardName).
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @throws \Pkeogan\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Pkeogan\Permission\Contracts\Permission
     */
    public static function findById(int $id, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->where('id', $id)->where('guard_name', $guardName)->first();

        if (! $permission) {
            throw PermissionDoesNotExist::withId($id, $guardName);
        }

        return $permission;
    }
  
     /**
     * Find a permission by its id (and optionally guardName).
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @throws \Pkeogan\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Pkeogan\Permission\Contracts\Permission
     */
    public static function findByIdOrNull(int $id, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $permission = static::getPermissions()->where('id', $id)->where('guard_name', $guardName)->first();

        if (! $permission) {
            return null;
        }

        return $permission;
    }
  
  
    /**
     * Find all the roles of the given permissions, then return an array of the roles that have the given permissions.
     *
     * @param array $permissions
     * @param string|null $guardName
     *
     * @throws \Pkeogan\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return Array of Roles
     */
    public static function getAllRoles($permissions, $guardName = null)
    {
        $allRoles = Role::pluck('name', 'id')->all(); //Get all of the current roles, this array will be returned at the end
        $guardName = $guardName ?? Guard::getDefaultName(static::class);  //set the guard we are workong on
        foreach($permissions as $key=>$permission) // Loop through all the permissions given
        {
          //Check and see if the given permission is a number, it it is, we are calling it by the ID, if not, we are calling it by the name
          if(is_numeric($permission))
          {
              $permission = self::findById($permission, $guardName);
         
          } else {
              $permission = self::findByName($permission, $guardName);
          }
          //Now that we have a valid permisson, lets loop through our list of roles left
          foreach($allRoles as $roleID=>$roleName)
          {
            // Check and see if the role belongs 
            if(! array_key_exists($roleID, $permission->roles->pluck('id')->all()))
            { 
              unset($allRoles[$roleID]);
            }
          }
        }
      return $allRoles;
    }
    
    /**
     * Get the current cached permissions.
     */
    protected static function getPermissions(): Collection
    {
        return app(PermissionRegistrar::class)->getPermissions();
    }
       
}
