<?php

namespace Modules\Organisation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\common\Services\ModuleService;
use Modules\Organisation\Http\Middleware\SetOrganisationContext;
use Modules\Organisation\Models\Organisation;
use Modules\Team\Models\Team;
use Route;

class OrganisationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'platform.organisation');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        Route::aliasMiddleware('org-context', SetOrganisationContext::class);
        if(config('platform.organisation.replace_auth_middleware')) {
            Route::aliasMiddleware('auth', SetOrganisationContext::class);
        }

        if (ModuleService::isEnabled('Team')) {
            Team::resolveRelationUsing('organisation', function ($teamModel) {
                return $teamModel->belongsTo(Organisation::class, 'organisation_id');
            });
        }
    }
}
