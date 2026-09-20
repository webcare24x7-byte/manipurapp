<?php
declare(strict_types=1);
namespace App\Modules\CommercialRental\Services;
use App\Modules\CommercialRental\Models\CommercialRentalProvider;
use RuntimeException;
final class CommercialRentalProviderService{
 private CommercialRentalProvider $m; public function __construct(){ $this->m=new CommercialRentalProvider(); }
 public function all(int $t):array{return $this->m->all($t);} public function find(int $t,int $id):?array{return $this->m->find($t,$id);} public function count(int $t):int{return $this->m->count($t);}
 public function create(int $t,array $i,int $u):int{$d=$this->validate($i);if($this->m->businessNameExists($t,$d['business_name']))throw new RuntimeException('A commercial rental business with this name already exists.');$d['business_slug']=$this->slug($t,$this->slugify($d['business_name']));return $this->m->create($t,$d,$u);}
 public function update(int $t,int $id,array $i,int $u):void{if(!$this->find($t,$id))throw new RuntimeException('Commercial rental provider not found.');$d=$this->validate($i);if($this->m->businessNameExists($t,$d['business_name'],$id))throw new RuntimeException('A commercial rental business with this name already exists.');$d['business_slug']=$this->slug($t,$this->slugify($d['business_name']),$id);$this->m->update($t,$id,$d,$u);}
 public function delete(int $t,int $id,int $u):void{if(!$this->find($t,$id))throw new RuntimeException('Commercial rental provider not found.');$this->m->softDelete($t,$id,$u);}
 private function validate(array $i):array{$d=[];foreach(['business_name','legal_name','phone','email','address','city','district','state','postal_code','description','service_areas'] as $k)$d[$k]=$this->str($i[$k]??'');$d['latitude']=$this->coord($i['latitude']??null,'latitude');$d['longitude']=$this->coord($i['longitude']??null,'longitude');$d['state']=$d['state']?:'Manipur';$d['status']=$this->str($i['status']??'Active')?:'Active';if(!$d['business_name'])throw new RuntimeException('Business name is required.');if(!in_array($d['status'],['Active','Inactive','Suspended'],true))throw new RuntimeException('Invalid status.');return $d;}
 private function coord(mixed $v,string $name):?string{$v=trim((string)$v);if($v==='')return null;if(!is_numeric($v))throw new RuntimeException('Invalid '.$name.'.');$n=(float)$v;if($name==='latitude'&&($n<-90||$n>90))throw new RuntimeException('Latitude must be between -90 and 90.');if($name==='longitude'&&($n<-180||$n>180))throw new RuntimeException('Longitude must be between -180 and 180.');return number_format($n,7,'.','');}
 private function slug(int $t,string $b,?int $ignore=null):string{$b=$b?:'commercial-rental-business';$c=$b;$n=2;while($this->m->slugExists($t,$c,$ignore))$c=$b.'-'.$n++;return $c;}
 private function slugify(string $v):string{$v=strtolower(trim($v));$v=preg_replace('/[^a-z0-9]+/','-',$v)??'';return trim($v,'-');} private function str(mixed $v):?string{$v=trim((string)$v);return $v===''?null:$v;}
}
