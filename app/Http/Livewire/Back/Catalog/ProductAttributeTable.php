<?php

namespace App\Http\Livewire\Back\Catalog;

use App\Models\Back\Catalog\Attributes\Attributes;
use Livewire\Component;

/**
 *
 */
class ProductAttributeTable extends Component
{

    /**
     * @var array
     */
    public $values = [];

    /**
     * @var array
     */
    public $items = [];

    /**
     * @var array
     */
    public $item = [];


    /**
     * @return void
     */
    public function mount()
    {
        $this->setValues();
        $this->setPredefinedItems();
    }


    /**
     * @param array $item
     *
     * @return void
     */
    public function addItem(array $item = [])
    {
        if (empty($item)) {
            $item = $this->getEmptyItem();
        }

        array_unshift($this->items, $item);
    }


    /**
     * @param int $key
     *
     * @return void
     */
    public function deleteItem(int $key)
    {
        unset($this->items[$key]);
    }


    /**
     * @return array
     */
    private function getEmptyItem(): array
    {
        return [
            'id' => 0,
            'title' => '',
            'value' => '',
        ];
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.back.catalog.product-attribute-table');
    }


    /**
     * @return string
     */
    public function paginationView()
    {
        return 'vendor.pagination.bootstrap-livewire';
    }


    /**
     * @return void
     */
    private function setValues()
    {
        $this->values = Attributes::query()->where('status', 1)->pluck('title', 'id')->toArray();
    }


    private function setPredefinedItems()
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = [
                'id' => $item['attribute_id'],
                'title' => $item['attribute']['title'],
                'value' => $item['value'],
            ];
        }

        $this->items = $items;
    }
}
