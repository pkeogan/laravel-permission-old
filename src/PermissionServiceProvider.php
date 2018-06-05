<?php

namespace Pkeogan\Permission;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Pkeogan\Permission\Contracts\Role as RoleContract;
use Pkeogan\Permission\Contracts\Permission as PermissionContract;
use Pkeogan\Permission\Traits\Collections\ExtendCollections;

class PermissionServiceProvider extends ServiceProvider
{
    use ExtendCollections;
    
    public function boot(PermissionRegistrar $permissionLoader)
    {
        if (isNotLumen()) {
            $this->publishes([
                __DIR__.'/../config/permission.php' => config_path('permission.php'),
            ], 'config');

            if (! class_exists('CreatePermissionTables')) {
                $timestamp = date('Y_m_d_His', time());

                $this->publishes([
                    __DIR__.'/../database/migrations/create_permission_tables.php.stub' => $this->app->databasePath()."/migrations/{$timestamp}_create_permission_tables.php",
                ], 'migrations');
            }
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\CreateRole::class,
                Commands\CreatePermission::class,
            ]);
        }

        $this->registerModelBindings();
        $this->registerCollectionBindings();

        $permissionLoader->registerPermissions();
    }

    public function register()
    {
        if (isNotLumen()) {
            $this->mergeConfigFrom(
                __DIR__.'/../config/permission.php',
                'permission'
            );
        }

        $this->registerBladeExtensions();
                
      
      
    }
 
 

    protected function registerModelBindings()
    {
        $config = $this->app->config['permission.models'];

        $this->app->bind(PermissionContract::class, $config['permission']);
        $this->app->bind(RoleContract::class, $config['role']);
    }

    protected function registerBladeExtensions()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $bladeCompiler->directive('role', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');

                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endrole', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('hasrole', function ($arguments) {
                list($role, $guard) = explode(',', $arguments.',');

                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('hasanyrole', function ($arguments) {
                list($roles, $guard) = explode(',', $arguments.',');

                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasAnyRole({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasanyrole', function () {
                return '<?php endif; ?>';
            });

            $bladeCompiler->directive('hasallroles', function ($arguments) {
                list($roles, $guard) = explode(',', $arguments.',');

                return "<?php if(auth({$guard})->check() && auth({$guard})->user()->hasAllRoles({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasallroles', function () {
                return '<?php endif; ?>';
            });
            $bladeCompiler->directive('haspermission', function ($permission) {
                return "<?php if(Auth::user()->hasPermissionWithParents({$permission})): ?>";
            });
            $bladeCompiler->directive('endhaspermission', function () {
                return '<?php endif; ?>';
            });
        });
    }
}
