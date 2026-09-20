<?php

declare(strict_types=1);

namespace App\Modules\Roles\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Modules\Roles\Services\RolePermissionService;
use App\Core\Authorization;
use Throwable;

final class RoleController extends Controller
{
    private RolePermissionService $service;

    public function __construct()
    {
        $this->service = new RolePermissionService();
    }

    /**
     * GET /roles
     *
     * Display all roles.
     */
public function index(): void
{
    try {

        $authorization = new Authorization();

        $authorization->authorize(
            'roles.view'
        );

        $tenantId = Auth::tenantId();

        $roles = $this->service->roles(
            $tenantId
        );

        $this->view(
            'Roles.index',
            [
                'title' => 'Roles & Permissions',
                'roles' => $roles,
            ]
        );

    } catch (Throwable $e) {

        flash(
            'error',
            $e->getMessage()
        );

        redirect('/dashboard');
    }
}

    /**
     * GET /roles/{id}/permissions
     *
     * Display permissions for a role.
     */
public function permissions(
    int $id
): void {
    try {

        $authorization = new Authorization();

        $authorization->authorize(
            'roles.permissions.manage'
        );

        $tenantId = Auth::tenantId();

        $data = $this->service->editData(
            $tenantId,
            $id
        );

        $this->view(
            'Roles.permissions',
            [
                'title' =>
                    'Role Permissions',

                'role' =>
                    $data['role'],

                'roles' =>
                    $data['roles'],

                'permissions' =>
                    $data['permissions'],

                'assigned_permission_ids' =>
                    $data['assigned_permission_ids'],
            ]
        );

    } catch (Throwable $e) {

        flash(
            'error',
            $e->getMessage()
        );

        redirect('/roles');
    }
}

    /**
     * POST /roles/{id}/permissions
     *
     * Save permissions for a role.
     */
public function updatePermissions(
    int $id
): void {
    try {

        $authorization = new Authorization();

        $authorization->authorize(
            'roles.permissions.manage'
        );

        $tenantId = Auth::tenantId();

        $permissionIds =
            $_POST['permissions'] ?? [];

        if (!is_array($permissionIds)) {
            $permissionIds = [];
        }

        $this->service->save(
            $tenantId,
            $id,
            $permissionIds
        );

        flash(
            'success',
            'Role permissions updated successfully.'
        );

        redirect(
            '/roles/' . $id . '/permissions'
        );

    } catch (Throwable $e) {

        flash(
            'error',
            $e->getMessage()
        );

        redirect(
            '/roles/' . $id . '/permissions'
        );
    }
}
}