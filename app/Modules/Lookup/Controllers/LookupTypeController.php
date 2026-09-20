<?php

declare(strict_types=1);

namespace App\Modules\Lookup\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Authorization;
use App\Modules\Lookup\Services\LookupTypeService;
use App\Modules\Lookup\Services\LookupValueService;
use Throwable;

final class LookupTypeController extends Controller
{
    private LookupTypeService $types;

    private LookupValueService $values;

    private Authorization $authorization;

    public function __construct()
    {
        $this->types = new LookupTypeService();

        $this->values = new LookupValueService();

        $this->authorization = new Authorization();
    }

    /**
     * Authorization helper.
     */
    private function authorizeOrRedirect(
        string $permission
    ): bool {
        try {

            $this->authorization->authorize(
                $permission
            );

            return true;

        } catch (\RuntimeException $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect(
                '/dashboard'
            );

            return false;
        }
    }

    /**
     * GET /lookup-types
     */
    public function index(): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.view'
        )) {
            return;
        }

        $this->view(
            'Lookup::LookupTypes.index',
            [
                'title' => 'Lookup Types',

                'lookupTypes' => $this->types->all(
                    (int) Auth::tenantId()
                ),
            ]
        );
    }

    /**
     * GET /lookup-types/create
     */
    public function create(): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.create'
        )) {
            return;
        }

        $this->view(
            'Lookup::LookupTypes.create',
            [
                'title' => 'Add Lookup Type',
            ]
        );
    }

    /**
     * POST /lookup-types
     */
    public function store(): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.create'
        )) {
            return;
        }

        try {

            $data = $_POST;

            $data['tenant_id'] = (int) Auth::tenantId();

            $data['created_by'] = (int) Auth::id();

            $this->types->create($data);

            redirect('/lookup-types');

        } catch (Throwable $e) {

            $this->view(
                'Lookup::LookupTypes.create',
                [
                    'title' => 'Add Lookup Type',

                    'error' => $e->getMessage(),

                    'old' => $_POST,
                ]
            );

        }
    }

    /**
     * GET /lookup-types/{id}
     */
    public function show(
        int $id
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.view'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $lookupType = $this->types->find(
            $tenantId,
            $id
        );

        if ($lookupType === null) {

            http_response_code(404);

            exit('Lookup Type not found.');

        }

        $lookupValues = $this->values->all(
            $tenantId,
            $id
        );

        $this->view(
            'Lookup::LookupTypes.show',
            [
                'title' => $lookupType['name'],

                'lookupType' => $lookupType,

                'lookupValues' => $lookupValues,
            ]
        );
    }

    /**
     * GET /lookup-types/{id}/edit
     */
    public function edit(
        int $id
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.edit'
        )) {
            return;
        }

        $lookupType = $this->types->find(
            (int) Auth::tenantId(),
            $id
        );

        if ($lookupType === null) {

            http_response_code(404);

            exit('Lookup Type not found.');

        }

        $this->view(
            'Lookup::LookupTypes.edit',
            [
                'title' => 'Edit Lookup Type',

                'lookupType' => $lookupType,
            ]
        );
    }

    /**
     * POST /lookup-types/{id}
     */
    public function update(
        int $id
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.edit'
        )) {
            return;
        }

        try {

            $data = $_POST;

            $data['updated_by'] = (int) Auth::id();

            $this->types->update(
                (int) Auth::tenantId(),
                $id,
                $data
            );

            redirect('/lookup-types');

        } catch (Throwable $e) {

            $lookupType = $this->types->find(
                (int) Auth::tenantId(),
                $id
            );

            $this->view(
                'Lookup::LookupTypes.edit',
                [
                    'title' => 'Edit Lookup Type',

                    'error' => $e->getMessage(),

                    'lookupType' => array_merge(
                        $lookupType ?? [],
                        $_POST
                    ),
                ]
            );

        }
    }

    /**
     * POST /lookup-types/{id}/delete
     */
    public function delete(
        int $id
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.delete'
        )) {
            return;
        }

        $this->types->delete(
            (int) Auth::tenantId(),
            $id
        );

        redirect('/lookup-types');
    }
}