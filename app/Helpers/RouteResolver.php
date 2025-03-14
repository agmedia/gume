<?php

namespace App\Helpers;

use App\Models\Front\Catalog\Category;
use App\Models\Front\Catalog\Product;
use Illuminate\Support\Str;

/**
 *
 */
class RouteResolver
{

    /**
     * @var string
     */
    public $group;

    /**
     * @var string|null
     */
    public $category;

    /**
     * @var string|null
     */
    public $subcategory;

    /**
     * @var Product|null
     */
    public $product;

    /**
     * @var string
     */
    private $all_products_path;

    /**
     * @var string[]
     */
    private $abort_paths = [
        'media'
    ];

    /**
     * @var int
     */
    private $abort_code = 404;

    /**
     * @var string
     */
    private $cache_tag = 'category';


    /**
     * RouteResolver constructor.
     */
    public function __construct(string $group, string $category = null, string $subcategory = null, Product $product = null)
    {
        $this->group             = $group;
        $this->category          = $category;
        $this->subcategory       = $subcategory;
        $this->product           = $product;
        $this->all_products_path = Str::slug(config('settings.group_path'));
    }


    /**
     * @return \stdClass
     */
    public function getData(): \stdClass
    {
        return $this->setData();
    }


    /**
     * @param int|null $code
     *
     * @return void
     */
    public function checkForUnwantedPaths(int $code = null): void
    {
        if (in_array($this->group, $this->abort_paths)) {
            abort($code ?? $this->abort_code);
        }
    }


    /**
     * @return $this
     */
    public function isAllowedGroup()
    {
        if ($this->group) {
            $groups      = $this->getDefaultGroups();
            $group_exist = $this->checkGroup($groups);

            if ( ! $group_exist) {
                $this->product = $this->getProduct($this->group);

                if ( ! $this->product) {
                    abort($this->abort_code);
                }
            }
        }

        return $this;
    }


    /**
     * @return $this
     */
    public function setRoute()
    {
        // Ako je grupa i kategorija_ili_artikl
        if ( ! $this->product && ! $this->subcategory && $this->category) {
            $this->category = $this->resolveCategory($this->category, 0, true);
        }

        // Ako je grupa, kategorija, podkategorija_ili_artikl
        if ( ! $this->product && $this->subcategory && $this->category) {
            $this->category = $this->resolveCategory($this->category, 0);

            if ($this->category) {
                $this->subcategory = $this->resolveCategory($this->subcategory, $this->category->id, true);
            }
        }

        // Ako je grupa, kategorija, podkategorija i artikl.
        if ($this->product && $this->subcategory && $this->category) {
            $this->category = $this->resolveCategory($this->category, 0);

            if ($this->category) {
                $this->subcategory = $this->resolveCategory($this->subcategory, $this->category->id);
            }

            if ( ! isset($this->product->id)) {
                abort($this->abort_code);
            }
        }

        return $this;
    }


    /**
     * @param string $slug
     * @param int    $parent_id
     * @param bool   $check_product
     *
     * @return Category|string
     */
    private function resolveCategory(string $slug, int $parent_id, bool $check_product = false): Category|string
    {
        $category = $this->getCategory($slug, $parent_id);

        if ( ! $category) {
            if ($check_product) {
                $this->product = $this->getProduct($slug);

                if ($this->product) {
                    return $slug;
                }
            }

            abort($this->abort_code);
        }

        return $category;
    }


    /**
     * @return \stdClass
     */
    private function setData(): \stdClass
    {
        $data = new \stdClass();

        $data->group       = $this->group;
        $data->category    = $this->category;
        $data->subcategory = $this->subcategory;
        $data->product     = $this->product;

        return $data;
    }


    private function checkProduct()
    {
        if ( ! $this->product ||  ! isset($this->product->id) || ! $this->product->status) {
            abort($this->abort_code);
        }
    }


    /**
     * @param $groups
     *
     * @return bool
     */
    private function checkGroup($groups): bool
    {
        $exist = false;

        foreach ($groups as $item) {
            if ($item->slug == $this->group) {
                $exist = true;
            }
        }

        if ($this->group == $this->all_products_path) {
            $exist = true;
        }

        return $exist;
    }


    /**
     * @return array|mixed
     */
    private function getDefaultGroups()
    {
        return Helper::resolveCache($this->cache_tag)->remember('groups', config('cache.life'), function () {
            return Category::groups()->get();
        });
    }


    /**
     * @param string $slug
     * @param int    $parent_id
     *
     * @return array|mixed
     */
    private function getCategory(string $slug, int $parent_id)
    {
        $key = $slug . $parent_id;

        return Helper::resolveCache($this->cache_tag)->remember($key, config('cache.life'), function () use ($slug, $parent_id) {
            $category = Category::query()->where('slug', $slug)->where('parent_id', $parent_id)->first();

            if ($category && is_a($category, Category::class)) {
                $category->count = Category::getProductsCount($category);
            }

            return $category;
        });
    }


    /**
     * @param string $slug
     *
     * @return array|mixed
     */
    private function getProduct(string $slug)
    {
        return Helper::resolveCache($this->cache_tag)->remember('product' . $slug, config('cache.life'), function () use ($slug) {
            return Product::where('slug', $slug)->first();
        });
    }

}
