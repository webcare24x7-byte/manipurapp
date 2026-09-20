<?php

declare(strict_types=1);

namespace App\Modules\Packages\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Authorization;
use App\Modules\Packages\PackageRegistry;
use Throwable;

final class PackageController extends Controller
{
    /**
     * Package Manager.
     */
    private $manager;

    /**
     * Authorization.
     */
    private Authorization $authorization;

    public function __construct()
    {
        $this->manager = PackageRegistry::make();

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
     * GET /packages
     */
    public function index(): void
    {
        if (!$this->authorizeOrRedirect(
            'packages.view'
        )) {
            return;
        }

        $tenantId = (int) Auth::tenantId();

        $registered = $this->manager->packages();

        $installed = [];

        foreach ($registered as $package) {

            $installed[$package->code()] = $this->manager
                ->service()
                ->findByCode(
                    $tenantId,
                    $package->code()
                );

        }

        $this->view(
            'Packages::Packages.index',
            [

                'title' => 'Packages',

                'packages' => $registered,

                'installed' => $installed,

            ]
        );
    }

    /**
     * POST /packages/install/{code}
     */
    public function install(
        string $code
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'packages.install'
        )) {
            return;
        }

        try {

            $result = $this->manager->install(

                $code,

                (int) Auth::tenantId(),

                (int) Auth::id()

            );

            $this->view(
                'Packages::Packages.operation-result',
                [

                    'title' => 'Package Installed',

                    'packageCode' => $code,

                    'result' => $result,

                ]
            );

        } catch (Throwable $e) {

            $this->view(
                'Packages::Packages.error',
                [

                    'title' => 'Package Installation',

                    'error' => $e->getMessage(),

                ]
            );

        }
    }

    /**
     * POST /packages/upgrade/{code}
     */
    public function upgrade(
        string $code
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'packages.upgrade'
        )) {
            return;
        }

        try {

            $result = $this->manager->upgrade(

                $code,

                (int) Auth::tenantId(),

                (int) Auth::id()

            );

            $this->view(
                'Packages::Packages.operation-result',
                [

                    'title' => 'Package Upgraded',

                    'packageCode' => $code,

                    'result' => $result,

                ]
            );

        } catch (Throwable $e) {

            $this->view(
                'Packages::Packages.error',
                [

                    'title' => 'Package Upgrade',

                    'error' => $e->getMessage(),

                ]
            );

        }
    }

    /**
     * POST /packages/uninstall/{code}
     */
    public function uninstall(
        string $code
    ): void
    {
        if (!$this->authorizeOrRedirect(
            'packages.uninstall'
        )) {
            return;
        }

        try {

            $this->manager->uninstall(

                $code,

                (int) Auth::tenantId(),

                (int) Auth::id()

            );

            redirect('/packages');

        } catch (Throwable $e) {

            $this->view(
                'Packages::Packages.error',
                [

                    'title' => 'Package Uninstall',

                    'error' => $e->getMessage(),

                ]
            );

        }
    }
}