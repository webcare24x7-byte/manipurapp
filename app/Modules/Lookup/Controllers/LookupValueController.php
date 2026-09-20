<?php

declare(strict_types=1);

namespace App\Modules\Lookup\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Authorization;
use App\Modules\Lookup\Services\LookupTypeService;
use App\Modules\Lookup\Services\LookupValueService;
use Throwable;

final class LookupValueController extends Controller
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
     * GET /lookup-types/{lookupTypeId}/values/create
     */
    public function create(
        int $lookupTypeId
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.create'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $lookupType = $this->types->find(
            $tenantId,
            $lookupTypeId
        );

        if ($lookupType === null) {

            http_response_code(404);

            exit('Lookup Type not found.');

        }

        $this->view(
            'Lookup::LookupValues.create',
            [
                'title' => 'Add Lookup Value',

                'lookupType' => $lookupType,
            ]
        );
    }

    /**
     * POST /lookup-types/{lookupTypeId}/values
     */
    public function store(
        int $lookupTypeId
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.create'
        )) {
            return;
        }

        try {

            $data = $_POST;

            $data['tenant_id'] = (int) Auth::tenantId();

            $data['lookup_type_id'] = $lookupTypeId;

            $data['created_by'] = (int) Auth::id();

            $this->values->create($data);

            redirect(
                '/lookup-types/' . $lookupTypeId
            );

        } catch (Throwable $e) {

            $lookupType = $this->types->find(
                (int) Auth::tenantId(),
                $lookupTypeId
            );

            $this->view(
                'Lookup::LookupValues.create',
                [
                    'title' => 'Add Lookup Value',

                    'error' => $e->getMessage(),

                    'lookupType' => $lookupType,

                    'old' => $_POST,
                ]
            );

        }
    }

    /**
     * GET /lookup-types/{lookupTypeId}/values/{id}/edit
     */
    public function edit(
        int $lookupTypeId,
        int $id
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.edit'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $lookupType = $this->types->find(
            $tenantId,
            $lookupTypeId
        );

        if ($lookupType === null) {

            http_response_code(404);

            exit('Lookup Type not found.');

        }

        $lookupValue = $this->values->find(
            $tenantId,
            $id
        );

        if ($lookupValue === null) {

            http_response_code(404);

            exit('Lookup Value not found.');

        }

        $this->view(
            'Lookup::LookupValues.edit',
            [
                'title' => 'Edit Lookup Value',

                'lookupType' => $lookupType,

                'lookupValue' => $lookupValue,
            ]
        );
    }

    /**
     * POST /lookup-types/{lookupTypeId}/values/{id}
     */
    public function update(
        int $lookupTypeId,
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

            $this->values->update(
                (int) Auth::tenantId(),
                $id,
                $data
            );

            redirect(
                '/lookup-types/' . $lookupTypeId
            );

        } catch (Throwable $e) {

            $lookupType = $this->types->find(
                (int) Auth::tenantId(),
                $lookupTypeId
            );

            $lookupValue = $this->values->find(
                (int) Auth::tenantId(),
                $id
            );

            $this->view(
                'Lookup::LookupValues.edit',
                [
                    'title' => 'Edit Lookup Value',

                    'error' => $e->getMessage(),

                    'lookupType' => $lookupType,

                    'lookupValue' => array_merge(
                        $lookupValue ?? [],
                        $_POST
                    ),
                ]
            );

        }
    }

    /**
     * POST /lookup-types/{lookupTypeId}/values/{id}/delete
     */
    public function delete(
        int $lookupTypeId,
        int $id
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'lookup.delete'
        )) {
            return;
        }

        $this->values->delete(
            (int) Auth::tenantId(),
            $id
        );

        redirect(
            '/lookup-types/' . $lookupTypeId
        );
    }
}