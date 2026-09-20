<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

final class MakeModuleCommand extends Command
{
    public function handle(array $arguments): void
    {
        $name = $arguments[0] ?? null;

        if (!$name) {
            $this->error('Module name is required.');
            return;
        }

        $generator = new \App\Core\Console\Generators\ModuleGenerator();

        $generator->generate($name);

        $this->success("Module '{$name}' created successfully.");
    }

    private function createRoutesFile(
            string $modulePath,
            string $module
        ): void {

            $stub = file_get_contents(
                base_path('stubs/module/routes.stub.php')
            );

            $stub = str_replace(
                '{{module}}',
                $module,
                $stub
            );

            $stub = str_replace(
                '{{moduleLower}}',
                strtolower($module),
                $stub
            );

            file_put_contents(
                $modulePath . '/routes.php',
                $stub
            );
        }
}