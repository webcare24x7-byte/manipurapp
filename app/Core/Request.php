<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function path(): string
    {
        $uri = parse_url(
            $_SERVER['REQUEST_URI'] ?? '/',
            PHP_URL_PATH
        ) ?? '/';

        $basePath = config('app.base_path', '');

        if (
            $basePath !== '' &&
            str_starts_with($uri, $basePath)
        ) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function is(string $path): bool
    {
        $current = $this->path();

        if ($path === '/') {
            return $current === '/';
        }

        $path = rtrim($path, '/');

        return $current === $path
            || str_starts_with($current, $path . '/');
    }
}