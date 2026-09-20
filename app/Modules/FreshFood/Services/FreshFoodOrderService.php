<?php
declare(strict_types=1);

namespace App\Modules\FreshFood\Services;

use App\Modules\FreshFood\Models\FreshFoodOrder;
use App\Modules\FreshFood\Services\FreshFoodCouponService;
use RuntimeException;

final class FreshFoodOrderService
{
    private FreshFoodOrder $m;
    private FreshFoodCouponService $coupons;

    private const TRANSITIONS = [
        'PENDING'=>['ACCEPTED','REJECTED','CANCELLED'],'ACCEPTED'=>['PACKING','CANCELLED'],'PACKING'=>['READY','CANCELLED'],
        'READY'=>['ASSIGNED','COMPLETED'],'ASSIGNED'=>['OUT_FOR_DELIVERY','CANCELLED'],'OUT_FOR_DELIVERY'=>['DELIVERED'],'DELIVERED'=>['COMPLETED'],'COMPLETED'=>[],'CANCELLED'=>[],'REJECTED'=>[]
    ];
    public function __construct(){ $this->m=new FreshFoodOrder();$this->coupons=new FreshFoodCouponService(); }
    public function all(int $t,?string $status=null):array{return $this->m->all($t,$status);}
    public function find(int $t,int $id):?array{return $this->m->find($t,$id);}
    public function details(int $t,int $id):array{$o=$this->find($t,$id);if(!$o)throw new RuntimeException('Order not found.');$o['items']=$this->m->items($t,$id);$o['history']=$this->m->history($t,$id);return $o;}
    public function formData(int $t,int $f=0):array{return ['businesses'=>$this->m->businesses($t),'members'=>$this->m->members($t),'products'=>$f>0?$this->m->productsForBusiness($t,$f):[],'coupons'=>$f>0?$this->coupons->all($t,$f):[]];}
    public function products(int $t,int $f):array{return $this->m->productsForBusiness($t,$f);}
    public function coupons(int $t,int $f):array{return $this->coupons->all($t,$f);}
    public function previewCoupon(int $t,int $f,string $code,float $subtotal):array{if($subtotal<0)throw new RuntimeException('Invalid subtotal.');$c=$this->coupons->applicable($t,$f,$code,$subtotal);if(!$c)throw new RuntimeException('Coupon is invalid, inactive, expired, unavailable, or the minimum order amount has not been reached.');return $c;}
    public function deliveryUsers(int $t):array{return $this->m->users($t);}

