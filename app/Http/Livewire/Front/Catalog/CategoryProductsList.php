<?php

namespace App\Http\Livewire\Front\Catalog;

use App\Models\Front\Catalog\Category;
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.front.catalog.category-products-list');
    }
}
