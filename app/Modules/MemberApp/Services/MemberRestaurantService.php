<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use App\Modules\MemberApp\Models\MemberRestaurant;
use App\Modules\Restaurant\Services\RestaurantOrderService;
use RuntimeException;

final class MemberRestaurantService
{
    public const TENANT_ID = 1;

    private MemberRestaurant $model;
    private RestaurantOrderService $orders;

    public function __construct()
    {
        $this->model = new MemberRestaurant();
        $this->orders = new RestaurantOrderService();
    }

    public function restaurants(?float $latitude = null, ?float $longitude = null, ?string $search = null, ?string $cuisine = null): array
    {
        $restaurants = $this->model->restaurants(self::TENANT_ID, $latitude, $longitude, $search, $cuisine);

        foreach ($restaurants as &$restaurant) {
            $restaurant['is_open'] = $this->isOpenNow(self::TENANT_ID, (int) $restaurant['id']);
            $restaurant['distance_km'] = null;

            if ($latitude !== null && $longitude !== null && $restaurant['latitude'] !== null && $restaurant['longitude'] !== null) {
                $restaurant['distance_km'] = $this->distanceKm(
                    $latitude,
                    $longitude,
                    (float) $restaurant['latitude'],
                    (float) $restaurant['longitude']
                );
            }
        }
        unset($restaurant);

        usort($restaurants, static function (array $a, array $b): int {
            $openA = !empty($a['is_open']) ? 0 : 1;
            $openB = !empty($b['is_open']) ? 0 : 1;
            if ($openA !== $openB) {
                return $openA <=> $openB;
            }

            $distA = $a['distance_km'];
            $distB = $b['distance_km'];
            if ($distA !== null && $distB !== null) {
                return $distA <=> $distB;
            }
            if ($distA !== null) {
                return -1;
            }
            if ($distB !== null) {
                return 1;
            }

            return strcasecmp((string) ($a['business_name'] ?? ''), (string) ($b['business_name'] ?? ''));
        });

        return $restaurants;
    }

    /**
     * Small discovery payload for the MemberApp landing screen.
     * Keeps the landing page useful today while leaving room for a real
     * popularity/review engine later.
     */
    public function featuredItems(array $restaurants, int $limit = 8): array
    {
        $items = [];
        foreach (array_slice($restaurants, 0, 6) as $restaurant) {
            $restaurantId = (int) ($restaurant['id'] ?? 0);
            if ($restaurantId <= 0) continue;

            foreach ($this->model->menuItems(self::TENANT_ID, $restaurantId) as $item) {
                $item['restaurant_name'] = (string) ($restaurant['business_name'] ?? 'Restaurant');
                $item['restaurant_id'] = $restaurantId;
                $item['display_price'] = $this->discountedBasePrice($item);
                $items[] = $item;
            }
        }

        usort($items, static function (array $a, array $b): int {
            $aAvailable = !empty($a['is_available']) ? 0 : 1;
            $bAvailable = !empty($b['is_available']) ? 0 : 1;
            return ($aAvailable <=> $bAvailable) ?: strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });

        return array_slice($items, 0, max(1, $limit));
    }

    public function restaurant(int $restaurantId): ?array
    {
        $restaurant = $this->model->restaurant(self::TENANT_ID, $restaurantId);
        if (!$restaurant) {
            return null;
        }

        $restaurant['is_open'] = $this->isOpenNow(self::TENANT_ID, $restaurantId);
        $restaurant['hours'] = $this->model->hours(self::TENANT_ID, $restaurantId);
        $restaurant['menu'] = $this->menu($restaurantId);

        return $restaurant;
    }

    public function menu(int $restaurantId): array
    {
        $items = $this->model->menuItems(self::TENANT_ID, $restaurantId);
        $grouped = [];

        foreach ($items as $item) {
            $category = trim((string) ($item['category_name'] ?? '')) ?: 'Uncategorized';
            $key = (string) ($item['category_id'] ?? 'none') . '|' . $category;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'id' => $item['category_id'] !== null ? (int) $item['category_id'] : null,
                    'name' => $category,
                    'sort_order' => (int) ($item['category_sort_order'] ?? 999999),
                    'items' => [],
                ];
            }

