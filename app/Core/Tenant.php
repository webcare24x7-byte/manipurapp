<?php

declare(strict_types=1);

namespace App\Core;

final class Tenant
{
    public function __construct(
        private readonly Database $db
    ) {
    }

    public function resolve(): ?array
    {
        // Local development
        if (!empty($_GET['tenant'])) {

            return $this->findBySlug($_GET['tenant']);
        }

        // Host name
        $host = $_SERVER['HTTP_HOST'] ?? '';

        // localhost = no tenant
        if (str_contains($host, 'localhost')) {

            return null;
        }

        // Custom domain
        $tenant = $this->db->fetch(
            "SELECT *
             FROM tenants
             WHERE domain = ?
             LIMIT 1",
            [$host]
        );

        if ($tenant) {
            return $tenant;
        }

        // Subdomain
        $parts = explode('.', $host);

        if (count($parts) >= 3) {

            return $this->findBySlug($parts[0]);
        }

        return null;
    }

    private function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT *
             FROM tenants
             WHERE slug = ?
             LIMIT 1",
            [$slug]
        );
    }
}