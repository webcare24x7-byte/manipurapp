<?php
declare(strict_types=1);
namespace App\Modules\FreshFood\Services;
use App\Modules\FreshFood\Models\FreshFoodCoupon;
use App\Modules\FreshFood\Models\FreshFoodProduct;
use RuntimeException;
final class FreshFoodCouponService
{
    private FreshFoodCoupon $m; private FreshFoodProduct $businesses;
    public function __construct(){ $this->m=new FreshFoodCoupon();$this->businesses=new FreshFoodProduct(); }
    public function all(int $t,?int $f=null):array{return $this->m->all($t,$f);}
    public function find(int $t,int $id):?array{return $this->m->find($t,$id);}
    public function businesses(int $t):array{return $this->businesses->businesses($t);}
    public function create(int $t,array $d,int $u):int{return $this->m->create($t,$this->validate($t,$d),$u);}
    public function update(int $t,int $id,array $d,int $u):void{if(!$this->find($t,$id))throw new RuntimeException('Coupon not found.');$this->m->update($t,$id,$this->validate($t,$d,$id),$u);}
    public function delete(int $t,int $id,int $u):void{if(!$this->find($t,$id))throw new RuntimeException('Coupon not found.');$this->m->delete($t,$id,$u);}
    public function applicable(int $t,int $f,string $code,float $subtotal):?array{$c=$this->m->findApplicable($t,$f,$code,$subtotal);if(!$c)return null;$discount=$c['discount_type']==='PERCENT'?round($subtotal*((float)$c['discount_value']/100),2):round((float)$c['discount_value'],2);if($c['maximum_discount']!==null)$discount=min($discount,(float)$c['maximum_discount']);$c['calculated_discount']=round(min($discount,$subtotal),2);return $c;}
    public function incrementUsage(int $t,int $id):void{$this->m->incrementUsage($t,$id);}
    private function validate(int $t,array $d,?int $ignore=null):array{$f=(int)($d['fresh_food_id']??0);$valid=false;foreach($this->businesses($t) as $b)if((int)$b['id']===$f){$valid=true;break;}if(!$valid)throw new RuntimeException('Please select a valid Fresh Food business.');$code=strtoupper(trim((string)($d['code']??'')));if(!preg_match('/^[A-Z0-9_-]{3,80}$/',$code))throw new RuntimeException('Coupon code must contain 3–80 letters, numbers, hyphens or underscores.');if($this->m->codeExists($t,$f,$code,$ignore))throw new RuntimeException('This coupon code already exists for this Fresh Food business.');$name=trim((string)($d['name']??''));if($name==='')throw new RuntimeException('Coupon name is required.');$type=strtoupper(trim((string)($d['discount_type']??'PERCENT')));if(!in_array($type,['PERCENT','FLAT'],true))throw new RuntimeException('Invalid coupon discount type.');$value=(float)($d['discount_value']??0);if($value<=0)throw new RuntimeException('Coupon discount must be greater than zero.');if($type==='PERCENT'&&$value>100)throw new RuntimeException('Percentage coupon cannot exceed 100%.');$min=max(0,(float)($d['minimum_order_amount']??0));$max=trim((string)($d['maximum_discount']??''))===''?null:(float)$d['maximum_discount'];if($max!==null&&$max<0)throw new RuntimeException('Maximum discount cannot be negative.');$usage=trim((string)($d['usage_limit']??''))===''?null:(int)$d['usage_limit'];if($usage!==null&&$usage<1)throw new RuntimeException('Usage limit must be at least 1.');$start=trim((string)($d['starts_at']??''))?:null;$end=trim((string)($d['ends_at']??''))?:null;if($start&&$end&&strtotime($end)<strtotime($start))throw new RuntimeException('Coupon end date cannot be before the start date.');$status=in_array(($d['status']??'Active'),['Active','Inactive'],true)?$d['status']:'Active';return ['fresh_food_id'=>$f,'code'=>$code,'name'=>$name,'description'=>trim((string)($d['description']??''))?:null,'discount_type'=>$type,'discount_value'=>round($value,2),'minimum_order_amount'=>round($min,2),'maximum_discount'=>$max===null?null:round($max,2),'starts_at'=>$start,'ends_at'=>$end,'usage_limit'=>$usage,'status'=>$status];}
}
