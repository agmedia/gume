<?php

namespace App\Http\Livewire\Front\Catalog;

use App\Models\Front\Catalog\Category;
use App\Models\Front\Catalog\Product;
use Livewire\Component;

/**
 *
 */
class CategoryProductsList extends Component
{

    /**
     * @var
     */
    public $products;

    /**
     * @var
     */
    public $route_data;


    /**
     * @return void
     */
    public function mount() {
        $data = json_decode($this->route_data);
        $category = Category::getById($data->category->id);

        $this->products = $category->products()->get();
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
        return view('livewire.front.catalog.category-products-list');
    }
}
