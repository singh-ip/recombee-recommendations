<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\common\Services\ModuleService;

return new class () extends Migration {
    public function up(): void
    {
        if (!ModuleService::isEnabled('Team')) {
            echo " :: no Teams module, SKIP";
            return;
        }

        Schema::table('user_invitations', function (Blueprint $table) {
            $table->foreignId('team_id')
                ->nullable()
                ->references('id')
                ->on('teams');
        });
    }

    public function down(): void
    {
        if (!ModuleService::isEnabled('Team')) {
            echo " :: no Teams module, SKIP";
            return;
        }

        Schema::table('user_invitations', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });
    }
};
