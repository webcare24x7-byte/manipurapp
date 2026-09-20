<?php
declare(strict_types=1);
namespace App\Modules\CommercialRental\Services;
final class HaversineService{public function kilometers(float$lat1,float$lng1,float$lat2,float$lng2):float{$r=6371.0088;$p=pi()/180;$a=sin(($lat2-$lat1)*$p/2)**2+cos($lat1*$p)*cos($lat2*$p)*sin(($lng2-$lng1)*$p/2)**2;return 2*$r*asin(min(1,sqrt($a)));}}
