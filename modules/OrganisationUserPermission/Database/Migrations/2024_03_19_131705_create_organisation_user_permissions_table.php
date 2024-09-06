<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\common\Services\ModuleService;
use Modules\Organisation\Models\Organisation;
use Spatie\Permission\Models\Permission;

return new class () extends Migration {
    public function up(): void
    {
        if (!ModuleService::isEnabled('Organisation')
            || !collect(app()->getLoadedProviders())->has('Spatie\Permission\PermissionServiceProvider')) {
            echo " :: no Organisations module, SKIP";
            return;
        }

        Schema::create('organisation_user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organisation::class);
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Permission::class);
            $table->timestamps();
            $table->index(['organisation_id', 'user_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        if (!ModuleService::isEnabled('Organisation')
            || !collect(app()->getLoadedProviders())->has('Spatie\Permission\PermissionServiceProvider')) {
            echo " :: no Organisations module, SKIP";
            return;
        }

        Schema::dropIfExists('organisation_user_permission');
    }
};
