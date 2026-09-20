<?php

declare(strict_types=1);

namespace App\Modules\Members\Services;

use App\Modules\Members\Models\Member;
use App\Modules\Onboarding\Models\User;
use App\Modules\Onboarding\Models\Role;
use InvalidArgumentException;
use RuntimeException;

final class MemberService
{
    private Member $members;

    private User $users;

    private Role $roles;

    public function __construct()
    {
        $this->members = new Member();

        $this->users = new User();

        $this->roles = new Role();
    }

    public function all(int $tenantId): array
    {
        return $this->members->all($tenantId);
    }

    public function find(
        int $tenantId,
        int $id
    ): ?array {
        return $this->members->find(
            $tenantId,
            $id
        );
    }

    /**
     * Create a new church member.
     *
     * Existing member creation behavior is preserved.
     *
     * If create_user is enabled, a Member login account is also
     * created using the supplied password.
     */
    public function create(array $data): int
    {
        $this->validate($data);

        $data['member_no'] = $this->generateMemberNumber(
            (int) $data['tenant_id']
        );

        $data = $this->normalize($data);

        /*
        |--------------------------------------------------------------------------
        | Create Church Member
        |--------------------------------------------------------------------------
        */

        $memberId = $this->members->create($data);

        /*
        |--------------------------------------------------------------------------
        | Create Login Account (Optional)
        |--------------------------------------------------------------------------
        */

        $shouldCreateLogin = !empty($data['create_user']);

        if ($shouldCreateLogin) {

            $this->createMemberLoginAccount(
                (int) $data['tenant_id'],
                $memberId,
                $data
            );
        }

        return $memberId;
    }

    /**
     * Update an existing church member.
     *
     * Existing member update behavior is preserved.
     *
     * Member login behavior:
     *
     * - No account + checkbox checked:
     *      Create Member login.
     *
     * - Existing account + checkbox checked:
     *      Enable/keep account active.
     *
     * - Existing account + checkbox unchecked:
     *      Disable account.
     *
     * - Existing account + password supplied:
     *      Reset password.
     */
    public function update(
        int $tenantId,
        int $id,
        array $data
    ): void {
        $this->validate($data, false);

        /*
        |--------------------------------------------------------------------------
        | Make sure the member exists within this tenant
        |--------------------------------------------------------------------------
        */

        $member = $this->members->find(
            $tenantId,
            $id
        );

        if ($member === null) {
            throw new RuntimeException(
                'Member not found.'
            );
        }

        $data = $this->normalize($data);

        /*
        |--------------------------------------------------------------------------
        | Update Church Member
        |--------------------------------------------------------------------------
        */

        $this->members->update(
            $tenantId,
            $id,
            $data
        );

        /*
        |--------------------------------------------------------------------------
        | Member Login
        |--------------------------------------------------------------------------
        */

        $this->syncMemberLoginAccount(
            $tenantId,
            $id,
            $data
        );
    }

    public function delete(
        int $tenantId,
        int $id
    ): void {
        $this->members->softDelete(
            $tenantId,
            $id
        );
    }

