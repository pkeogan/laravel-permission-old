<?php

namespace Pkeogan\Permission\Traits\Exceptions;

use Pkeogan\Permission\Exceptions\GuardDoesNotMatch;

trait HasPermissionExceptions
{
    /**
     * Checks if a model shares a gaurd with the given role or model, if not throw exception
     *
     * @param \Pkeogan\Permission\Contracts\Permission|\Pkeogan\Permission\Contracts\Role $roleOrPermission
     *
     * @throws \Pkeogan\Permission\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {
        if (! $this->getGuardNames()->contains($roleOrPermission->guard_name)) {
            throw GuardDoesNotMatch::create($roleOrPermission->guard_name, $this->getGuardNames());
        }
    }
}
