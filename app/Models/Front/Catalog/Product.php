<?php

namespace App\Models\Front\Catalog;

use App\Helpers\Currency;
use App\Helpers\Special;
use App\Models\Back\Catalog\Product\ProductAction;
use App\Models\Back\Catalog\Product\ProductAttribute;
use App\Models\Back\Settings\Settings;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class Product extends Model
{

    /**
     * @var string
     */
    protected $table = 'products';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var string[]
     */
    protected $appends = [
        'main_price',
        'main_price_text',
        'main_special',
        'main_special_text',
        'image',
        'thumb',
        'url'
    ];

    /**
     * @var
     */
    protected $eur;


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
     * @return Collection|string
     */
    public function getMainPriceAttribute()
    {
        return Currency::main($this->price);
    }


    /**
     * @return Collection|string
     */
    public function getMainPriceTextAttribute()
    {
        return Currency::main($this->price, true);
    }


    /**
     * @return Collection|string
     */
    public function getMainSpecialAttribute()
    {
        return Currency::main($this->special());
    }


    /**
     * @return Collection|string
     */
    public function getMainSpecialTextAttribute()
    {
        return Currency::main($this->special(), true);
    }


    /**
     * @param $value
     *
     * @return array|string|string[]
     */
    public function getImageAttribute($value)
    {
        return config('settings.images_domain') . str_replace('.jpg', '.webp', $value);
    }


    /**
     * @param $value
     *
     * @return array|string|string[]
     */
    public function getThumbAttribute($value)
    {
        return str_replace('.webp', '-thumb.webp', $this->image);
    }


    /**
     * @param $value
     *
     * @return array|string|string[]
     */
    public function getUrlAttribute($value)
    {
        return url($value);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id')->where('published', 1)->orderBy('sort_order');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action()
    {
        return $this->belongsTo(ProductAction::class, 'action_id', 'id')
                    ->where('status', 1);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function brand()
    {
        return $this->hasOne(Brand::class, 'id', 'author_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function categories()
    {
        return $this->hasManyThrough(Category::class, CategoryProducts::class, 'product_id', 'id', 'id', 'category_id');
    }


    /**
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasOneThrough|\Illuminate\Database\Query\Builder|mixed|object|null
     */
    public function category()
    {
        return $this->hasOneThrough(Category::class, CategoryProducts::class, 'product_id', 'id', 'id', 'category_id')
            ->where('parent_id', 0)
            ->first();
    }


    /**
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasOneThrough|\Illuminate\Database\Query\Builder|mixed|object|null
     */
    public function subcategory()
    {
        return $this->hasOneThrough(Category::class, CategoryProducts::class, 'product_id', 'id', 'id', 'category_id')
            ->where('parent_id', '!=', 0)
            ->first();
    }


    /**
     * @return Relation
     */
    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id')->with('attribute');
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeOnAction($query)
    {
        $actions = ProductAction::active()->pluck('product_id');

        if ($actions->count() < 8) {
            $count = 8 - $actions->count();

            for ($i = 0; $i < $count; $i++) {
                $product = Product::whereNotIn('id', $actions)->inRandomOrder()->limit(1)->pluck('id');
                $actions->push($product[0]);
            }
        }

        return $query->whereIn('id', $actions)->with('action');
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1)->where('price', '!=', 0);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 0);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeHasStock(Builder $query): Builder
    {
        return $query->where('quantity', '!=', 0);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeLast(Builder $query, $count = 12): Builder
    {
        return $query->where('status', 1)->orderBy('created_at', 'desc')->limit($count);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeCreated($query, $count = 9)
    {
        return $query->where('status', 1)->orderBy('created_at', 'desc')->limit($count);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('quantity', '!=', 0);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopePopular(Builder $query, $count = 12): Builder
    {
        return $query->where('status', 1)->orderBy('viewed', 'desc')->limit($count);
    }


    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeTopPonuda(Builder $query, $count = 12): Builder
    {
        return $query->where('status', 1)->where('topponuda', 1)->orderBy('updated_at', 'desc')->limit($count);
    }


    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeBasicData(Builder $query): Builder
    {
        return $query->select('id', 'name', 'url', 'image', 'price', 'special', 'author_id');
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @param Request         $request
     * @param Collection|null $ids
     *
     * @return Builder
     */
    public function filter(Request $request, Collection $ids = null): Builder
    {
        $query = $this->newQuery();

        $query->active()->hasStock();

        if ($ids && $ids->count() && ! \request()->has('pojam')) {
            $query->whereIn('id', $ids->unique());
        }

        if ($request->has('ids') && $request->input('ids') != '') {
            $_ids = explode(',', substr($request->input('ids'), 1, -1));
            $query->whereIn('id', collect($_ids)->unique());
        }

        if ($request->has('group')) {
            // Akcije
            if ($request->input('group') == 'snizenja') {
                $query->where('special', '!=', '')
                    ->where(function ($query) {
                        $query->whereDate('special_from', '<=', now())->orWhereNull('special_from');
                    })
                    ->where(function ($query) {
                        $query->whereDate('special_to', '>=', now())->orWhereNull('special_to');
                    });
            } else {
                // Kategorija...
                $group = $request->input('group');

                $query->whereHas('categories', function ($query) use ($request, $group) {
                    $query->where('group', $group);
                });
            }
        }

        if ($request->has('cat')) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('category_id', $request->input('cat'));
            });
        }

        if ($request->has('subcat')) {
            $query->whereHas('categories', function ($query) use ($request) {
                $query->where('category_id', $request->input('subcat'));
            });
        }

        if ($request->has('autor')) {
            $auts = [];

            foreach ($request->input('autor') as $key => $item) {
                if (isset($item->id)) {
                    array_push($auts, $item->id);
                } else {
                    array_push($auts, $key);
                }
            }

            $query->whereIn('author_id', $auts);
        }

        if ($request->has('nakladnik')) {
            $pubs = [];

            foreach ($request->input('nakladnik') as $key => $item) {
                if (isset($item->id)) {
                    array_push($pubs, $item->id);
                } else {
                    array_push($pubs, $key);
                }
            }

            $query->whereIn('publisher_id', $pubs);
        }

        if ($request->has('start')) {
            $query->where(function ($query) use ($request) {
                $query->where('year', '>=', $request->input('start'))->orWhereNull('year');
            });
        }

        if ($request->has('end')) {
            $query->where(function ($query) use ($request) {
                $query->where('year', '<=', $request->input('end'))->orWhereNull('year');
            });
        }

        if ($request->has('sort')) {
            $sort = $request->input('sort');

            if ($sort == 'novi') {
                $query->orderBy('created_at', 'desc');
            }

            if ($sort == 'price_up') {
                $query->orderBy('price');
            }

            if ($sort == 'price_down') {
                $query->orderBy('price', 'desc');
            }

            if ($sort == 'naziv_up') {
                $query->orderBy('name');
            }

            if ($sort == 'naziv_down') {
                $query->orderBy('name', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }


    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/
    // Static functions

    /**
     * Return the list usually for
     * select or autocomplete html element.
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function list()
    {
        $query = (new self())->newQuery();

        return $query->where('status', 1)->select('id', 'name')->get();
    }


    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    /**
     * @param bool $return_action
     *
     * @return array|float|int|null
     */
    public function special(bool $return_action = false)
    {

        $special = new Special($this);
        $action = $special->resolveAction();

        if ($action) {
            $coupon_ok = $special->checkCoupon($action);
            $dates_ok  = $special->checkDates($action);

            if ($coupon_ok && $dates_ok) {
                if ($return_action && $special->isProductOnAction($action)) {
                    return $action->toArray();

                } else {
                    return $special->getDiscountPrice($action);
                }
            }
        }

        return null;
    }

}
