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

    /**
     * @var array
     */
    public $sezone = [];

    /**
     * @var array
     */
    public $sirine = [];

    /**
     * @var array
     */
    public $visine = [];

    /**
     * @var array
     */
    public $promjeri = [];

    /**
     * @var array
     */
    public $sorting_list = [];

    /**
     * @var string
     */
    public $sezona = '';

    /**
     * @var string
     */
    public $sirina = '';

    /**
     * @var string
     */
    public $visina = '';

    /**
     * @var string
     */
    public $promjer = '';

    /**
     * @var string
     */
    public $sort = '';

    /**
     * @var array
     */
    public $brands = [];

    /**
     * @var string
     */
    public $brand = '';

    /**
     * @var array
     */
    public $prices = [];

    /**
     * @var string
     */
    public $price = '';

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var bool
     */
    public $show_additional_filters = false;

    /**
     * @var array
     */
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


    /**
     * @param string $target
     * @param string $value
     *
     * @return void
     */
    public function dropdownFilterSelected(string $target, string $value)
    {
        //dd($target, $value);
        $this->{$target} = $value;

        $this->resetPage();
    }


    /**
     * @return void
     */
    public function showFilter()
    {
        $this->show_additional_filters = ! $this->show_additional_filters;
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
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
        if ($data) {
            return Category::getById($data->id);
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
        $filter = $this->resolveData($data);

        //return Cache::remember($filter->cacheHash, config('cache.life'), function () use ($category, $filter) {

        $products = Product::query()->where('status', 1)
                           ->where('quantity', '>', 0);

        // Categories
        if ($filter->category->id) {
            if ( ! $filter->category->parent_id) {
                $cats = $filter->category->subcategories()->pluck('id');
                $cats->push($filter->category->id);

                $products->whereHas('categories', function (Builder $query) use ($cats) {
                    $query->whereIn('category_id', $cats);
                });
            }
        }

        // Subcategories
        if ($filter->subcategory->id) {
            $products->whereHas('categories', function (Builder $query) use ($filter) {
                $query->where('category_id', $filter->subcategory->id);
            });
        }

        // Brand
        if ($filter->brand != '') {
            $brand = Brand::getBySlug($filter->brand);

            if ($brand) {
                $products->where('brand_id', $brand->id);
            }
        }

        if ($filter->sezona != '') {
            $products->where('sezona', $this->resolveSezona($filter->sezona));
        }
        if ($filter->sirina != '') {
            $products->where('sirina', $filter->sirina);
        }
        if ($filter->visina != '') {
            $products->where('visina', $filter->visina);
        }
        if ($filter->promjer != '') {
            $products->where('promjer', $filter->promjer);
        }

        // Sort
        if ($filter->sort != '') {
            $sort = explode('-', $filter->sort);

            $products->orderBy($sort[0], $sort[1]);
        }

        return $products->paginate(config('settings.pagination.front'));

        //});
    }


    /**
     * @param $data
     *
     * @return \stdClass
     */
    private function resolveData($data): \stdClass
    {
        $data->category    = $this->resolveCategoryId($data->category);
        $data->subcategory = $this->resolveCategoryId($data->subcategory);
        $data->sezona      = $this->sezona;
        $data->sirina      = $this->sirina;
        $data->visina      = $this->visina;
        $data->promjer     = $this->promjer;
        $data->sort        = $this->sort;
        $data->brand       = $this->brand;
        $data->price       = $this->price;
        $data->page        = $this->page;

        $cache_hash = 'products.list' . $data->group
                      . $data->category->id
                      . $data->subcategory->id
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
     * @param string $sezona
     *
     * @return string
     */
    private function resolveSezona(string $sezona): string
    {
        if ($sezona == 'Ljeto') {
            return 'Summer';
        } elseif ($sezona == 'Zima') {
            return '';
        } elseif ($sezona == 'Sve') {
            return 'WholeYear';
        }

        return '';
    }


    /**
     * @return string
     */
    public function paginationView()
    {
        return 'vendor.pagination.bootstrap-livewire';
    }
}
