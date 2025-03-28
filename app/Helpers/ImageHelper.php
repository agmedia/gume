<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageHelper
{

    /**
     * @param string $image
     * @param string $title
     * @param int    $id
     *
     * @return string
     */
    public static function save(string $image, string $title, int $id): string
    {
        $time = Str::random(4);
        $img  = Image::make($image);
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

        return 'media/img/products/' . $path_jpg;
    }
}