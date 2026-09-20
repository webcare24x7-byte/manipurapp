<?php
declare(strict_types=1);
namespace App\Modules\CommercialRental\Services;
use App\Modules\CommercialRental\Models\CommercialRentalDashboard;
final class CommercialRentalDashboardService{private CommercialRentalDashboard$m;public function __construct(){$this->m=new CommercialRentalDashboard();}public function summary(int$t):array{return$this->m->summary($t);}}
