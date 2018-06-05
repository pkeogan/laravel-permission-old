<?php
  /*
  |----------------------------------------------------------------------------------------------------
  |      __                               __       
  |     / /  ____ __________ __   _____  / /  
  |    / /  / __ `/ ___/ __ `| | / / _ \/ /  
  |   / /__/ /_/ / /  / /_/ /| |/ /  __/ / 
  |  /_____\__,_/_/   \__,_/ |___/\___/_/  
  |----------------------------------------------------------------------------------------------------
  | Laravel Permission System - By Peter Keogan FORKED from spatie/permission-laravel - Link:https://github.com/pkeogan/laravel-permission
  |----------------------------------------------------------------------------------------------------
  |   Title : Permission System Facade Class
  |   Desc  : Facade Support Class
  |   Useage: Please Refer to readme.md 
  | 
  |
  */
namespace Pkeogan\Permission;

use Carbon\Carbon;
use App\Models\Auth\User;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;

use Illuminate\Support\HtmlString;
use App\Exceptions\GeneralException;

use Pkeogan\Permission\Exceptions\PermissionSystemExceptions;

use Pkeogan\Permission\Traits\PermissionSystemTraits;




/**
 * Class PermissionSystem
 */
class PermissionSystem
{    
  
}