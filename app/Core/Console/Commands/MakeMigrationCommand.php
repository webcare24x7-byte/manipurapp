<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

final class MakeMigrationCommand extends Command
{
    public function handle(array $arguments): void
    {
        $name = $arguments[0] ?? null;

        if (!$name) {
            $this->error('Migration name required.');
            return;
        }

        $timestamp = date('Y_m_d_His');

        $filename = "{$timestamp}_{$name}.php";

        $destination = base_path(
            "database/migrations/{$filename}"
        );

        if (file_exists($destination)) {
            $this->error('Migration already exists.');
            return;
        }

        $stub = file_get_contents(
            base_path('stubs/migration.stub.php')
        );

        $class = str_replace(
            ' ',
            '',
            ucwords(str_replace('_', ' ', $name))
        );

        $stub = str_replace(
            '{{class}}',
            $class,
            $stub
        );

        file_put_contents(
            $destination,
            $stub
        );

        $this->success("Created {$filename}");
    }
}