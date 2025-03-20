<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Front\Cart\CartSession;
use Livewire\Component;

/**
 *
 */
class CartNavIcon extends Component
{

    protected $cart;

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
    public function mount()
    {
        $this->cart = CartSession::resolve()->get();
        $this->count = $this->cart['count'];
    }


    /**
     * @param int $count
     *
     * @return void
     */
    public function updateCartNavIcon(int $count)
    {
        $this->count = $count;
    }


    /**
     * @param int $product_id
     *
     * @return void
     */
    public function removeFromCart(int $product_id)
    {
        $this->setCart();

        $this->cart->remove($product_id);

        return redirect()->to(request()->server('HTTP_REFERER'));
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.front.checkout.cart-nav-icon', ['cart' => $this->cart]);
    }


    /**
     * @return array
     */
    private function setCart(): array
    {
        return CartSession::resolve()->get();
    }
}
