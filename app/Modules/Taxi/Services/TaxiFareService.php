<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Services;

use RuntimeException;

/**
 * Single source of truth for Taxi service pricing.
 *
 * The selected taxi service supplies the pricing rules. Route distance/ETA
 * is only used where the selected pricing mode needs it.
 */
final class TaxiFareService
{
    private const MODES = [
        'PER_RIDE',
        'PER_KM',
        'PER_DAY',
        'PER_KM_WITH_MINIMUM',
        'CUSTOM_QUOTE',
    ];

    public function calculate(
        array $service,
        string $tripType = 'ONE_WAY',
        ?float $totalDistanceKm = null,
        ?int $totalEtaMinutes = null,
        ?string $scheduledAt = null,
        ?string $returnScheduledAt = null
    ): array {
        $mode = strtoupper(trim((string) ($service['pricing_mode'] ?? 'PER_RIDE')));
        if (!in_array($mode, self::MODES, true)) {
            throw new RuntimeException('The selected taxi service has an invalid pricing mode.');
        }

        $tripType = strtoupper(trim($tripType));
        if (!in_array($tripType, ['ONE_WAY', 'ROUND_TRIP'], true)) {
            throw new RuntimeException('Invalid trip type for fare calculation.');
        }

        $base = $this->money($service['base_fare'] ?? 0);
        $perKm = $this->money($service['per_km'] ?? 0);
        $perMinute = $this->money($service['per_minute'] ?? 0);
        $minimum = $this->money($service['minimum_fare'] ?? 0);
        $dailyRate = $this->money($service['daily_rate'] ?? 0);
        $extraKmRate = $this->money($service['extra_km_rate'] ?? 0);
        $includedKm = $this->money($service['included_km'] ?? 0);

        $distance = $totalDistanceKm !== null ? max(0.0, round($totalDistanceKm, 2)) : null;
        $eta = $totalEtaMinutes !== null ? max(0, $totalEtaMinutes) : null;

        $items = [];
        $subtotal = 0.0;
        $minimumApplied = false;
        $rentalDays = null;
        $requiresRoute = false;
        $requiresSchedule = false;
        $quoteRequired = false;
        $notes = [];

        switch ($mode) {
            case 'PER_RIDE':
                // A per-ride fare is fixed only up to the configured Included KM.
                // Once the committed route exceeds Included KM, Extra KM Rate applies.
                if ($includedKm > 0 && $distance === null) {
                    $requiresRoute = true;
                    return $this->result($service, $mode, $tripType, $distance, $eta, null, $items, $base, $minimum, false, null, true, false, false, [
                        'Calculate and commit the road distance because this Per Ride service has an Included KM limit.'
                    ]);
                }

                $items[] = ['label' => 'Base / fixed ride fare', 'amount' => $base];
                $subtotal = $base;

                if ($distance !== null && $includedKm > 0) {
                    $extraDistance = max(0.0, round($distance - $includedKm, 2));
                    $items[] = ['label' => 'Included distance', 'amount' => 0.0, 'meta' => number_format($includedKm, 2, '.', '') . ' km included'];
                    if ($extraDistance > 0) {
                        if ($extraKmRate <= 0) {
                            return $this->result($service, $mode, $tripType, $distance, $eta, null, $items, $base, $minimum, false, null, true, false, false, [
                                'The committed route exceeds the Included KM, but no Extra KM Rate is configured for this service. Configure an Extra KM Rate or use Custom Quote.'
                            ]);
                        }
                        $extraCharge = round($extraDistance * $extraKmRate, 2);
                        $items[] = ['label' => number_format($extraDistance, 2, '.', '') . ' extra km × ₹' . number_format($extraKmRate, 2, '.', '') . '/km', 'amount' => $extraCharge];
                        $subtotal += $extraCharge;
                        $notes[] = 'The route exceeds the Included KM, so the configured Extra KM Rate is applied to the additional distance.';
                    }
                }
                break;

            case 'PER_KM':
                $requiresRoute = true;
                if ($distance === null) {
                    return $this->result($service, $mode, $tripType, $distance, $eta, null, $items, $base, $minimum, false, null, true, false, false, ['Calculate the road distance before confirming the fare.']);
                }
                $items[] = ['label' => 'Base fare', 'amount' => $base];
                $items[] = ['label' => number_format($distance, 2, '.', '') . ' km × ₹' . number_format($perKm, 2, '.', '') . '/km', 'amount' => round($distance * $perKm, 2)];
                $subtotal = $base + ($distance * $perKm);
                if ($perMinute > 0 && $eta !== null) {
                    $timeCharge = $eta * $perMinute;
                    $items[] = ['label' => $eta . ' min × ₹' . number_format($perMinute, 2, '.', '') . '/min', 'amount' => round($timeCharge, 2)];
                    $subtotal += $timeCharge;
                    $notes[] = 'The configured per-minute charge is included because it is configured for this service.';
                }
                break;

            case 'PER_KM_WITH_MINIMUM':
                $requiresRoute = true;
                if ($distance === null) {
                    return $this->result($service, $mode, $tripType, $distance, $eta, null, $items, $base, $minimum, false, null, true, false, false, ['Calculate the road distance before confirming the fare.']);
                }
                $items[] = ['label' => 'Base fare', 'amount' => $base];
                $distanceCharge = $distance * $perKm;
                $items[] = ['label' => number_format($distance, 2, '.', '') . ' km × ₹' . number_format($perKm, 2, '.', '') . '/km', 'amount' => round($distanceCharge, 2)];
                $subtotal = $base + $distanceCharge;
                if ($perMinute > 0 && $eta !== null) {
                    $timeCharge = $eta * $perMinute;
                    $items[] = ['label' => $eta . ' min × ₹' . number_format($perMinute, 2, '.', '') . '/min', 'amount' => round($timeCharge, 2)];
                    $subtotal += $timeCharge;
                    $notes[] = 'The configured per-minute charge is included because it is configured for this service.';
                }
                if ($minimum > 0 && $subtotal < $minimum) {
                    $items[] = ['label' => 'Minimum fare adjustment', 'amount' => round($minimum - $subtotal, 2)];
                    $subtotal = $minimum;
                    $minimumApplied = true;
                }
                break;

            case 'PER_DAY':
                $requiresSchedule = true;
                $rentalDays = $this->rentalDays($scheduledAt, $returnScheduledAt);
                if ($rentalDays === null) {
                    return $this->result($service, $mode, $tripType, $distance, $eta, null, $items, $base, $minimum, false, null, false, true, false, ['Departure and return date/time are required to calculate a rental period.']);
                }
                $dailyCharge = $rentalDays * $dailyRate;
                $items[] = ['label' => $rentalDays . ' day' . ($rentalDays === 1 ? '' : 's') . ' × ₹' . number_format($dailyRate, 2, '.', '') . '/day', 'amount' => round($dailyCharge, 2)];
                $subtotal = $dailyCharge;
                if ($includedKm > 0 && $distance !== null) {
                    $extraDistance = max(0.0, round($distance - $includedKm, 2));
                    if ($extraDistance > 0) {
                        if ($extraKmRate <= 0) {
                            return $this->result($service, $mode, $tripType, $distance, $eta, $rentalDays, $items, $base, $minimum, false, null, true, true, false, [
                                'The committed route exceeds the Included KM, but no Extra KM Rate is configured for this rental service.'
                            ]);
                        }
                        $extraCharge = round($extraDistance * $extraKmRate, 2);
                        $items[] = ['label' => number_format($extraDistance, 2, '.', '') . ' extra km × ₹' . number_format($extraKmRate, 2, '.', '') . '/km', 'amount' => $extraCharge];
                        $subtotal += $extraCharge;
                        $notes[] = 'Extra distance beyond Included KM is charged using the configured Extra KM Rate.';
                    }
                } elseif ($extraKmRate > 0 && $includedKm <= 0) {
                    $notes[] = 'Extra KM Rate is configured, but Included KM is zero, so there is no defined threshold for automatically charging extra distance.';
                }
                break;

            case 'CUSTOM_QUOTE':
                $quoteRequired = true;
                $notes[] = 'This service requires an approved manual quote. No automatic fare is calculated.';
                break;
        }

        if ($mode === 'PER_RIDE' && $minimum > 0 && $subtotal < $minimum) {
            $items[] = ['label' => 'Minimum fare adjustment', 'amount' => round($minimum - $subtotal, 2)];
            $subtotal = $minimum;
            $minimumApplied = true;
        }

        $fare = $quoteRequired ? null : round($subtotal, 2);

        return $this->result(
            $service,
            $mode,
            $tripType,
            $distance,
            $eta,
            $rentalDays,
            $items,
            $base,
            $minimum,
            $minimumApplied,
            $fare,
            $requiresRoute,
            $requiresSchedule,
            $quoteRequired,
            $notes
        );
    }

