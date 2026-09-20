<?php
declare(strict_types=1);
namespace App\Modules\Tourism\Controllers;
use App\Core\Auth;use App\Core\Authorization;use App\Core\Controller;use App\Modules\Tourism\Services\TourismService;use RuntimeException;
final class TourismController extends Controller{private TourismService $s;private Authorization $a;public function __construct(){$this->s=new TourismService();$this->a=new Authorization();}public function index():void{try{$this->a->authorize('tourism.view');}catch(RuntimeException $e){flash('error',$e->getMessage());redirect('/dashboard');return;}$this->view('Tourism::Tourism.index',['title'=>'Tourism','summary'=>$this->s->dashboard((int)Auth::tenantId())]);}}
