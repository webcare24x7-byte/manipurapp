<?php
declare(strict_types=1);
namespace App\Modules\FreshFood\Controllers;
use App\Core\Auth;use App\Core\Authorization;use App\Core\Controller;use App\Modules\FreshFood\Services\FreshFoodCouponService;use RuntimeException;use Throwable;
final class FreshFoodCouponController extends Controller
{
    private FreshFoodCouponService $s; private Authorization $a;
    public function __construct(){ $this->s=new FreshFoodCouponService();$this->a=new Authorization(); }
    private function allow(string $p,string $r='/fresh-food/coupons'):bool{try{$this->a->authorize($p);return true;}catch(RuntimeException $e){flash('error',$e->getMessage());redirect($r);return false;}}
    public function index():void{if(!$this->allow('fresh_food.coupons.view','/fresh-food'))return;$t=(int)Auth::tenantId();$this->view('FreshFood::Coupons.index',['title'=>'Fresh Food Coupons','coupons'=>$this->s->all($t),'businesses'=>$this->s->businesses($t)]);}
    public function create():void{if(!$this->allow('fresh_food.coupons.manage','/fresh-food'))return;$t=(int)Auth::tenantId();$this->view('FreshFood::Coupons.create',['title'=>'Create Fresh Food Coupon','businesses'=>$this->s->businesses($t)]);}
    public function store():void{if(!$this->allow('fresh_food.coupons.manage','/fresh-food'))return;$t=(int)Auth::tenantId();try{$this->s->create($t,$_POST,(int)Auth::id());flash('success','Coupon created.');redirect('/fresh-food/coupons');}catch(Throwable $e){$this->view('FreshFood::Coupons.create',['title'=>'Create Fresh Food Coupon','businesses'=>$this->s->businesses($t),'old'=>$_POST,'error'=>$e->getMessage()]);}}
    public function edit(int $id):void{if(!$this->allow('fresh_food.coupons.manage','/fresh-food'))return;$t=(int)Auth::tenantId();$c=$this->s->find($t,$id);if(!$c){abort(404);return;}$this->view('FreshFood::Coupons.edit',['title'=>'Edit Fresh Food Coupon','record'=>$c,'businesses'=>$this->s->businesses($t)]);}
    public function update(int $id):void{if(!$this->allow('fresh_food.coupons.manage','/fresh-food'))return;$t=(int)Auth::tenantId();try{$this->s->update($t,$id,$_POST,(int)Auth::id());flash('success','Coupon updated.');redirect('/fresh-food/coupons');}catch(Throwable $e){$c=$this->s->find($t,$id)??$_POST;$this->view('FreshFood::Coupons.edit',['title'=>'Edit Fresh Food Coupon','record'=>$c,'businesses'=>$this->s->businesses($t),'error'=>$e->getMessage()]);}}
    public function destroy(int $id):void{if(!$this->allow('fresh_food.coupons.manage','/fresh-food'))return;try{$this->s->delete((int)Auth::tenantId(),$id,(int)Auth::id());flash('success','Coupon deleted.');}catch(Throwable $e){flash('error',$e->getMessage());}redirect('/fresh-food/coupons');}
}
