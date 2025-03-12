<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Cart;
use Livewire\Component;

class CartDrawer extends Component
{
    public $cart;

    public function mount() {
        $this->cart = Cart::resolve()->get();

        //dd($this->cart);

        foreach($this->cart['items'] as $item) {
            //dd($item);
        }

        //dd($this->cart);
    }

    public function render()
    {
        return view('livewire.front.checkout.cart-drawer');
    }
}
