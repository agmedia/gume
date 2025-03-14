<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Front\Cart\CartSession;
use App\Models\Front\Catalog\Product;
use Livewire\Component;

/**
 *
 */
class CartDrawer extends Component
{

    /**
     * @var
     */
    protected $cart;

    /**
     * @var string[]
     */
    protected $listeners = ['addCartItem'];


    /**
     * @return void
     */
    public function mount() {
        $this->setCart();


    }


    /**
     * @param Product $product
     * @param int     $quantity
     *
     * @return void
     */
    public function addCartItem(Product $product, int $quantity)
    {
        $this->setCart();

        $this->cart->add($product, $quantity);

        $this->updateNacIcon();
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

        $this->updateNacIcon();
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.front.checkout.cart-drawer', ['cart' => $this->cart]);
    }


    /**
     * @return void
     */
    private function setCart()
    {
        $this->cart = CartSession::resolve();
    }


    private function updateNacIcon()
    {
        $this->emit('updateCartNavIcon', $this->cart->get()['count']);
    }
}
