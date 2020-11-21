<?php
namespace App\Libs\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class StoreImage {

    public static function file($image, $imagePath, $thumbnail = true, $resize = false, $width = 0, $height = 0){
        try{
            $filename = $image->getClientOriginalName();
            $newName =  md5(microtime()) . '.' . pathinfo($filename, PATHINFO_EXTENSION);
            if($image->move(public_path($imagePath),  $newName)){
                if($thumbnail){
                    Image::make(public_path($imagePath) .  $newName)->resize(150, 150)->save(public_path($imagePath . 'thumb_'. $newName));
                }

                if($resize){
                    $img = Image::make(public_path($imagePath) .  $newName);
                    $img->resize($width, $height)->save(public_path($imagePath) .  $newName);
                }
                return $imagePath . $newName;
            }

            return "";

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return "";
        }
    }
}
?>
