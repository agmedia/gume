<?php


namespace App\Helpers;
use App\Models\Back\Catalog\Category;
use App\Models\Back\Marketing\Action;
use App\Models\Back\Settings\Settings;
use App\Models\Back\Widget\WidgetGroup;
use App\Models\Front\Blog;
use App\Models\Front\Catalog\Brand;
use App\Models\Front\Catalog\Product;
use App\Models\Front\Catalog\Publisher;
use Darryldecode\Cart\CartCondition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Cache\TaggableStore;
use phpDocumentor\Reflection\Types\False_;

class Helper
{

    /**
     * @param float $price
     * @param int   $discount
     *
     * @return float|int
     */
    public static function calculateDiscountPrice(float $price, int $discount, string $type)
    {
        if ($type == 'F') {
            return $price - $discount;
        }

        return $price - ($price * ($discount / 100));
    }


    /**
     * @param $list_price
     * @param $seling_price
     *
     * @return float|int
     */
    public static function calculateDiscount($list_price, $seling_price, string $type = 'P')
    {
        if (is_string($list_price)) {
            $list_price = str_replace('.', '', $list_price);
            $list_price = str_replace(',', '.', $list_price);
        }
        if (is_string($seling_price)) {
            $seling_price = str_replace('.', '', $seling_price);
            $seling_price = str_replace(',', '.', $seling_price);
        }

        if ($type == 'F') {
            return $list_price - $seling_price;
        }

        return (($list_price - $seling_price) / $list_price) * 100;
    }


    /**
     * @param float|int $gross_price
     * @param int       $tax_rate
     *
     * @return float
     */
    public static function calculateTax(float|int|string $gross_price, int|string $tax_rate): float
    {
        $nett = floatval($gross_price) / ((intval($tax_rate) / 100) + 1);

        return floatval($gross_price - $nett);
    }


