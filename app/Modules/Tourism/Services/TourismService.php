<?php
declare(strict_types=1);
namespace App\Modules\Tourism\Services;
use App\Modules\Tourism\Models\Tourism;
final class TourismService {
    private Tourism $m;
    public function __construct(){ $this->m=new Tourism(); }
    public function dashboard(int $t):array{return $this->m->dashboard($t);}
    public function locations():array{return ['state'=>['Manipur'],'districts'=>['Bishnupur','Chandel','Churachandpur','Imphal East','Imphal West','Jiribam','Kakching','Kamjong','Kangpokpi','Noney','Pherzawl','Senapati','Tamenglong','Tengnoupal','Thoubal','Ukhrul'],'cities'=>['Imphal','Bishnupur','Chandel','Churachandpur','Jiribam','Kakching','Kamjong','Kangpokpi','Noney','Pherzawl','Senapati','Tamenglong','Moreh','Thoubal','Ukhrul']];}
}
