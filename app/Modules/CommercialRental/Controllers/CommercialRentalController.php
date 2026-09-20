<?php
declare(strict_types=1);
namespace App\Modules\CommercialRental\Controllers;
use App\Core\Auth;use App\Core\Authorization;use App\Core\Controller;use RuntimeException;use Throwable;
use App\Modules\CommercialRental\Services\CommercialRentalDashboardService;
final class CommercialRentalController extends Controller{private CommercialRentalDashboardService$s;private Authorization$a;public function __construct(){$this->s=new CommercialRentalDashboardService();$this->a=new Authorization();}public function index():void{try{$this->a->authorize('commercial_rental.view');}catch(RuntimeException$e){flash('error',$e->getMessage());redirect('/dashboard');return;}$this->view('CommercialRental::CommercialRental.index',['title'=>'Commercial Vehicle Rental','summary'=>$this->s->summary((int)Auth::tenantId())]);}}
