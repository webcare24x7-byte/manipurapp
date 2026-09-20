<?php

declare(strict_types=1);

namespace App\Modules\Onboarding\Controllers;

use App\Core\Controller;
use App\Modules\Onboarding\Models\Tenant;
use App\Modules\Onboarding\Services\AuthenticationService;
use Throwable;

final class LoginController extends Controller
{
    private Tenant $tenant;

    public function __construct()
    {
        $this->tenant = new Tenant();
    }

    /**
     * GET /login
     *
     * Public ChurchOS tenant discovery page.
     */
    public function index(): void
    {
        $this->view(
            'Onboarding.login',
            [
                'title'   => 'Welcome, Business Owner / Vendor',
                'results' => [],
                'search'  => '',
            ],
            'guest'
        );
    }

    /**
     * POST /login
     *
     * Search for a church.
     */
    public function store(): void
    {
        $search = trim(
            $_POST['church'] ?? ''
        );

        if ($search === '') {

            $this->view(
                'Onboarding.login',
                [
                    'title'   => 'Welcome, Business Owner / Vendor',
                    'results' => [],
                    'search'  => '',
                    'error'   => 'Please enter your business or workspace name.',
                ],
                'guest'
            );

            return;
        }

        try {

            $results = $this->tenant->search(
                $search
            );

            $this->view(
                'Onboarding.login',
                [
                    'title'   => 'Welcome, Business Owner / Vendor',
                    'results' => $results,
                    'search'  => $search,
                ],
                'guest'
            );

        } catch (Throwable $e) {

            $this->view(
                'Onboarding.login',
                [
                    'title'   => 'Welcome, Business Owner / Vendor',
                    'results' => [],
                    'search'  => $search,
                    'error'   => 'Unable to search for business workspaces.',
                ],
                'guest'
            );
        }
    }

    /**
     * GET /c/{slug}/login
     */
    public function tenantIndex(
        string $slug
    ): void {
        $tenant = $this->tenant->findBySlug(
            $slug
        );

        if ($tenant === null) {
            abort(404);
        }

        $this->view(
            'Onboarding.tenant-login',
            [
                'title'  => 'Welcome, Business Owner / Vendor',
                'tenant' => $tenant,
            ],
            'guest'
        );
    }

    /**
     * POST /c/{slug}/login
     */
    public function tenantStore(
        string $slug
    ): void {
        $tenant = $this->tenant->findBySlug(
            $slug
        );

        if ($tenant === null) {
            abort(404);
        }

        $service = new AuthenticationService();

        try {

            $data = $_POST;

            /*
            |--------------------------------------------------------------------------
            | Tenant is determined by the URL.
            |--------------------------------------------------------------------------
            */

            $data['tenant_slug'] = $slug;

            $result = $service->login(
                $data
            );

            /*
            |--------------------------------------------------------------------------
            | Member-only account
            |--------------------------------------------------------------------------
            |
            | Do not authenticate into the admin application.
            |
            */

            if (
                ($result['status'] ?? '') ===
                'member_only'
            ) {

                $this->view(
                    'Onboarding.tenant-login',
                    [
                        'title'  => 'Welcome, Business Owner / Vendor',
                        'tenant' => $tenant,
                        'error'   =>
                            'This account is a member-only account. Please use the Member App to access your member area.',
                    ],
                    'guest'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Staff / Administrator
            |--------------------------------------------------------------------------
            |
            | Existing Admin destination remains unchanged.
            |
            */

            header(
                'Location: '
                . config('app.base_path')
                . '/dashboard'
            );

            exit;

        } catch (Throwable $e) {

            $this->view(
                'Onboarding.tenant-login',
                [
                    'title'  => 'Welcome, Business Owner / Vendor',
                    'tenant' => $tenant,
                    'error'  => $e->getMessage(),
                ],
                'guest'
            );
        }
    }
}