<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Controllers;

use App\Core\Auth;
use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\Restaurant\Services\RestaurantCouponService;
use RuntimeException;
use Throwable;

final class RestaurantCouponController extends Controller
{
    private RestaurantCouponService $service;
    private Authorization $authorization;
    public function __construct(){ $this->service=new RestaurantCouponService(); $this->authorization=new Authorization(); }
    private function allow(string $permission,string $redirect='/restaurant/coupons'): bool { try{$this->authorization->authorize($permission);return true;}catch(RuntimeException $e){flash('error',$e->getMessage());redirect($redirect);return false;} }
    public function index(): void { if(!$this->allow('restaurant.coupons.view'))return; $tid=(int)Auth::tenantId(); $this->view('Restaurant::RestaurantCoupons.index',['title'=>'Restaurant Coupons','coupons'=>$this->service->all($tid),'restaurants'=>$this->service->restaurants($tid)]); }
    public function create(): void { if(!$this->allow('restaurant.coupons.manage'))return; $tid=(int)Auth::tenantId(); $this->view('Restaurant::RestaurantCoupons.create',['title'=>'Create Coupon','restaurants'=>$this->service->restaurants($tid)]); }
    public function store(): void { if(!$this->allow('restaurant.coupons.manage'))return; try{$id=$this->service->create((int)Auth::tenantId(),$_POST,(int)Auth::id());flash('success','Coupon created.');redirect('/restaurant/coupons');}catch(Throwable $e){$tid=(int)Auth::tenantId();$this->view('Restaurant::RestaurantCoupons.create',['title'=>'Create Coupon','restaurants'=>$this->service->restaurants($tid),'error'=>$e->getMessage(),'old'=>$_POST]);} }
    public function edit(int $id): void { if(!$this->allow('restaurant.coupons.manage'))return; $tid=(int)Auth::tenantId();$c=$this->service->find($tid,$id);if(!$c)abort(404);$this->view('Restaurant::RestaurantCoupons.edit',['title'=>'Edit Coupon','record'=>$c,'restaurants'=>$this->service->restaurants($tid)]); }
    public function update(int $id): void { if(!$this->allow('restaurant.coupons.manage'))return; try{$this->service->update((int)Auth::tenantId(),$id,$_POST,(int)Auth::id());flash('success','Coupon updated.');redirect('/restaurant/coupons');}catch(Throwable $e){$tid=(int)Auth::tenantId();$c=$this->service->find($tid,$id)??$_POST;$this->view('Restaurant::RestaurantCoupons.edit',['title'=>'Edit Coupon','record'=>$c,'restaurants'=>$this->service->restaurants($tid),'error'=>$e->getMessage()]);} }
    public function destroy(int $id): void { if(!$this->allow('restaurant.coupons.manage'))return; try{$this->service->delete((int)Auth::tenantId(),$id,(int)Auth::id());flash('success','Coupon deleted.');}catch(Throwable $e){flash('error',$e->getMessage());}redirect('/restaurant/coupons'); }
}
