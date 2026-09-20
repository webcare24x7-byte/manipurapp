<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use App\Modules\FreshFood\Services\FreshFoodCouponService;
use App\Modules\FreshFood\Services\FreshFoodOrderService;
use App\Modules\MemberApp\Models\MemberFreshFood;
use RuntimeException;
use Throwable;

final class MemberFreshFoodService
{
    public const TENANT_ID = 1;

    private MemberFreshFood $model;
    private FreshFoodCouponService $coupons;
    private FreshFoodOrderService $orders;
    private ?MemberFreshFoodRouteService $route = null;

    public function __construct()
    {
        $this->model = new MemberFreshFood();
        $this->coupons = new FreshFoodCouponService();
        $this->orders = new FreshFoodOrderService();
    }

    public function businesses(?float $latitude = null, ?float $longitude = null, ?string $search = null): array
    {
        $rows = $this->model->businesses(self::TENANT_ID);
        $search = trim((string) $search);

        if ($search !== '') {
            $needle = strtolower($search);
            $rows = array_values(array_filter($rows, static function (array $r) use ($needle): bool {
                $haystack = strtolower(implode(' ', [
                    (string) ($r['business_name'] ?? ''),
                    (string) ($r['description'] ?? ''),
                    (string) ($r['city'] ?? ''),
                    (string) ($r['district'] ?? ''),
                ]));
                return str_contains($haystack, $needle);
            }));
        }

        foreach ($rows as &$row) {
            $row['is_open'] = $this->isOpenNow((int) $row['id']);
            // Road distance is calculated with Gemini only when a delivery
            // route is actually needed. Avoid making one API call per store
            // merely to render the discovery list.
            $row['distance_km'] = null;
        }
        unset($row);

        usort($rows, static function (array $a, array $b): int {
            $open = (!empty($a['is_open']) ? 0 : 1) <=> (!empty($b['is_open']) ? 0 : 1);
            if ($open !== 0) return $open;
            if ($a['distance_km'] !== null && $b['distance_km'] !== null) return $a['distance_km'] <=> $b['distance_km'];
            if ($a['distance_km'] !== null) return -1;
            if ($b['distance_km'] !== null) return 1;
            return strcasecmp((string) $a['business_name'], (string) $b['business_name']);
        });

        return $rows;
    }

    /**
     * Discovery payload for the Fresh Food landing screen.
     * Popularity/review scores are intentionally presentation data for now.
     */
    public function featuredProducts(array $businesses, int $limit = 8): array
    {
        $items = [];
        foreach (array_slice($businesses, 0, 6) as $business) {
            $freshFoodId = (int) ($business['id'] ?? 0);
            if ($freshFoodId <= 0) continue;

            foreach ($this->model->products(self::TENANT_ID, $freshFoodId) as $product) {
                $product['business_name'] = (string) ($business['business_name'] ?? 'Fresh Food Store');
                $product['business_id'] = $freshFoodId;
                $product['display_price'] = $this->discountedPrice($product);
                $product['stock_available'] = (float) ($product['current_quantity'] ?? 0);
                $items[] = $product;
            }
        }

        usort($items, static function (array $a, array $b): int {
            $aAvailable = !empty($a['is_available']) && (float) ($a['current_quantity'] ?? 0) > 0 ? 0 : 1;
            $bAvailable = !empty($b['is_available']) && (float) ($b['current_quantity'] ?? 0) > 0 ? 0 : 1;
            return ($aAvailable <=> $bAvailable) ?: strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });

        return array_slice($items, 0, max(1, $limit));
    }

    public function business(int $id): ?array
    {
        $business = $this->model->business(self::TENANT_ID, $id);
        if (!$business) return null;
        $business['hours'] = $this->model->hours(self::TENANT_ID, $id);
        $business['is_open'] = $this->isOpenNow($id);
        $business['categories'] = $this->model->categories(self::TENANT_ID, $id);
        return $business;
    }

