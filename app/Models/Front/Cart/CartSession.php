<?php

namespace App\Models\Front\Cart;

use App\Models\Back\Settings\Settings;
use App\Models\Front\Catalog\Product;
use Carbon\Carbon;
use Darryldecode\Cart\Exceptions\InvalidConditionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
     * @return Cart
     */
    public static function resolve(): Cart
    {
        $cart = null;
        $session_id = self::getSessionId();

        if (session()->has(config('session.cart'))) {
            $cart = new Cart(session(config('session.cart')));
        }

        if (auth()->user() && ! $cart) {
            $cart = self::hasCart();

            if ($cart) {
                $cart = self::inflate(
                    new Cart($session_id),
                    json_decode($cart->cart_data, true)
                );
            }
        }

        if ( ! $cart) {
            $cart = new Cart($session_id);
        }

        session([config('session.cart') => $session_id]);

        if (auth()->user()) {
            self::store($cart->get());
        }

        return $cart;
    }


    /**
     * @param $request
     *
     * @return mixed
     */
    private static function store($request)
    {
        $id = auth()->id();

        if (self::hasCart()) {
            return self::where('user_id', $id)->update([
                'cart_data'  => $request,
                'updated_at' => Carbon::now()
            ]);
        }

        return self::create([
            'user_id'    => $id,
            'session_id' => session(config('session.cart')),
            'cart_data'  => json_encode($request)
        ]);
    }


    /**
     * @param Cart  $cart
     * @param array $cart_data
     *
     * @return Cart
     * @throws InvalidConditionException
     */
    private static function inflate(Cart $cart, array $cart_data): Cart
    {
        // Set cart items
        foreach ($cart_data['items'] as $item) {
            $product = Product::query()->find($item['id']);

            if ($product) {
                $cart->add($product, $item['quantity']);
            }
        }

        // Set cart conditions
        $payments = Settings::getList('payment');
        $shippings = Settings::getList('shipping');

        foreach ($cart_data['conditions'] as $title => $condition) {
            $is_payment = $payments->where('title', $title)->first();

            if ($is_payment) {
                $cart->setMethod('payment', $is_payment->code);
            }

            $is_shipping = $shippings->where('title', $title)->first();

            if ($is_shipping) {
                $cart->setMethod('shipping', $is_shipping->code);
            }
        }

        // Set cart coupon
        if ( ! empty($cart_data['coupon'])) {
            $cart->setCoupon($cart_data['coupon']);
        }

        return $cart;
    }


    /**
     * @param int $length
     *
     * @return string
     */
    private static function getSessionId(int $length = 8)
    {
        return auth()->guest() ? Str::random($length) : auth()->id();
    }


    /**
     * @return Builder|Model|object|null
     */
    private static function hasCart()
    {
        return self::query()->where('user_id', auth()->id())->first();
    }
}
