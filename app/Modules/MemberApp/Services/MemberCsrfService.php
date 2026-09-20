<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

final class MemberCsrfService
{
    private const SESSION_KEY = 'memberapp_csrf_token';

    /**
     * Return the current MemberApp CSRF token.
     */
    public function token(): string
    {
        $token = $_SESSION[self::SESSION_KEY] ?? null;

        if (!is_string($token) || $token === '') {
            $token = $this->generate();

            $_SESSION[self::SESSION_KEY] = $token;
        }

        return $token;
    }

    /**
     * Validate a submitted token.
     */
    public function validate(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $stored = $_SESSION[self::SESSION_KEY] ?? null;

        if (!is_string($stored) || $stored === '') {
            return false;
        }

        return hash_equals($stored, $token);
    }

    /**
     * Regenerate the token.
     */
    public function regenerate(): string
    {
        $token = $this->generate();

        $_SESSION[self::SESSION_KEY] = $token;

        return $token;
    }

    private function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}