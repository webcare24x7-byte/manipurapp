<?php
declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use App\Modules\MemberApp\Models\MemberCommercialRental;
use RuntimeException;

final class MemberCommercialRentalService
{
    public const TENANT_ID = 1;

    private MemberCommercialRental $model;

    public function __construct()
    {
        $this->model = new MemberCommercialRental();
    }

    public function categories(): array
    {
        return $this->model->categories(self::TENANT_ID);
    }

    public function search(array $query): array
    {
        $categoryId = isset($query['category_id']) && (int)$query['category_id'] > 0
            ? (int)$query['category_id'] : null;
        $operator = !empty($query['operator_required']) ? '1' : null;

        $lat = $this->coord($query['pickup_lat'] ?? null, 90);
        $lng = $this->coord($query['pickup_lng'] ?? null, 180);

        return $this->model->vehicles(
            self::TENANT_ID,
            $categoryId,
            $operator,
            $lat,
            $lng
        );
    }

    public function vehicle(int $id): ?array
    {
        return $this->model->vehicle(self::TENANT_ID, $id);
    }

    public function member(int $memberId): ?array
    {
        return $this->model->member(self::TENANT_ID, $memberId);
    }

    public function createBooking(int $memberId, int $userId, array $input): int
    {
        $vehicleId = (int)($input['vehicle_id'] ?? 0);
        $vehicle = $this->vehicle($vehicleId);

        if (!$vehicle) {
            throw new RuntimeException('The selected vehicle is no longer available.');
        }

        $member = $this->member($memberId);
        if (!$member) {
            throw new RuntimeException('Member account could not be found.');
        }

        $pickup = trim((string)($input['pickup_address'] ?? ''));
        $purpose = trim((string)($input['purpose'] ?? ''));
        $start = $this->datetime($input['start_at'] ?? null);
        $end = $this->datetime($input['end_at'] ?? null);

        if ($pickup === '') throw new RuntimeException('Pickup / work location is required.');
        if ($purpose === '') throw new RuntimeException('Please tell us what the vehicle will be used for.');
        if ($start === '') throw new RuntimeException('Start date and time are required.');

        $phone = trim((string)($input['customer_phone'] ?? ($member['phone'] ?? '')));
        if ($phone === '') throw new RuntimeException('A contact phone number is required.');

        return $this->model->createRequest(
            self::TENANT_ID,
            [
                'member_id' => $memberId,
                'provider_id' => (int)$vehicle['provider_id'],
                'vehicle_id' => $vehicleId,
                'customer_name' => trim((string)($input['customer_name'] ?? $this->memberName($member))),
                'customer_phone' => $phone,
                'purpose' => $purpose,
                'pickup_address' => $pickup,
                'pickup_lat' => $this->coord($input['pickup_lat'] ?? null, 90),
                'pickup_lng' => $this->coord($input['pickup_lng'] ?? null, 180),
                'destination_address' => $this->nullableString($input['destination_address'] ?? null),
                'destination_lat' => $this->coord($input['destination_lat'] ?? null, 90),
                'destination_lng' => $this->coord($input['destination_lng'] ?? null, 180),
                'start_at' => $start,
                'end_at' => $this->nullableString($end),
                'duration_text' => $this->nullableString($input['duration_text'] ?? null),
                'operator_required' => !empty($input['operator_required']) ? 1 : 0,
                'customer_notes' => $this->nullableString($input['customer_notes'] ?? null),
            ],
            $userId
        );
    }

    public function booking(int $memberId, int $id): ?array
    {
        return $this->model->requestForMember(self::TENANT_ID, $memberId, $id);
    }

    public function bookings(int $memberId): array
    {
        return $this->model->requestsForMember(self::TENANT_ID, $memberId);
    }

    private function memberName(array $member): string
    {
        return trim(implode(' ', array_filter([
            $member['first_name'] ?? '',
            $member['middle_name'] ?? '',
            $member['last_name'] ?? '',
        ])));
    }

    private function datetime(mixed $value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') return null;
        $value = str_replace('T', ' ', $value);
        return strlen($value) === 16 ? $value . ':00' : $value;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }

    private function coord(mixed $value, float $limit): ?string
    {
        if ($value === null || trim((string)$value) === '' || !is_numeric($value)) {
            return null;
        }

        $number = (float)$value;
        if (abs($number) > $limit) {
            throw new RuntimeException('Invalid location coordinates.');
        }

        return number_format($number, 7, '.', '');
    }
}
