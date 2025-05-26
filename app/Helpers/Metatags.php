<?php

namespace App\Helpers;

use App\Models\Front\Catalog\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class Metatags
{

    public static function noFollow()
    {
        return [
            'name' => 'robots',
            'content' => 'noindex,nofollow'
        ];
    }


    public static function empty()
    {
        return [
            'name' => '',
            'content' => ''
        ];
    }

    /**
     * @param Product|null    $prod
     * @param Collection|null $reviews
     *
     * @return array
     */
    public static function productSchema(Product $prod = null, Collection $reviews = null): array
    {
        $response = [];

        if ($prod) {
            $price = ($prod->special()) ? $prod->special() : number_format($prod->price, 2, '.', '');

            $response = [
                '@context' => 'https://schema.org/',
                '@type' => 'Product',
                'sku' => $prod->sku,
                'description' => strip_tags(html_entity_decode($prod->description)),
                'name' => $prod->name,
                'itemCondition' => 'https://schema.org/NewCondition',
                'image' => [
                    '@type' => 'ImageObject',
                    'url' => asset($prod->image),
                    'name' => isset($prod->alt['title']) ? $prod->alt['title'] : '',
                    'width' => 500,
                    'height' => 500,
                ],
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $prod->brand ? $prod->brand->title : '',
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => 'EUR',
                    'price' => (string) $price,
                    'priceValidUntil' => now()->endOfYear()->format('Y-m-d'),
                    'sku' => $prod->sku,
                    'url' => url($prod->url),
                    'availability' => ($prod->quantity) ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
                ],
            ];


        }

        return $response;
    }
}
