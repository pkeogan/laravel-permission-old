<?php

namespace Pkeogan\Permission\Traits;

use Pkeogan\Permission\Traits\Methods\PermissionSystemConvertMethods;
use Pkeogan\Permission\Traits\Methods\PermissionSystemCollectionMethods;
use Pkeogan\Permission\Traits\Methods\PermissionSystemAssignMethods;
use Pkeogan\Permission\Traits\Methods\PermissionSystemBooleanMethods;
use Pkeogan\Permission\Traits\Methods\PermissionSystemExceptionMethods;

trait PermissionSystemTraits
{
    use PermissionSystemConvertMethods;
    use PermissionSystemCollectionMethods;
    use PermissionSystemAssignMethods;
    use PermissionSystemBooleanMethods;
    use PermissionSystemExceptionMethods;
}
