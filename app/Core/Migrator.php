<?php

declare(strict_types=1);

namespace App\Core;
use RuntimeException;

final class Migrator
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Run all pending migrations.
     */
    public function migrate(): void
    {
        $this->ensureMigrationTable();

        $executed = $this->executedMigrations();

        $files = glob(
            base_path('database/migrations/*.php')
        );

        sort($files);

        $batch = $this->nextBatch();

        foreach ($files as $file) {

            $migration = basename($file);

            if (in_array($migration, $executed, true)) {
                continue;
            }

            $instance = require $file;

            if (!$instance instanceof Migration) {
                throw new RuntimeException(
                    "{$migration} must return an instance of Migration."
                );
            }

            echo "Migrating {$migration}...\n";

            $instance->up($this->db);

            $this->db->execute(
                "INSERT INTO migrations
                (migration, batch)
                VALUES
                (?, ?)",
                [
                    $migration,
                    $batch
                ]
            );

            echo "Done\n";
        }

        echo PHP_EOL;
        echo "Migration complete." . PHP_EOL;
    }

    /**
     * Create migrations table if missing.
     */
    private function ensureMigrationTable(): void
    {
        $this->db->execute("
            CREATE TABLE IF NOT EXISTS migrations (

                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                migration VARCHAR(255) NOT NULL UNIQUE,

                batch INT NOT NULL,

                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

            )
        ");
    }

    /**
     * Already executed migrations.
     */
    private function executedMigrations(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT migration FROM migrations"
        );

        return array_column($rows, 'migration');
    }

    /**
     * Next batch number.
     */
    private function nextBatch(): int
    {
        $row = $this->db->fetch(
            "SELECT MAX(batch) AS batch
             FROM migrations"
        );

        return ((int)($row['batch'] ?? 0)) + 1;
    }
}