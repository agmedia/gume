<?php

namespace App\Models\Front\Cart;

use Darryldecode\Cart\Facades\CartFacade;

class Cart
{
    /**
     * @var string
     */
    private $cart_id;

    /**
     * @var
     */
    private $cart;

    /**
     * @var string
     */
    private $session_key;

    /**
     * @var string
     */
    private $coupon;


    public function __construct(string $id)
    {
        $this->cart_id     = $id;
        $this->cart        = CartFacade::session($id);
        $this->session_key = config('session.cart') ?: 'agm';
        $this->coupon      = session()->has($this->session_key . '_coupon') ? session($this->session_key . '_coupon') : '';
    }
}