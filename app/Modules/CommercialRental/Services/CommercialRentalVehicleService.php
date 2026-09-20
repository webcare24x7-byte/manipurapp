<?php
declare(strict_types=1);
namespace App\Modules\CommercialRental\Services;
use App\Modules\CommercialRental\Models\CommercialRentalVehicle;
use RuntimeException;

final class CommercialRentalVehicleService
{
    private CommercialRentalVehicle $m;
    public function __construct(){ $this->m=new CommercialRentalVehicle(); }
    public function all(int $t):array{return $this->m->all($t);}
    public function find(int $t,int $id):?array{return $this->m->find($t,$id);}
    public function count(int $t):int{return $this->m->count($t);}
    public function create(int $t,array $i,int $u):int{
        $d=$this->v($i);
        if($this->m->registrationExists($t,$d['registration_no'])) throw new RuntimeException('Registration number already exists.');
        return $this->m->create($t,$d,$u);
    }
    public function update(int $t,int $id,array $i,int $u):void{
        $d=$this->v($i);
        if($this->m->registrationExists($t,$d['registration_no'],$id)) throw new RuntimeException('Registration number already exists.');
        $this->m->update($t,$id,$d,$u);
    }
    public function delete(int $t,int $id,int $u):void{$this->m->delete($t,$id,$u);}
    private function v(array $i):array{
        $name=trim((string)($i['name']??''));
        if(!$name) throw new RuntimeException('Vehicle/equipment name is required.');
        $rt=(string)($i['rate_type']??'CUSTOM_QUOTE');
        if(!in_array($rt,['FIXED','HOURLY','DAILY','PER_TRIP','CUSTOM_QUOTE'],true)) throw new RuntimeException('Invalid rate type.');
        $st=(string)($i['status']??'Active'); $av=(string)($i['availability']??'Available');
        if(!in_array($st,['Active','Inactive','Maintenance'],true)||!in_array($av,['Available','Booked','Unavailable'],true)) throw new RuntimeException('Invalid status or availability.');
        $ownership=(string)($i['ownership_type']??'OWNER');
        if(!in_array($ownership,['OWNER','LEASED','PARTNER','OTHER'],true)) throw new RuntimeException('Invalid ownership type.');
        $condition=(string)($i['condition_status']??'GOOD');
        if(!in_array($condition,['NEW','GOOD','FAIR','NEEDS_SERVICE'],true)) throw new RuntimeException('Invalid condition.');
        $year=($i['model_year']??'')===''?null:(int)$i['model_year'];
        if($year!==null && ($year<1900 || $year>date('Y')+1)) throw new RuntimeException('Invalid model year.');
        $seats=($i['seating_capacity']??'')===''?null:(int)$i['seating_capacity'];
        if($seats!==null && $seats<0) throw new RuntimeException('Invalid seating capacity.');
        return [
            'provider_id'=>(int)($i['provider_id']??0),
            'category_id'=>($i['category_id']??'')===''?null:(int)$i['category_id'],
            'name'=>$name,'make'=>$this->s($i['make']??''),'model'=>$this->s($i['model']??''),'model_year'=>$year,
            'fuel_type'=>$this->s($i['fuel_type']??''),'ownership_type'=>$ownership,'engine_power'=>$this->s($i['engine_power']??''),
            'body_type'=>$this->s($i['body_type']??''),'seating_capacity'=>$seats,'condition_status'=>$condition,
            'registration_no'=>$this->s($i['registration_no']??''),'capacity'=>$this->s($i['capacity']??''),
            'operator_included'=>!empty($i['operator_included'])?1:0,'operator_name'=>$this->s($i['operator_name']??''),
            'operator_phone'=>$this->s($i['operator_phone']??''),'operator_experience'=>$this->s($i['operator_experience']??''),
            'rate_type'=>$rt,'rate'=>max(0,(float)($i['rate']??0)),'minimum_rental'=>max(0,(float)($i['minimum_rental']??0)),
            'description'=>$this->s($i['description']??''),'service_notes'=>$this->s($i['service_notes']??''),
            'photo_path'=>$this->s($i['photo_path']??''),'status'=>$st,'availability'=>$av
        ];
    }
    private function s(mixed $v):?string{$v=trim((string)$v);return $v===''?null:$v;}
}
