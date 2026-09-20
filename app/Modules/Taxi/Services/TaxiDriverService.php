<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Services;

use App\Modules\Taxi\Models\TaxiDriver;
use App\Modules\Taxi\Models\TaxiVendor;
use RuntimeException;
use Throwable;

final class TaxiDriverService
{
    private TaxiDriver $drivers;
    private TaxiVendor $vendorsModel;
    private TaxiImageUploadService $images;

    public function __construct()
    {
        $this->drivers = new TaxiDriver();
        $this->vendorsModel = new TaxiVendor();
        $this->images = new TaxiImageUploadService();
    }

    public function all(int $tenantId): array
    {
        return $this->drivers->all($tenantId);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->drivers->find($tenantId, $id);
    }

    public function create(int $tenantId, array $input, int $userId, ?array $photo = null): int
    {
        $data = $this->validate($tenantId, $input);
        $this->assertVendor($tenantId, (int) $data['vendor_id']);

        if (!empty($data['license_no']) && $this->drivers->license_no_exists($tenantId, (string) $data['license_no'])) {
            throw new RuntimeException('A driver with this license number already exists.');
        }

        $data['photo_path'] = null;
        $storedPhoto = null;

        try {
            if ($this->hasUpload($photo)) {
                $storedPhoto = $this->images->store($photo, 'drivers');
                $data['photo_path'] = $storedPhoto;
            }

            return $this->drivers->create($tenantId, $data, $userId);
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
            throw new RuntimeException('Driver name not found.');
        }

        $data = $this->validate($tenantId, $input);
        $this->assertVendor($tenantId, (int) $data['vendor_id']);

        if (!empty($data['license_no']) && $this->drivers->license_no_exists($tenantId, (string) $data['license_no'], $id)) {
            throw new RuntimeException('A driver with this license number already exists.');
        }

        $oldPhoto = !empty($existing['photo_path']) ? (string) $existing['photo_path'] : null;
        $data['photo_path'] = $oldPhoto;
        $newPhoto = null;

        try {
            if ($this->hasUpload($photo)) {
                $newPhoto = $this->images->store($photo, 'drivers');
                $data['photo_path'] = $newPhoto;
            } elseif (!empty($input['remove_photo'])) {
                $data['photo_path'] = null;
            }

            $this->drivers->update($tenantId, $id, $data, $userId);
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
            throw new RuntimeException('Driver name not found.');
        }
        $this->drivers->softDelete($tenantId, $id, $userId);
    }

    public function vendors(int $tenantId): array
    {
        return $this->vendorsModel->all($tenantId);
    }

    public function users(int $tenantId): array
    {
        return $this->drivers->users($tenantId);
    }

    private function validate(int $tenantId, array $input): array
    {
        $data = [];
        $data['vendor_id'] = $this->nullableInt($input['vendor_id'] ?? null);
        $data['user_id'] = $this->nullableInt($input['user_id'] ?? null);
        $data['name'] = $this->nullableString($input['name'] ?? '');
        $data['phone'] = $this->nullableString($input['phone'] ?? '');
        $data['license_no'] = $this->nullableString($input['license_no'] ?? '');
        $data['license_expiry'] = $this->nullableString($input['license_expiry'] ?? '');
        $data['status'] = $this->nullableString($input['status'] ?? '');
        $data['availability'] = $this->nullableString($input['availability'] ?? '');

        if ($data['vendor_id'] === null) {
            throw new RuntimeException('Taxi business is required.');
        }
        if ($data['name'] === null) {
            throw new RuntimeException('Name is required.');
        }
        if ($data['phone'] === null) {
            throw new RuntimeException('Phone is required.');
        }

        $data['status'] = $data['status'] ?: 'Active';
        $data['availability'] = $data['availability'] ?: 'Offline';

        if (!empty($data['user_id']) && !$this->drivers->userBelongsToTenant($tenantId, (int) $data['user_id'])) {
            throw new RuntimeException('Selected staff user does not belong to this tenant.');
        }
        if (!in_array($data['status'], ['Active', 'Inactive', 'Suspended'], true)) {
            throw new RuntimeException('Invalid status.');
        }
        if (!in_array($data['availability'], ['Offline', 'Available', 'On Trip', 'Unavailable'], true)) {
            throw new RuntimeException('Invalid availability.');
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
