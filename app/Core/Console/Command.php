<?php

declare(strict_types=1);

namespace App\Core\Console;

abstract class Command
{
    /**
     * Execute the command.
     */
    abstract public function handle(array $arguments): void;

    protected function info(string $message): void
    {
        echo $message . PHP_EOL;
    }

    protected function error(string $message): void
    {
        echo "Error: {$message}" . PHP_EOL;
    }

    protected function success(string $message): void
    {
        echo "✓ {$message}" . PHP_EOL;
    }
}