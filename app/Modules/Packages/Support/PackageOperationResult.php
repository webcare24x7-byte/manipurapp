<?php

declare(strict_types=1);

namespace App\Modules\Packages\Support;

final class PackageOperationResult
{
    /*
    |--------------------------------------------------------------------------
    | Package Information
    |--------------------------------------------------------------------------
    */

    public string $operation = '';

    public string $packageName = '';

    public string $packageCode = '';

    public string $fromVersion = '';

    public string $toVersion = '';

    /*
    |--------------------------------------------------------------------------
    | Lookup Types
    |--------------------------------------------------------------------------
    */

    public int $lookupTypesCreated = 0;

    public int $lookupTypesExisting = 0;

    public int $lookupTypesRestored = 0;

    /*
    |--------------------------------------------------------------------------
    | Lookup Values
    |--------------------------------------------------------------------------
    */

    public int $lookupValuesCreated = 0;

    public int $lookupValuesExisting = 0;

    public int $lookupValuesRestored = 0;

    /*
    |--------------------------------------------------------------------------
    | General
    |--------------------------------------------------------------------------
    */

    public bool $success = true;

    /**
     * Execution time in milliseconds.
     */
    public float $executionTime = 0;

    /**
     * Warnings.
     *
     * @var string[]
     */
    public array $warnings = [];

    /**
     * Messages.
     *
     * @var string[]
     */
    public array $messages = [];

    /**
     * Errors.
     *
     * @var string[]
     */
    public array $errors = [];

    /**
     * Add message.
     */
    public function message(
        string $message
    ): void
    {
        $this->messages[] = $message;
    }

    /**
     * Add warning.
     */
    public function warning(
        string $warning
    ): void
    {
        $this->warnings[] = $warning;
    }

    /**
     * Add error.
     */
    public function error(
        string $error
    ): void
    {
        $this->success = false;

        $this->errors[] = $error;
    }

    /**
     * Set package information.
     */
    public function package(
        string $operation,
        string $name,
        string $code,
        string $fromVersion = '',
        string $toVersion = ''
    ): void
    {
        $this->operation = $operation;

        $this->packageName = $name;

        $this->packageCode = $code;

        $this->fromVersion = $fromVersion;

        $this->toVersion = $toVersion;
    }
}