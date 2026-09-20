<?php

declare(strict_types=1);

namespace App\Modules\Packages\Packages;
use App\Modules\Packages\Support\PackageOperationResult;

abstract class AbstractPackage
{
    /**
     * Unique package code.
     *
     * Example:
     * lookup.core
     */
    abstract public function code(): string;

    /**
     * Display name.
     */
    abstract public function name(): string;

    /**
     * Description.
     */
    abstract public function description(): string;

    /**
     * Current package version.
     */
    abstract public function version(): string;

    /**
     * Install package.
     */
    abstract public function install(
        int $tenantId,
        int $userId
    ): PackageOperationResult;

    /**
     * Upgrade package.
     *
     * Default implementation.
     */
    abstract public function upgrade(
        int $tenantId,
        int $userId,
        string $installedVersion
    ): PackageOperationResult;

    /**
     * Uninstall package.
     *
     * Default implementation.
     */
    public function uninstall(
        int $tenantId,
        int $userId
    ): void
    {
        //
    }

    /**
     * Package dependencies.
     *
     * Example:
     *
     * [
     *     'lookup.core',
     *     'roles.core'
     * ]
     */
    public function dependencies(): array
    {
        return [];
    }

    /**
     * Can this package be uninstalled?
     */
    public function canUninstall(): bool
    {
        return true;
    }

    /**
     * Is this a core package?
     */
    public function isCore(): bool
    {
        return false;
    }

}