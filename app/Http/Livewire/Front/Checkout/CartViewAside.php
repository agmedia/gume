<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Front\Cart\CartSession;
use Livewire\Component;

/**
 *
 */
class CartViewAside extends Component
{

    /**
     * @var
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

    public $shipping;

    /**
     * @var
     */
    public $total;

    /**
     * @var
     */
    public $count;


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $this->setCart();

        return view('livewire.front.checkout.cart-view-aside', [
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


    /**
     * @return mixed
     */
    private function getItems()
    {
        return $this->cart->get()['items']->sortBy('name')->toArray();
    }
}