    public function products(int $freshFoodId, ?int $categoryId = null): array
    {
        $products = $this->model->products(self::TENANT_ID, $freshFoodId, $categoryId);
        foreach ($products as &$p) {
            $p['display_price'] = $this->discountedPrice($p);
            $p['stock_available'] = (float) ($p['current_quantity'] ?? 0);
        }
        unset($p);
        return $products;
    }

    public function product(int $freshFoodId, int $productId): ?array
    {
        $p = $this->model->product(self::TENANT_ID, $freshFoodId, $productId);
        if (!$p) return null;
        $p['display_price'] = $this->discountedPrice($p);
        return $p;
    }

    public function memberProfile(int $memberId): ?array
    {
        return $this->model->memberProfile(self::TENANT_ID, $memberId);
    }

    public function memberOrders(int $memberId): array
    {
        return $this->model->memberOrders(self::TENANT_ID, $memberId);
    }

    public function order(int $memberId, int $orderId): ?array
    {
        return $this->model->orderForMember(self::TENANT_ID, $memberId, $orderId);
    }

    public function cartPreview(array $cart): array
    {
        $freshFoodId = (int) ($cart['fresh_food_id'] ?? 0);
        if ($freshFoodId <= 0 || !$this->model->business(self::TENANT_ID, $freshFoodId)) {
            return $this->emptyPreview();
        }

        $products = [];
        foreach ($this->model->products(self::TENANT_ID, $freshFoodId) as $p) $products[(int) $p['id']] = $p;

        $gross = 0.0;
        $itemDiscount = 0.0;
        $lines = [];

        foreach ((array) ($cart['items'] ?? []) as $line) {
            $id = (int) ($line['product_id'] ?? 0);
            $qty = (float) ($line['quantity'] ?? 0);
            if ($id <= 0 || $qty <= 0 || !isset($products[$id])) continue;
            $p = $products[$id];
            $price = (float) $p['price'];
            $unitDiscount = $this->unitDiscount($p);
            $lineGross = round($price * $qty, 2);
            $lineDiscount = round($unitDiscount * $qty, 2);
            $lineTotal = round($lineGross - $lineDiscount, 2);
            $gross += $lineGross;
            $itemDiscount += $lineDiscount;
            $lines[] = [
                'key' => (string) ($line['key'] ?? $id),
                'product_id' => $id,
                'product_name' => $p['name'],
                'unit' => $p['unit'],
                'quantity' => $qty,
                'unit_price' => $price,
                'discount_type' => $p['discount_type'],
                'discount_value' => (float) $p['discount_value'],
                'discount_amount' => $lineDiscount,
                'line_total' => $lineTotal,
                'current_stock' => (float) ($p['current_quantity'] ?? 0),
                'increment_quantity' => (float) ($p['increment_quantity'] ?? 1),
            ];
        }

        $subtotal = round($gross - $itemDiscount, 2);
        $business = $this->model->business(self::TENANT_ID, $freshFoodId);
        $coupon = null;
        $couponDiscount = 0.0;
        $couponCode = trim((string) ($cart['coupon_code'] ?? ''));
        if ($couponCode !== '' && $subtotal > 0) {
            $coupon = $this->coupons->applicable(self::TENANT_ID, $freshFoodId, $couponCode, $subtotal);
            if (!$coupon) throw new RuntimeException('Coupon is invalid or no longer applicable.');
            $couponDiscount = (float) $coupon['calculated_discount'];
        }

        $deliveryFee = 0.0;
        $distance = null;
        return [
            'gross_subtotal' => round($gross, 2),
            'item_discount' => round($itemDiscount, 2),
            'subtotal' => $subtotal,
            'coupon_discount' => round($couponDiscount, 2),
            'delivery_fee' => $deliveryFee,
            'total' => round(max(0, $subtotal - $couponDiscount), 2),
            'minimum_order_amount' => 0.0,
            'delivery_distance_km' => $distance,
            'business' => $business,
            'coupon' => $coupon,
            'lines' => $lines,
        ];
    }