    /**
     * @return string[]
     */
    public static function abc()
    {
        return ['A', 'B', 'C', 'Ć', 'Č', 'D', 'Đ', 'Dž', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'Lj', 'M', 'N', 'Nj', 'O', 'P', 'R', 'S', 'Š', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ž'];
    }


    /**
     * @param       $items
     * @param int   $perPage
     * @param null  $page
     * @param array $options
     *
     * @return LengthAwarePaginator
     */
    public static function paginateColl($items, $perPage = 20, $page = null, $options = []): LengthAwarePaginator
    {
        $page  = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }


    /**
     * @param string $description
     *
     * @return false|string
     */
    public static function setDescription(string $description)
    {
        if ($description == '') {
            return '';
        }

        $ids = Cache::remember('wg_ids', config('cache.life'), function () use ($description) {
            $iterator = substr_count($description, '++');
            $offset   = 0;
            $ids      = [];

            for ($i = 0; $i < $iterator / 2; $i++) {
                $from  = strpos($description, '++', $offset) + 2;
                $to    = strpos($description, '++', $from + 2);
                $ids[] = substr($description, $from, $to - $from);

                $offset = $to + 2;
            }

            return $ids;
        });

        $wgs = Cache::remember('wgs', config('cache.life'), function () use ($ids) {
            return WidgetGroup::whereIn('id', $ids)->orWhereIn('slug', $ids)->where('status', 1)->with('widgets')->get();
        });

        foreach ($ids as $id) {
            $description = Cache::remember('wg.' . $id, config('cache.life'), function () use ($wgs, $description, $id) {
                return static::resolveDescription($wgs, $description, $id);
            });
        }

        return $description;
    }


    /**
     * @param Collection $wgs
     * @param string     $description
     * @param string     $id
     *
     * @return string
     */
    private static function resolveDescription(Collection $wgs, string $description, string $id): string
    {
        $wg = $wgs->where('id', $id)->first();

        if ( ! $wg) {
            $wg = $wgs->where('slug', $id)->first();
        }

        $widgets = [];

        if ($wg->template == 'product_carousel' || $wg->template == 'page_carousel') {
            $widget = $wg->widgets()->first();
            $data   = unserialize($widget->data);

            if (static::isDescriptionTarget($data, 'product')) {
                $items     = static::products($data)->get();
                $tablename = 'product';
            }

            if (static::isDescriptionTarget($data, 'blog')) {
                $items     = static::blogs($data)->get();
                $tablename = 'blog';
            }

            if (static::isDescriptionTarget($data, 'category')) {
                $items     = static::category($data)->get();
                $tablename = 'category';
            }

            if (static::isDescriptionTarget($data, 'brand')) {
                $items     = static::brand($data)->get();
                $tablename = 'brand';
            }

            if (static::isDescriptionTarget($data, 'reviews')) {
                $items     = static::reviews($data)->get();
                $tablename = 'reviews';
            }

            $widgets = [
                'title'      => $widget->title,
                'subtitle'   => $widget->subtitle,
                'url'        => $widget->url,
                'tablename'  => $tablename,
                'css'        => $data['css'],
                'container'  => (isset($data['container']) && $data['container'] == 'on') ? 1 : null,
                'background' => (isset($data['background']) && $data['background'] == 'on') ? 1 : null,
                'items'      => $items
            ];

        } else {
            foreach ($wg->widgets()->orderBy('sort_order')->get() as $widget) {
                $data = unserialize($widget->data);

                $widgets[] = [
                    'title'    => $widget->title,
                    'subtitle' => $widget->subtitle,
                    'color'    => $widget->badge,
                    'url'      => $widget->url,
                    'image'    => $widget->image,
                    'width'    => $widget->width,
                    'right'    => (isset($data['right']) && $data['right'] == 'on') ? 1 : null,
                ];
            }
        }

        return str_replace(
            '++' . $id . '++',
            view('front.layouts.widget.widget_' . $wg->template, ['data' => $widgets]),
            $description
        );
    }


    /**
     * @param array  $data
     * @param string $target
     *
     * @return bool
     */
    public static function isDescriptionTarget(array $data, string $target): bool
    {
        if (isset($data['target']) && $data['target'] == $target) {
            return true;
        }
        if (isset($data['group']) && $data['group'] == $target) {
            return true;
        }

        return false;
    }


    /**
     * @param string $text
     *
     * @return string
     */
    public static function resolveFirstLetter(string $text): string
    {
        $letter = substr($text, 0, 1);

        if (in_array(substr($text, 0, 2), ['Nj', 'Lj', 'Š', 'Č', 'Ć', 'Ž', 'Đ'])) {
            $letter = substr($text, 0, 2);
        }

        if (in_array(substr($text, 0, 3), ['Dž', 'Đ'])) {
            $letter = substr($text, 0, 3);
        }

        return $letter;
    }


    /**
     * @param array $data
     *
     * @return Builder
     */
    private static function products(array $data): Builder
    {
        $prods = (new Product())->newQuery();

        $prods->active()->available();

        if (isset($data['popular']) && $data['popular'] == 'on') {
            $prods->popular();
        }

        $prods->distinct()->last();

        if (isset($data['list']) && $data['list']) {
            $prods->whereIn('id', $data['list']);
        }

        return $prods->with('brand');
    }


    /**
     * @param array $data
     *
     * @return Builder
     */
    private static function blogs(array $data): Builder
    {
        $blogs = (new Blog())->newQuery();

        $blogs->active();

        if (isset($data['new']) && $data['new'] == 'on') {
            $blogs->last();
        }

        if (isset($data['popular']) && $data['popular'] == 'on') {
            $blogs->popular();
        }

        if (isset($data['list']) && $data['list']) {
            $blogs->whereIn('id', $data['list']);
        }

        return $blogs;
    }


    /**
     * @param array $data
     *
     * @return Builder
     */
    private static function category(array $data): Builder
    {
        $category = (new Category())->newQuery();

        $category->active();

        if (isset($data['new']) && $data['new'] == 'on') {
            $category->latest();
        }

        if (isset($data['popular']) && $data['popular'] == 'on') {
            $category->latest();
        }

        if (isset($data['list']) && $data['list']) {
            $category->whereIn('id', $data['list']);
        }

        return $category;
    }


    /**
     * @param array $data
     *
     * @return Builder
     */
    private static function brand(array $data): Builder
    {
        $brand = (new Brand())->newQuery();

        $brand->active();

        if (isset($data['new']) && $data['new'] == 'on') {
            $brand->orderByDesc('created_at');
        }

        if (isset($data['popular']) && $data['popular'] == 'on') {
            $brand->featured();
        }

        if (isset($data['list']) && $data['list']) {
            $brand->whereIn('id', $data['list']);
        }

        return $brand;
    }


    /**
     * @param string $tag
     *
     * @return \Illuminate\Cache\TaggedCache|mixed|object
     */
    public static function resolveCache(string $tag): ?object
    {
        if (app()->environment(['local', 'testing']) || ! Cache::getStore() instanceof TaggableStore) {
            return Cache::getFacadeRoot();
        }

        return Cache::tags([$tag]);
    }


    /**
     * @param string $tag
     * @param string $key
     *
     * @return object|bool|mixed|null
     */
    public static function flushCache(string $tag, string $key)
    {
        if (app()->environment(['local', 'testing']) || ! Cache::getStore() instanceof TaggableStore) {
            return Cache::forget($key);
        }

        return Cache::tags([$tag])->forget($key);
    }


    /**
     * @param bool $slug
     *
     * @return string
     */
    public static function categoryGroupPath(bool $slug = false): string
    {
        if ($slug) {
            return Str::slug(config('settings.group_path'));
        }

        return config('settings.group_path');
    }


    /**
     * @param $date
     *
     * @return bool
     */
    public static function isDateBetween($date = null): bool
    {
        if (config('settings.special_action.start')) {
            $now   = $date ?: Carbon::now();
            $start = Carbon::createFromFormat('d/m/Y H:i:s', config('settings.special_action.start'));
            $end   = Carbon::createFromFormat('d/m/Y H:i:s', config('settings.special_action.end'));

            if ($now->isBetween($start, $end)) {
                return true;
            }
        }

        return false;
    }

}
