<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use App\Modules\Restaurant\Models\Restaurant;
use RuntimeException;
use Throwable;

final class RestaurantService
{
    private Restaurant $restaurants;
    private RestaurantImageUploadService $images;

    public function __construct()
    {
        $this->restaurants = new Restaurant();
        $this->images = new RestaurantImageUploadService();
    }

    public function all(int $tenantId, bool $withDeleted = false): array
    {
        return $this->restaurants->all($tenantId, $withDeleted);
    }

    public function trash(int $tenantId): array
    {
        return $this->restaurants->trash($tenantId);
    }

    public function dashboard(int $tenantId): array
    {
        return $this->restaurants->dashboard($tenantId);
    }

    public function find(int $tenantId, int $id): ?array
    {
        return $this->restaurants->find($tenantId, $id);
    }

    public function findDeleted(int $tenantId, int $id): ?array
    {
        return $this->restaurants->findDeleted($tenantId, $id);
    }

    public function hours(int $tenantId, int $id): array
    {
        return $this->restaurants->hours($tenantId, $id);
    }

    public function menuSummary(int $tenantId, int $id): array
    {
        return $this->restaurants->menuSummary($tenantId, $id);
    }

    public function create(int $tenantId, array $input, int $userId, ?array $logo = null, ?array $cover = null): int
    {
        $data = $this->validate($input);

        if ($this->restaurants->businessNameExists($tenantId, $data['business_name'])) {
            throw new RuntimeException('A restaurant business with this name already exists.');
        }

        $data['business_slug'] = $this->uniqueSlug(
            $tenantId,
            $this->slugify($data['business_name'])
        );

        $storedLogo = null;
        $storedCover = null;

        try {
            if ($this->hasUpload($logo)) {
                $storedLogo = $this->images->store($logo, 'logos');
                $data['logo_path'] = $storedLogo;
            }
            if ($this->hasUpload($cover)) {
                $storedCover = $this->images->store($cover, 'covers');
                $data['cover_image_path'] = $storedCover;
            }

            return $this->restaurants->create($tenantId, $data, $userId);
        } catch (Throwable $e) {
            if ($storedLogo !== null) $this->images->delete($storedLogo);
            if ($storedCover !== null) $this->images->delete($storedCover);
            throw $e;
        }
    }

