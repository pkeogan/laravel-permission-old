<?php

namespace Pkeogan\Permission\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Pkeogan\Permission\Models\Permission;
use Pkeogan\Permission\Exceptions\UnauthorizedException;

class PermissionsMiddleware
{
    public function handle($request, Closure $next, $permission)
    {
        if (app('auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
		
		$permission = Permission::all()->firstWhere('name', $permission)->idWithParents;

		if((app('auth')->user()->hasAnyPermission($permission)))
		{
			return $next($request);
		}
		
        throw UnauthorizedException::forPermissions($permission);
    }
}
