<?php

namespace Modules\common\Database\Seeders;

use File;
use Illuminate\Database\Seeder;
use Modules\common\Services\ModuleService;
use RuntimeException;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        $composer = require base_path('vendor/autoload.php');
        $classMap = array_flip(array_filter(array_map('realpath', $composer->getClassMap()), fn ($v) => is_string($v)));

        foreach (ModuleService::enabled() as $moduleName) {
            $seederFiles = $this->getSeederFiles($moduleName);
            if (is_null($seederFiles)) {
                continue;
            }
            foreach ($seederFiles as $seederFile) {
                $filePath = $seederFile->getRealPath();
                if (!array_key_exists($filePath, $classMap)) {
                    throw new RuntimeException(
                        __('Modules/common::messages.seeder_file_missing', ['seeder_file' => $seederFile->getRealPath()])
                    );
                }

                $seederClass = $classMap[$filePath];
                if (is_subclass_of($seederClass, Seeder::class)) {
                    $this->call($seederClass);
                }
            }
        }
    }

    private function getSeederFiles(string $moduleName): ?array
    {
        $moduleDirectory = ModuleService::getModuleDirectory($moduleName)
            . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Seeders';

        if (!is_dir($moduleDirectory)) {
            return null;
        }
        return File::files($moduleDirectory);
    }

}
