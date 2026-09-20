<?php

declare(strict_types=1);

namespace App\Modules\Packages\Services;

use App\Modules\Packages\Models\Package;
use InvalidArgumentException;

final class PackageService
{
    private Package $packages;

    public function __construct()
    {
        $this->packages = new Package();
    }

    /**
     * Get installed packages.
     */
    public function all(
        int $tenantId
    ): array
    {
        return $this->packages->all(
            $tenantId
        );
    }

    /**
     * Find package.
     */
    public function find(
        int $tenantId,
        int $id
    ): ?array
    {
        return $this->packages->find(
            $tenantId,
            $id
        );
    }

    /**
     * Find package by code.
     */
    public function findByCode(
        int $tenantId,
        string $code
    ): ?array
    {
        return $this->packages->findByCode(
            $tenantId,
            $code
        );
    }

    /**
     * Check whether package is installed.
     */
    public function isInstalled(
        int $tenantId,
        string $code
    ): bool
    {
        return $this->packages->isInstalled(
            $tenantId,
            $code
        );
    }

    /**
     * Record package installation.
     */
    public function installRecord(
        array $data
    ): int
    {
        $this->validate($data);

        if (

            $this->packages->isInstalled(
                (int) $data['tenant_id'],
                $data['code']
            )

        ) {

            throw new InvalidArgumentException(
                'Package is already installed.'
            );

        }

        $data = $this->normalize(
            $data
        );

        return $this->packages->create(
            $data
        );
    }

    /**
     * Update installed version.
     */
    public function updateVersion(
        int $tenantId,
        string $code,
        string $version
    ): void
    {
        $package = $this->packages->findByCode(
            $tenantId,
            $code
        );

        if ($package === null) {

            throw new InvalidArgumentException(
                'Package not installed.'
            );

        }

        $package['installed_version'] = $version;

        $this->packages->update(
            $tenantId,
            (int) $package['id'],
            $package
        );
    }

    /**
     * Remove package record.
     */
    public function uninstallRecord(
        int $tenantId,
        string $code
    ): void
    {
        $package = $this->packages->findByCode(
            $tenantId,
            $code
        );

        if ($package === null) {

            return;

        }

        $this->packages->softDelete(
            $tenantId,
            (int) $package['id']
        );
    }

    /**
     * Validate.
     */
    private function validate(
        array $data
    ): void
    {
        if (
            empty($data['tenant_id'])
        ) {

            throw new InvalidArgumentException(
                'Tenant is required.'
            );

        }

        if (
            empty(trim($data['code'] ?? ''))
        ) {

            throw new InvalidArgumentException(
                'Package code is required.'
            );

        }

        if (
            empty(trim($data['installed_version'] ?? ''))
        ) {

            throw new InvalidArgumentException(
                'Installed version is required.'
            );

        }

    }

    /**
     * Normalize.
     */
    private function normalize(
        array $data
    ): array
    {
        $defaults = [

            'status' => 'Installed',

            'installed_by' => null,

        ];

        return array_merge(
            $defaults,
            $data
        );
    }
}