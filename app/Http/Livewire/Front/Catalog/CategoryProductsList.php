<?php

namespace App\Http\Livewire\Front\Catalog;

use App\Models\Front\Catalog\Category;
use App\Models\Front\Catalog\Product;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

/**
 *
 */
class CategoryProductsList extends Component
{

    /**
     * @var
     */
    //protected $products;

    /**
     * @var
     */
    public $route_data;


    /**
     * @return void
     */
    public function mount() {

    }


    /**
     * @param string $product
     * @param int    $quantity
     *
     * @return void
     */
    public function addToCart(string $product, int $quantity)
    {
        $this->emit('addCartItem', $product, $quantity);
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $products = $this->resolveProducts(json_decode($this->route_data));
        //dd($products);
        return view('livewire.front.catalog.category-products-list', [
            'products' => $products,
        ]);
    }


    private function resolveCategoryId($data)
    {
        if ($data->category) {
            return Category::getById($data->category->id);
        }

        $category = new \stdClass();
        $category->id = 0;

        return $category;
    }


    private function resolveProducts($data)
    {
        $category = $this->resolveCategoryId($data);


        return Cache::remember('products.category.' . $category->id, config('cache.life'), function () use ($category) {
            if ($category->id) {
        //dd($category);
                return $category->products()->get();
            }

            return Product::query()->get();
        });
    }
}
