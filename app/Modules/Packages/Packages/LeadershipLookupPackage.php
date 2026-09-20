<?php

declare(strict_types=1);

namespace App\Modules\Packages\Packages;

use App\Modules\Lookup\Services\LookupTypeService;
use App\Modules\Lookup\Services\LookupValueService;
use App\Modules\Packages\Data\Lookup\LeadershipLookupDefaults;
use App\Modules\Packages\Support\PackageOperationResult;

final class LeadershipLookupPackage extends AbstractPackage
{
    private LookupTypeService $lookupTypes;

    private LookupValueService $lookupValues;

    public function __construct()
    {
        $this->lookupTypes = new LookupTypeService();

        $this->lookupValues = new LookupValueService();
    }

    /**
     * Package Code
     */
    public function code(): string
    {
        return 'lookup.leadership';
    }

    /**
     * Package Name
     */
    public function name(): string
    {
        return 'Leadership Lookup Package';
    }

    /**
     * Description
     */
    public function description(): string
    {
        return
            'Installs lookup types and lookup values '
            . 'required by the Leadership module.';
    }

    /**
     * Current Version
     */
    public function version(): string
    {
        return '1.0.0';
    }

    /**
     * Package Dependencies
     */
    public function dependencies(): array
    {
        return [];
    }

    /**
     * Core package.
     */
    public function isCore(): bool
    {
        return true;
    }

    /**
     * Prevent uninstall.
     */
    public function canUninstall(): bool
    {
        return false;
    }

    /**
     * Install package.
     */
    public function install(
        int $tenantId,
        int $userId
    ): PackageOperationResult
    {
        $result = new PackageOperationResult();

        $lookupTypes = $this->installLookupTypes(
            $tenantId,
            $userId,
            $result
        );

        $this->installLookupValues(
            $tenantId,
            $userId,
            $lookupTypes,
            $result
        );

        return $result;
    }

        /**
     * Install lookup types.
     *
     * @return array<string,int>
     */
    private function installLookupTypes(
    int $tenantId,
    int $userId,
    PackageOperationResult $result
    ): array
    {
        $installed = [];

        foreach (

            LeadershipLookupDefaults::LOOKUPS

            as

            $slug => $lookup

        ) {

            $response = $this->lookupTypes->ensure(

                [

                    'tenant_id' => $tenantId,

                    'name' => $lookup['name'],

                    'slug' => $slug,

                    'description' =>
                        $lookup['description'],

                    'status' => 'Active',

                    'created_by' => $userId,

                    'is_system' => 1,

                ]

            );

            $installed[$slug] = $response['id'];

            switch ($response['action']) {

                case 'created':

                    $result->lookupTypesCreated++;

                    break;

                case 'existing':

                    $result->lookupTypesExisting++;

                    break;

                case 'restored':

                    $result->lookupTypesRestored++;

                    break;

            }

        }

        return $installed;
    }

        /**
     * Install lookup values.
     *
     * @param array<string,int> $lookupTypes
     */
    private function installLookupValues(
    int $tenantId,
    int $userId,
    array $lookupTypes,
    PackageOperationResult $result
    ): void
    {
        foreach (

            LeadershipLookupDefaults::LOOKUPS

            as

            $slug => $lookup

        ) {

            if (
                !isset($lookupTypes[$slug])
            ) {

                continue;

            }

            $lookupTypeId = $lookupTypes[$slug];

            foreach (

                $lookup['values']

                as

                $value

            ) {

                $response = $this->lookupValues->ensure(

                    [

                        'tenant_id' => $tenantId,

                        'lookup_type_id' => $lookupTypeId,

                        'name' => $value['name'],

                        'slug' => $value['slug'],

                        'display_order' =>
                            $value['display_order'],

                        'is_default' =>
                            $value['is_default'],

                        'status' => 'Active',

                        'created_by' => $userId,

                        'is_system' => 1,

                    ]

                );

                switch ($response['action']) {

                    case 'created':

                        $result->lookupValuesCreated++;

                        break;

                    case 'existing':

                        $result->lookupValuesExisting++;

                        break;

                    case 'restored':

                        $result->lookupValuesRestored++;

                        break;

                }

            }

        }

    }
    
    public function upgrade(
        int $tenantId,
        int $userId,
        string $installedVersion
    ): PackageOperationResult
    {
        return $this->install(

            $tenantId,

            $userId

        );
    }
}