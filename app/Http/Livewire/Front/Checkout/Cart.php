<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Front\Cart\CartSession;
use App\Models\Front\Catalog\Product;
use Livewire\Component;

class Cart extends Component
{

    /**
     * @var \App\Models\Front\Cart\Cart
     */
    protected $cart;

    protected $items;
    public $subtotal;
    public $total;
    public $count;


    public function mount()
    {
        //$this->setCart();

        //$this->items = $this->cart->get()['items']->toArray();

        //dd($this->items);
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

        return redirect()->to(request()->server('HTTP_REFERER'));
    }


    public function changeItemQuantity(int $product_id, int $quantity)
    {
        $this->setCart();

        $product = Product::query()->find($product_id);

        $this->cart->add($product, $quantity);

        $this->emit('updateCartNavIcon', $this->cart->get()['count']);
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $this->setCart();
        //$this->items = $this->cart->get()['items']->toArray();

        return view('livewire.front.checkout.cart', [
            'cart' => $this->cart->get(),
            'items' => $this->getItems()
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


    private function updateNavIcon()
    {
        $this->emit('updateCartNavIcon', $this->cart->get()['count']);
    }


    private function getItems()
    {
        return $this->cart->get()['items']->sortBy('name')->toArray();
    }
}
