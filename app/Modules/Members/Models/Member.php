<?php

declare(strict_types=1);

namespace App\Modules\Members\Models;

use App\Core\Database;

final class Member
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function all(int $tenantId): array
    {
        return $this->db->fetchAll(
            "
            SELECT *

            FROM members

            WHERE tenant_id = ?

            AND deleted_at IS NULL

            ORDER BY first_name,last_name
            ",
            [$tenantId]
        );
    }

    public function find(
        int $tenantId,
        int $id
    ): ?array
    {
        return $this->db->fetch(
            "
            SELECT *

            FROM members

            WHERE id = ?

            AND tenant_id = ?

            AND deleted_at IS NULL
            ",
            [
                $id,
                $tenantId
            ]
        );
    }

    public function create(array $data): int
    {
        $this->db->execute(
            "
            INSERT INTO members (
                uuid,
                tenant_id,

                member_no,

                first_name,

                middle_name,

                last_name,

                preferred_name,

                gender,

                occupation,

                photo,

                dob,

                marital_status,

                email,

                phone,

                alternate_phone,

                address,

                city,

                state,

                country,

                postal_code,

                membership_date,

                baptism_date,

                status,

                emergency_name,

                emergency_phone,

                emergency_relationship,

                notes,

                created_by

            )

            VALUES (

                UUID(),
                ?,?,?,?,?,?,?,?,?,?,
                ?,?,?,?,?,?,?,?,?,?,
                ?,?,?,?,?,?,?

            )
            ",
            [
                
                $data['tenant_id'],

                $data['member_no'],

                $data['first_name'],

                $data['middle_name'],

                $data['last_name'],

                $data['preferred_name'],

                $data['gender'],

                $data['occupation'],

                $data['photo'],

                $data['dob'],

                $data['marital_status'],

                $data['email'],

                $data['phone'],

                $data['alternate_phone'],

                $data['address'],

                $data['city'],

                $data['state'],

                $data['country'],

                $data['postal_code'],

                $data['membership_date'],

                $data['baptism_date'],

                $data['status'],

                $data['emergency_name'],

                $data['emergency_phone'],

                $data['emergency_relationship'],

                $data['notes'],

                $data['created_by']

            ]
        );

        return (int)$this->db->lastInsertId();
    }

    public function update(
        int $tenantId,
        int $id,
        array $data
    ): void
    {
        $this->db->execute(
            "
            UPDATE members

            SET

                first_name=?,

                middle_name=?,

                last_name=?,

                preferred_name=?,

                gender=?,

                occupation=?,

                photo=?,

                dob=?,

                marital_status=?,

                email=?,

                phone=?,

                alternate_phone=?,

                address=?,

                city=?,

                state=?,

                country=?,

                postal_code=?,

                membership_date=?,

                baptism_date=?,

                status=?,

                emergency_name=?,

                emergency_phone=?,

                emergency_relationship=?,

                notes=?,

                updated_by=?

            WHERE id=?

            AND tenant_id=?
            ",
            [

                $data['first_name'],

                $data['middle_name'],

                $data['last_name'],

                $data['preferred_name'],

                $data['gender'],

                $data['occupation'],

                $data['photo'],

                $data['dob'],

                $data['marital_status'],

                $data['email'],

                $data['phone'],

                $data['alternate_phone'],

                $data['address'],

                $data['city'],

                $data['state'],

                $data['country'],

                $data['postal_code'],

                $data['membership_date'],

                $data['baptism_date'],

                $data['status'],

                $data['emergency_name'],

                $data['emergency_phone'],

                $data['emergency_relationship'],

                $data['notes'],

                $data['updated_by'],

                $id,

                $tenantId

            ]
        );
    }

    public function softDelete(
        int $tenantId,
        int $id
    ): void
    {
        $this->db->execute(
            "
            UPDATE members

            SET deleted_at=NOW()

            WHERE id=?

            AND tenant_id=?
            ",
            [
                $id,
                $tenantId
            ]
        );
    }

    public function lastMemberNumber(
        int $tenantId
    ): ?string
    {
        $row = $this->db->fetch(
            "
            SELECT member_no

            FROM members

            WHERE tenant_id=?

            ORDER BY id DESC

            LIMIT 1
            ",
            [
                $tenantId
            ]
        );

        return $row['member_no'] ?? null;
    }

    /**
     * Get active members.
     */
    public function active(
        int $tenantId
    ): array
    {
        return $this->db->fetchAll(
            "
            SELECT *

            FROM members

            WHERE tenant_id = ?

            AND status = 'Active'

            AND deleted_at IS NULL

            ORDER BY

                first_name,

                last_name
            ",
            [
                $tenantId
            ]
        );
    }

    public function byFamily(
    int $tenantId,
    int $familyId
): array
{
    return $this->db->fetchAll(
        "
        SELECT

            *

        FROM members

        WHERE

            tenant_id = ?

        AND

            family_id = ?

        AND

            deleted_at IS NULL

        ORDER BY

            CASE family_role

                WHEN 'Head' THEN 1
                WHEN 'Spouse' THEN 2
                WHEN 'Father' THEN 3
                WHEN 'Mother' THEN 4
                WHEN 'Son' THEN 5
                WHEN 'Daughter' THEN 6
                ELSE 99

            END,

            first_name,

            last_name
        ",
        [
            $tenantId,
            $familyId
        ]
    );
}

public function moveToFamily(
    int $tenantId,
    int $memberId,
    ?int $familyId,
    ?string $familyRole
): void
{
   
    $this->db->execute(
        "
        UPDATE members

        SET

            family_id = ?,

            family_role = ?,

            updated_at = NOW()

        WHERE

            tenant_id = ?

        AND

            id = ?
        ",
        [
            $familyId,
            $familyRole,
            $tenantId,
            $memberId
        ]
    );

    error_log(
    sprintf(
        'moveToFamily: member=%d role=%s',
        $memberId,
        var_export($familyRole, true)
    )
);
}

public function clearFamilyHead(
    int $tenantId,
    int $familyId
): void
{
    $this->db->execute(
        "
        UPDATE members

        SET

            family_role = NULL,

            updated_at = NOW()

        WHERE

            tenant_id = ?

        AND

            family_id = ?

        AND

            family_role = 'Head'
        ",
        [
            $tenantId,
            $familyId
        ]
    );
}

public function getByFamily(
    int $tenantId,
    int $familyId
): array
{
    return $this->db->fetchAll(
        "
        SELECT *

        FROM members

        WHERE

            tenant_id = ?

        AND

            family_id = ?
        ",
        [
            $tenantId,
            $familyId
        ]
    );
}
}