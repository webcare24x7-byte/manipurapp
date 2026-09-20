<?php
declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use App\Modules\MemberApp\Models\MemberTourism;
use RuntimeException;

final class MemberTourismService
{
    public const TENANT_ID = 1;
    private MemberTourism $model;
    public function __construct(){ $this->model=new MemberTourism(); }

    public function districts():array{return $this->model->districts(self::TENANT_ID);}
    public function destinations(?string $q=null,?string $district=null):array{return $this->model->destinations(self::TENANT_ID,$q,$district);}
    public function destination(int $id):?array{return $this->model->destination(self::TENANT_ID,$id);}
    public function stays(?int $destinationId=null,?string $q=null,?string $district=null):array{return $this->model->stays(self::TENANT_ID,$destinationId,$q,$district);}
    public function stay(int $id):?array{return $this->model->stay(self::TENANT_ID,$id);}
    public function packages(?string $q=null,?string $district=null):array{return $this->model->packages(self::TENANT_ID,$q,$district);}
    public function package(int $id):?array{return $this->model->package(self::TENANT_ID,$id);}
    public function guides(?string $q=null,?string $district=null):array{return $this->model->guides(self::TENANT_ID,$q,$district);}
    public function guide(int $id):?array{return $this->model->guide(self::TENANT_ID,$id);}
    public function experiences(?int $destinationId=null,?string $q=null,?string $district=null):array{return $this->model->experiences(self::TENANT_ID,$destinationId,$q,$district);}
    public function experience(int $id):?array{return $this->model->experience(self::TENANT_ID,$id);}
    public function events(?string $q=null,?string $district=null):array{return $this->model->events(self::TENANT_ID,$q,$district);}
    public function event(int $id):?array{return $this->model->event(self::TENANT_ID,$id);}
    public function reviews(string $type,int $id):array{return $this->model->reviews(self::TENANT_ID,$type,$id);}
    public function review(int $userId,string $type,int $id,int $rating,string $title,string $text):int{
        if(!in_array($type,['DESTINATION','STAY','PACKAGE','GUIDE','EXPERIENCE','EVENT'],true)) throw new RuntimeException('Invalid review type.');
        if($rating<1||$rating>5) throw new RuntimeException('Rating must be between 1 and 5.');
        if(trim($text)==='') throw new RuntimeException('Please write your review.');
        return $this->model->createReview(self::TENANT_ID,$userId,$type,$id,$rating,trim($title),trim($text));
    }
    public function createTrip(int $userId,string $title,string $description,?string $start,?string $end):int{
        if(trim($title)==='') throw new RuntimeException('Give your trip a name.');
        return $this->model->createTrip(self::TENANT_ID,$userId,trim($title),trim($description),$start,$end);
    }
    public function tripPlans(int $userId):array{return $this->model->tripPlans(self::TENANT_ID,$userId);}
    public function trip(int $userId,int $id):?array{return $this->model->trip(self::TENANT_ID,$userId,$id);}
    public function tripPlannerSources(?string $district=null):array{return $this->model->tripPlannerSources(self::TENANT_ID,$district);}
    public function addTripItem(int $userId,int $planId,int $day,string $type,int $itemId,string $title,string $notes=''):void{
        if($day<1) throw new RuntimeException('Day must be at least 1.');
        if(!$this->model->addTripItem(self::TENANT_ID,$userId,$planId,$day,$type,$itemId,trim($title),trim($notes))) throw new RuntimeException('Unable to add this item to your trip plan.');
    }
    public function removeTripItem(int $userId,int $planId,int $itemId):void{
        if(!$this->model->removeTripItem(self::TENANT_ID,$userId,$planId,$itemId)) throw new RuntimeException('Unable to remove this trip item.');
    }
}
