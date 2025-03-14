<?php

namespace App\Models\Front\Cart;

use App\Models\Front\AgCart;
use App\Models\Front\Catalog\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 *
 */
class CartSession extends Model
{

    /**
     * @var string
     */
    protected $table = 'carts';

    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];


    /**
     * @return AgCart
     */
    public static function resolve(): Cart
    {
        if (session()->has(config('session.cart'))) {
            return new Cart(session(config('session.cart')));
        }

        return new Cart(config('session.cart'));
    }


    /**
     * @param $request
     *
     * @return mixed
     */
    public static function store($request)
    {
        /*if (auth()->guest()) {
            return session(config('session.cart'))->put(config('session.cart'), $request->all());
        }*/

        return self::create([
            'user_id'    => Auth::user()->id,
            'session_id' => session(config('session.cart')),
            'cart_data'  => json_encode($request)
        ]);
    }


    /**
     * @param array $request
     *
     * @return bool
     */
    public static function edit($request)
    {
        return self::where('user_id', Auth::user()->id)->update([
            'cart_data'  => $request,
            'updated_at' => Carbon::now()
        ]);
    }


    /**
     * @param AgCart $cart
     * @param        $session_id
     *
     * @return string
     */
    public static function checkLogged(Cart $cart, $session_id = null): string
    {
        if (Auth::user()) {
            $has_cart = self::where('user_id', Auth::user()->id)->first();

            if ($has_cart) {
                $cart_items = $cart->getCartItems(true);
                $cart_data  = json_decode($has_cart->cart_data, true);

                if (isset($cart_data['items'])) {
                    foreach ($cart_data['items'] as $item) {
                        $has_item_in_cart = $cart_items->where('id', $item['id'])->first();

                        if ( ! $has_item_in_cart) {
                            $cart_item = $cart->resolveItemRequest($item);

                            if (isset($cart_item['item']['id']) && isset($cart_item['item']['quantity'])) {
                                $product = Product::where('id', $cart_item['item']['id'])->first();

                                if ($product && $cart_item['item']['quantity'] < $product->quantity) {
                                    $cart->add($cart_item);
                                }
                            }
                        }
                    }
                }

                if (isset($cart_data['coupon']) && ! empty($cart_data['coupon'])) {
                    $cart->coupon($cart_data['coupon']);
                }

                $has_cart->update(['session_id' => $session_id]);

                return $session_id;
            }
        }

        return Str::random(8);
    }
}
