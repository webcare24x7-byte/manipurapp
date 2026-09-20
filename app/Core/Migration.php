<?php

declare(strict_types=1);

namespace App\Core;

abstract class Migration
{
    /**
     * Execute the migration.
     */
    abstract public function up(Database $db): void;

    /**
     * Rollback the migration.
     */
    abstract public function down(Database $db): void;
}