    public function calculateDeliveryFee(
        int $freshFoodId,
        string $type,
        ?float $latitude,
        ?float $longitude,
        float $subtotal,
        string $customerAddress = ''
    ): array {
        $business = $this->model->business(self::TENANT_ID, $freshFoodId);
        if (!$business) throw new RuntimeException('Fresh Food business is no longer available.');
        if ($type === 'PICKUP') {
            return ['fee' => 0.0, 'distance_km' => null, 'eta_minutes' => null];
        }
        if (empty($business['delivery_available'])) throw new RuntimeException('Delivery is not available from this Fresh Food business.');
        if ($latitude === null || $longitude === null || $business['latitude'] === null || $business['longitude'] === null) {
            throw new RuntimeException('Please provide your delivery location so we can calculate the road distance and ETA.');
        }

        $this->route ??= new MemberFreshFoodRouteService();

        $route = $this->route->estimate(
            $latitude,
            $longitude,
            (float) $business['latitude'],
            (float) $business['longitude'],
            $customerAddress,
            trim(implode(', ', array_filter([
                (string) ($business['address'] ?? ''),
                (string) ($business['city'] ?? ''),
                (string) ($business['district'] ?? ''),
                (string) ($business['state'] ?? 'Manipur'),
            ]))),
            (string) ($business['business_name'] ?? 'Fresh Food store')
        );

        $distance = (float) $route['distance_km'];
        $max = (float) $business['max_delivery_distance_km'];
        if ($max > 0 && $distance > $max + 0.000001) {
            throw new RuntimeException('This delivery address is outside the business delivery area.');
        }

        $included = max(0.0, (float) $business['included_delivery_distance_km']);
        $fee = max(0.0, (float) $business['minimum_delivery_fee']);
        if ($distance > $included) {
            $fee += ceil($distance - $included) * max(0.0, (float) $business['additional_delivery_fee_per_km']);
        }

        return [
            'fee' => round($fee, 2),
            'distance_km' => $distance,
            'eta_minutes' => (int) $route['eta_minutes'],
        ];
    }

    public function applyCoupon(array $cart, string $code): array
    {
        $cart['coupon_code'] = strtoupper(trim($code));
        $preview = $this->cartPreview($cart);
        return [$cart, $preview];
    }

    public function saveCart(int $memberId, array $cart): void
    {
        if ($memberId <= 0 || (int) ($cart['fresh_food_id'] ?? 0) <= 0) return;
        $this->model->saveCart(self::TENANT_ID, $memberId, (int) $cart['fresh_food_id'], (array) ($cart['items'] ?? []), !empty($cart['coupon_code']) ? (string) $cart['coupon_code'] : null);
    }

    public function loadPersistentCart(int $memberId): ?array
    {
        return $memberId > 0 ? $this->model->loadCart(self::TENANT_ID, $memberId) : null;
    }

    public function deletePersistentCart(int $memberId, ?int $freshFoodId = null): void
    {
        if ($memberId > 0) $this->model->deleteCart(self::TENANT_ID, $memberId, $freshFoodId);
    }

