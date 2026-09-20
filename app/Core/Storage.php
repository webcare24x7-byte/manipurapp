<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Storage
{
    private string $root;

    public function __construct()
    {
        $this->root = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'storage';
    }

    /**
     * Storage root.
     */
    public function root(): string
    {
        return $this->ensure(
            $this->root
        );
    }

    /**
     * Tenant root.
     */
    public function tenant(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->root()
            . DIRECTORY_SEPARATOR
            . 'tenants'
            . DIRECTORY_SEPARATOR
            . $tenantId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    public function media(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->tenant($tenantId)
            . DIRECTORY_SEPARATOR
            . 'media'
        );
    }

    public function images(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->media($tenantId)
            . DIRECTORY_SEPARATOR
            . 'images'
        );
    }

    public function thumbnails(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->media($tenantId)
            . DIRECTORY_SEPARATOR
            . 'thumbnails'
        );
    }

    public function videos(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->media($tenantId)
            . DIRECTORY_SEPARATOR
            . 'videos'
        );
    }

    public function audio(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->media($tenantId)
            . DIRECTORY_SEPARATOR
            . 'audio'
        );
    }

    public function documents(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->media($tenantId)
            . DIRECTORY_SEPARATOR
            . 'documents'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Members
    |--------------------------------------------------------------------------
    */

    public function members(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->tenant($tenantId)
            . DIRECTORY_SEPARATOR
            . 'members'
        );
    }

    public function memberPhotos(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->members($tenantId)
            . DIRECTORY_SEPARATOR
            . 'photos'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Church
    |--------------------------------------------------------------------------
    */

    public function church(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->tenant($tenantId)
            . DIRECTORY_SEPARATOR
            . 'church'
        );
    }

    public function churchLogo(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->church($tenantId)
            . DIRECTORY_SEPARATOR
            . 'logo'
        );
    }

    public function branding(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->church($tenantId)
            . DIRECTORY_SEPARATOR
            . 'branding'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Temporary
    |--------------------------------------------------------------------------
    */

    public function temporary(
        int $tenantId
    ): string
    {
        return $this->ensure(
            $this->tenant($tenantId)
            . DIRECTORY_SEPARATOR
            . 'temporary'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Files
    |--------------------------------------------------------------------------
    */

    /**
     * Ensure a directory exists.
     */
    public function ensure(
        string $path
    ): string
    {
        if (!is_dir($path)) {

            if (
                !mkdir(
                    $path,
                    0755,
                    true
                )
                && !is_dir($path)
            ) {
                throw new RuntimeException(
                    sprintf(
                        'Unable to create directory: %s',
                        $path
                    )
                );
            }
        }

        return $path;
    }

    /**
     * Store an uploaded file.
     */
    public function moveUploadedFile(
        string $temporaryFile,
        string $destination
    ): void
    {
        $directory = dirname($destination);

        $this->ensure($directory);

        if (
            !move_uploaded_file(
                $temporaryFile,
                $destination
            )
        ) {
            throw new RuntimeException(
                'Unable to move uploaded file.'
            );
        }
    }

    /**
     * Delete a file.
     */
    public function delete(
        string $path
    ): void
    {
        if (
            is_file($path)
        ) {
            unlink($path);
        }
    }

    /**
     * Check file existence.
     */
    public function exists(
        string $path
    ): bool
    {
        return is_file($path);
    }

    /**
     * Get file size.
     */
    public function size(
        string $path
    ): int
    {
        return $this->exists($path)
            ? (int) filesize($path)
            : 0;
    }

    /**
     * Get file extension.
     */
    public function extension(
        string $path
    ): string
    {
        return strtolower(
            pathinfo(
                $path,
                PATHINFO_EXTENSION
            )
        );
    }

    /**
     * Build an absolute tenant path.
     */
    public function path(
        int $tenantId,
        string $relativePath
    ): string
    {
        return $this->tenant($tenantId)
            . DIRECTORY_SEPARATOR
            . ltrim(
                $relativePath,
                '/\\'
            );
    }
}