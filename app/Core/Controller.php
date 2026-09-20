<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'app'
    ): void {
        view($view, $data, $layout);
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . config('app.base_path') . $url);

        exit;
    }

    protected function db(): Database
    {
        return app()->get('db');
    }

    protected function session(): Session
    {
        return app()->get('session');
    }

    protected function auth()
    {
        return app()->get('auth');
    }

    protected function tenant(): ?array
    {
        return tenant();
    }

    protected function tenantId(): ?int
    {
        return tenant_id();
    }
}