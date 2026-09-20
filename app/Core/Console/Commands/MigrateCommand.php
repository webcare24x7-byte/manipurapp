<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Migrator;

final class MigrateCommand extends Command
{
    public function handle(array $arguments): void
    {
        (new Migrator())->migrate();
    }
}