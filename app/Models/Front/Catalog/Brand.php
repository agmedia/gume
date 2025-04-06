<?php

namespace App\Models\Front\Catalog;

use App\Helpers\Helper;
use App\Helpers\ProductHelper;
use App\Helpers\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class Brand
 * @package App\Models\Front\Catalog
 */
class Brand extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'brands';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'id')->active()->hasStock();
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', 1);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeBasicData($query)
    {
        return $query->select('id', 'title', 'slug', 'url');
    }


    /**
     * @param array $request
     * @param int   $limit
     *
     * @return Builder
     */
    public function filter(array $request, int $limit = 20): Builder
    {
        $query = (new Brand())->newQuery();

        if ($request['search_brand']) {
            $query->active();

            $query = Query::searchByTitle($query, $request['search_brand']);

        } else {
            $query->active()->featured();

            if (! $request['group'] && $request['ids']) {
                $_ids = collect(explode(',', substr($request['ids'], 1, -1)))->unique();

                $query->whereHas('products', function ($query) use ($_ids) {
                    $query->active()->hasStock()->whereIn('id', $_ids);
                });
            }
        }

        $query->limit($limit)
              ->basicData()
              ->withCount('products')
              ->orderBy('title');

        return $query;
    }


    /**
     * @return Collection
     */
    public static function letters(): Collection
    {
        $letters = collect();
        $brands = Brand::active()->pluck('letter')->unique();

        foreach (Helper::abc() as $item) {
            if ($item == $brands->contains($item)) {
                $letters->push([
                    'value' => $item,
                    'active' => true
                ]);
            } else {
                $letters->push([
                    'value' => $item,
                    'active' => false
                ]);
            }
        }

        return $letters;
    }


    /**
     * @param int $id
     *
     * @return Collection
     */
    public function categories(int $id = 0): Collection
    {
        $query = (new Category())->newQuery();

        $query->active();

        if ( ! $id) {
            $query->topList()->select('id', 'group', 'title', 'slug')->whereHas('products', function ($query) {
                $query->where('brand_id', $this->id);
            });

        } else {
            $query->whereHas('products', function ($query) {
                $query->where('brand_id', $this->id);
            })->where('parent_id', $id);
        }

        return $query->withCount(['products as products_count' => function ($query) {
                         $query->where('brand_id', $this->id);
                     }])
                     ->sortByName()
                     ->get();
    }

    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    public static function getSelectList(string $key = 'id')
    {
        return Helper::resolveCache('brands')->remember('list' . $key, config('cache.life'), function () use ($key) {
            return Brand::query()->where('status', 1)->orderBy('sort_order')->pluck('title', $key)->toArray();
        });
    }


    /**
     * @param int $id
     *
     * @return Brand|null
     */
    public static function getById(int $id): Brand|null
    {
        return Helper::resolveCache('brand')->remember($id, config('cache.life'), function () use ($id) {
            return Brand::query()->find($id);
        });
    }


    /**
     * @param string $slug
     *
     * @return Brand|null
     */
    public static function getBySlug(string $slug): Brand|null
    {
        return Helper::resolveCache('brand')->remember($slug, config('cache.life'), function () use ($slug) {
            return Brand::query()->where('slug', $slug)->first();
        });
    }
}
