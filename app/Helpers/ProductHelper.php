<?php

namespace App\Helpers;

use App\Models\Back\Catalog\Product\Product;
use App\Models\Back\Orders\OrderProduct;
use App\Models\Front\Catalog\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductHelper
{

    /**
     * @param Product       $product
     * @param Category|null $category
     * @param Category|null $subcategory
     *
     * @return string
     */
    public static function categoryString(Product $product, Category $category = null, Category $subcategory = null): string
    {
        $data        = static::resolveCategories($product, $category, $subcategory);
        $category    = $data['category'];
        $subcategory = $data['subcategory'];
        $catstring   = '';

        if ($category) {
            $catstring = '<span class="fs-xs ms-1"><a href="' . route('catalog.route', ['group' => Str::slug($category->group), 'cat' => $category->slug]) . '">' . $category->title . '</a> ';
        }

        if ($subcategory) {
            $substring = '</span><span class="fs-xs ms-1"><a href="' . route('catalog.route',
                    ['group' => Str::slug($category->group), 'cat' => $category->slug, 'subcat' => $subcategory->slug]) . '">' . $subcategory->title . '</a></span>';

            return $catstring . $substring;
        }

        return $catstring;
    }


    /**
     * @param Product       $product
     * @param Category|null $category
     * @param Category|null $subcategory
     *
     * @return string
     */
    public static function url(Product $product, Category $category = null, Category $subcategory = null): string
    {
        $data        = static::resolveCategories($product, $category, $subcategory);
        $category    = $data['category'];
        $subcategory = $data['subcategory'];

        if ($subcategory) {
            return Str::slug($category->group) . '/' . $category->slug . '/' . $subcategory->slug . '/' . $product->slug;
        }

        if ($category) {
            return Str::slug($category->group) . '/' . $category->slug . '/' . $product->slug;
        }

        return '/';
    }


    /**
     * @param Product       $product
     * @param Category|null $category
     * @param Category|null $subcategory
     *
     * @return array
     */
    public static function resolveCategories(Product $product, Category $category = null, Category $subcategory = null): array
    {
        if ( ! $category) {
            $category = $product->category();
        }

        if ( ! $subcategory) {
            $psub = $product->subcategory();

            if ($psub) {
                foreach ($category->subcategories()->get() as $sub) {
                    if ($sub->id == $psub->id) {
                        $subcategory = $psub;
                    }
                }
            }
        }

        return [
            'category'    => $category,
            'subcategory' => $subcategory
        ];
    }


    /**
     * @param Builder $query
     * @param array   $request
     *
     * @return Builder
     */
    public static function queryCategories(Builder $query, array $request): Builder
    {
        $query->whereHas('categories', function ($query) use ($request) {
            if ($request['group'] && ! $request['cat'] && ! $request['subcat']) {
                $query->where('group', $request['group']);
            }

            if ($request['cat'] && ! $request['subcat']) {
                $query->where('category_id', $request['cat']);
            }

            if ($request['subcat']) {
                $query->where('category_id', $request['subcat']);
            }
        });

        return $query;
    }


    /**
     * @param string $path
     *
     * @return string
     */
    public static function getCleanImageTitle(string $path): string
    {
        $from   = strrpos($path, '/') + 1;
        $length = strrpos($path, '-') - $from;

        return substr($path, $from, $length);
    }


    /**
     * @param string $path
     *
     * @return string
     */
    public static function getFullImageTitle(string $path): string
    {
        $from   = strrpos($path, '/') + 1;
        $length = strrpos($path, '.') - $from;

        return substr($path, $from, $length);
    }


    /**
     * @param string $title
     *
     * @return string
     */
    public static function setFullImageTitle(string $title): string
    {
        return $title . '-' . Str::random(4);
    }


    /**
     * @param int|string $order_id
     *
     * @return bool
     */
    public static function makeAvailable($order_id): bool
    {
        $ops = OrderProduct::query()->where('order_id', $order_id)->get();

        foreach ($ops as $op) {
            Product::query()->where('id', $op->product_id)->increment('quantity', $op->quantity);
        }

        return true;
    }

    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    /**
     * @return Collection
     */
    public static function getSezoneList(): Collection
    {
        return Cache::remember('products.sezone', config('cache.life'), function () {
            return collect(config('settings.sezone'));
        });
    }


    /**
     * @return Collection
     */
    public static function getAmperiList(): Collection
    {
        return Cache::remember('products.ampere', config('cache.life'), function () {
            return collect(config('settings.ampere'));
        });
    }


    /**
     * @return Collection
     */
    public static function getSirineList($data): Collection
    {
        return Cache::remember(self::getCacheHash($data, 'sirine'), config('cache.life'), function () use ($data) {
            $products = Product::query();

            if ($data->category && ! $data->subcategory) {
                $products->whereHas('categories', function ($query) use ($data) {
                    $query->where('category_id', $data->category->id);
                });
            }

            if ($data->subcategory) {
                $products->whereHas('subcategories', function ($query) use ($data) {
                    $query->where('category_id', $data->subcategory->id);
                });
            }

            return $products->groupBy('sirina')->orderBy('sirina')->pluck('sirina');
        });
    }


    /**
     * @return Collection
     */
    public static function getVisineList($data): Collection
    {
        return Cache::remember(self::getCacheHash($data, 'visine'), config('cache.life'), function () use ($data) {
            $products = Product::query();

            if ($data->category && ! $data->subcategory) {
                $products->whereHas('categories', function ($query) use ($data) {
                    $query->where('category_id', $data->category->id);
                });
            }

            if ($data->subcategory) {
                $products->whereHas('subcategories', function ($query) use ($data) {
                    $query->where('category_id', $data->subcategory->id);
                });
            }

            return $products->groupBy('visina')->orderBy('visina')->pluck('visina');
        });
    }


    /**
     * @return Collection
     */
    public static function getPromjeriList($data): Collection
    {
        return Cache::remember(self::getCacheHash($data, 'promjeri'), config('cache.life'), function () use ($data) {
            $products = Product::query();

            if ($data->category && ! $data->subcategory) {
                $products->whereHas('categories', function ($query) use ($data) {
                    $query->where('category_id', $data->category->id);
                });
            }

            if ($data->subcategory) {
                $products->whereHas('subcategories', function ($query) use ($data) {
                    $query->where('category_id', $data->subcategory->id);
                });
            }

            return $products->groupBy('promjer')->orderBy('promjer')->pluck('promjer');
        });
    }


    /**
     * @return Collection
     */
    public static function getSortingList(): Collection
    {
        return Cache::remember('products.sort', config('cache.life'), function () {
            return collect(config('settings.sorting_list'))->sortBy('sort_order');
        });
    }


    private static function getCacheHash($data, $target)
    {
        return hash('crc32', 'products.' . $target . (isset($data->group) ? $data->group : '') .
                             ($data->category ? $data->category->id : 0) .
                             ($data->subcategory ? $data->subcategory->id : 0)
        );
    }
}
