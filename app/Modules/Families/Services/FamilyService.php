<?php

declare(strict_types=1);

namespace App\Modules\Families\Services;

use App\Modules\Families\Models\Family;
use RuntimeException;
use App\Modules\Members\Services\MemberService;
use App\Core\Database;

final class FamilyService
{
    private Family $families;
    private MemberService $members;
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
        $this->families = new Family();
        $this->members = new MemberService();
    }

    public function all(
        int $tenantId
    ): array
    {
        return $this->families->all(
            $tenantId
        );
    }

    public function find(
        int $tenantId,
        int $id
    ): ?array
    {
        return $this->families->find(
            $tenantId,
            $id
        );
    }

    public function create(
        array $data
    ): int
    {
        return $this->families->create(
            $data
        );
    }

    public function update(
        int $tenantId,
        int $id,
        array $data
    ): void
    {
        $this->families->update(
            $tenantId,
            $id,
            $data
        );
    }

public function delete(
    int $tenantId,
    int $familyId
): void
{
    /*
    |--------------------------------------------------------------------------
    | Validate Family
    |--------------------------------------------------------------------------
    */

    $family = $this->families->find(
        $tenantId,
        $familyId
    );

    if (!$family) {

        throw new RuntimeException(
            'Family not found.'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Check for Members
    |--------------------------------------------------------------------------
    */

    $members = $this->members->getByFamily(
        $tenantId,
        $familyId
    );

    if (!empty($members)) {

        throw new RuntimeException(
            'Cannot delete a family that still has members.'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Delete Family
    |--------------------------------------------------------------------------
    */

    $this->families->delete(
        $tenantId,
        $familyId
    );
}

    public function count(
        int $tenantId
    ): int
    {
        return $this->families->count(
            $tenantId
        );
    }
    
public function changeHead(
    int $tenantId,
    int $familyId,
    ?int $memberId
): void
{
    $this->db->beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | Validate Family
        |--------------------------------------------------------------------------
        */

        $family = $this->families->find(
            $tenantId,
            $familyId
        );

        if (!$family) {

            throw new RuntimeException(
                'Family not found.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Remove Head Only
        |--------------------------------------------------------------------------
        */

        if ($memberId === null) {

            $this->clearFamilyHead(
                $tenantId,
                $familyId
            );

        } else {

            /*
            |--------------------------------------------------------------------------
            | Validate Member
            |--------------------------------------------------------------------------
            */

            $member = $this->members->find(
                $tenantId,
                $memberId
            );

            if (!$member) {

                throw new RuntimeException(
                    'Member not found.'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Member belongs to another Family?
            |--------------------------------------------------------------------------
            */

            if (
                !empty($member['family_id'])
                && (int) $member['family_id'] !== $familyId
            ) {

                $oldFamily = $this->families->find(
                    $tenantId,
                    (int) $member['family_id']
                );

                if (
                    $oldFamily
                    && !empty($oldFamily['head_member_id'])
                    && (int) $oldFamily['head_member_id'] === $memberId
                ) {

                    $this->clearFamilyHead(
                        $tenantId,
                        (int) $oldFamily['id']
                    );

                }

            }

            /*
            |--------------------------------------------------------------------------
            | Clear Current Head of Destination Family
            |--------------------------------------------------------------------------
            */

            $this->clearFamilyHead(
                $tenantId,
                $familyId
            );

            /*
            |--------------------------------------------------------------------------
            | Move Member to Destination Family
            |--------------------------------------------------------------------------
            */
           
            $this->members->moveMemberToFamily(
                $tenantId,
                $memberId,
                $familyId,
                'Head'
            );

            /*
            |--------------------------------------------------------------------------
            | Update Family
            |--------------------------------------------------------------------------
            */

            $this->families->updateHead(
                $tenantId,
                $familyId,
                $memberId
            );

        }

        $this->db->commit();

    } catch (Throwable $e) {

        if ($this->db->inTransaction()) {

            $this->db->rollBack();

        }

        throw $e;

    }
}

/**
 * Clear every Head role in a family and synchronize the
 * family-level head_member_id.
 *
 * This intentionally does not rely on head_member_id to find
 * the current Head, because that reference may be stale.
 */
private function clearFamilyHead(
    int $tenantId,
    int $familyId
): void {
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

    $this->families->updateHead(
        $tenantId,
        $familyId,
        null
    );
}



    /**
     * Move an existing family member to another family.
     *
     * This is intentionally separate from addMember(). A member
     * already assigned to a family must be explicitly moved.
     */
public function moveMember(
    int $tenantId,
    int $sourceFamilyId,
    int $memberId,
    int $destinationFamilyId,
    string $familyRole
): void {
    $allowedRoles = [
        'Head',
        'Spouse',
        'Parent',
        'Child',
        'Other Adult',
        'Dependent',
        'Guardian',
        'Other',
    ];

    if (!in_array($familyRole, $allowedRoles, true)) {
        throw new RuntimeException(
            'Invalid family role.'
        );
    }

    if ($sourceFamilyId === $destinationFamilyId) {
        throw new RuntimeException(
            'The destination family must be different from the current family.'
        );
    }

    $sourceFamily = $this->families->find(
        $tenantId,
        $sourceFamilyId
    );

    if (!$sourceFamily) {
        throw new RuntimeException(
            'Current family not found.'
        );
    }

    $destinationFamily = $this->families->find(
        $tenantId,
        $destinationFamilyId
    );

    if (!$destinationFamily) {
        throw new RuntimeException(
            'Destination family not found.'
        );
    }

    if (($destinationFamily['status'] ?? null) !== 'Active') {
        throw new RuntimeException(
            'The destination family is not active.'
        );
    }

    $member = $this->members->find(
        $tenantId,
        $memberId
    );

    if (!$member) {
        throw new RuntimeException(
            'Member not found.'
        );
    }

    if (
        (int) ($member['family_id'] ?? 0)
        !== $sourceFamilyId
    ) {
        throw new RuntimeException(
            'Member is not assigned to the current family.'
        );
    }

    $this->db->beginTransaction();

    try {

        /*
         * If this member is the PRIMARY head of the source family,
         * clear only the primary reference.
         *
         * We do NOT remove the member's Head role here because
         * they may be moved to another family as Head.
         */
        if (
            (int) ($sourceFamily['head_member_id'] ?? 0)
            === $memberId
        ) {
            $this->families->updateHead(
                $tenantId,
                $sourceFamilyId,
                null
            );
        }

        /*
         * Move the member.
         *
         * If familyRole is Head, this creates another Head in the
         * destination family. Existing Heads are preserved.
         */
        $this->members->moveMemberToFamily(
            $tenantId,
            $memberId,
            $destinationFamilyId,
            $familyRole
        );

        /*
         * If moved as Head, make this member the PRIMARY family head.
         *
         * This does NOT demote any other Head.
         */
        if ($familyRole === 'Head') {
            $this->families->updateHead(
                $tenantId,
                $destinationFamilyId,
                $memberId
            );
        }

        $this->db->commit();

    } catch (Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}

public function addMember(
    int $tenantId,
    int $familyId,
    int $memberId,
    string $familyRole
): void {
    $allowedRoles = [
        'Head',
        'Spouse',
        'Parent',
        'Child',
        'Other Adult',
        'Dependent',
        'Guardian',
        'Other',
    ];

    if (!in_array($familyRole, $allowedRoles, true)) {
        throw new RuntimeException(
            'Invalid family role.'
        );
    }

    $family = $this->families->find(
        $tenantId,
        $familyId
    );

    if (!$family) {
        throw new RuntimeException(
            'Family not found.'
        );
    }

    $member = $this->members->find(
        $tenantId,
        $memberId
    );

    if (!$member) {
        throw new RuntimeException(
            'Member not found.'
        );
    }

    if (!empty($member['family_id'])) {
        throw new RuntimeException(
            'This member already belongs to a family. Use Move Member instead.'
        );
    }

    $this->db->beginTransaction();

    try {

        $this->members->moveMemberToFamily(
            $tenantId,
            $memberId,
            $familyId,
            $familyRole
        );

        /*
         * A newly added Head becomes the Primary Family Head,
         * but existing Heads remain Heads.
         */
        if ($familyRole === 'Head') {
            $this->families->updateHead(
                $tenantId,
                $familyId,
                $memberId
            );
        }

        $this->db->commit();

    } catch (Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}

public function changeMemberRole(
    int $tenantId,
    int $familyId,
    int $memberId,
    string $familyRole
): void {
    $allowedRoles = [
        'Head',
        'Spouse',
        'Parent',
        'Child',
        'Other Adult',
        'Dependent',
        'Guardian',
        'Other',
    ];

    if (!in_array($familyRole, $allowedRoles, true)) {
        throw new RuntimeException(
            'Invalid family role.'
        );
    }

    $family = $this->families->find(
        $tenantId,
        $familyId
    );

    if (!$family) {
        throw new RuntimeException(
            'Family not found.'
        );
    }

    $member = $this->members->find(
        $tenantId,
        $memberId
    );

    if (
        !$member ||
        (int) ($member['family_id'] ?? 0) !== $familyId
    ) {
        throw new RuntimeException(
            'Member is not assigned to this family.'
        );
    }

    $this->db->beginTransaction();

    try {

        $this->members->moveMemberToFamily(
            $tenantId,
            $memberId,
            $familyId,
            $familyRole
        );

        if ($familyRole === 'Head') {

            /*
             * Add another Head and make this one primary.
             */
            $this->families->updateHead(
                $tenantId,
                $familyId,
                $memberId
            );

        } elseif (
            (int) ($family['head_member_id'] ?? 0)
            === $memberId
        ) {

            /*
             * The primary Head is no longer a Head.
             *
             * Other Heads remain untouched.
             */
            $this->families->updateHead(
                $tenantId,
                $familyId,
                null
            );
        }

        $this->db->commit();

    } catch (Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}

public function removeMember(
    int $tenantId,
    int $familyId,
    int $memberId
): void {
    $family = $this->families->find(
        $tenantId,
        $familyId
    );

    if (!$family) {
        throw new RuntimeException(
            'Family not found.'
        );
    }

    $member = $this->members->find(
        $tenantId,
        $memberId
    );

    if (
        !$member ||
        (int) ($member['family_id'] ?? 0) !== $familyId
    ) {
        throw new RuntimeException(
            'Member is not assigned to this family.'
        );
    }

    $this->db->beginTransaction();

    try {

        /*
         * If this member is the Primary Head, clear only the
         * primary reference.
         */
        if (
            (int) ($family['head_member_id'] ?? 0)
            === $memberId
        ) {
            $this->families->updateHead(
                $tenantId,
                $familyId,
                null
            );
        }

        /*
         * Remove member from family.
         *
         * This clears family_id and family_role.
         */
        $this->members->moveMemberToFamily(
            $tenantId,
            $memberId,
            null,
            null
        );

        $this->db->commit();

    } catch (Throwable $e) {

        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }

        throw $e;
    }
}

}