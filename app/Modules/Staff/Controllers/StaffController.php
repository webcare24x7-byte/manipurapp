<?php

declare(strict_types=1);

namespace App\Modules\Staff\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Staff\Services\StaffService;
use Throwable;

final class StaffController extends Controller
{
    private StaffService $service;

    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new StaffService();

        $this->authorization = new Authorization();
    }

    /**
     * Staff list.
     *
     * GET /staff
     */
    public function index(): void
    {
        try {

            $this->authorization->authorize(
                'staff.view'
            );

            $tenantId = Auth::tenantId();

            $staff = $this->service->list(
                $tenantId
            );

            $this->view(
                'Staff.index',
                [
                    'title' => 'Staff',
                    'staff' => $staff,
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
     * Create form.
     *
     * GET /staff/create
     */
    public function create(): void
    {
        try {

            $this->authorization->authorize(
                'staff.create'
            );

            $tenantId = Auth::tenantId();

            $data = $this->service->createData(
                $tenantId
            );

            $this->view(
                'Staff.create',
                [
                    'title'   => 'Add Staff',
                    'members' => $data['members'],
                    'roles'   => $data['roles'],
                ]
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect('/staff');
        }
    }

    /**
     * Store new staff.
     *
     * POST /staff
     */
    public function store(): void
    {
        try {

            $this->authorization->authorize(
                'staff.create'
            );

            $tenantId = Auth::tenantId();

            $id = $this->service->create(
                $tenantId,
                $_POST
            );

            flash(
                'success',
                'Staff account created successfully.'
            );

            redirect(
                '/staff/' . $id
            );

        } catch (Throwable $e) {

            $tenantId = Auth::tenantId();

            $data = $this->service->createData(
                $tenantId
            );

            $this->view(
                'Staff.create',
                [
                    'title'   => 'Add Staff',
                    'members' => $data['members'],
                    'roles'   => $data['roles'],
                    'error'   => $e->getMessage(),
                    'old'     => $_POST,
                ]
            );
        }
    }

    /**
     * Staff profile.
     *
     * GET /staff/{id}
     */
    public function show(int $id): void
    {
        try {

            $this->authorization->authorize(
                'staff.view'
            );

            $tenantId = Auth::tenantId();

            $staff = $this->service->get(
                $tenantId,
                $id
            );

            $this->view(
                'Staff.show',
                [
                    'title' => 'Staff Profile',
                    'staff' => $staff,
                ]
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect('/staff');
        }
    }

    /**
     * Edit form.
     *
     * GET /staff/{id}/edit
     */
    public function edit(int $id): void
    {
        try {

            $this->authorization->authorize(
                'staff.edit'
            );

            $tenantId = Auth::tenantId();

            $staff = $this->service->get(
                $tenantId,
                $id
            );

            $data = $this->service->createData(
                $tenantId
            );

            $this->view(
                'Staff.edit',
                [
                    'title'   => 'Edit Staff',
                    'staff'   => $staff,
                    'members' => $data['members'],
                    'roles'   => $data['roles'],
                ]
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect('/staff');
        }
    }

    /**
     * Update staff.
     *
     * POST /staff/{id}/update
     */
    public function update(int $id): void
    {
        try {

            $this->authorization->authorize(
                'staff.edit'
            );

            $tenantId = Auth::tenantId();

            $this->service->update(
                $tenantId,
                $id,
                $_POST
            );

            flash(
                'success',
                'Staff account updated successfully.'
            );

            redirect(
                '/staff/' . $id
            );

        } catch (Throwable $e) {

            $tenantId = Auth::tenantId();

            try {

                $staff = $this->service->get(
                    $tenantId,
                    $id
                );

                $data = $this->service->createData(
                    $tenantId
                );

                $this->view(
                    'Staff.edit',
                    [
                        'title'   => 'Edit Staff',
                        'staff'   => $staff,
                        'members' => $data['members'],
                        'roles'   => $data['roles'],
                        'error'   => $e->getMessage(),
                        'old'     => $_POST,
                    ]
                );

            } catch (Throwable $inner) {

                flash(
                    'error',
                    $e->getMessage()
                );

                redirect('/staff');
            }
        }
    }

    /**
     * Deactivate staff account.
     *
     * POST /staff/{id}/delete
     */
    public function delete(int $id): void
    {
        try {

            $this->authorization->authorize(
                'staff.deactivate'
            );

            $tenantId = Auth::tenantId();

            $this->service->updateStatus(
                $tenantId,
                $id,
                'Inactive'
            );

            flash(
                'success',
                'Staff account deactivated successfully.'
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );
        }

        redirect('/staff');
    }
}