<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use App\Modules\Restaurant\Models\RestaurantOrder;
use App\Modules\Restaurant\Models\RestaurantCoupon;
use RuntimeException;

final class RestaurantOrderService
{
    private RestaurantOrder $orders;
    private RestaurantCoupon $coupons;
    public function __construct(){ $this->orders=new RestaurantOrder(); $this->coupons=new RestaurantCoupon(); }
    public function restaurants(int $tenantId): array{return $this->orders->restaurants($tenantId);}
    public function restaurant(int $tenantId,int $restaurantId):?array{return $this->orders->restaurant($tenantId,$restaurantId);}
    public function members(int $tenantId): array{return $this->orders->members($tenantId);}
    public function all(int $tenantId,?int $restaurantId=null,?string $status=null): array{return $this->orders->all($tenantId,$restaurantId,$status);}
    public function find(int $tenantId,int $id):?array{return $this->orders->find($tenantId,$id);}
    public function memberOrders(int $tenantId,int $memberId):array{return $this->orders->memberOrders($tenantId,$memberId);}
    public function updateStatus(int $tenantId,int $id,string $status,?string $reason,int $userId):void{$this->orders->updateStatus($tenantId,$id,$status,$reason,$userId);}
    public function menuForItem(int $tenantId,int $restaurantId,int $itemId):array{$item=$this->orders->item($tenantId,$restaurantId,$itemId);if(!$item)throw new RuntimeException('Menu item not found.');return ['item'=>$item,'variants'=>$this->orders->variants($tenantId,$restaurantId,$itemId),'modifier_groups'=>$this->orders->modifierGroups($tenantId,$restaurantId,$itemId)];}
    public function menu(int $tenantId,int $restaurantId):array{$items=$this->orders->menuItems($tenantId,$restaurantId);foreach($items as &$item){$item['variants']=$this->orders->variants($tenantId,$restaurantId,(int)$item['id']);$item['modifier_groups']=$this->orders->modifierGroups($tenantId,$restaurantId,(int)$item['id']);}unset($item);return $items;}
    public function coupons(int $tenantId,int $restaurantId):array{return $this->coupons->all($tenantId,$restaurantId);}
    public function couponPreview(int $tenantId,int $restaurantId,string $code,float $subtotal):array{ $coupon=$this->coupons->findApplicable($tenantId,$restaurantId,strtoupper(trim($code)),max(0,$subtotal));if(!$coupon)throw new RuntimeException('Coupon is invalid, inactive, expired, exhausted, or the minimum order has not been reached.');$discount=$coupon['discount_type']==='PERCENT'?round($subtotal*((float)$coupon['discount_value']/100),2):(float)$coupon['discount_value'];if($coupon['maximum_discount']!==null)$discount=min($discount,(float)$coupon['maximum_discount']);$discount=min($discount,$subtotal);return ['code'=>$coupon['code'],'name'=>$coupon['name'],'discount'=>round($discount,2),'type'=>$coupon['discount_type'],'value'=>(float)$coupon['discount_value'],'minimum'=>(float)$coupon['minimum_order_amount']]; }