    public function create(int $t,array $input,int $userId):int
    {
        $businessId=(int)($input['fresh_food_id']??0);if($businessId<=0||!$this->businessExists($t,$businessId))throw new RuntimeException('Select a valid Fresh Food business.');
        $type=strtoupper(trim((string)($input['order_type']??'')));if(!in_array($type,['DELIVERY','PICKUP'],true))throw new RuntimeException('Invalid order type.');
        $name=trim((string)($input['customer_name']??''));if($name==='')throw new RuntimeException('Customer name is required.');
        if($type==='DELIVERY'&&trim((string)($input['delivery_address']??''))==='')throw new RuntimeException('Delivery address is required for delivery orders.');
        $rawItems=$input['items']??[];if(!is_array($rawItems))throw new RuntimeException('At least one product is required.');
        $products=[];foreach($this->products($t,$businessId) as $p)$products[(int)$p['id']]=$p;
        $items=[];$gross=0.0;$itemDiscountTotal=0.0;
        foreach($rawItems as $row){
            $productId=(int)($row['product_id']??0);$quantity=(float)($row['quantity']??0);if($productId<=0||$quantity<=0)continue;if(!isset($products[$productId]))throw new RuntimeException('One of the selected products is invalid.');
            $p=$products[$productId];if(empty($p['is_available']))throw new RuntimeException('Product "'.$p['name'].'" is unavailable.');
            $min=(float)$p['min_order_quantity'];$inc=(float)$p['increment_quantity'];if($quantity<$min-0.000001)throw new RuntimeException($p['name'].' minimum order quantity is '.$min.' '.$p['unit'].'.');$steps=($quantity-$min)/$inc;if(abs($steps-round($steps))>0.0001)throw new RuntimeException($p['name'].' must be ordered in increments of '.$inc.' '.$p['unit'].'.');
            $stock=$this->m->stockState($t,$productId);$available=(float)($stock['current_quantity']??0);if($available+0.000001<$quantity)throw new RuntimeException('Insufficient stock for '.$p['name'].'. Available: '.$available.' '.$p['unit'].'.');
            $price=(float)$p['price'];$dt=strtoupper((string)($p['discount_type']??'NONE'));$dv=(float)($p['discount_value']??0);$unitDiscount=$dt==='PERCENT'?min($price,$price*$dv/100):($dt==='FLAT'?min($price,$dv):0.0);$lineGross=round($price*$quantity,2);$lineDiscount=round($unitDiscount*$quantity,2);$lineTotal=round($lineGross-$lineDiscount,2);
            $items[]=['product_id'=>$productId,'product_name'=>$p['name'],'unit'=>$p['unit'],'quantity'=>$quantity,'original_unit_price'=>$price,'unit_price'=>round($price,2),'discount_type'=>$dt,'discount_value'=>$dv,'discount'=>$lineDiscount,'discount_amount'=>$lineDiscount,'line_total'=>$lineTotal];$gross+=$lineGross;$itemDiscountTotal+=$lineDiscount;
        }
        if(!$items)throw new RuntimeException('Add at least one product to the order.');
        $afterItems=round($gross-$itemDiscountTotal,2);$couponCode=strtoupper(trim((string)($input['coupon_code']??'')));$couponDiscount=0.0;$couponId=null;
        if($couponCode!==''){ $coupon=$this->coupons->applicable($t,$businessId,$couponCode,$afterItems);if(!$coupon)throw new RuntimeException('Coupon is invalid, inactive, expired, unavailable, or the minimum order amount has not been reached.');$couponDiscount=(float)$coupon['calculated_discount'];$couponId=(int)$coupon['id']; }
        $deliveryFee=$type==='DELIVERY'?max(0.0,(float)($input['delivery_fee']??0)):0.0;$manualDiscount=max(0.0,(float)($input['discount']??0));$manualDiscount=min($manualDiscount,max(0.0,$afterItems-$couponDiscount));$total=round($afterItems-$couponDiscount-$manualDiscount+$deliveryFee,2);
        $memberId=(int)($input['member_id']??0);$memberId=$memberId>0?$memberId:null;
        $order=['fresh_food_id'=>$businessId,'member_id'=>$memberId,'order_no'=>$this->m->nextOrderNo($t),'order_type'=>$type,'payment_method'=>trim((string)($input['payment_method']??''))?:null,'customer_name'=>$name,'customer_phone'=>trim((string)($input['customer_phone']??''))?:null,'customer_email'=>trim((string)($input['customer_email']??''))?:null,'delivery_address'=>$type==='DELIVERY'?trim((string)($input['delivery_address']??'')):null,'delivery_city'=>trim((string)($input['delivery_city']??''))?:null,'delivery_district'=>trim((string)($input['delivery_district']??''))?:null,'delivery_state'=>trim((string)($input['delivery_state']??''))?:null,'delivery_postal_code'=>trim((string)($input['delivery_postal_code']??''))?:null,'customer_latitude'=>$this->nullableFloat($input['customer_latitude']??null),'customer_longitude'=>$this->nullableFloat($input['customer_longitude']??null),'distance_km'=>$this->nullableFloat($input['distance_km']??null),'customer_note'=>trim((string)($input['customer_note']??''))?:null,'gross_subtotal'=>round($gross,2),'item_discount'=>round($itemDiscountTotal,2),'coupon_code'=>$couponCode!==''?$couponCode:null,'coupon_discount'=>round($couponDiscount,2),'delivery_fee'=>round($deliveryFee,2),'discount'=>round($manualDiscount,2),'total'=>$total];
        $id=$this->m->create($t,$order,$items,$userId);if($couponId!==null)$this->coupons->incrementUsage($t,$couponId);return $id;
    }
    public function transition(int $t,int $orderId,string $toStatus,array $input,int $userId):void{$o=$this->find($t,$orderId);if(!$o)throw new RuntimeException('Order not found.');$from=$o['status'];$to=strtoupper(trim($toStatus));if(!in_array($to,self::TRANSITIONS[$from]??[],true))throw new RuntimeException('Invalid status transition: '.$from.' → '.$to.'.');$fields=[];$note=trim((string)($input['note']??''))?:null;if($to==='ACCEPTED'){$this->commitInventory($t,$o,$userId);$fields['accepted_at']=date('Y-m-d H:i:s');$fields['inventory_committed_at']=date('Y-m-d H:i:s');}elseif($to==='REJECTED'){$reason=trim((string)($input['reason']??''));if($reason==='')throw new RuntimeException('Rejection reason is required.');$fields['rejection_reason']=$reason;$fields['rejected_at']=date('Y-m-d H:i:s');$note=$reason;}elseif($to==='PACKING')$fields['packing_at']=date('Y-m-d H:i:s');elseif($to==='READY')$fields['ready_at']=date('Y-m-d H:i:s');elseif($to==='ASSIGNED'){$assigned=(int)($input['delivery_assigned_to']??0);if($assigned<=0)throw new RuntimeException('Select a delivery person before assigning the order.');$valid=false;foreach($this->deliveryUsers($t) as $u)if((int)$u['id']===$assigned){$valid=true;break;}if(!$valid)throw new RuntimeException('Selected delivery person is invalid.');$fields['delivery_assigned_to']=$assigned;$fields['assigned_at']=date('Y-m-d H:i:s');$fields['delivery_assigned_at']=date('Y-m-d H:i:s');}elseif($to==='OUT_FOR_DELIVERY')$fields['out_for_delivery_at']=date('Y-m-d H:i:s');elseif($to==='DELIVERED')$fields['delivered_at']=date('Y-m-d H:i:s');elseif($to==='COMPLETED')$fields['completed_at']=date('Y-m-d H:i:s');elseif($to==='CANCELLED'){$reason=trim((string)($input['reason']??''));if($reason==='')throw new RuntimeException('Cancellation reason is required.');$fields['cancellation_reason']=$reason;$fields['cancelled_at']=date('Y-m-d H:i:s');$note=$reason;if(!empty($o['inventory_committed_at'])&&empty($o['inventory_released_at'])){$this->releaseInventory($t,$o,$userId);$fields['inventory_released_at']=date('Y-m-d H:i:s');}}$this->m->setStatus($t,$orderId,$to,$fields,$userId);$this->m->addHistory($t,$orderId,$from,$to,$note,$userId);}
    private function businessExists(int $t,int $id):bool{return $this->findBusiness($t,$id);}
    private function findBusiness(int $t,int $id):bool{foreach($this->m->businesses($t) as $b)if((int)$b['id']===$id)return true;return false;}
    private function commitInventory(int $t,array $o,int $u):void{if(!empty($o['inventory_committed_at']))return;foreach($this->m->items($t,(int)$o['id']) as $i){$state=$this->m->stockState($t,(int)$i['product_id']);$before=(float)($state['current_quantity']??0);$qty=(float)$i['quantity'];if(!$state||$before+0.000001<$qty)throw new RuntimeException('Insufficient stock for '.$i['product_name'].'.');$after=$before-$qty;$this->m->updateInventory($t,(int)$state['id'],$after,$u);$this->m->addInventoryMovement($t,(int)$state['fresh_food_id'],(int)$i['product_id'],(int)$state['id'],-$qty,$before,$after,$o['order_no'],$u);}}
    private function releaseInventory(int $t,array $o,int $u):void{foreach($this->m->items($t,(int)$o['id']) as $i){$state=$this->m->stockState($t,(int)$i['product_id']);if(!$state)continue;$before=(float)$state['current_quantity'];$qty=(float)$i['quantity'];$after=$before+$qty;$this->m->updateInventory($t,(int)$state['id'],$after,$u);$this->m->addInventoryMovement($t,(int)$state['fresh_food_id'],(int)$i['product_id'],(int)$state['id'],$qty,$before,$after,$o['order_no'].'-CANCEL',$u);}}
    private function nullableFloat(mixed $v):?float{if($v===null||trim((string)$v)==='')return null;$n=(float)$v;return is_finite($n)?$n:null;}
}