    public function update(int $tenantId, int $id, array $input, int $userId, ?array $logo = null, ?array $cover = null): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Restaurant business not found.');
        }

        $data = $this->validate($input);

        if ($this->restaurants->businessNameExists($tenantId, $data['business_name'], $id)) {
            throw new RuntimeException('A restaurant business with this name already exists.');
        }

        $data['business_slug'] = $this->uniqueSlug(
            $tenantId,
            $this->slugify($data['business_name']),
            $id
        );

        $existing = $this->find($tenantId, $id);
        $oldLogo = !empty($existing['logo_path']) ? (string) $existing['logo_path'] : null;
        $oldCover = !empty($existing['cover_image_path']) ? (string) $existing['cover_image_path'] : null;
        $data['logo_path'] = $oldLogo;
        $data['cover_image_path'] = $oldCover;
        $newLogo = null;
        $newCover = null;

        try {
            if ($this->hasUpload($logo)) {
                $newLogo = $this->images->store($logo, 'logos');
                $data['logo_path'] = $newLogo;
            } elseif (!empty($input['remove_logo'])) {
                $data['logo_path'] = null;
            }

            if ($this->hasUpload($cover)) {
                $newCover = $this->images->store($cover, 'covers');
                $data['cover_image_path'] = $newCover;
            } elseif (!empty($input['remove_cover_image'])) {
                $data['cover_image_path'] = null;
            }

            $this->restaurants->update($tenantId, $id, $data, $userId);
        } catch (Throwable $e) {
            if ($newLogo !== null) $this->images->delete($newLogo);
            if ($newCover !== null) $this->images->delete($newCover);
            throw $e;
        }

        if ($oldLogo !== null && $data['logo_path'] !== $oldLogo) $this->images->delete($oldLogo);
        if ($oldCover !== null && $data['cover_image_path'] !== $oldCover) $this->images->delete($oldCover);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Restaurant business not found.');
        }

        $this->restaurants->softDelete($tenantId, $id, $userId);
    }

    public function restore(int $tenantId, int $id, int $userId): void
    {
        if (!$this->findDeleted($tenantId, $id)) {
            throw new RuntimeException('Deleted restaurant business not found.');
        }
        $this->restaurants->restore($tenantId, $id, $userId);
    }

    public function saveHours(int $tenantId, int $id, array $input, int $userId): void
    {
        if (!$this->find($tenantId, $id)) {
            throw new RuntimeException('Restaurant not found.');
        }

        $hours = [];

        for ($day = 0; $day <= 6; $day++) {
            $isClosed = !empty($input['is_closed'][$day]);
            $opensAt = $isClosed ? null : $this->time($input['opens_at'][$day] ?? null);
            $closesAt = $isClosed ? null : $this->time($input['closes_at'][$day] ?? null);

            if (!$isClosed && ($opensAt === null || $closesAt === null)) {
                $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                throw new RuntimeException(($dayNames[$day] ?? 'Selected day') . ' must have both opening and closing times, or be marked closed.');
            }

            $hours[$day] = [
                'opens_at' => $opensAt,
                'closes_at' => $closesAt,
                'is_closed' => $isClosed,
                'created_by' => $userId,
                'updated_by' => $userId,
            ];
        }

        $this->restaurants->replaceHours($tenantId, $id, $hours);
    }

    public function locations(): array
    {
        return [
            'state' => ['Manipur'],
            'districts' => [
                'Bishnupur', 'Chandel', 'Churachandpur', 'Imphal East', 'Imphal West',
                'Jiribam', 'Kakching', 'Kamjong', 'Kangpokpi', 'Noney', 'Pherzawl',
                'Senapati', 'Tamenglong', 'Tengnoupal', 'Thoubal', 'Ukhrul',
            ],
            'cities' => [
                'Imphal', 'Bishnupur', 'Chandel', 'Churachandpur', 'Jiribam', 'Kakching',
                'Kamjong', 'Kangpokpi', 'Noney', 'Pherzawl', 'Senapati', 'Tamenglong',
                'Moreh', 'Thoubal', 'Ukhrul',
            ],
        ];
    }

    private function hasUpload(?array $file): bool
    {
        return is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function validate(array $input): array
    {
        $locations = $this->locations();

        $data = [
            'business_name' => $this->nullableString($input['business_name'] ?? ''),
            'phone' => $this->nullableString($input['phone'] ?? ''),
            'email' => $this->nullableString($input['email'] ?? ''),
            'address' => $this->nullableString($input['address'] ?? ''),
            'city' => $this->nullableString($input['city'] ?? ''),
            'district' => $this->nullableString($input['district'] ?? ''),
            'state' => $this->nullableString($input['state'] ?? ''),
            'postal_code' => $this->nullableString($input['postal_code'] ?? ''),
            'description' => $this->nullableString($input['description'] ?? ''),
            'cuisine_type' => $this->nullableString($input['cuisine_type'] ?? ''),
            'minimum_order_amount' => $this->money($input['minimum_order_amount'] ?? 0),
            'delivery_available' => !empty($input['delivery_available']) ? 1 : 0,
            'pickup_available' => !empty($input['pickup_available']) ? 1 : 0,
            'delivery_fee' => $this->money($input['delivery_fee'] ?? 0),
            'free_delivery_above' => $this->money($input['free_delivery_above'] ?? 0),
            'estimated_prep_minutes' => max(1, (int) ($input['estimated_prep_minutes'] ?? 30)),
            'accepting_orders' => !empty($input['accepting_orders']) ? 1 : 0,
            'latitude' => $this->coordinate($input['latitude'] ?? null, -90.0, 90.0, 'Latitude'),
            'longitude' => $this->coordinate($input['longitude'] ?? null, -180.0, 180.0, 'Longitude'),
            'status' => $this->nullableString($input['status'] ?? '') ?: 'Active',
        ];

        if (!$data['business_name']) {
            throw new RuntimeException('Restaurant business name is required.');
        }

        if (($data['latitude'] === null) xor ($data['longitude'] === null)) {
            throw new RuntimeException('Please provide both latitude and longitude, or leave both blank.');
        }

        $data['state'] = $data['state'] ?: 'Manipur';

        if (!in_array($data['status'], ['Active', 'Inactive', 'Suspended'], true)) {
            throw new RuntimeException('Invalid restaurant status.');
        }

        if (!in_array($data['state'], $locations['state'], true)) {
            throw new RuntimeException('Please select a valid Manipur state.');
        }
        if (!in_array($data['district'], $locations['districts'], true)) {
            throw new RuntimeException('Please select a valid Manipur district.');
        }
        if (!in_array($data['city'], $locations['cities'], true)) {
            throw new RuntimeException('Please select a valid Manipur city/town.');
        }

        return $data;
    }

    private function uniqueSlug(int $tenantId, string $base, ?int $ignoreId = null): string
    {
        $slug = $base ?: 'restaurant';
        $candidate = $slug;
        $suffix = 2;

        while ($this->restaurants->slugExists($tenantId, $candidate, $ignoreId)) {
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

    private function money(mixed $value): string
    {
        $number = (float) $value;

        if ($number < 0) {
            throw new RuntimeException('Amounts cannot be negative.');
        }

        return number_format($number, 2, '.', '');
    }

    private function coordinate(mixed $value, float $min, float $max, string $label): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            throw new RuntimeException($label . ' must be a valid number.');
        }

        $number = (float) $value;

        if ($number < $min || $number > $max) {
            throw new RuntimeException($label . ' must be between ' . $min . ' and ' . $max . '.');
        }

        return number_format($number, 7, '.', '');
    }

    private function time(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $value)) {
            throw new RuntimeException('Invalid opening/closing time.');
        }

        return strlen($value) === 5 ? $value . ':00' : $value;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}
