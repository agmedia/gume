<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Front\Cart\CartSession;
use Livewire\Component;

class Cart extends Component
{

    /**
     * @var \App\Models\Front\Cart\Cart
     */
    protected $cart;

    public $items;
    public $subtotal;
    public $total;
    public $count;


    public function mount()
    {
        $this->setCart();

        $this->items = $this->cart->get()['items'];



        //dd($this->items);
    }


    public function render()
    {
        //dd($this->cart->get());
        return view('livewire.front.checkout.cart', ['cart' => $this->cart->get()]);
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
}