    private function result(
        array $service,
        string $mode,
        string $tripType,
        ?float $distance,
        ?int $eta,
        ?int $rentalDays,
        array $items,
        float $base,
        float $minimum,
        bool $minimumApplied,
        ?float $fare,
        bool $requiresRoute,
        bool $requiresSchedule,
        bool $quoteRequired,
        array $notes
    ): array {
        return [
            'service_id' => (int) ($service['id'] ?? 0),
            'service_name' => (string) ($service['name'] ?? ''),
            'service_type' => (string) ($service['service_type'] ?? ''),
            'pricing_mode' => $mode,
            'pricing_mode_label' => $this->modeLabel($mode),
            'trip_type' => $tripType,
            'distance_km' => $distance,
            'eta_minutes' => $eta,
            'rental_days' => $rentalDays,
            'base_fare' => $base,
            'per_km' => $this->money($service['per_km'] ?? 0),
            'per_minute' => $this->money($service['per_minute'] ?? 0),
            'minimum_fare' => $minimum,
            'daily_rate' => $this->money($service['daily_rate'] ?? 0),
            'extra_km_rate' => $this->money($service['extra_km_rate'] ?? 0),
            'included_km' => $this->money($service['included_km'] ?? 0),
            'line_items' => $items,
            'minimum_applied' => $minimumApplied,
            'estimated_fare' => $fare,
            'quote_required' => $quoteRequired,
            'requires_route' => $requiresRoute,
            'requires_schedule' => $requiresSchedule,
            'notes' => $notes,
        ];
    }

    private function rentalDays(?string $scheduledAt, ?string $returnScheduledAt): ?int
    {
        if (!$scheduledAt || !$returnScheduledAt) {
            return null;
        }

        $start = strtotime($scheduledAt);
        $end = strtotime($returnScheduledAt);
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        return max(1, (int) ceil(($end - $start) / 86400));
    }

    private function money(mixed $value): float
    {
        return round((float) ($value === null || $value === '' ? 0 : $value), 2);
    }

    private function modeLabel(string $mode): string
    {
        return match ($mode) {
            'PER_RIDE' => 'Per Ride',
            'PER_KM' => 'Per KM',
            'PER_DAY' => 'Per Day / Rental',
            'PER_KM_WITH_MINIMUM' => 'Per KM with Minimum',
            'CUSTOM_QUOTE' => 'Custom Quote',
            default => $mode,
        };
    }
}
