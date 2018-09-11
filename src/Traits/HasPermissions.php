<?php

namespace Pkeogan\Permission\Traits;

use Illuminate\Support\Collection;
use Pkeogan\Permission\PermissionRegistrar;
use Pkeogan\Permission\Contracts\Permission;
use Pkeogan\Permission\Traits\Boolean\HasPermissionBoolean;
use Pkeogan\Permission\Traits\Exceptions\HasPermissionExceptions;
use Pkeogan\Permission\Traits\Attributes\HasPermissionAttributes;
use Pkeogan\Permission\Traits\Methods\HasPermissionConvertMethods;
use Pkeogan\Permission\Traits\Collections\HasPermissionCollections;
use Pkeogan\Permission\Traits\Methods\HasPermissionRelationshipMethods;

/**
 *  Trait that allows models to use Permissions Based Methods
 */
trait HasPermissions {
    
    use HasPermissionBoolean;
    use HasPermissionExceptions;
    use HasPermissionAttributes;
    use HasPermissionCollections;
    use HasPermissionConvertMethods;
    use HasPermissionRelationshipMethods;
    
    /**
     * Forget the cached permissions.
     */
    public function forgetCachedPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
    
}
