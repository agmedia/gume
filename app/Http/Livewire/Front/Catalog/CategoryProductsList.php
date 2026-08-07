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
    public $ampere = [];

    /**
     * @var array
     */
    public $wiper_vehicles = [];

    /**
     * @var array
     */
    public $wiper_dimensions = [];

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

    public $kat = [];

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
    public $amperi = '';

    /**
     * Vehicle make/model search used by the wiper category.
     *
     * @var string
     */
    public $vozilo = '';

    /**
     * Wiper installation position (front or rear).
     *
     * @var string
     */
    public $pozicija = '';

    /**
     * Wiper length, for example 600mm or 600/530mm.
     *
     * @var string
     */
    public $dimenzija_brisaca = '';


    /**
     * @var string
     */
    public $sort = '';

    /**
     * @var array
     */
   // public $brands = [];

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
        'amperi' => ['except' => ''],
        'vozilo'  => ['except' => ''],
        'pozicija' => ['except' => ''],
        'dimenzija_brisaca' => ['except' => ''],
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
        $routeData          = json_decode($this->route_data);
        $this->sezone       = ProductHelper::getSezoneList($routeData);
        $this->sirine       = ProductHelper::getSirineList($routeData);
        $this->visine       = ProductHelper::getVisineList($routeData);
        $this->promjeri     = ProductHelper::getPromjeriList($routeData);
        $this->sorting_list = ProductHelper::getSortingList($routeData);
        $this->ampere       = ProductHelper::getAmperiList($routeData);
        //
        $this->brands = Brand::getSelectList('slug', $routeData);

        if (isset($routeData->subcategory->slug) && $routeData->subcategory->slug === 'metlica-brisaca') {
            $this->resolveWiperFilterOptions((int) $routeData->subcategory->id);
        }
       // $this->kat = json_decode($this->route_data);

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

        $cat = json_decode($this->route_data);

        //dd( $products);
        return view('livewire.front.catalog.category-products-list', compact('products', 'cat'));
    }


    /**
     * @param $data
     *
     * @return mixed
     */
    private function resolveProducts($data)
    {
        $filter = $this->resolveData($data);
        //dd($data, $filter);

        return Cache::remember($filter->cacheHash, config('cache.life'), function () use ($filter) {

        $products = Product::query()->where('status', 1)
                           ->where('quantity', '>', 0);

        // Search
        if (isset($filter->ids) && $filter->ids) {
            $products->whereIn('id', json_decode($filter->ids));
        }

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

        // Wiper compatibility and dimensions are stored in the description.
        if ($filter->vozilo != '') {
            $vehicle = addcslashes($filter->vozilo, '\\%_');

            $products->where(function (Builder $query) use ($vehicle) {
                $query->where('description', 'LIKE', '%' . $vehicle . '%')
                      ->orWhere('name', 'LIKE', '%' . $vehicle . '%');
            });
        }

        if ($filter->dimenzija_brisaca != '') {
            $dimension = addcslashes($filter->dimenzija_brisaca, '\\%_');

            $products->where(function (Builder $query) use ($dimension) {
                $query->where('description', 'LIKE', '%' . $dimension . '%')
                      ->orWhere('name', 'LIKE', '%' . $dimension . '%');
            });
        }

        if (in_array($filter->pozicija, ['sprijeda', 'straga'], true)) {
            $products->where('description', 'LIKE', '%mjesto ugradnje: ' . $filter->pozicija . '%');
        }

        if ($filter->amperi != '') {
            $products->where('description', 'LIKE', '%' . $filter->amperi . '%');
        }

        // Sort
        if ($filter->sort != '') {
            $sort = explode('-', $filter->sort);

            $products->orderBy($sort[0], $sort[1]);
        }

        return $products->paginate(config('settings.pagination.front'));

        });
    }


    /**
     * @param $data
     *
     * @return \stdClass
     */
    private function resolveData($data): \stdClass
    {
        $data->category    = $this->resolveCategoryId(isset($data->category) ? $data->category : null);
        $data->subcategory = $this->resolveCategoryId(isset($data->subcategory) ? $data->subcategory : null);
        $data->sezona      = $this->sezona;
        $data->sirina      = $this->sirina;
        $data->visina      = $this->visina;
        $data->promjer     = $this->promjer;
        $data->sort        = $this->sort;
        $data->brand       = $this->brand;
        $data->amperi       = $this->amperi;
        $data->vozilo      = $this->vozilo;
        $data->pozicija    = $this->pozicija;
        $data->dimenzija_brisaca = $this->dimenzija_brisaca;
        $data->price       = $this->price;
        $data->page        = $this->page;

        //$ids = $data->ids ? hash('crc32', $data->ids) : 0;

        $cache_hash = 'products.list' . (isset($data->group) ? $data->group : '')
                      . $data->category->id
                      . $data->subcategory->id
                      . $data->sezona
                      . $data->sirina
                      . $data->visina
                      . $data->promjer
                      . $data->brand
                      . $data->amperi
                      . $data->vozilo
                      . $data->pozicija
                      . $data->dimenzija_brisaca
                      . $data->sort
                      . $data->price
                      . $data->page
                      /*. $ids*/;

        $data->cacheHash = hash('crc32', $cache_hash);

        return $data;
    }


    /**
     * Build searchable vehicle/model and wiper-dimension lists from the
     * products that are actually available in the wiper category.
     *
     * @param int $categoryId
     *
     * @return void
     */
    private function resolveWiperFilterOptions(int $categoryId): void
    {
        $options = Cache::remember('wiper.filter.options.' . $categoryId, config('cache.life'), function () use ($categoryId) {
            $vehicles   = [];
            $dimensions = [];

            $descriptions = Product::query()
                ->where('status', 1)
                ->where('quantity', '>', 0)
                ->whereHas('categories', function (Builder $query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->pluck('description');

            foreach ($descriptions as $description) {
                $text = html_entity_decode(strip_tags((string) $description), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $text = trim(preg_replace('/\s+/u', ' ', $text));

                if (preg_match_all('/\b\d{3}(?:\/\d{3})?mm\b/iu', $text, $matches)) {
                    foreach ($matches[0] as $dimension) {
                        $dimensions[$dimension] = $dimension;
                    }
                }

                if (preg_match('/\b\d{3}(?:\/\d{3})?mm\s+(.+)$/iu', $text, $matches)) {
                    $compatibility = preg_replace('/\s+\d{2}\.\d{2}-.*$/u', '', $matches[1]);

                    foreach (preg_split('/;\s*/u', $compatibility) as $vehicle) {
                        $vehicle = trim($vehicle, " \t\n\r\0\x0B,;");

                        if ($vehicle !== '') {
                            $vehicles[$vehicle] = $vehicle;
                        }
                    }
                }
            }

            natcasesort($vehicles);
            uksort($dimensions, 'strnatcasecmp');

            return [
                'vehicles'   => array_values($vehicles),
                'dimensions' => array_values($dimensions),
            ];
        });

        $this->wiper_vehicles   = $options['vehicles'];
        $this->wiper_dimensions = $options['dimensions'];
    }


    /**
     * @param $data
     *
     * @return Category|\stdClass|null
     */
    private function resolveCategoryId($data = null)
    {
        if ($data) {
            return Category::getById($data->id);
        }

        $category     = new \stdClass();
        $category->id = 0;

        return $category;
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