    public function create(int $tenantId,array $payload,int $createdBy):int
    {
        $restaurantId=(int)($payload['restaurant_id']??0);$restaurant=$this->orders->restaurant($tenantId,$restaurantId);if(!$restaurant)throw new RuntimeException('Restaurant is not available.');if(empty($restaurant['accepting_orders']))throw new RuntimeException('Restaurant is not currently accepting orders.');
        $type=strtoupper((string)($payload['order_type']??'DELIVERY'));if(!in_array($type,['DELIVERY','PICKUP'],true))throw new RuntimeException('Invalid order type.');if($type==='DELIVERY'&&empty($restaurant['delivery_available']))throw new RuntimeException('Delivery is unavailable for this restaurant.');if($type==='PICKUP'&&empty($restaurant['pickup_available']))throw new RuntimeException('Pickup is unavailable for this restaurant.');
        $memberId=(int)($payload['member_id']??0);$member=null;foreach($this->orders->members($tenantId) as $m)if((int)$m['id']===$memberId){$member=$m;break;}if(!$member)throw new RuntimeException('Member account not found.');
        $rawItems=is_array($payload['items']??null)?$payload['items']:[];if(!$rawItems)throw new RuntimeException('Your cart is empty.');
        $snapshot=[];$gross=0.0;$subtotal=0.0;$itemDiscount=0.0;
        foreach($rawItems as $raw){$itemId=(int)($raw['item_id']??0);$qty=(int)($raw['quantity']??0);if($itemId<=0||$qty<1||$qty>50)throw new RuntimeException('Invalid cart item.');$item=$this->orders->item($tenantId,$restaurantId,$itemId);if(!$item)throw new RuntimeException('One of the selected menu items no longer exists.');if(empty($item['is_available']))throw new RuntimeException('One of the selected items is sold out.');
            $variantId=(int)($raw['variant_id']??0);$variantId=$variantId>0?$variantId:null;$variantName=null;$base=(float)$item['price'];
            if($variantId!==null){$found=null;foreach($this->orders->variants($tenantId,$restaurantId,$itemId) as $v)if((int)$v['id']===$variantId){$found=$v;break;}if(!$found)throw new RuntimeException('Selected variant is invalid.');if(empty($found['is_available']))throw new RuntimeException('Selected variant is unavailable.');$variantName=(string)$found['name'];$base=(float)$found['price'];}
            $selected=array_values(array_unique(array_map('intval',is_array($raw['modifier_option_ids']??null)?$raw['modifier_option_ids']:[])));$groups=$this->orders->modifierGroups($tenantId,$restaurantId,$itemId);$mods=[];$modTotal=0.0;$validIds=[];
            foreach($groups as $group){$valid=[];foreach($group['options'] as $o)if(in_array((int)$o['id'],$selected,true)&&!empty($o['is_available']))$valid[]=$o;$count=count($valid);$min=(int)$group['min_selections'];$max=$group['max_selections']===null?null:(int)$group['max_selections'];if($count<$min||($max!==null&&$count>$max)||(!empty($group['is_required'])&&$count<max(1,$min)))throw new RuntimeException('Please complete the required options for '.$group['name'].'.');foreach($valid as $o){$validIds[]=(int)$o['id'];$adj=(float)$o['price_adjustment'];$modTotal+=$adj;$mods[]=['group_id'=>(int)$group['id'],'option_id'=>(int)$o['id'],'group_name'=>(string)$group['name'],'option_name'=>(string)$o['name'],'price_adjustment'=>$adj];}}
            if(array_diff($selected,$validIds))throw new RuntimeException('Invalid modifier selection.');
            $discountType=(string)($item['discount_type']??'PERCENT');$discountValue=(float)($item['discount_value']??0);$discountBase=$discountType==='FLAT'?min($discountValue,$base):min($base,round($base*$discountValue/100,2));$originalUnit=round($base+$modTotal,2);$unit=round($base-$discountBase+$modTotal,2);$line=round($unit*$qty,2);$gross+=round($originalUnit*$qty,2);$subtotal+=$line;$itemDiscount+=round($discountBase*$qty,2);
            $snapshot[]=['item_id'=>$itemId,'variant_id'=>$variantId,'item_name'=>(string)$item['name'],'variant_name'=>$variantName,'original_unit_price'=>$originalUnit,'discount_type'=>$discountType,'discount_value'=>$discountValue,'discount_amount'=>round($discountBase,2),'unit_price'=>$unit,'quantity'=>$qty,'line_total'=>$line,'modifiers'=>$mods];
        }
        $minimum=(float)$restaurant['minimum_order_amount'];if($subtotal<$minimum)throw new RuntimeException('Minimum order amount is ₹'.number_format($minimum,2).'.');
        $couponCode=strtoupper(trim((string)($payload['coupon_code']??'')));$couponDiscount=0.0;$couponId=null;if($couponCode!==''){$coupon=$this->coupons->findApplicable($tenantId,$restaurantId,$couponCode,$subtotal);if(!$coupon)throw new RuntimeException('Coupon is invalid, inactive, expired, exhausted, or the minimum order has not been reached.');$couponId=(int)$coupon['id'];$couponDiscount=$coupon['discount_type']==='PERCENT'?round($subtotal*((float)$coupon['discount_value']/100),2):(float)$coupon['discount_value'];if($coupon['maximum_discount']!==null)$couponDiscount=min($couponDiscount,(float)$coupon['maximum_discount']);$couponDiscount=min($couponDiscount,$subtotal);$couponCode=(string)$coupon['code'];}
        $delivery=0.0;if($type==='DELIVERY'){$free=$restaurant['free_delivery_above']===null?null:(float)$restaurant['free_delivery_above'];if($free===null||$subtotal<$free)$delivery=(float)$restaurant['delivery_fee'];if(trim((string)($payload['delivery_address']??''))==='')throw new RuntimeException('Delivery address is required.');}
        $total=max(0,round($subtotal+$delivery-$couponDiscount,2));$totalDiscount=round($itemDiscount+$couponDiscount,2);
        return $this->orders->create($tenantId,['restaurant_id'=>$restaurantId,'member_id'=>$memberId,'order_type'=>$type,'payment_method'=>'CASH','customer_name'=>trim((string)$member['first_name'].' '.(string)$member['last_name']),'customer_phone'=>$member['phone'],'customer_email'=>$member['email'],'delivery_address'=>$type==='DELIVERY'?trim((string)$payload['delivery_address']):null,'delivery_city'=>$type==='DELIVERY'?trim((string)($payload['delivery_city']??'')):null,'delivery_district'=>$type==='DELIVERY'?trim((string)($payload['delivery_district']??'')):null,'delivery_state'=>$type==='DELIVERY'?trim((string)($payload['delivery_state']??'')):null,'delivery_postal_code'=>$type==='DELIVERY'?trim((string)($payload['delivery_postal_code']??'')):null,'customer_note'=>trim((string)($payload['customer_note']??'')),'gross_subtotal'=>round($gross,2),'item_discount'=>round($itemDiscount,2),'coupon_code'=>$couponCode!==''?$couponCode:null,'coupon_discount'=>round($couponDiscount,2),'subtotal'=>round($subtotal,2),'delivery_fee'=>round($delivery,2),'discount'=>$totalDiscount,'total'=>$total,'coupon_id'=>$couponId],$snapshot,$createdBy);
    }
}
