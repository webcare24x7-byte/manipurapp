<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use App\Modules\Restaurant\Models\RestaurantCoupon;
use App\Modules\Restaurant\Models\RestaurantItem;
use RuntimeException;

final class RestaurantCouponService
{
    private RestaurantCoupon $m;
    private RestaurantItem $items;

    public function __construct()
    {
        $this->m = new RestaurantCoupon();
        $this->items = new RestaurantItem();
    }

    public function all(int $tenantId, ?int $restaurantId = null): array { return $this->m->all($tenantId, $restaurantId); }
    public function find(int $tenantId, int $id): ?array { return $this->m->find($tenantId, $id); }
    public function restaurants(int $tenantId): array { return $this->items->restaurants($tenantId); }

    public function create(int $tenantId, array $data, int $userId): int
    {
        $normalized = $this->validate($tenantId, $data);
        return $this->m->create($tenantId, $normalized, $userId);
    }

    public function update(int $tenantId, int $id, array $data, int $userId): void
    {
        if (!$this->find($tenantId, $id)) throw new RuntimeException('Coupon not found.');
        $normalized = $this->validate($tenantId, $data, $id);
        $this->m->update($tenantId, $id, $normalized, $userId);
    }

    public function delete(int $tenantId, int $id, int $userId): void
    {
        if (!$this->find($tenantId, $id)) throw new RuntimeException('Coupon not found.');
        $this->m->delete($tenantId, $id, $userId);
    }

    public function applicable(int $tenantId, int $restaurantId, string $code, float $subtotal): ?array
    {
        $code = strtoupper(trim($code));
        if ($code === '') return null;
        $coupon = $this->m->findApplicable($tenantId, $restaurantId, $code, $subtotal);
        if (!$coupon) return null;
        return $this->calculate($coupon, $subtotal);
    }

    private function calculate(array $coupon, float $subtotal): array
    {
        $discount = (string)$coupon['discount_type'] === 'PERCENT'
            ? round($subtotal * ((float)$coupon['discount_value'] / 100), 2)
            : round((float)$coupon['discount_value'], 2);
        if ($coupon['maximum_discount'] !== null) $discount = min($discount, (float)$coupon['maximum_discount']);
        $discount = min($discount, $subtotal);
        $coupon['calculated_discount'] = round($discount, 2);
        return $coupon;
    }

    private function validate(int $tenantId, array $data, ?int $ignoreId = null): array
    {
        $restaurantId = (int)($data['restaurant_id'] ?? 0);
        if ($restaurantId <= 0) throw new RuntimeException('Please select a restaurant.');
        if (!$this->items->restaurantExists($tenantId, $restaurantId)) throw new RuntimeException('Selected restaurant is invalid.');
        $code = strtoupper(trim((string)($data['code'] ?? '')));
        if (!preg_match('/^[A-Z0-9_-]{3,80}$/', $code)) throw new RuntimeException('Coupon code must contain 3–80 letters, numbers, hyphens or underscores.');
        if ($this->m->codeExists($tenantId, $restaurantId, $code, $ignoreId)) throw new RuntimeException('This coupon code already exists for this restaurant.');
        $name = trim((string)($data['name'] ?? ''));
        if ($name === '') throw new RuntimeException('Coupon name is required.');
        $type = strtoupper(trim((string)($data['discount_type'] ?? 'PERCENT')));
        if (!in_array($type, ['PERCENT','FLAT'], true)) throw new RuntimeException('Invalid coupon discount type.');
        $value = (float)($data['discount_value'] ?? 0);
        if ($value <= 0) throw new RuntimeException('Coupon discount must be greater than zero.');
        if ($type === 'PERCENT' && $value > 100) throw new RuntimeException('Percentage coupon cannot exceed 100%.');
        $minimum = max(0, (float)($data['minimum_order_amount'] ?? 0));
        $maximum = trim((string)($data['maximum_discount'] ?? '')) === '' ? null : (float)$data['maximum_discount'];
        if ($maximum !== null && $maximum < 0) throw new RuntimeException('Maximum discount cannot be negative.');
        $usage = trim((string)($data['usage_limit'] ?? '')) === '' ? null : (int)$data['usage_limit'];
        if ($usage !== null && $usage < 1) throw new RuntimeException('Usage limit must be at least 1.');
        $status = in_array(($data['status'] ?? 'Active'), ['Active','Inactive'], true) ? $data['status'] : 'Active';
        return ['restaurant_id'=>$restaurantId,'code'=>$code,'name'=>$name,'description'=>trim((string)($data['description']??'')),
            'discount_type'=>$type,'discount_value'=>round($value,2),'minimum_order_amount'=>round($minimum,2),
            'maximum_discount'=>$maximum===null?null:round($maximum,2),'starts_at'=>trim((string)($data['starts_at']??''))?:null,
            'ends_at'=>trim((string)($data['ends_at']??''))?:null,'usage_limit'=>$usage,'status'=>$status];
    }
}
