<?php

declare(strict_types=1);

namespace App\Core\Console;

use App\Core\Console\Commands\MakeMigrationCommand;
use App\Core\Console\Commands\MakeModuleCommand;
use App\Core\Console\Commands\MigrateCommand;

final class Console
{
    /**
     * @var array<string, Command>
     */
    private array $commands = [];

    public function __construct()
    {
        $this->commands = [

            'migrate' => new MigrateCommand(),

            'make:migration' => new MakeMigrationCommand(),

            'make:module' => new MakeModuleCommand(),

        ];
    }

    /**
     * Execute the CLI command.
     */
    public function run(array $argv): void
    {
        $command = $argv[1] ?? '';

        if ($command === '') {
            $this->help();
            return;
        }

        if (!isset($this->commands[$command])) {

            echo PHP_EOL;
            echo "Unknown command: {$command}" . PHP_EOL;
            echo PHP_EOL;

            $this->help();

            return;
        }

        $arguments = array_slice($argv, 2);

        $this->commands[$command]->handle($arguments);
    }

    /**
     * Display help.
     */
    private function help(): void
    {
        echo PHP_EOL;
        echo "==========================================" . PHP_EOL;
        echo "          ChurchOS CLI v1.0" . PHP_EOL;
        echo "==========================================" . PHP_EOL;
        echo PHP_EOL;

        echo "Available Commands" . PHP_EOL;
        echo "------------------" . PHP_EOL;
        echo PHP_EOL;

        echo "  migrate" . PHP_EOL;
        echo "      Run pending database migrations." . PHP_EOL;
        echo PHP_EOL;

        echo "  make:migration <name>" . PHP_EOL;
        echo "      Create a new migration." . PHP_EOL;
        echo PHP_EOL;

        echo "  make:module <name>" . PHP_EOL;
        echo "      Create a new application module." . PHP_EOL;
        echo PHP_EOL;
    }
}