<?php

declare(strict_types=1);

namespace App\Modules\Families\Controllers;

use Throwable;
use App\Core\Controller;
use App\Modules\Members\Services\MemberService;
use App\Modules\Families\Services\FamilyService;
use App\Core\Auth;
use App\Core\Authorization;

final class FamilyController extends Controller
{
    private FamilyService $families;

    private MemberService $members;

    private Authorization $authorization;

    public function __construct()
    {
        $this->families = new FamilyService();

        $this->members = new MemberService();

        $this->authorization = new Authorization();
    }

    private function loadForm(
        array $family = [],
        ?string $error = null
    ): void {
        $tenantId = (int) Auth::tenantId();

        $this->view(
            empty($family)
                ? 'Families::Families.create'
                : 'Families::Families.edit',
            [
                'title' => empty($family)
                    ? 'New Family'
                    : 'Edit Family',

                'family' => $family,

                'members' => $this->members->all(
                    $tenantId
                ),

                'error' => $error,
            ]
        );
    }

    /**
     * Authorization helper.
     *
     * This does not alter existing business logic.
     * It only handles unauthorized access gracefully.
     */
    private function authorizeOrRedirect(
        string $permission,
        string $redirectTo = '/dashboard'
    ): bool {
        try {
            $this->authorization->authorize($permission);

            return true;

        } catch (\RuntimeException $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect(
                $redirectTo
            );

            return false;
        }
    }

