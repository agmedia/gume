<?php

namespace App\Http\Livewire\Back\Catalog;

use App\Helpers\Helper;
use App\Models\Back\Catalog\Brand;
use Illuminate\Support\Str;
use Livewire\Component;

class BrandSearchInput extends Component
{
    /**
     * @var string
     */
    public $search = '';

    /**
     * @var array
     */
    public $search_results = [];

    /**
     * @var int
     */
    public $brand_id = 0;

    /**
     * @var bool
     */
    public $show_add_window = false;

    /**
     * @var null|bool
     */
    public $list = null;

    /**
     * @var array
     */
    public $new = [
        'title' => ''
    ];


    /**
     *
     */
    public function mount()
    {
        if ($this->brand_id) {
            $brand = Brand::find($this->brand_id);

            if ($brand) {
                $this->search = $brand->title;
            }
        }
    }


    /**
     *
     */
    public function viewAddWindow()
    {
        $this->show_add_window =! $this->show_add_window;
    }


    /**
     *
     */
    public function updatingSearch($value)
    {
        $this->search         = $value;
        $this->search_results = [];

        if ($this->search != '') {
            $this->search_results = (new Brand())->where('title', 'LIKE', '%' . $this->search . '%')
                                                  ->limit(5)
                                                  ->get();
        }
    }


    /**
     * @param $id
     */
    public function addBrand($id)
    {
        $brand = (new Brand())->where('id', $id)->first();

        $this->search_results = [];
        $this->search         = $brand->title;
        $this->brand_id     = $brand->id;

        if ($this->list) {
            return $this->emit('brandSelect', ['brand' => $brand->toArray()]);
        }
    }


    /**
     *
     */
    public function makeNewBrand()
    {
        if ($this->new['title'] == '') {
            return $this->emit('error_alert', ['message' => 'Molimo vas da popunite sve podatke!']);
        }

        $slug = Str::slug($this->new['title']);

        $id = Brand::insertGetId([
            'letter'           => Helper::resolveFirstLetter($this->new['title']),
            'title'            => $this->new['title'],
            'description'      => '',
            'meta_title'       => $this->new['title'],
            'meta_description' => '',
            'slug'             => $slug,
            'url'              => config('settings.brand_path') . '/' . $slug,
            'sort_order'       => 0,
            'status'           => 1,
            'created_at'       => now(),
            'updated_at'       => now()
        ]);

        if ($id) {
            $brand = Brand::find($id);

            $this->show_add_window = false;

            $this->brand_id = $brand->id;
            $this->search   = $brand->title;

            return $this->emit('success_alert', ['message' => 'Brand je uspješno dodan..!']);
        }

        return $this->emit('error_alert');
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        if ($this->search == '') {
            $this->brand_id = 0;

            if ($this->list) {
                $this->emit('brandSelect', ['brand' => ['id' => '']]);
            }
        }

        return view('livewire.back.catalog.brand-search-input');
    }
}
