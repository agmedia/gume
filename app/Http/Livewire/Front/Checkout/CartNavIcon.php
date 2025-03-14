<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Front\Cart\CartSession;
use Livewire\Component;

/**
 *
 */
class CartNavIcon extends Component
{

    /**
     * @var int
     */
    public $count = 0;

    /**
     * @var string[]
     */
    protected $listeners = ['updateCartNavIcon'];


    /**
     * @return void
     */
    public function mount() {
        $this->count = CartSession::resolve()->get()['count'];
    }


    /**
     * @param int $count
     *
     * @return void
     */
    public function updateCartNavIcon(int $count) {
        $this->count = $count;
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.front.checkout.cart-nav-icon');
    }
}
