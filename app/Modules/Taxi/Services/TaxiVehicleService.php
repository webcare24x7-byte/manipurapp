<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Services;

use App\Modules\Taxi\Models\TaxiVehicle;
use App\Modules\Taxi\Models\TaxiVendor;
use RuntimeException;
use Throwable;

final class TaxiVehicleService
{
    private TaxiVehicle $vehicles;
    private TaxiVendor $vendorsModel;
    private TaxiImageUploadService $images;

    public function __construct()
    {
        $this->vehicles = new TaxiVehicle();
        $this->vendorsModel = new TaxiVendor();
        $this->images = new TaxiImageUploadService();
    }

    public function all(int $tenantId): array
    {
        return $this->vehicles->all($tenantId);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->vehicles->find($tenantId, $id);
    }

    public function create(int $tenantId, array $input, int $userId, ?array $photo = null): int
    {
        $data = $this->validate($input);
        $this->assertVendor($tenantId, (int) $data['vendor_id']);

        if ($this->vehicles->registration_no_exists($tenantId, (string) $data['registration_no'])) {
            throw new RuntimeException('A record with this registration no already exists.');
        }

        $data['photo_path'] = null;
        $storedPhoto = null;

        try {
            if ($this->hasUpload($photo)) {
                $storedPhoto = $this->images->store($photo, 'vehicles');
                $data['photo_path'] = $storedPhoto;
            }

            return $this->vehicles->create($tenantId, $data, $userId);
        } catch (Throwable $e) {
            if ($storedPhoto !== null) {
                $this->images->delete($storedPhoto);
            }
            throw $e;
        }
    }

    public function update(int $tenantId, int $id, array $input, int $userId, ?array $photo = null): void
    {
        $existing = $this->find($tenantId, $id);
        if (!$existing) {
            throw new RuntimeException('Registration number not found.');
        }

        $data = $this->validate($input);
        $this->assertVendor($tenantId, (int) $data['vendor_id']);

        if ($this->vehicles->registration_no_exists($tenantId, (string) $data['registration_no'], $id)) {
            throw new RuntimeException('A record with this registration no already exists.');
        }

        $oldPhoto = !empty($existing['photo_path']) ? (string) $existing['photo_path'] : null;
        $data['photo_path'] = $oldPhoto;
        $newPhoto = null;

        try {
            if ($this->hasUpload($photo)) {
                $newPhoto = $this->images->store($photo, 'vehicles');
                $data['photo_path'] = $newPhoto;
            } elseif (!empty($input['remove_photo'])) {
                $data['photo_path'] = null;
            }

            $this->vehicles->update($tenantId, $id, $data, $userId);
        } catch (Throwable $e) {
            if ($newPhoto !== null) {
                $this->images->delete($newPhoto);
            }
            throw $e;
        }

        if ($oldPhoto !== null && $data['photo_path'] !== $oldPhoto) {
            $this->images->delete($oldPhoto);
        }
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Registration number not found.');
        }
        $this->vehicles->softDelete($tenantId, $id, $userId);
    }

    public function vendors(int $tenantId): array
    {
        return $this->vendorsModel->all($tenantId);
    }

    private function validate(array $input): array
    {
        $data = [];
        $data['vendor_id'] = $this->nullableInt($input['vendor_id'] ?? null);
        $data['registration_no'] = $this->nullableString($input['registration_no'] ?? '');
        $data['vehicle_type'] = $this->nullableString($input['vehicle_type'] ?? '');
        $data['make'] = $this->nullableString($input['make'] ?? '');
        $data['model'] = $this->nullableString($input['model'] ?? '');
        $data['model_year'] = $this->nullableInt($input['model_year'] ?? null);
        $data['color'] = $this->nullableString($input['color'] ?? '');
        $data['seating_capacity'] = $this->nullableInt($input['seating_capacity'] ?? null);
        $data['status'] = $this->nullableString($input['status'] ?? '');

        if ($data['vendor_id'] === null) {
            throw new RuntimeException('Taxi business is required.');
        }
        if ($data['registration_no'] === null) {
            throw new RuntimeException('Registration no is required.');
        }
        if ($data['vehicle_type'] === null) {
            throw new RuntimeException('Vehicle type is required.');
        }

        $data['seating_capacity'] = $data['seating_capacity'] ?? 4;
        $data['status'] = $data['status'] ?: 'Active';

        if (!in_array($data['status'], ['Active', 'Inactive', 'Maintenance'], true)) {
            throw new RuntimeException('Invalid status.');
        }

        return $data;
    }

    private function hasUpload(?array $file): bool
    {
        return is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = (int) $value;
        return $value > 0 ? $value : null;
    }

    private function assertVendor(int $tenantId, int $vendorId): void
    {
        if ($vendorId <= 0 || $this->vendorsModel->find($tenantId, $vendorId) === null) {
            throw new RuntimeException('Taxi business not found.');
        }
    }
}
