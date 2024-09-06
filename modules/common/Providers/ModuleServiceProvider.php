<?php

namespace Modules\common\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'platform.common');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'Modules/common');

        foreach(config('platform.common.modules.enabled') as $moduleName) {
            $this->app->register(config('platform.common.modules.classes')[$moduleName]);
        }
    }
}
