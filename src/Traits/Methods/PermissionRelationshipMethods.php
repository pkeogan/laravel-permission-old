<?php

namespace Pkeogan\Permission\Traits\Methods;

use Pkeogan\Permission\Models\Role;


trait PermissionRelationshipMethods
{
  /**
	 * cGives roles permissions based off a request and config. This methods cycles through the request looking for matching permission names from the config. If found, and role ids in the request's permissions array are given and set.
     * @param string|array|\Pkeogan\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return void
     */
    public static function givePermisisonsToRolesFromRequest($request, $config, $model)
    {
		if(!config($config)){dd('config was null');} 
		
		 foreach(config($config) as $perm)
			{
			  if(isset($request[$perm])){
				   Role::assignRolesThesePermissions($request[$perm], $model->getPermission($perm));
			  }
    		}	
	}
}
        


