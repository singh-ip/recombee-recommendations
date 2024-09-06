<?php

namespace Modules\Team\Database\Seeders;

use App;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\common\Services\ModuleService;
use Modules\Organisation\Models\Organisation;
use Modules\Team\Models\Team;
use Spatie\Permission\Models\Permission;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        if (!ModuleService::isEnabled('Team')) {
            return;
        }

        if (App::environment() !== 'local' && !App::runningUnitTests()) {
            return;
        }

        $user = User::firstOrFail();
        $data = [
            'default' => true,
        ];

        if (ModuleService::isEnabled('Organisation')) {
            $organisation = Organisation::firstOrFail();
            $data['organisation_id'] = $organisation->id;
        }

        $team = Team::updateOrCreate($data, ['name' => 'Admin team']);

        $team->users()->attach($user);

        $permissions = [
            'list teams',
            'view team',
            'delete team',
            'edit team',
            'create team',
            'change team assignment',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }
    }
}
