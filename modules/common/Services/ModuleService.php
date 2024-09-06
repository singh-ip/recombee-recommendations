<?php

namespace Modules\common\Services;

use Composer\Autoload\ClassLoader;
use RuntimeException;

class ModuleService
{
    public static function enabled(): array
    {
        return config('platform.common.modules.enabled');
    }

    public static function isEnabled(string $moduleName): bool
    {
        return in_array($moduleName, static::enabled());
    }

    public static function list(bool $withClasses = false): array
    {
        if ($withClasses) {
            return config('platform.common.modules.classes');
        }
        return array_keys(config('platform.common.modules.classes'));
    }

    public static function getModuleDirectory(string $moduleName): string
    {
        $providerClass = config('platform.common.modules.classes')[$moduleName];
        $providerFile = static::getClassFilePath($providerClass);
        if(empty($providerFile)) {
            throw new RuntimeException(__('Modules/common::messages.provider_missing', ['provider_class' => $providerClass]));
        }

        return dirname($providerFile, 2);
    }

    private static function getClassFilePath($className): ?string
    {
        $autoloadFile = base_path('vendor/autoload.php');
        if (!file_exists($autoloadFile)) {
            throw new RuntimeException(__('Modules/common::messages.autoload_missing'));
        }

        $autoloader = require $autoloadFile;

        if ($autoloader instanceof ClassLoader) {
            $filePath = $autoloader->findFile($className);
            if ($filePath) {
                return realpath($filePath);
            }
        }

        return null;
    }
}
