<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Services;

use App\Modules\Taxi\Models\TaxiService;
use App\Modules\Taxi\Models\TaxiVendor;
use RuntimeException;

final class TaxiServiceService
{
    private TaxiService $services;
    private TaxiVendor $vendorsModel;

    public function __construct()
    {
        $this->services = new TaxiService();
        $this->vendorsModel = new TaxiVendor();
    }

    public function all(int $tenantId): array
    {
        return $this->services->all($tenantId);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->services->find($tenantId, $id);
    }

    public function create(int $tenantId, array $input, int $userId): int
    {
        $data = $this->validate($input);
        $this->assertVendor($tenantId, (int) $data['vendor_id']);

        if ($this->services->code_exists($tenantId, (string) $data['code'])) {
            throw new RuntimeException('A record with this code already exists.');
        }
        return $this->services->create($tenantId, $data, $userId);
    }

    public function update(int $tenantId, int $id, array $input, int $userId): void
    {
        if (!$this->find($tenantId, $id)) throw new RuntimeException('Service code not found.');
        $data = $this->validate($input);
        $this->assertVendor($tenantId, (int) $data['vendor_id']);

        if ($this->services->code_exists($tenantId, (string) $data['code'], $id)) {
            throw new RuntimeException('A record with this code already exists.');
        }
        $this->services->update($tenantId, $id, $data, $userId);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) throw new RuntimeException('Service code not found.');
        $this->services->softDelete($tenantId, $id, $userId);
    }

    public function vendors(int $tenantId): array
    {
        return $this->vendorsModel->all($tenantId);
    }


    private function validate(array $input): array
    {
        $data = [];
        $data['vendor_id'] = $this->nullableInt($input['vendor_id'] ?? null);
        $data['name'] = $this->nullableString($input['name'] ?? '');
        $data['code'] = $this->nullableString($input['code'] ?? '');
        $data['service_type'] = $this->nullableString($input['service_type'] ?? '');
        $data['description'] = $this->nullableString($input['description'] ?? '');
        $data['pricing_mode'] = strtoupper((string) ($this->nullableString($input['pricing_mode'] ?? '') ?? 'PER_RIDE'));
        $data['daily_rate'] = $this->decimal($input['daily_rate'] ?? null);
        $data['extra_km_rate'] = $this->decimal($input['extra_km_rate'] ?? null);
        $data['base_fare'] = $this->decimal($input['base_fare'] ?? null);
        $data['per_km'] = $this->decimal($input['per_km'] ?? null);
        $data['per_minute'] = $this->decimal($input['per_minute'] ?? null);
        $data['minimum_fare'] = $this->decimal($input['minimum_fare'] ?? null);
        $data['included_km'] = $this->decimal($input['included_km'] ?? null);
        $data['status'] = $this->nullableString($input['status'] ?? '');
        if ($data['vendor_id'] === null || $data['vendor_id'] === '') { throw new RuntimeException('Taxi business is required.'); }
        if ($data['name'] === null || $data['name'] === '') { throw new RuntimeException('Name is required.'); }
        if ($data['code'] === null || $data['code'] === '') { throw new RuntimeException('Code is required.'); }
        $data['service_type'] = $data['service_type'] ?: 'Local';
        $data['status'] = $data['status'] ?: 'Active';
        if (!in_array($data['service_type'], ['Local','Airport Transfer','Outstation','Scheduled','Other'], true)) { throw new RuntimeException('Invalid taxi service type.'); }
        if (!in_array($data['pricing_mode'], ['PER_RIDE','PER_KM','PER_DAY','PER_KM_WITH_MINIMUM','CUSTOM_QUOTE'], true)) { throw new RuntimeException('Invalid pricing mode.'); }
        if ($data['daily_rate'] < 0 || $data['extra_km_rate'] < 0 || $data['included_km'] < 0) { throw new RuntimeException('Pricing values cannot be negative.'); }
        if (!in_array($data['status'], ['Active', 'Inactive'], true)) { throw new RuntimeException('Invalid status.'); }

        return $data;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') return null;
        $value = (int) $value;
        return $value > 0 ? $value : null;
    }

    private function decimal(mixed $value): float
    {
        return round((float) ($value === null || $value === '' ? 0 : $value), 2);
    }
    private function assertVendor(int $tenantId, int $vendorId): void
    {
        if ($vendorId <= 0 || $this->vendorsModel->find($tenantId, $vendorId) === null) {
            throw new RuntimeException('Taxi business not found.');
        }
    }
}
