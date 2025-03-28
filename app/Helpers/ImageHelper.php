<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageHelper
{

    public static function save($image, $title, $id)
    {
        $path = Storage::disk('local')->put('temp/images/' . $title, $image);

        Log::info($path);

        $time = Str::random(4);
        $img  = Image::make(Storage::disk('local')->get('temp/images/' . $title));
        $path = $id . '/' . Str::slug($title) . '-' . $time . '.';

        $path_jpg = $path . 'jpg';
        Storage::disk('products')->put($path_jpg, $img->encode('jpg'));

        $path_webp = $path . 'webp';
        Storage::disk('products')->put($path_webp, $img->encode('webp'));

        // Thumb creation
        $path_thumb = $id . '/' . Str::slug($title) . '-' . $time . '-thumb.';

        $img = $img->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        })->resizeCanvas(250, null);

        $path_webp_thumb = $path_thumb . 'webp';
        Storage::disk('products')->put($path_webp_thumb, $img->encode('webp', 80));

        return $path_jpg;
    }
}