            $item['variants'] = $this->model->variants(self::TENANT_ID, $restaurantId, (int) $item['id']);
            $item['modifier_groups'] = $this->model->modifierGroups(self::TENANT_ID, $restaurantId, (int) $item['id']);
            $item['display_price'] = $this->discountedBasePrice($item);
            $grouped[$key]['items'][] = $item;
        }

        $categories = array_values($grouped);
        usort($categories, static fn(array $a, array $b): int => ($a['sort_order'] <=> $b['sort_order']) ?: strcasecmp($a['name'], $b['name']));
        return $categories;
    }

    public function item(int $restaurantId, int $itemId): ?array
    {
        $item = $this->model->item(self::TENANT_ID, $restaurantId, $itemId);
        if (!$item) {
            return null;
        }

        $item['variants'] = $this->model->variants(self::TENANT_ID, $restaurantId, $itemId);
        $item['modifier_groups'] = $this->model->modifierGroups(self::TENANT_ID, $restaurantId, $itemId);
        $item['display_price'] = $this->discountedBasePrice($item);
        return $item;
    }

    public function memberOrders(int $memberId): array
    {
        return $this->model->memberOrders(self::TENANT_ID, $memberId);
    }

    public function order(int $memberId, int $orderId): ?array
    {
        return $this->model->orderForMember(self::TENANT_ID, $memberId, $orderId);
    }

    public function memberProfile(int $memberId): ?array
    {
        return $this->model->memberProfile(self::TENANT_ID, $memberId);
    }

    public function couponPreview(int $restaurantId, string $code, float $subtotal): array
    {
        return $this->orders->couponPreview(self::TENANT_ID, $restaurantId, $code, max(0, $subtotal));
    }

    public function createOrder(int $memberId, int $userId, array $payload): int
    {
        $payload['member_id'] = $memberId;
        return $this->orders->create(self::TENANT_ID, $payload, $userId);
    }

    public function isOpenNow(int $tenantId, int $restaurantId): bool
    {
        $hours = $this->model->hours($tenantId, $restaurantId);
        if (!$hours) {
            return true;
        }

        $day = (int) date('w');
        $now = date('H:i:s');

        foreach ($hours as $row) {
            if ((int) $row['day_of_week'] !== $day) {
                continue;
            }
            if (!empty($row['is_closed'])) {
                return false;
            }

            $opens = (string) ($row['opens_at'] ?? '');
            $closes = (string) ($row['closes_at'] ?? '');
            if ($opens === '' || $closes === '') {
                return true;
            }

            // Supports ordinary hours and overnight hours such as 18:00–02:00.
            if ($closes > $opens) {
                return $now >= $opens && $now <= $closes;
            }
            if ($closes < $opens) {
                return $now >= $opens || $now <= $closes;
            }
            return true;
        }

        // If no row exists for today, don't incorrectly mark the restaurant closed.
        return true;
    }

    public function discountedBasePrice(array $item): float
    {
        $base = (float) ($item['price'] ?? 0);
        $type = (string) ($item['discount_type'] ?? 'PERCENT');
        $value = max(0, (float) ($item['discount_value'] ?? 0));

        if ($type === 'FLAT') {
            return round(max(0, $base - min($value, $base)), 2);
        }

        return round(max(0, $base - min($base, round($base * $value / 100, 2))), 2);
    }

    public function cartPreview(int $restaurantId, array $cart): array
    {
        $restaurant = $this->model->restaurant(self::TENANT_ID, $restaurantId);
        if (!$restaurant) {
            throw new RuntimeException('Restaurant is no longer available.');
        }

        $gross = 0.0;
        $itemDiscount = 0.0;
        $subtotal = 0.0;
        $lines = [];

        foreach ($cart as $line) {
            $itemId = (int) ($line['item_id'] ?? 0);
            $qty = max(1, min(50, (int) ($line['quantity'] ?? 1)));
            $item = $this->model->item(self::TENANT_ID, $restaurantId, $itemId);
            if (!$item) {
                continue;
            }

            $base = (float) $item['price'];
            $variantId = (int) ($line['variant_id'] ?? 0);
            $variantName = null;
            if ($variantId > 0) {
                foreach ($this->model->variants(self::TENANT_ID, $restaurantId, $itemId) as $variant) {
                    if ((int) $variant['id'] === $variantId) {
                        $base = (float) $variant['price'];
                        $variantName = (string) $variant['name'];
                        break;
                    }
                }
            }

            $discountType = (string) ($item['discount_type'] ?? 'PERCENT');
            $discountValue = max(0, (float) ($item['discount_value'] ?? 0));
            $discount = $discountType === 'FLAT'
                ? min($discountValue, $base)
                : min($base, round($base * $discountValue / 100, 2));

            $modTotal = 0.0;
            $selectedIds = array_values(array_unique(array_map('intval', is_array($line['modifier_option_ids'] ?? null) ? $line['modifier_option_ids'] : [])));
            $groups = $this->model->modifierGroups(self::TENANT_ID, $restaurantId, $itemId);
            foreach ($groups as $group) {
                foreach ($group['options'] as $option) {
                    if (in_array((int) $option['id'], $selectedIds, true) && !empty($option['is_available'])) {
                        $modTotal += (float) $option['price_adjustment'];
                    }
                }
            }

            $originalUnit = round($base + $modTotal, 2);
            $unit = round($base - $discount + $modTotal, 2);
            $lineTotal = round($unit * $qty, 2);
            $gross += round($originalUnit * $qty, 2);
            $itemDiscount += round($discount * $qty, 2);
            $subtotal += $lineTotal;

            $lines[] = [
                'key' => (string) ($line['key'] ?? ''),
                'item_id' => $itemId,
                'item_name' => (string) $item['name'],
                'variant_name' => $variantName,
                'quantity' => $qty,
                'unit_price' => $unit,
                'line_total' => $lineTotal,
            ];
        }

        $delivery = 0.0;
        if ($subtotal > 0 && !empty($restaurant['delivery_available'])) {
            $free = $restaurant['free_delivery_above'] === null ? null : (float) $restaurant['free_delivery_above'];
            if ($free === null || $subtotal < $free) {
                $delivery = (float) $restaurant['delivery_fee'];
            }
        }

        return [
            'gross_subtotal' => round($gross, 2),
            'item_discount' => round($itemDiscount, 2),
            'subtotal' => round($subtotal, 2),
            'delivery_fee' => round($delivery, 2),
            'total' => round($subtotal + $delivery, 2),
            'minimum_order_amount' => (float) $restaurant['minimum_order_amount'],
            'free_delivery_above' => $restaurant['free_delivery_above'] !== null ? (float) $restaurant['free_delivery_above'] : null,
            'lines' => $lines,
        ];
    }

    private function distanceKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earth = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return round($earth * 2 * atan2(sqrt($a), sqrt(max(0, 1 - $a))), 2);
    }
}