    /**
     * Generate the next member number.
     *
     * Example:
     * MEM000001
     */
    private function generateMemberNumber(
        int $tenantId
    ): string {
        $last = $this->members->lastMemberNumber(
            $tenantId
        );

        if ($last === null) {
            return 'MEM000001';
        }

        $number = (int) preg_replace(
            '/\D/',
            '',
            $last
        );

        $number++;

        return 'MEM' . str_pad(
            (string) $number,
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Validate required fields.
     */
    private function validate(
        array $data,
        bool $creating = true
    ): void {
        if (empty(trim($data['first_name'] ?? ''))) {

            throw new InvalidArgumentException(
                'First name is required.'
            );
        }

        if (empty(trim($data['last_name'] ?? ''))) {

            throw new InvalidArgumentException(
                'Last name is required.'
            );
        }

        if (
            !empty($data['email']) &&
            !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw new InvalidArgumentException(
                'Invalid email address.'
            );
        }

        if (
            $creating &&
            empty($data['tenant_id'])
        ) {

            throw new InvalidArgumentException(
                'Tenant ID is required.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Member Login Validation
        |--------------------------------------------------------------------------
        |
        | Password validation is only required when a new login is being
        | created. Existing accounts may be edited without entering a
        | password. A password is only changed when supplied.
        |
        */

        if (!empty($data['create_user'])) {

            if (empty(trim((string) ($data['email'] ?? '')))) {
                throw new InvalidArgumentException(
                    'Email address is required when Member Login is enabled.'
                );
            }

            $password = (string) ($data['password'] ?? '');
            $passwordConfirmation = (string) (
                $data['password_confirmation'] ?? ''
            );

            if ($creating) {

                if ($password === '') {
                    throw new InvalidArgumentException(
                        'Password is required when Member Login is enabled.'
                    );
                }

                if ($passwordConfirmation === '') {
                    throw new InvalidArgumentException(
                        'Password confirmation is required.'
                    );
                }

                if ($password !== $passwordConfirmation) {
                    throw new InvalidArgumentException(
                        'Passwords do not match.'
                    );
                }
            }
        }
    }

    /**
     * Normalize optional fields.
     */
    private function normalize(
        array $data
    ): array {
        $defaults = [

            'middle_name' => null,

            'preferred_name' => null,

            'gender' => null,

            'occupation' => null,

            'photo' => null,

            'dob' => null,

            'marital_status' => null,

            'email' => null,

            'phone' => null,

            'alternate_phone' => null,

            'address' => null,

            'city' => null,

            'state' => null,

            'country' => null,

            'postal_code' => null,

            'membership_date' => null,

            'baptism_date' => null,

            'status' => 'Active',

            'emergency_name' => null,

            'emergency_phone' => null,

            'emergency_relationship' => null,

            'notes' => null,

            'created_by' => null,

            'updated_by' => null,

        ];

        $data = array_merge(
            $defaults,
            $data
        );

        foreach ([
            'preferred_name',
            'occupation',
            'photo',
        ] as $field) {

            if (
                isset($data[$field]) &&
                trim((string) $data[$field]) === ''
            ) {
                $data[$field] = null;
            }
        }

        return $data;
    }

    /**
     * Create a Member login account.
     */
    private function createMemberLoginAccount(
        int $tenantId,
        int $memberId,
        array $data
    ): void {
        $email = trim((string) ($data['email'] ?? ''));

        if ($email === '') {
            throw new InvalidArgumentException(
                'Email address is required for Member Login.'
            );
        }

        $password = (string) ($data['password'] ?? '');
        $passwordConfirmation = (string) (
            $data['password_confirmation'] ?? ''
        );

        if ($password === '') {
            throw new InvalidArgumentException(
                'Password is required for Member Login.'
            );
        }

        if ($password !== $passwordConfirmation) {
            throw new InvalidArgumentException(
                'Passwords do not match.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get Member Role
        |--------------------------------------------------------------------------
        */

        $role = $this->roles->findBySlug(
            $tenantId,
            'member'
        );

        if ($role === null) {
            throw new InvalidArgumentException(
                'Default Member role not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Build account name
        |--------------------------------------------------------------------------
        */

        $name = $this->buildMemberName($data);

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate Member account
        |--------------------------------------------------------------------------
        |
        | This is intentionally checked before creation even though the
        | database now allows multiple accounts per member. A Member should
        | only have one account with the Member role.
        |
        */

        $existing = $this->users->findMemberAccount(
            $tenantId,
            $memberId,
            (int) $role['id']
        );

        if ($existing !== null) {
            throw new InvalidArgumentException(
                'This member already has a Member login account.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create account
        |--------------------------------------------------------------------------
        */

        $this->users->createAccount(
            $tenantId,
            $memberId,
            (int) $role['id'],
            $name,
            $email,
            $password,
            'Active'
        );
    }

    /**
     * Synchronize the Member login account during member editing.
     */
    private function syncMemberLoginAccount(
        int $tenantId,
        int $memberId,
        array $data
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Find Member role
        |--------------------------------------------------------------------------
        */

        $role = $this->roles->findBySlug(
            $tenantId,
            'member'
        );

        if ($role === null) {
            throw new InvalidArgumentException(
                'Default Member role not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Find existing Member login account
        |--------------------------------------------------------------------------
        */

        $account = $this->users->findMemberAccount(
            $tenantId,
            $memberId,
            (int) $role['id']
        );

        $loginEnabled = !empty($data['create_user']);

        /*
        |--------------------------------------------------------------------------
        | No Member login requested
        |--------------------------------------------------------------------------
        */

        if (!$loginEnabled) {

            if ($account !== null) {

                $this->users->updateStatus(
                    $tenantId,
                    (int) $account['id'],
                    'Inactive'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Login requested — email is required
        |--------------------------------------------------------------------------
        */

        $email = trim((string) ($data['email'] ?? ''));

        if ($email === '') {
            throw new InvalidArgumentException(
                'Email address is required when Member Login is enabled.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Build account name
        |--------------------------------------------------------------------------
        */

        $name = $this->buildMemberName($data);

        /*
        |--------------------------------------------------------------------------
        | Account doesn't exist — create it
        |--------------------------------------------------------------------------
        */

        if ($account === null) {

            $password = (string) ($data['password'] ?? '');
            $passwordConfirmation = (string) (
                $data['password_confirmation'] ?? ''
            );

            if ($password === '') {
                throw new InvalidArgumentException(
                    'Password is required when enabling Member Login.'
                );
            }

            if ($password !== $passwordConfirmation) {
                throw new InvalidArgumentException(
                    'Passwords do not match.'
                );
            }

            $this->users->createAccount(
                $tenantId,
                $memberId,
                (int) $role['id'],
                $name,
                $email,
                $password,
                'Active'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Existing account — enable it
        |--------------------------------------------------------------------------
        */

        $this->users->updateStatus(
            $tenantId,
            (int) $account['id'],
            'Active'
        );

        /*
        |--------------------------------------------------------------------------
        | Optional password reset
        |--------------------------------------------------------------------------
        |
        | On an ordinary Member edit, password fields can remain blank.
        | If the administrator enters a new password, it is treated as a
        | password reset.
        |
        */

        $password = (string) ($data['password'] ?? '');
        $passwordConfirmation = (string) (
            $data['password_confirmation'] ?? ''
        );

        if ($password !== '') {

            if ($password !== $passwordConfirmation) {
                throw new InvalidArgumentException(
                    'Passwords do not match.'
                );
            }

            $this->users->updatePassword(
                $tenantId,
                (int) $account['id'],
                $password
            );
        }
    }

    /**
     * Build the display/login account name from the member data.
     */
    private function buildMemberName(
        array $data
    ): string {
        return trim(
            (string) ($data['first_name'] ?? '')
            . ' '
            . (string) ($data['middle_name'] ?? '')
            . ' '
            . (string) ($data['last_name'] ?? '')
        );
    }

    /**
     * Get active members.
     */
    public function active(
        int $tenantId
    ): array {
        return $this->members->active(
            $tenantId
        );
    }

    public function byFamily(
        int $tenantId,
        int $familyId
    ): array {
        return $this->members->byFamily(
            $tenantId,
            $familyId
        );
    }

    public function moveMemberToFamily(
        int $tenantId,
        int $memberId,
        ?int $familyId,
        ?string $familyRole
    ): void {
        $member = $this->members->find(
            $tenantId,
            $memberId
        );

        if (!$member) {

            throw new RuntimeException(
                'Member not found.'
            );
        }

        $this->members->moveToFamily(
            $tenantId,
            $memberId,
            $familyId,
            $familyRole
        );
    }

    public function clearFamilyHead(
        int $tenantId,
        int $familyId
    ): bool {
        return $this->members->clearFamilyHead(
            $tenantId,
            $familyId
        );
    }

    public function assignAsFamilyHead(
        int $tenantId,
        int $memberId,
        int $familyId
    ): void {
        $member = $this->members->find(
            $tenantId,
            $memberId
        );

        if (!$member) {
            throw new RuntimeException('Member not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | If the member already belongs to another family
        |--------------------------------------------------------------------------
        */

        if (
            !empty($member['family_id'])
            && (int) $member['family_id'] !== $familyId
        ) {

            /*
            |--------------------------------------------------------------------------
            | Remove head reference from previous family
            |--------------------------------------------------------------------------
            */

            $this->db->query(
                "
                UPDATE families
                SET head_member_id = NULL
                WHERE tenant_id = ?
                  AND id = ?
                  AND head_member_id = ?
                ",
                [
                    $tenantId,
                    $member['family_id'],
                    $memberId
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove existing Head of this family
        |--------------------------------------------------------------------------
        */

        $this->members->clearFamilyHead(
            $tenantId,
            $familyId
        );

        /*
        |--------------------------------------------------------------------------
        | Assign member
        |--------------------------------------------------------------------------
        */

        $this->members->assignToFamily(
            $tenantId,
            $memberId,
            $familyId,
            'Head'
        );

        /*
        |--------------------------------------------------------------------------
        | Update family
        |--------------------------------------------------------------------------
        */

        $this->db->update(
            'families',
            [
                'head_member_id' => $memberId,
            ],
            [
                'tenant_id' => $tenantId,
                'id' => $familyId,
            ]
        );
    }

    public function getByFamily(
        int $tenantId,
        int $familyId
    ): array {
        return $this->members->getByFamily(
            $tenantId,
            $familyId
        );
    }

    /**
 * Get the Member login account for a member.
 *
 * Returns null when the member does not have a Member login account.
 */
public function getMemberLogin(
    int $tenantId,
    int $memberId
): ?array {
    $role = $this->roles->findBySlug(
        $tenantId,
        'member'
    );

    if ($role === null) {
        return null;
    }

    return $this->users->findMemberAccount(
        $tenantId,
        $memberId,
        (int) $role['id']
    );
}

}