    public function createOrder(int $memberId, int $userId, array $payload): int
    {
        $cart = $payload['cart'] ?? [];
        $preview = $this->cartPreview($cart);
        if (empty($preview['lines'])) throw new RuntimeException('Your Fresh Food cart is empty.');
        if ((float) $preview['subtotal'] <= 0) throw new RuntimeException('Your order total must be greater than zero.');

        $type = strtoupper((string) ($payload['order_type'] ?? 'DELIVERY'));
        if (!in_array($type, ['DELIVERY', 'PICKUP'], true)) {
            throw new RuntimeException('Invalid order type.');
        }

        // IMPORTANT: placing a Fresh Food order must never wait for Gemini.
        // Create the order immediately with a valid initial delivery fee and
        // final total. Gemini runs asynchronously from the order page and
        // replaces the initial fee/total when the real road distance is known.
        $deliveryFee = 0.0;
        if ($type === 'DELIVERY') {
            $business = $this->model->business(self::TENANT_ID, (int) $cart['fresh_food_id']);
            if (!$business || (int) ($business['delivery_available'] ?? 0) !== 1) {
                throw new RuntimeException('Delivery is not available from this Fresh Food business.');
            }
            $deliveryFee = max(0.0, (float) ($business['minimum_delivery_fee'] ?? 0));
        }
        $distanceKm = null;
        $etaMinutes = null;

        // This is the valid initial/fallback total. It is immediately stored
        // with the order, so a Gemini failure never leaves the order without a
        // payable total. On successful routing, processRoute() recalculates the
        // actual distance-based delivery fee and replaces this total.
        $initialTotal = round(
            (float) $preview['gross_subtotal']
            - (float) $preview['item_discount']
            - (float) $preview['coupon_discount']
            + $deliveryFee,
            2
        );

        $items = [];
        foreach ($preview['lines'] as $line) {
            $items[] = [
                'product_id' => $line['product_id'],
                'quantity' => $line['quantity'],
            ];
        }

        $orderPayload = [
            'fresh_food_id' => (int) $cart['fresh_food_id'],
            'member_id' => $memberId,
            'order_type' => $type,
            'customer_name' => trim((string) ($payload['customer_name'] ?? '')),
            'customer_phone' => trim((string) ($payload['customer_phone'] ?? '')),
            'customer_email' => trim((string) ($payload['customer_email'] ?? '')),
            'delivery_address' => trim((string) ($payload['delivery_address'] ?? '')),
            'delivery_city' => trim((string) ($payload['delivery_city'] ?? '')),
            'delivery_district' => trim((string) ($payload['delivery_district'] ?? '')),
            'delivery_state' => trim((string) ($payload['delivery_state'] ?? '')),
            'delivery_postal_code' => trim((string) ($payload['delivery_postal_code'] ?? '')),
            'customer_latitude' => $payload['customer_latitude'] ?? null,
            'customer_longitude' => $payload['customer_longitude'] ?? null,
            'distance_km' => $distanceKm,
            'eta_minutes' => $etaMinutes,
            'delivery_fee' => $deliveryFee,
            'total' => $initialTotal,
            'customer_note' => trim((string) ($payload['customer_note'] ?? '')),
            'coupon_code' => trim((string) ($cart['coupon_code'] ?? '')),
            'discount' => 0,
            'items' => $items,
        ];

        $id = $this->orders->create(self::TENANT_ID, $orderPayload, $userId);
        $this->deletePersistentCart($memberId, (int) $cart['fresh_food_id']);
        return $id;
    }

