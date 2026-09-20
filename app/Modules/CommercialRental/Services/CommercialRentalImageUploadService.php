<?php
declare(strict_types=1);
namespace App\Modules\CommercialRental\Services;
use RuntimeException;

final class CommercialRentalImageUploadService
{
    private const MAX_BYTES=5*1024*1024;
    private const ALLOWED_MIMES=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    public function store(array $file):string{
        $error=(int)($file['error']??UPLOAD_ERR_NO_FILE);
        if($error!==UPLOAD_ERR_OK) throw new RuntimeException($this->uploadErrorMessage($error));
        $tmp=(string)($file['tmp_name']??''); $size=(int)($file['size']??0);
        if($tmp===''||!is_uploaded_file($tmp)) throw new RuntimeException('Invalid uploaded image.');
        if($size<=0||$size>self::MAX_BYTES) throw new RuntimeException('Image must be between 1 byte and 5 MB.');
        $finfo=new \finfo(FILEINFO_MIME_TYPE); $mime=$finfo->file($tmp);
        if(!is_string($mime)||!isset(self::ALLOWED_MIMES[$mime])) throw new RuntimeException('Only JPG, PNG and WebP images are allowed.');
        if(@getimagesize($tmp)===false) throw new RuntimeException('The uploaded file is not a valid image.');
        $root=dirname(__DIR__,4); $relative='uploads/commercial-rental/vehicles'; $dir=$root.'/public/'.$relative;
        if(!is_dir($dir)&&!mkdir($dir,0755,true)&&!is_dir($dir)) throw new RuntimeException('Unable to create the commercial rental image upload directory.');
        $filename=bin2hex(random_bytes(16)).'.'.self::ALLOWED_MIMES[$mime];
        if(!move_uploaded_file($tmp,$dir.'/'.$filename)) throw new RuntimeException('Unable to save the uploaded image.');
        return $relative.'/'.$filename;
    }
    public function delete(?string $path):void{
        $path=trim((string)$path); if($path==='')return; $root=dirname(__DIR__,4); $public=realpath($root.'/public'); if($public===false)return;
        $real=realpath($root.'/public/'.ltrim($path,'/')); if($real===false||!is_file($real))return;
        $prefix=rtrim($public,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR; if(!str_starts_with($real,$prefix))return; @unlink($real);
    }
    private function uploadErrorMessage(int $e):string{return match($e){UPLOAD_ERR_INI_SIZE,UPLOAD_ERR_FORM_SIZE=>'The uploaded image is too large.',UPLOAD_ERR_PARTIAL=>'The image upload was interrupted. Please try again.',UPLOAD_ERR_NO_FILE=>'Please select an image.',UPLOAD_ERR_NO_TMP_DIR=>'The server is missing its temporary upload directory.',UPLOAD_ERR_CANT_WRITE=>'The server could not write the uploaded image.',UPLOAD_ERR_EXTENSION=>'The image upload was blocked by a server extension.',default=>'The image upload failed.'};}
}
