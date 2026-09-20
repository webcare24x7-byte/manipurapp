<?php

declare(strict_types=1);

if (!function_exists('redirect')) {

    function redirect(string $path): never
    {
        header(
            'Location: ' .
            config('app.base_path') .
            $path
        );

        exit;
    }

}

if (!function_exists('base_path')) {

    function base_path(string $path = ''): string
    {
        $base = __DIR__;

        return $path
            ? $base . '/' . ltrim($path, '/')
            : $base;
    }

}

if (!function_exists('storage_path')) {

    function storage_path(string $path = ''): string
    {
        $storage = base_path('storage');

        return $path
            ? $storage . '/' . ltrim($path, '/')
            : $storage;
    }

}

if (!function_exists('flash')) {

    function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = [
            'type' => $type,
            'message' => $message,
        ];
    }

}

if (!function_exists('get_flash')) {

    function get_flash(): array
    {
        $messages = $_SESSION['_flash'] ?? [];

        unset($_SESSION['_flash']);

        return $messages;
    }

}