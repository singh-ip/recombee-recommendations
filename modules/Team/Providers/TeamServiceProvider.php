<?php

namespace Modules\Team\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Modules\common\Exceptions\RequirementsNotMetException;
use Modules\Team\Models\Team;

class TeamServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!collect(app()->getLoadedProviders())->has('Spatie\Permission\PermissionServiceProvider')) {
            throw new RequirementsNotMetException('Spatie Permisions package');
        }

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'platform.team');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'Modules/Team');

        User::resolveRelationUsing('teams', function ($userModel) {
            return $userModel->belongsToMany(Team::class, 'user_team');
        });
    }
}