    /**
     * Process the Gemini route after the order has already been created.
     * This method is intentionally separate from createOrder so the checkout
     * POST returns immediately and the browser can wait asynchronously.
     */
    public function processRoute(int $memberId, int $orderId): array
    {
        $order = $this->model->orderForMember(self::TENANT_ID, $memberId, $orderId);
        if (!$order) throw new RuntimeException('Order not found.');

        if (strtoupper((string)($order['order_type'] ?? '')) !== 'DELIVERY') {
            return ['order' => $order, 'already_processed' => true, 'status' => 'COMPLETED'];
        }

        $status = strtoupper((string)($order['route_status'] ?? 'PENDING'));
        if ($status === 'COMPLETED' && $order['distance_km'] !== null && $order['eta_minutes'] !== null) {
            return ['order' => $order, 'already_processed' => true, 'status' => 'COMPLETED'];
        }

        if ($status === 'PROCESSING') {
            return ['order' => $order, 'already_processed' => false, 'status' => 'PROCESSING'];
        }

        if (!$this->model->claimRoute(self::TENANT_ID, $memberId, $orderId)) {
            $latest = $this->model->orderForMember(self::TENANT_ID, $memberId, $orderId);
            return ['order' => $latest ?: $order, 'already_processed' => false, 'status' => strtoupper((string)($latest['route_status'] ?? 'PROCESSING'))];
        }

        try {
            $latest = $this->model->orderForMember(self::TENANT_ID, $memberId, $orderId);
            if (!$latest) throw new RuntimeException('Order not found.');

            if ($latest['customer_latitude'] === null || $latest['customer_longitude'] === null) {
                throw new RuntimeException('We could not determine your delivery location. Please contact ManipurApp Support.');
            }

            $business = $this->model->business(self::TENANT_ID, (int)$latest['fresh_food_id']);
            if (!$business || $business['latitude'] === null || $business['longitude'] === null) {
                throw new RuntimeException('The Fresh Food store location is not configured. Please contact ManipurApp Support.');
            }

            $customerAddress = trim(implode(', ', array_filter([
                (string)($latest['delivery_address'] ?? ''),
                (string)($latest['delivery_city'] ?? ''),
                (string)($latest['delivery_district'] ?? ''),
                (string)($latest['delivery_state'] ?? 'Manipur'),
                (string)($latest['delivery_postal_code'] ?? ''),
            ])));
            $storeAddress = trim(implode(', ', array_filter([
                (string)($business['address'] ?? ''),
                (string)($business['city'] ?? ''),
                (string)($business['district'] ?? ''),
                (string)($business['state'] ?? 'Manipur'),
            ])));

            $this->route ??= new MemberFreshFoodRouteService();
            $route = $this->route->estimate(
                (float)$latest['customer_latitude'],
                (float)$latest['customer_longitude'],
                (float)$business['latitude'],
                (float)$business['longitude'],
                $customerAddress,
                $storeAddress,
                (string)($business['business_name'] ?? 'Fresh Food store')
            );

            $distance = (float)$route['distance_km'];
            $max = (float)$business['max_delivery_distance_km'];
            if ($max > 0 && $distance > $max + 0.000001) {
                throw new RuntimeException('This delivery address is outside the business delivery area.');
            }

            $included = max(0.0, (float)$business['included_delivery_distance_km']);
            $fee = max(0.0, (float)$business['minimum_delivery_fee']);
            if ($distance > $included) {
                $fee += ceil($distance - $included) * max(0.0, (float)$business['additional_delivery_fee_per_km']);
            }

            $newTotal = round(
                (float)$latest['gross_subtotal']
                - (float)$latest['item_discount']
                - (float)$latest['coupon_discount']
                - (float)$latest['discount']
                + $fee,
                2
            );

            $this->model->commitRouteEstimate(
                self::TENANT_ID,
                $memberId,
                $orderId,
                $distance,
                (int)$route['eta_minutes'],
                $fee,
                $newTotal
            );

            $updated = $this->model->orderForMember(self::TENANT_ID, $memberId, $orderId);
            return ['order' => $updated ?: $latest, 'already_processed' => false, 'status' => 'COMPLETED'];
        } catch (Throwable $e) {
            $this->model->failRouteEstimate(self::TENANT_ID, $memberId, $orderId, $e->getMessage());
            throw $e;
        }
    }

    public function discountedPrice(array $p): float
    {
        return round(max(0, (float) $p['price'] - $this->unitDiscount($p)), 2);
    }

    private function unitDiscount(array $p): float
    {
        $price = (float) ($p['price'] ?? 0);
        $value = max(0, (float) ($p['discount_value'] ?? 0));
        return match (strtoupper((string) ($p['discount_type'] ?? 'NONE'))) {
            'PERCENT' => min($price, round($price * $value / 100, 2)),
            'FLAT' => min($price, $value),
            default => 0.0,
        };
    }

    public function isOpenNow(int $freshFoodId): bool
    {
        $hours = $this->model->hours(self::TENANT_ID, $freshFoodId);
        if (!$hours) return true;
        $day = (int) date('w');
        $now = date('H:i:s');
        foreach ($hours as $row) {
            if ((int) $row['day_of_week'] !== $day) continue;
            if (!empty($row['is_closed'])) return false;
            $open = (string) ($row['opens_at'] ?? '');
            $close = (string) ($row['closes_at'] ?? '');
            if ($open === '' || $close === '') return true;
            if ($close > $open) return $now >= $open && $now <= $close;
            if ($close < $open) return $now >= $open || $now <= $close;
            return true;
        }
        return true;
    }

    private function emptyPreview(): array
    {
        return ['gross_subtotal'=>0.0,'item_discount'=>0.0,'subtotal'=>0.0,'coupon_discount'=>0.0,'delivery_fee'=>0.0,'total'=>0.0,'minimum_order_amount'=>0.0,'delivery_distance_km'=>null,'business'=>null,'coupon'=>null,'lines'=>[]];
    }
}
