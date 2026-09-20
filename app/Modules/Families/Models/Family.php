<?php

declare(strict_types=1);

namespace App\Modules\Families\Models;

use App\Core\Database;

final class Family
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(
        int $tenantId
    ): array
    {
        return $this->db->fetchAll(
            "
            SELECT

                f.*,

                CONCAT(
                    m.first_name,
                    ' ',
                    m.last_name
                ) AS head_name,

                (
                    SELECT COUNT(*)

                    FROM members fm

                    WHERE

                        fm.family_id = f.id

                    AND

                        fm.deleted_at IS NULL

                ) AS member_count

            FROM families f

            LEFT JOIN members m

                ON m.id = f.head_member_id

            WHERE

                f.tenant_id = ?

            ORDER BY

                f.family_name ASC
            ",
            [
                $tenantId
            ]
        );
    }

    public function find(
        int $tenantId,
        int $id
    ): ?array
    {
        return $this->db->fetch(
            "
            SELECT

                f.*,

                CONCAT(
                    m.first_name,
                    ' ',
                    m.last_name
                ) AS head_name

            FROM families f

            LEFT JOIN members m

                ON m.id = f.head_member_id

            WHERE

                f.tenant_id = ?

            AND

                f.id = ?

            LIMIT 1
            ",
            [
                $tenantId,
                $id
            ]
        );
    }

    public function create(
        array $data
    ): int
    {
        $this->db->execute(
            "
            INSERT INTO families
            (

                tenant_id,

                family_code,

                family_name,

                head_member_id,

                address,

                city,

                state,

                country,

                postal_code,

                phone,

                email,

                anniversary,

                status,

                remarks

            )

            VALUES
            (

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?,

                ?

            )
            ",
            [

                $data['tenant_id'],

                $data['family_code'],

                $data['family_name'],

                $data['head_member_id'],

                $data['address'],

                $data['city'],

                $data['state'],

                $data['country'],

                $data['postal_code'],

                $data['phone'],

                $data['email'],

                $data['anniversary'],

                $data['status'],

                $data['remarks']

            ]
        );

        return (int) $this->db->lastInsertId();
    }

  public function update(
    int $tenantId,
    int $id,
    array $data
): void
{
    $this->db->execute(
        "
        UPDATE families

        SET

            family_name = ?,

            address = ?,

            city = ?,

            state = ?,

            country = ?,

            postal_code = ?,

            phone = ?,

            email = ?,

            anniversary = ?,

            status = ?,

            remarks = ?,

            updated_at = NOW()

        WHERE

            tenant_id = ?

        AND

            id = ?
        ",
        [

            $data['family_name'],

            $data['address'],

            $data['city'],

            $data['state'],

            $data['country'],

            $data['postal_code'],

            $data['phone'],

            $data['email'],

            $data['anniversary'],

            $data['status'],

            $data['remarks'],

            $tenantId,

            $id

        ]
    );
}

    public function delete(
        int $tenantId,
        int $id
    ): void
    {
        $this->db->execute(
            "
            DELETE

            FROM families

            WHERE

                tenant_id = ?

            AND

                id = ?
            ",
            [
                $tenantId,
                $id
            ]
        );
    }

    public function count(
        int $tenantId
    ): int
    {
        $row = $this->db->fetch(
            "
            SELECT

                COUNT(*) AS total

            FROM families

            WHERE

                tenant_id = ?
            ",
            [
                $tenantId
            ]
        );

        return (int) ($row['total'] ?? 0);
    }

    public function updateHead(
    int $tenantId,
    int $familyId,
    ?int $memberId
): void
{
    $this->db->execute(
        "
        UPDATE families
        SET
            head_member_id = ?,
            updated_at = NOW()
        WHERE
            tenant_id = ?
        AND
            id = ?
        ",
        [
            $memberId,
            $tenantId,
            $familyId
        ]
    );
}
}