<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

final class ModuleGenerator
{
    public function generate(string $module): void
    {
        $module = ucfirst($module);

        $base = base_path("app/Modules/{$module}");

        $directories = [
            '',
            '/Controllers',
            '/Models',
            '/Services',
            '/Views',
        ];

        foreach ($directories as $directory) {

            $path = $base . $directory;

            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $this->createFromStub(
            'module/routes.stub.php',
            "{$base}/routes.php",
            $module
        );

        $this->createFromStub(
            'module/controller.stub.php',
            "{$base}/Controllers/{$module}Controller.php",
            $module
        );

        $this->createFromStub(
            'module/service.stub.php',
            "{$base}/Services/{$module}Service.php",
            $module
        );

        $this->createFromStub(
            'module/model.stub.php',
            "{$base}/Models/{$module}.php",
            $module
        );

        $this->createFromStub(
            'module/index.stub.php',
            "{$base}/Views/index.php",
            $module
        );
    }

    private function createFromStub(
    string $stub,
    string $destination,
    string $module
): void {

    $stubPath = base_path("stubs/{$stub}");

    if (!file_exists($stubPath)) {
        throw new \RuntimeException(
            "Stub not found: {$stubPath}"
        );
    }

    $content = file_get_contents($stubPath);

    if ($content === false) {
        throw new \RuntimeException(
            "Unable to read stub: {$stubPath}"
        );
    }

    $content = str_replace(
        ['{{module}}', '{{moduleLower}}'],
        [$module, strtolower($module)],
        $content
    );

    file_put_contents($destination, $content);
}
}