<?php

namespace App\Http\Livewire\Front\Catalog;

use App\Helpers\ProductHelper;
use App\Models\Front\Catalog\Brand;
use App\Models\Front\Catalog\Category;
use App\Models\Front\Catalog\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

/**
 *
 */
class CategoryProductsList extends Component
{

    use WithPagination;

    /**
     * @var
     */
    //protected $products;

    /**
     * @var
     */
    public $route_data;

    public $sezone = [];

    public $sirine = [];

    public $visine = [];

    public $promjeri = [];

    public $sorting_list = [];

    public $sezona = '';

    public $sirina = '';

    public $visina = '';

    public $promjer = '';

    public $sort = '';

    public $brands = [];

    public $brand = '';

    public $prices = [];

    public $price = '';

    public $page = 1;

    public $show_additional_filters = false;

    protected $queryString = [
        'sezona'  => ['except' => ''],
        'sirina'  => ['except' => ''],
        'visina'  => ['except' => ''],
        'promjer' => ['except' => ''],
        'brand'   => ['except' => ''],
        'price'   => ['except' => ''],
        'sort'    => ['except' => ''],
        'page'    => ['except' => 1],
    ];


    /**
     * @return void
     */
    public function mount()
    {
        $this->sezone       = ProductHelper::getSezoneList();
        $this->sirine       = ProductHelper::getSirineList();
        $this->visine       = ProductHelper::getVisineList();
        $this->promjeri     = ProductHelper::getPromjeriList();
        $this->sorting_list = ProductHelper::getSortingList();
        //
        $this->brands = Brand::getSelectList('slug');
        //dd($this->brands);
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


    public function dropdownFilterSelected(string $target, string $value)
    {
        $this->{$target} = $value;
        //$this->page = 1;
        $this->resetPage();

        //dd(request()->all());

        //return redirect(request()->header('Referer'));
    }


    public function showFilter()
    {
        $this->show_additional_filters = ! $this->show_additional_filters;
    }


    public function cleanFilter()
    {
        return redirect()->to(request()->input('fingerprint')['path']);
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $products = $this->resolveProducts(json_decode($this->route_data));

        //dd($sezone, $products);
        return view('livewire.front.catalog.category-products-list', compact('products'));
    }


    /**
     * @param $data
     *
     * @return Category|\stdClass|null
     */
    private function resolveCategoryId($data)
    {
        if ($data->category) {
            return Category::getById($data->category->id);
        }

        $category     = new \stdClass();
        $category->id = 0;

        return $category;
    }


    /**
     * @param $data
     *
     * @return mixed
     */
    private function resolveProducts($data)
    {
        $category    = $this->resolveCategoryId($data);
        $filter_data = $this->resolveData();

        return Cache::remember($filter_data->cacheHash, config('cache.life'), function () use ($category, $filter_data) {

            $products = Product::query()->where('status', 1)
                               ->where('quantity', '>', 0);
            if ($category->id) {
                if ( ! $category->parent_id) {
                    $cats = $category->subcategories()->pluck('id');
                    $cats->push($category->id);

                    $products->whereHas('categories', function (Builder $query) use ($cats) {
                        $query->whereIn('category_id', $cats);
                    });
                }
            }

            if ($filter_data->sezona != '') {
                $products->where('sezona', $filter_data->sezona);
            }
            if ($filter_data->sirina != '') {
                $products->where('sirina', $filter_data->sirina);
            }
            if ($filter_data->visina != '') {
                $products->where('visina', $filter_data->visina);
            }
            if ($filter_data->promjer != '') {
                $products->where('promjer', $filter_data->promjer);
            }

            // Sort
            if ($filter_data->sort != '') {
                $sort = explode('_', $filter_data->sort);

                $products->orderBy($sort[0], $sort[1]);
            }

            return $products->paginate(5);

        });
    }


    private function resolveData(): \stdClass
    {
        $data          = json_decode($this->route_data);
        $data->sezona  = $this->sezona;
        $data->sirina  = $this->sirina;
        $data->visina  = $this->visina;
        $data->promjer = $this->promjer;
        $data->sort    = $this->sort;
        $data->brand   = $this->brand;
        $data->price   = $this->price;
        $data->page    = $this->page;

        $cache_hash = 'products.list' . $data->group
                      . ($data->category ? $data->category->id : 0)
                      . ($data->subcategory ? $data->subcategory->id : 0)
                      . $data->sezona
                      . $data->sirina
                      . $data->visina
                      . $data->promjer
                      . $data->brand
                      . $data->sort
                      . $data->price
                      . $data->page;

        $data->cacheHash = hash('crc32', $cache_hash);

        return $data;
    }


    /**
     * @return string
     */
    public function paginationView()
    {
        return 'vendor.pagination.bootstrap-livewire';
    }
}
