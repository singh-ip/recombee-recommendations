<?php

namespace Modules\Organisation\Database\Seeders;

use App;
use Illuminate\Database\Seeder;
use Modules\common\Services\ModuleService;
use Modules\Organisation\Models\Organisation;

class OrganisationSeeder extends Seeder
{
    public function run(): void
    {
        if(!ModuleService::isEnabled('Organisation')) {
            return;
        }

        if (App::environment() !== 'local' && !App::runningUnitTests()) {
            return;
        }

        Organisation::updateOrCreate(['name' => 'Admin organisation']);
    }
}
