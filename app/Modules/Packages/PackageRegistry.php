<?php

declare(strict_types=1);

namespace App\Modules\Packages;

use App\Modules\Packages\Services\PackageManager;
use App\Modules\Packages\Packages\LeadershipLookupPackage;

final class PackageRegistry
{
    /**
     * Build the Package Manager with all
     * available packages registered.
     */
    public static function make(): PackageManager
    {
        return (new PackageManager())

            ->register(

                new LeadershipLookupPackage()

            );

    }
}