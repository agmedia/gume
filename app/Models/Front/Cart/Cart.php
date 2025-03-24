<?php

namespace App\Models\Front\Cart;

use App\Helpers\Helper;
use App\Models\Front\Catalog\Product;
use App\Models\Front\Checkout\PaymentMethod;
use App\Models\Front\Checkout\ShippingMethod;
use Darryldecode\Cart\CartCondition;
use Darryldecode\Cart\Facades\CartFacade;

/**
 *
 */
class Cart
{

    /**
     * @var string
     */
    private $cart_id;

    /**
     * @var CartFacade
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

    /**
     * @var Product
     */
    private $product;

    /**
     * @var
     */
    private $product_quantity;


    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->cart_id     = $id;
        $this->cart        = CartFacade::session($id);
        $this->session_key = config('session.cart');
        $this->coupon      = session()->has($this->session_key . '_coupon') ? session($this->session_key . '_coupon') : '';
    }


    /**
     * @return array
     */
    public function get()
    {
        $response = [
            'id'         => $this->cart_id,
            'coupon'     => $this->coupon,
            'items'      => $this->cart->getContent(),
            'count'      => $this->cart->getTotalQuantity(),
            'subtotal'   => $this->cart->getSubTotal(),
            'conditions' => $this->cart->getConditions(),
            'total'      => $this->cart->getTotal()
        ];

        return $response;
    }


    /**
     * @param Product $product
     * @param int     $quantity
     *
     * @return array|string[]
     */
    public function add(Product $product, int $quantity)
    {
        $this->product          = $product;
        $this->product_quantity = $quantity;

        foreach ($this->cart->getContent() as $item) {
            if ($item->id == $this->product->id) {

                if ($quantity > $this->product->quantity) {
                    return ['error' => 'Nažalost nema dovoljnih količina artikla..!'];
                }

                return $this->update(true);
            }
        }

        return $this->store();
    }


    /**
     * @param $id
     *
     * @return array
     */
    public function remove($id)
    {
        $this->cart->remove($id);

        return $this->get();
    }


    /**
     * @param object|string $shipping
     *
     * @return void
     * @throws \Darryldecode\Cart\Exceptions\InvalidConditionException
     */
    public function setShippingMethod(object|string $shipping_method): self
    {
        if (is_string($shipping_method)) {
            $shipping_method = (new ShippingMethod())->find($shipping_method);
        }

        if ($shipping_method) {
            $value = $shipping_method->data->price;

            if ($this->cart->getTotal() > config('settings.free_shipping')) {
                $value = 0;
            }

            $shipping_condition = new CartCondition(array(
                'name' => $shipping_method->title,
                'type' => 'shipping',
                'target' => 'total', // this condition will be applied to cart's subtotal when getSubTotal() is called.
                'value' => $value,
                'attributes' => [
                    'description' => $shipping_method->data->short_description,
                    'geo_zone' => $shipping_method->geo_zone
                ]
            ));

            $this->cart->removeConditionsByType('shipping');

            $this->cart->condition($shipping_condition);
        }

        return $this;
    }


    /**
     * @param object|string $payment_method
     *
     * @return $this
     * @throws \Darryldecode\Cart\Exceptions\InvalidConditionException
     */
    public function setPaymentMethod(object|string $payment_method): self
    {
        if (is_string($payment_method)) {
            $payment_method = (new PaymentMethod())->find($payment_method);
        }

        if ($payment_method) {
            $value = $payment_method->data->price ?: 0;

            $payment_condition = new CartCondition(array(
                'name' => $payment_method->title,
                'type' => 'payment',
                'target' => 'total', // this condition will be applied to cart's subtotal when getSubTotal() is called.
                'value' => $value,
                'attributes' => [
                    'description' => $payment_method->data->short_description,
                    'geo_zone' => $payment_method->geo_zone
                ]
            ));

            $this->cart->removeConditionsByType('payment');

            $this->cart->condition($payment_condition);
        }

        return $this;
    }


    /**
     * @return $this
     */
    public function flush(): static
    {
        $this->cart->clear();

        return $this;
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @param $request
     *
     * @return array
     */
    private function store(): array
    {
        $this->cart->add($this->structureCartItem());

        return $this->get();
    }


    /**
     * @param      $id
     * @param      $quantity
     * @param bool $relative
     *
     * @return array
     */
    private function update(bool $relative): array
    {
        $this->cart->update($this->product->id, [
            'quantity' => [
                'relative' => $relative,
                'value'    => $this->product_quantity
            ],
        ]);

        return $this->get();
    }


    /**
     * @param $request
     *
     * @return array
     */
    private function structureCartItem()
    {
        $condition = $this->structureCartItemConditions();
        $attributes = $this->structureCartItemAttributes($condition);

        $response = [
            'id'              => $this->product->id,
            'name'            => $this->product->name,
            'price'           => $this->product->price,
            'quantity'        => $this->product_quantity,
            //'associatedModel' => $this->product,
            'conditions'      => $condition,
            'attributes'      => $attributes
        ];

        return $response;
    }


    /**
     * @param CartCondition|null $condition
     *
     * @return array
     */
    private function structureCartItemAttributes(CartCondition $condition = null)
    {
        $tax = $this->product->getTax(true);

        $attr = [
            'thumb'      => $this->product->thumb,
            'path'       => $this->product->url,
            'org_price'  => price($this->product->price, true),
            'tax'        => [
                'title' => $tax->title,
                'rate'  => $tax->rate,
            ],
            'tax_amount' => $this->product->getTax(),
            'action'     => []
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


    /**
     * @param $product
     *
     * @return CartCondition|bool
     * @throws \Darryldecode\Cart\Exceptions\InvalidConditionException
     */
    private function structureCartItemConditions()
    {
        // Ako artikl ima akciju.
        $action_price = $this->product->special();

        if ($action_price) {
            $action = $this->product->special(true);
            /*$coupon = $this->product->coupon();

            if ($coupon != '') {
                return new CartCondition([
                    'name'   => 'Kupon akcija',
                    'type'   => 'coupon',
                    'target' => $coupon,
                    'value'  => -($this->product->price - $this->product->special())
                ]);
            }*/

            if ($action) {
                $value = '-' . $action['discount'] . '%';

                if ($action['type'] == 'P') {
                    $value = '-' . $action['discount'];
                }

                return new CartCondition([
                    'name'       => $action['title'],
                    'type'       => 'action',
                    'target'     => 'product',
                    'attributes' => [
                        'discount'   => $action['discount'],
                        'price'      => $action_price,
                        'price_text' => price($action_price, true),
                        'diff'       => $this->product->price - $action_price,
                    ],
                    'value'      => $value
                ]);
            }
        }

        // Ako nema akcije na artiklu.
        // Ako nije ispravan kupon.
        return null;
    }
}