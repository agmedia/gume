<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use RuntimeException;
use Throwable;

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
        $img  = Image::make(static::download($image));
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


    /**
     * @param string $url
     *
     * @return string
     */
    private static function download(string $url): string
    {
        try {
            $response = Http::timeout((int) config('services.intercars.asset_timeout', config('services.intercars.timeout', 120)))
                            ->retry(
                                (int) config('services.intercars.asset_retries', 2),
                                (int) config('services.intercars.asset_retry_sleep', 1000)
                            )
                            ->accept('image/*')
                            ->get($url)
                            ->throw();
        } catch (Throwable $e) {
            throw new RuntimeException('Image download failed for ' . $url . ': ' . $e->getMessage(), 0, $e);
        }

        $body = $response->body();

        if ($body === '') {
            throw new RuntimeException('Image download failed for ' . $url . ': empty response body.');
        }

        return $body;
    }
}
