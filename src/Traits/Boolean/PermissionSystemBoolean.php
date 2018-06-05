<?php

namespace Pkeogan\Permission\Traits\Boolean;

trait PermissionSystemBoolean
{
       /**
         * Determine if the current user has permission to do an action
         *
         * @param \Pkeogan\Permission\Contracts\Permission $permission
         *
         * @return bool
         */
        protected function can($input, $guardName = null): bool
        {
            if(Auth::user()->hasPermission($input, $guardName = null))
            {
              return true;
            }
            elseif()
            {

            }
            else
            {
              return false;
            }
        }
}
