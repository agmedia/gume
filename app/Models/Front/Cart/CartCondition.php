<?php

namespace App\Models\Front\Cart;

use App\Helpers\Helper;
use App\Models\Front\Catalog\Product;
use App\Models\Front\Catalog\ProductAction;
use App\Models\Front\Checkout\PaymentMethod;
use App\Models\Front\Checkout\ShippingMethod;
use Darryldecode\Cart\Exceptions\InvalidConditionException;

class CartCondition
{

    /**
     * @param string       $type
     * @param string       $target
     * @param float|string $cart_total
     * @param string       $coupon
     *
     * @return \Darryldecode\Cart\CartCondition
     */
    public static function set(string $type, string $target, float|string $cart_total, string $coupon = ''): \Darryldecode\Cart\CartCondition
    {
        $condition = null;
        $actions   = ProductAction::query()->where('group', 'total')->active()->get();

        if ($actions->count()) {
            foreach ($actions as $action) {
                if ($coupon != '' && $action->coupon && $action->coupon == $coupon) {
                    $condition[] = self::setAction($type, $target, $cart_total, $action, $coupon);
                }

                if ( ! $action->coupon) {
                    $condition[] = self::setAction($type, $target, $cart_total, $action);
                }
            }
        }

        return $condition;
    }


    /**
     * @param string        $type
     * @param string        $target
     * @param object|string $method
     *
     * @return \Darryldecode\Cart\CartCondition|null
     * @throws InvalidConditionException
     */
    public static function setMethod(string $type, string $target, object|string $method): \Darryldecode\Cart\CartCondition|null
    {
        if (is_string($method)) {
            if ($type == 'payment') {
                $method = (new PaymentMethod())->find($method);
            } elseif ($type == 'shipping') {
                $method = (new ShippingMethod())->find($method);
            }
        }

        if ($method) {
            $value = $method->data->price ?: 0;

            if ($type == 'shipping' && $method->code == 'gls') {
                $cart = CartSession::resolve();

                $value = $cart['count'] * $value;
            }

            return new \Darryldecode\Cart\CartCondition([
                'name'       => $method->title,
                'type'       => $type, // payment, shipping, action, sale...
                'target'     => $target, // this condition will be applied to cart's subtotal when getSubTotal() is called.
                'value'      => $value,
                'attributes' => [
                    'description' => $method->data->short_description,
                    'geo_zone'    => $method->geo_zone
                ]
            ]);
        }

        return null;
    }


    /**
     * @param Product $product
     *
     * @return \Darryldecode\Cart\CartCondition|null
     * @throws InvalidConditionException
     */
    public static function setItem(Product $product): \Darryldecode\Cart\CartCondition|null
    {
        // Ako artikl ima akciju.
        $action_price = $product->special();

        if ($action_price) {
            $action = $product->special(true);

            if ($action && is_array($action)) {
                $type  = 'action';
                $value = '-' . intval($action['discount']) . '%';

                if ($action['type'] == 'F') {
                    $value = '-' . intval($action['discount']);
                }

                if ($action['coupon'] || $action['coupon'] != '') {
                    $type = 'coupon';
                }

                return new \Darryldecode\Cart\CartCondition([
                    'name'       => $action['title'],
                    'type'       => $type,
                    'value'      => $value,
                    'attributes' => [
                        'discount'   => $action['discount'],
                        'price'      => $action_price,
                        'price_text' => price($action_price, true),
                        'diff'       => $product->price - $action_price,
                    ]
                ]);
            }
        }

        // Ako nema akcije na artiklu.
        // Ako nije ispravan kupon.
        return null;
    }


    /**
     * @param Product                               $product
     * @param \Darryldecode\Cart\CartCondition|null $condition
     *
     * @return array
     */
    public static function setItemAttributes(Product $product, \Darryldecode\Cart\CartCondition $condition = null): array
    {
        $tax = $product->getTax(true);

        $attr = [
            'thumb'              => $product->thumb,
            'path'               => $product->url,
            'available_quantity' => $product->quantity,
            'org_price'          => price($product->price, true),
            'tax'                => [
                'title' => $tax->title,
                'rate'  => $tax->rate,
            ],
            'tax_amount'         => $product->getTax(),
            'action'             => []
        ];

        if ( ! empty($condition)) {
            $attr['action'] = [
                'title'      => $condition->getName(),
                'discount'   => $condition->getValue(),
                'price'      => $condition->getAttributes()['price'],
                'price_text' => $condition->getAttributes()['price_text'],
            ];

            $attr['tax_amount'] = Helper::calculateTax($condition->getAttributes()['price'], $tax->rate);
        }

        return $attr;
    }


    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    private static function setAction(string $type, string $target, float|string $cart_total, ProductAction $action, string $coupon = '')
    {
        $value    = Helper::calculateDiscountPrice($cart_total, $action->discount, $action->type);
        $discount = $cart_total - $value;

        $condition = [
            'name'   => $action->title,
            'type'   => $type,
            'target' => $target, // this condition will be applied to cart's subtotal when getSubTotal() is called.
            'value'  => '-' . $discount,
        ];

        if ($coupon != '') {
            $condition['attributes'] = [
                'coupon' => $coupon,
            ];
        }

        return new \Darryldecode\Cart\CartCondition($condition);
    }
}