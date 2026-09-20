<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use App\Modules\MemberApp\Models\MemberAppAccount;
use InvalidArgumentException;
use RuntimeException;

final class MemberAuthService
{
    /** ManipurApp consumer/member platform tenant. */
    public const TENANT_ID = 1;

    private MemberAppAccount $accounts;

    public function __construct()
    {
        $this->accounts = new MemberAppAccount();
    }

    public function register(array $data): array
    {
        $data = $this->validateRegistration($data);

        return $this->accounts->register(
            self::TENANT_ID,
            $data
        );
    }

    public function login(string $email, string $password): array
    {
        $email = strtolower(trim($email));

        if ($email === '' || $password === '') {
            throw new InvalidArgumentException('Email and password are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }

        $user = $this->accounts->findMemberLogin(
            self::TENANT_ID,
            $email
        );

        if ($user === null || !password_verify($password, (string) $user['password'])) {
            throw new RuntimeException('Invalid email or password.');
        }

        if (($user['member_status'] ?? 'Active') !== 'Active') {
            throw new RuntimeException('Your member account is inactive.');
        }

        return [
            'user_id' => (int) $user['id'],
            'member_id' => (int) $user['member_id'],
            'tenant_id' => self::TENANT_ID,
            'role' => 'member',
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
        ];
    }

    private function validateRegistration(array $data): array
    {
        $firstName = trim((string) ($data['first_name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $phone = trim((string) ($data['phone'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $confirmation = (string) ($data['password_confirmation'] ?? '');

        if ($firstName === '') {
            throw new InvalidArgumentException('First name is required.');
        }

        if ($lastName === '') {
            throw new InvalidArgumentException('Last name is required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }

        if ($phone === '') {
            throw new InvalidArgumentException('Phone number is required.');
        }

        if (!preg_match('/^[0-9+()\-\s]{7,25}$/', $phone)) {
            throw new InvalidArgumentException('Please enter a valid phone number.');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters.');
        }

        if ($password !== $confirmation) {
            throw new InvalidArgumentException('Passwords do not match.');
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ];
    }
}
