<?php

declare(strict_types=1);

namespace App\Core;

final class Application
{
    /**
     * Loaded configuration.
     *
     * @var array<string, mixed>
     */
    private array $config = [];

    /**
     * Shared application services.
     *
     * @var array<string, mixed>
     */
    private array $services = [];

    public function set(string $key, mixed $service): void
    {
        $this->services[$key] = $service;
    }

    public function get(string $key): mixed
    {
        return $this->services[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->services);
    }

    public function __construct(
        private readonly string $basePath
    ) {
    }

    public function boot(): void
    {
        $this->loadConfigurations();
    }

    private function loadConfigurations(): void
    {
        $configPath = $this->basePath . '/config';

        foreach (glob($configPath . '/*.php') as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            $this->config[$name] = require $file;
        }
    }

    /**
     * Get configuration.
     *
     * Examples:
     * config('app')
     * config('database')
     * config('database.host')
     */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        $segments = explode('.', $key);

        $value = $this->config;

        foreach ($segments as $segment) {

            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
    }


}