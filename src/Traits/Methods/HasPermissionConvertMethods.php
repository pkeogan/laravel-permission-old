<?php

namespace Pkeogan\Permission\Traits\Methods;

use Pkeogan\Permission\Models\Permission;

trait HasPermissionConvertMethods
{
  /**
     * @param string|array|\Pkeogan\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return array
     */
    protected function convertToPermissionModels($permissions): array
    {
        if ($permissions instanceof Collection) {
            $permissions = $permissions->all();
        }

        $permissions = array_wrap($permissions);

        return array_map(function ($permission) {
            if ($permission instanceof Permission) {
                return $permission;
            }

            return app(Permission::class)->findByName($permission, $this->getDefaultGuardName());
        }, $permissions);
    }
}
