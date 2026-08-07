<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Front\Cart\CartSession;
use App\Models\Front\Catalog\Product;
use Livewire\Component;

/**
 *
 */
class Cart extends Component
{

    /**
     * @var \App\Models\Front\Cart\Cart
     */
    protected $cart;

    /**
     * @var
     */
    protected $items;

    /**
     * @var
     */
    public $subtotal;

    /**
     * @var
     */
    public $total;

    /**
     * @var
     */
    public $count;

    /**
     * @var bool
     */
    public $free_wiper_inspection = false;


    /**
     * @return void
     */
    public function mount()
    {
        $this->free_wiper_inspection = (bool) session()->get('free_wiper_inspection', false);
    }


    /**
     * @param bool $value
     *
     * @return void
     */
    public function updatedFreeWiperInspection($value)
    {
        $this->free_wiper_inspection = (bool) $value;

        if ($this->free_wiper_inspection) {
            session()->put('free_wiper_inspection', true);
        } else {
            session()->forget('free_wiper_inspection');
        }
    }


    /**
     * @param int $product_id
     *
     * @return void
     */
    public function removeItemFromCart(int $product_id)
    {
        $this->setCart();

        $this->cart->remove($product_id);

        $this->cart->resetMethods_IfNeeded();

        return redirect()->to(request()->server('HTTP_REFERER'));
    }


    /**
     * @param int $product_id
     * @param int $quantity
     *
     * @return void
     */
    public function changeItemQuantity(int $product_id, int $quantity)
    {
        $this->setCart();

        $items = $this->getItems();
        $product = Product::query()->find($product_id);

        foreach ($items as $item) {
            if ($item['id'] == $product_id) {
                if (($item['quantity'] + $quantity) <= $product->quantity) {
                    $this->cart->add($product, $quantity);

                    $this->emit('updateCartNavIcon', $this->cart->get()['count']);
                }
            }
        }
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $this->setCart();
        $items    = $this->getItems();
        $hasTires = $this->hasTires($items);

        if ( ! $hasTires && $this->free_wiper_inspection) {
            $this->free_wiper_inspection = false;
            session()->forget('free_wiper_inspection');
        }

        //dd(Product::query()->find(3)->coupon());

        return view('livewire.front.checkout.cart', [
            'cart'     => $this->cart->get(),
            'items'    => $items,
            'hasTires' => $hasTires,
        ]);
    }

    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    /**
     * @return void
     */
    private function setCart()
    {
        $this->cart = CartSession::resolve();
    }


    /**
     * @return mixed
     */
    private function getItems()
    {
        return $this->cart->get()['items']->sortBy('name')/*->toArray()*/;
    }


    /**
     * @param mixed $items
     *
     * @return bool
     */
    private function hasTires($items): bool
    {
        $productIds = collect($items)->pluck('id')->filter()->all();

        if (empty($productIds)) {
            return false;
        }

        return Product::query()
            ->whereIn('id', $productIds)
            ->whereNotNull('sirina')
            ->where('sirina', '!=', '')
            ->exists();
    }
}
