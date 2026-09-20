<?php

declare(strict_types=1);

namespace App\Modules\Packages\Services;

use App\Modules\Packages\Packages\AbstractPackage;
use InvalidArgumentException;
use App\Modules\Packages\Support\PackageOperationResult;

final class PackageManager
{
    /**
     * @var array<string, AbstractPackage>
     */
    private array $packages = [];

    private PackageService $installed;

    public function __construct()
    {
        $this->installed = new PackageService();
    }

    /**
     * Register package.
     */
    public function register(
        AbstractPackage $package
    ): self
    {
        $this->packages[
            $package->code()
        ] = $package;

        return $this;
    }

    /**
     * Get all registered packages.
     */
    public function packages(): array
    {
        return $this->packages;
    }

    /**
     * Find package.
     */
    public function package(
        string $code
    ): AbstractPackage
    {
        if (!isset(
            $this->packages[$code]
        )) {

            throw new InvalidArgumentException(
                "Package [{$code}] is not registered."
            );

        }

        return $this->packages[$code];
    }

    /**
     * Install package.
     */
    public function install(
        string $code,
        int $tenantId,
        int $userId
    ): PackageOperationResult
    {
        $package = $this->package(
            $code
        );

        if (

            $this->installed->isInstalled(
                $tenantId,
                $code
            )

        ) {

            throw new InvalidArgumentException(
                'Package already installed.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Check dependencies.
        |--------------------------------------------------------------------------
        */

        foreach (

            $package->dependencies()

            as

            $dependency

        ) {

            if (

                !$this->installed->isInstalled(
                    $tenantId,
                    $dependency
                )

            ) {

                throw new InvalidArgumentException(
                    "Missing package dependency: {$dependency}"
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Execute package.
        |--------------------------------------------------------------------------
        */

        $package->install(

            $tenantId,

            $userId

        );

        /*
        |--------------------------------------------------------------------------
        | Record installation.
        |--------------------------------------------------------------------------
        */

        $this->installed->installRecord(

            [

                'tenant_id' => $tenantId,

                'code' => $package->code(),

                'installed_version' => $package->version(),

                'installed_by' => $userId,

            ]

        );

    }

    /**
     * Upgrade package.
     */
    /**
     * Upgrade package.
     */
    /**
     * Upgrade package.
     */
    public function upgrade(
        string $code,
        int $tenantId,
        int $userId
    ): PackageOperationResult
    {
        $package = $this->package(
            $code
        );

        $installed = $this->installed->findByCode(
            $tenantId,
            $code
        );

        if ($installed === null) {

            throw new InvalidArgumentException(
                'Package not installed.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Run package upgrade / verification.
        |--------------------------------------------------------------------------
        |
        | Even if the package is already on the latest version,
        | execute the package so it can:
        |
        | - Verify resources
        | - Restore missing records
        | - Create newly added defaults
        |
        */

        $result = $package->upgrade(

            $tenantId,

            $userId,

            $installed['installed_version']

        );

        /*
        |--------------------------------------------------------------------------
        | Update installed version only when newer.
        |--------------------------------------------------------------------------
        */

        if (

            version_compare(

                $installed['installed_version'],

                $package->version(),

                '<'

            )

        ) {

            $this->installed->updateVersion(

                $tenantId,

                $code,

                $package->version()

            );

            $result->message(
                sprintf(
                    'Package upgraded from %s to %s.',
                    $installed['installed_version'],
                    $package->version()
                )
            );

        } else {

            $result->message(
                'Package is already up to date. Verification completed.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Package Information
        |--------------------------------------------------------------------------
        */

        $result->package(

            'Upgrade',

            $package->name(),

            $package->code(),

            $installed['installed_version'],

            $package->version()

        );

        return $result;
    }

    /**
     * Uninstall package.
     */
    public function uninstall(
        string $code,
        int $tenantId,
        int $userId
    ): void
    {
        $package = $this->package(
            $code
        );

        if (

            !$package->canUninstall()

        ) {

            throw new InvalidArgumentException(
                'Package cannot be uninstalled.'
            );

        }

        $package->uninstall(

            $tenantId,

            $userId

        );

        $this->installed->uninstallRecord(

            $tenantId,

            $code

        );

    }

    /**
     * Package service.
     */
    public function service(): PackageService
    {
        return $this->installed;
    }
}