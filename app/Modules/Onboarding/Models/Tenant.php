<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Models;

use App\Core\Database;

final class Tenant
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    /**
     * Create a new tenant.
     */
    public function create(array $data): int
    {
        $this->db->execute(
            "
            INSERT INTO tenants
            (
                uuid,
                name,
                slug,
                timezone,
                currency,
                country,
                status,
                created_at,
                updated_at
            )
            VALUES
            (
                UUID(),
                :name,
                :slug,
                'Asia/Kolkata',
                'INR',
                'India',
                'Trial',
                NOW(),
                NOW()
            )
            ",
            [
                'name' => $data['church_name'],
                'slug' => strtolower($data['slug']),
            ]
        );

        return (int)$this->db->lastInsertId();
    }

    /**
     * Check if slug already exists.
     */
    public function slugExists(string $slug): bool
    {
        $row = $this->db->fetch(
            "
            SELECT id
            FROM tenants
            WHERE slug = ?
            LIMIT 1
            ",
            [
                strtolower($slug)
            ]
        );

        return $row !== null;
    }

    /**
 * Find an active tenant by slug.
 */
public function findBySlug(string $slug): ?array
{
    return $this->db->fetch(
        "
        SELECT
            id,
            name,
            slug,
            domain,
            timezone,
            currency,
            country,
            status
        FROM tenants
        WHERE slug = ?
          AND status IN ('Trial', 'Active')
        LIMIT 1
        ",
        [
            strtolower(trim($slug))
        ]
    );
}

/**
 * Search active tenants by church name or slug.
 */
public function search(string $term, int $limit = 10): array
{
    $term = trim($term);

    if ($term === '') {
        return [];
    }

    $limit = max(1, min($limit, 20));

    return $this->db->fetchAll(
        "
        SELECT
            id,
            name,
            slug,
            domain,
            country,
            status
        FROM tenants
        WHERE status IN ('Trial', 'Active')
          AND (
                name LIKE ?
                OR slug LIKE ?
          )
        ORDER BY
            CASE
                WHEN LOWER(name) = LOWER(?) THEN 0
                WHEN LOWER(slug) = LOWER(?) THEN 1
                ELSE 2
            END,
            name ASC
        LIMIT {$limit}
        ",
        [
            '%' . $term . '%',
            '%' . strtolower($term) . '%',
            $term,
            $term
        ]
    );
}

}