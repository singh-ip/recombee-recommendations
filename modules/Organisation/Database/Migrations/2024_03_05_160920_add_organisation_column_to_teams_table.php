<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\common\Services\ModuleService;
use Modules\Organisation\Models\Organisation;

return new class () extends Migration {
    public function up(): void
    {
        if (!ModuleService::isEnabled('Team')) {
            echo " :: no Teams module, SKIP";
            return;
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->foreignIdFor(Organisation::class)->nullable();
        });
    }

    public function down(): void
    {
        if (!ModuleService::isEnabled('Team')) {
            echo " :: no Teams module, SKIP";
            return;
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeignIdFor(Organisation::class);
        });
    }
};
