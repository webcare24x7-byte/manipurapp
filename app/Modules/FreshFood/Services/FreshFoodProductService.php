<?php
declare(strict_types=1);

namespace App\Modules\FreshFood\Services;

use App\Modules\FreshFood\Models\FreshFoodProduct;
use RuntimeException;

final class FreshFoodProductService
{
    private FreshFoodProduct $m;
    public function __construct(){ $this->m=new FreshFoodProduct(); }
    public function all(int $t,bool $d=false):array{return $this->m->all($t,$d);}
    public function businesses(int $t):array{return $this->m->businesses($t);}
    public function categories(int $t,int $f):array{return $this->m->categories($t,$f);}
    public function find(int $t,int $id):?array{return $this->m->find($t,$id);}
    public function findDeleted(int $t,int $id):?array{return $this->m->findDeleted($t,$id);}
    public function create(int $t,array $i,int $u):int{$d=$this->v($t,$i);$d['slug']=$this->slug($t,$d['fresh_food_id'],$this->slugify($d['name']));return $this->m->create($t,$d,$u);}
    public function update(int $t,int $id,array $i,int $u):void{if(!$this->find($t,$id))throw new RuntimeException('Product not found.');$d=$this->v($t,$i);$d['slug']=$this->slug($t,$d['fresh_food_id'],$this->slugify($d['name']),$id);$this->m->update($t,$id,$d,$u);}
    public function delete(int $t,int $id,int $u):void{if(!$this->find($t,$id))throw new RuntimeException('Product not found.');$this->m->delete($t,$id,$u);}
    public function restore(int $t,int $id,int $u):void{if(!$this->findDeleted($t,$id))throw new RuntimeException('Deleted product not found.');$this->m->restore($t,$id,$u);}
    private function v(int $t,array $i):array
    {
        $f=(int)($i['fresh_food_id']??0);if(!$this->m->businessExists($t,$f))throw new RuntimeException('Selected Fresh Food business is invalid.');
        $c=(int)($i['category_id']??0);if($c>0&&!$this->m->categoryBelongs($t,$f,$c))throw new RuntimeException('Selected category is invalid for this Fresh Food business.');
        $n=trim((string)($i['name']??''));if($n==='')throw new RuntimeException('Product name is required.');
        $units=['piece','kg','gram','litre','ml','pack','dozen','box'];$unit=(string)($i['unit']??'piece');if(!in_array($unit,$units,true))throw new RuntimeException('Invalid product unit.');
        $price=(float)($i['price']??0);$min=(float)($i['min_order_quantity']??1);$inc=(float)($i['increment_quantity']??1);if($price<0||$min<=0||$inc<=0)throw new RuntimeException('Price and quantities must be valid positive values.');
        $dt=strtoupper(trim((string)($i['discount_type']??'NONE')));if(!in_array($dt,['NONE','PERCENT','FLAT'],true))throw new RuntimeException('Invalid product discount type.');
        $dv=(float)($i['discount_value']??0);if($dv<0)throw new RuntimeException('Product discount cannot be negative.');if($dt==='PERCENT'&&$dv>100)throw new RuntimeException('Product percentage discount cannot exceed 100%.');if($dt==='NONE')$dv=0;if($dt==='FLAT'&&$dv>$price)throw new RuntimeException('Flat product discount cannot exceed the product price.');
        $s=(string)($i['status']??'Active');if(!in_array($s,['Active','Inactive'],true))throw new RuntimeException('Invalid product status.');
        return ['fresh_food_id'=>$f,'category_id'=>$c?:null,'name'=>$n,'description'=>trim((string)($i['description']??''))?:null,'unit'=>$unit,'price'=>number_format($price,2,'.',''),'discount_type'=>$dt,'discount_value'=>number_format($dv,2,'.',''),'min_order_quantity'=>number_format($min,3,'.',''),'increment_quantity'=>number_format($inc,3,'.',''),'is_variable_weight'=>!empty($i['is_variable_weight'])?1:0,'is_available'=>!empty($i['is_available'])?1:0,'sort_order'=>(int)($i['sort_order']??0),'status'=>$s];
    }
    private function slug(int $t,int $f,string $b,?int $ignore=null):string{$b=$b?:'fresh-food-product';$c=$b;$n=2;while($this->m->slugExists($t,$f,$c,$ignore))$c=$b.'-'.$n++;return $c;}
    private function slugify(string $v):string{$v=strtolower(trim($v));$v=preg_replace('/[^a-z0-9]+/','-',$v)??'';return trim($v,'-');}
}
