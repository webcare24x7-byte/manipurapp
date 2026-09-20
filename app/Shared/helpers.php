<?php

declare(strict_types=1);

use App\Core\Application;

if (!function_exists('app')) {

    /**
     * Get or set the application instance.
     */
    function app(?Application $instance = null): ?Application
    {
        static $app = null;

        if ($instance !== null) {
            $app = $instance;
        }

        return $app;
    }
}

if (!function_exists('config')) {

    /**
     * Get configuration values.
     *
     * Examples:
     * config('app.name')
     * config('database.host')
     */
    function config(?string $key = null, mixed $default = null): mixed
    {
        $application = app();

        if (!$application) {
            return $default;
        }

        return $application->config($key, $default);
    }
}

if (!function_exists('base_path')) {

    /**
     * Get the project base path.
     */
    function base_path(string $path = ''): string
    {
        $application = app();

        if (!$application) {
            return '';
        }

        return $application->basePath($path);
    }
}

if (!function_exists('dd')) {

    /**
     * Dump and die.
     */
    function dd(mixed ...$values): never
    {
        echo '<pre>';

        foreach ($values as $value) {
            var_dump($value);
        }

        echo '</pre>';

        exit;
    }
}

if (!function_exists('view')) {

    function view(
        string $view,
        array $data = [],
        string $layout = 'app'
    ): void {

        app()->get('view')->render(
            $view,
            $data,
            $layout
        );
    }
}

if (!function_exists('session')) {

    function session(): App\Core\Session
    {
        return app()->get('session');
    }
}

if (!function_exists('auth')) {

    function auth(): App\Modules\Auth\Services\AuthService
    {
        return app()->get('auth');
    }
}

if (!function_exists('tenant')) {

    function tenant(): ?array
    {
        return app()->get('currentTenant');
    }
}

if (!function_exists('tenant_id')) {

    function tenant_id(): ?int
    {
        return tenant()['id'] ?? null;
    }
}