    public function index(): void
    {
        if (!$this->authorizeOrRedirect(
            'families.view'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $families = $this->families->all(
            $tenantId
        );

        $this->view(
            'Families::Families.index',
            [
                'title' => 'Families',
                'families' => $families,
                'error' => null,
            ]
        );
    }

    public function create(): void
    {
        if (!$this->authorizeOrRedirect(
            'families.create'
        )) {
            return;
        }

        $this->loadForm();
    }

    public function show(
        int $id
    ): void {
        if (!$this->authorizeOrRedirect(
            'families.view'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $family = $this->families->find(
            $tenantId,
            $id
        );

        if ($family === null) {

            http_response_code(404);

            exit('Family not found.');
        }

        $familyMembers = $this->members->byFamily(
            $tenantId,
            $id
        );

        $availableMembers = array_values(
            array_filter(
                $this->members->active($tenantId),
                static function (array $member): bool {
                    return empty($member['family_id']);
                }
            )
        );

        $availableMoveFamilies = array_values(
            array_filter(
                $this->families->all($tenantId),
                static function (array $candidateFamily) use ($id): bool {
                    return (int) ($candidateFamily['id'] ?? 0) !== $id
                        && ($candidateFamily['status'] ?? null) === 'Active';
                }
            )
        );

        $this->view(
            'Families::Families.show',
            [
                'title' => $family['family_name'],

                'family' => $family,

                'members' => $familyMembers,

                'availableMembers' => $availableMembers,

                'availableMoveFamilies' => $availableMoveFamilies,
            ]
        );
    }

    public function edit(
        int $id
    ): void {
        if (!$this->authorizeOrRedirect(
            'families.edit'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $family = $this->families->find(
            $tenantId,
            $id
        );

        if ($family === null) {

            http_response_code(404);

            exit('Family not found.');
        }

        $this->loadForm(
            $family
        );
    }

    public function store(): void
    {
        if (!$this->authorizeOrRedirect(
            'families.create'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        try {

            $familyCode = 'FAM-' . date('Y') . '-' . strtoupper(
                substr(
                    md5(
                        uniqid(
                            '',
                            true
                        )
                    ),
                    0,
                    6
                )
            );

            $headMemberId = !empty($_POST['head_member_id'])
                ? (int) $_POST['head_member_id']
                : null;

            $id = $this->families->create(
                [
                    'tenant_id' => $tenantId,

                    'family_code' => $familyCode,

                    'family_name' => trim(
                        $_POST['family_name'] ?? ''
                    ),

                    'head_member_id' => null,

                    'address' => trim(
                        $_POST['address'] ?? ''
                    ),

                    'city' => trim(
                        $_POST['city'] ?? ''
                    ),

                    'state' => trim(
                        $_POST['state'] ?? ''
                    ),

                    'country' => trim(
                        $_POST['country'] ?? ''
                    ),

                    'postal_code' => trim(
                        $_POST['postal_code'] ?? ''
                    ),

                    'phone' => trim(
                        $_POST['phone'] ?? ''
                    ),

                    'email' => trim(
                        $_POST['email'] ?? ''
                    ),

                    'anniversary' => !empty($_POST['anniversary'])
                        ? $_POST['anniversary']
                        : null,

                    'status' => $_POST['status'] ?? 'Active',

                    'remarks' => trim(
                        $_POST['remarks'] ?? ''
                    ),
                ]
            );

            if ($headMemberId !== null) {

                $this->families->changeHead(
                    $tenantId,
                    $id,
                    $headMemberId
                );
            }

            flash(
                'success',
                'Family created successfully.'
            );

            $this->redirect(
                '/families/' . $id
            );

        } catch (Throwable $e) {

            $this->loadForm(
                $_POST,
                $e->getMessage()
            );
        }
    }

    public function update(
        int $id
    ): void {
        if (!$this->authorizeOrRedirect(
            'families.edit'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $family = $this->families->find(
            $tenantId,
            $id
        );

        if ($family === null) {

            http_response_code(404);

            exit('Family not found.');
        }

        try {

            $headMemberId = !empty($_POST['head_member_id'])
                ? (int) $_POST['head_member_id']
                : null;

            $this->families->update(
                $tenantId,
                $id,
                [
                    'family_name' => trim(
                        $_POST['family_name'] ?? ''
                    ),

                    'address' => trim(
                        $_POST['address'] ?? ''
                    ),

                    'city' => trim(
                        $_POST['city'] ?? ''
                    ),

                    'state' => trim(
                        $_POST['state'] ?? ''
                    ),

                    'country' => trim(
                        $_POST['country'] ?? ''
                    ),

                    'postal_code' => trim(
                        $_POST['postal_code'] ?? ''
                    ),

                    'phone' => trim(
                        $_POST['phone'] ?? ''
                    ),

                    'email' => trim(
                        $_POST['email'] ?? ''
                    ),

                    'anniversary' => !empty($_POST['anniversary'])
                        ? $_POST['anniversary']
                        : null,

                    'status' => $_POST['status'] ?? 'Active',

                    'remarks' => trim(
                        $_POST['remarks'] ?? ''
                    ),
                ]
            );

            if (
                (int) ($family['head_member_id'] ?? 0)
                !== (int) ($headMemberId ?? 0)
            ) {

                $this->families->changeHead(
                    $tenantId,
                    $id,
                    $headMemberId
                );
            }

            flash(
                'success',
                'Family updated successfully.'
            );

            $this->redirect(
                '/families/' . $id
            );

        } catch (Throwable $e) {

            $this->loadForm(
                array_merge(
                    $family,
                    $_POST
                ),
                $e->getMessage()
            );
        }
    }

    /**
     * Add an existing unassigned member to a family.
     */
    public function addMember(int $id): void
    {
        if (!$this->authorizeOrRedirect(
            'families.members.manage',
            '/families/' . $id
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        try {
            $memberId = (int) ($_POST['member_id'] ?? 0);
            $familyRole = trim((string) ($_POST['family_role'] ?? ''));

            if ($memberId <= 0 || $familyRole === '') {
                throw new \RuntimeException(
                    'Please select a member and family role.'
                );
            }

            $this->families->addMember(
                $tenantId,
                $id,
                $memberId,
                $familyRole
            );

            flash(
                'success',
                'Member added to family successfully.'
            );

            $this->redirect('/families/' . $id);

        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            $this->redirect('/families/' . $id);
        }
    }

    /**
     * Move an existing family member to another active family.
     */
    public function moveMember(
        int $id,
        int $memberId
    ): void {
        if (!$this->authorizeOrRedirect(
            'families.members.manage',
            '/families/' . $id
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        try {
            $destinationFamilyId = (int) (
                $_POST['destination_family_id'] ?? 0
            );

            $familyRole = trim(
                (string) ($_POST['family_role'] ?? '')
            );

            if ($destinationFamilyId <= 0 || $familyRole === '') {
                throw new \RuntimeException(
                    'Please select a destination family and family role.'
                );
            }

            $this->families->moveMember(
                $tenantId,
                $id,
                $memberId,
                $destinationFamilyId,
                $familyRole
            );

            flash(
                'success',
                'Member moved to the new family successfully.'
            );

            $this->redirect('/families/' . $destinationFamilyId);

        } catch (Throwable $e) {
            flash('error', $e->getMessage());

            $this->redirect('/families/' . $id);
        }
    }

    public function changeMemberRole(
        int $id,
        int $memberId
    ): void {
        if (!$this->authorizeOrRedirect(
            'families.members.manage',
            '/families/' . $id
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        try {
            $familyRole = trim((string) ($_POST['family_role'] ?? ''));

            if ($familyRole === '') {
                throw new \RuntimeException(
                    'Please select a family role.'
                );
            }

            $this->families->changeMemberRole(
                $tenantId,
                $id,
                $memberId,
                $familyRole
            );

            flash(
                'success',
                'Family role updated successfully.'
            );

            $this->redirect('/families/' . $id);

        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            $this->redirect('/families/' . $id);
        }
    }

    /**
     * Remove a member from a family without deleting the member.
     */
    public function removeMember(
        int $id,
        int $memberId
    ): void {
        if (!$this->authorizeOrRedirect(
            'families.members.manage',
            '/families/' . $id
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        try {
            $this->families->removeMember(
                $tenantId,
                $id,
                $memberId
            );

            flash(
                'success',
                'Member removed from family successfully.'
            );

            $this->redirect('/families/' . $id);

        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            $this->redirect('/families/' . $id);
        }
    }

    public function delete(
        int $id
    ): void {
        if (!$this->authorizeOrRedirect(
            'families.delete'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        try {

            $this->families->delete(
                $tenantId,
                $id
            );

            flash(
                'success',
                'Family archived successfully.'
            );

            redirect(
                '/families'
            );

        } catch (Throwable $e) {

            $this->view(
                'Families::Families.index',
                [
                    'title' => 'Families',

                    'families' => $this->families->all(
                        $tenantId
                    ),

                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}