<?php

namespace App\Http\Livewire\Front\Checkout;

use App\Models\Front\Cart\CartSession;
use App\Models\Front\Checkout\PaymentMethod;
use App\Models\Front\Checkout\ShippingMethod;
use Livewire\Component;

/**
 *
 */
class CartViewAside extends Component
{

    protected $listeners = ['shippingUpdated', 'paymentUpdated'];

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

    public $shipping;

    public $payment;

    /**
     * @var
     */
    public $total;

    /**
     * @var
     */
    public $count;


    public function mount()
    {
        if (session()->has('selected_shipping')) {
            $this->shipping = session()->get('selected_shipping')->code;
        }

        if (session()->has('selected_shipping')) {
            $this->payment = session()->get('selected_shipping')->code;
        }
    }


    public function shippingUpdated(string $shipping)
    {
        $this->setCart();

        $this->shipping = $shipping;

        $method = (new ShippingMethod())->find($shipping);

        if ($method) {
            session()->put('selected_shipping', $method);

            $this->cart->setShippingMethod($method);
        }

        return redirect()->to(request()->server('HTTP_REFERER'));
    }


    public function paymentUpdated(string $payment)
    {
        $this->setCart();

        $this->payment = $payment;

        $method = (new PaymentMethod())->find($payment);

        if ($method) {
            session()->put('selected_payment', $method);

            $this->cart->setPaymentMethod($method);
        }

        //return redirect()->to(request()->server('HTTP_REFERER'));
    }


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
