<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Services;

use App\Modules\Taxi\Models\TaxiVendor;
use RuntimeException;

final class TaxiVendorService
{
    private TaxiVendor $vendors;

    public function __construct()
    {
        $this->vendors = new TaxiVendor();
    }

    public function all(int $tenantId): array
    {
        return $this->vendors->all($tenantId);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->vendors->find($tenantId, $id);
    }

    public function create(int $tenantId, array $input, int $userId): int
    {
        $data = $this->validate($input);
        if ($this->vendors->business_name_exists($tenantId, $data['business_name'])) {
            throw new RuntimeException('A taxi business with this name already exists.');
        }
        $data['business_slug'] = $this->uniqueSlug($tenantId, $this->slugify($data['business_name']));
        return $this->vendors->create($tenantId, $data, $userId);
    }

    public function update(int $tenantId, int $id, array $input, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Taxi business not found.');
        }

        $data = $this->validate($input);
        if ($this->vendors->business_name_exists($tenantId, $data['business_name'], $id)) {
            throw new RuntimeException('A taxi business with this name already exists.');
        }
        $data['business_slug'] = $this->uniqueSlug($tenantId, $this->slugify($data['business_name']), $id);
        $this->vendors->update($tenantId, $id, $data, $userId);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Taxi business not found.');
        }
        $this->vendors->softDelete($tenantId, $id, $userId);
    }

    private function validate(array $input): array
    {
        $data = [
            'business_name' => $this->nullableString($input['business_name'] ?? ''),
            'legal_name' => $this->nullableString($input['legal_name'] ?? ''),
            'phone' => $this->nullableString($input['phone'] ?? ''),
            'email' => $this->nullableString($input['email'] ?? ''),
            'address' => $this->nullableString($input['address'] ?? ''),
            'city' => $this->nullableString($input['city'] ?? ''),
            'district' => $this->nullableString($input['district'] ?? ''),
            'state' => $this->nullableString($input['state'] ?? ''),
            'postal_code' => $this->nullableString($input['postal_code'] ?? ''),
            'description' => $this->nullableString($input['description'] ?? ''),
            'status' => $this->nullableString($input['status'] ?? ''),
        ];

        if (!$data['business_name']) {
            throw new RuntimeException('Taxi business name is required.');
        }

        $data['state'] = $data['state'] ?: 'Manipur';
        $data['status'] = $data['status'] ?: 'Active';

        if (!in_array($data['status'], ['Active', 'Inactive', 'Suspended'], true)) {
            throw new RuntimeException('Invalid status.');
        }

        return $data;
    }

    private function uniqueSlug(int $tenantId, string $base, ?int $ignoreId = null): string
    {
        $slug = $base ?: 'taxi-business';
        $candidate = $slug;
        $suffix = 2;
        while ($this->vendors->slug_exists($tenantId, $candidate, $ignoreId)) {
            $candidate = $slug . '-' . $suffix++;
        }
        return $candidate;
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
