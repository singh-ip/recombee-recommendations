<?php

namespace Modules\OrganisationUserPermission\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Modules\common\Exceptions\ModuleNotEnabledException;
use Modules\common\Exceptions\RequirementsNotMetException;
use Modules\common\Services\ModuleService;
use Modules\OrganisationUserPermission\Http\Middleware\CheckOrganisationUserPermission;
use Modules\OrganisationUserPermission\Models\OrganisationUserPermission;
use Route;

class OrganisationUserPermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!collect(app()->getLoadedProviders())->has('Spatie\Permission\PermissionServiceProvider')) {
            throw new RequirementsNotMetException('Spatie Permisions package');
        }

        if (!ModuleService::isEnabled('Organisation')) {
            throw new ModuleNotEnabledException('Organisation');
        }

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'platform.organisationUserPermission');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        User::resolveRelationUsing('organisationUserPermissions', function ($userModel) {
            return $userModel->hasMany(OrganisationUserPermission::class);
        });

        Route::aliasMiddleware('can-within-org', CheckOrganisationUserPermission::class);
    }
}
