<?php

namespace App\Models\Front\Cart;

use App\Models\Front\Catalog\Product;
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
     * @var \Darryldecode\Cart\Cart
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
        $this->setProduct($product, $quantity);

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
    public function setMethod(string $type, object|string $shipping_method): self
    {
        $condition = CartCondition::setMethod($type, 'total', $shipping_method);

        $this->resetCartCondition($type, $condition);

        return $this;
    }


    /**
     * @return $this
     * @throws \Darryldecode\Cart\Exceptions\InvalidConditionException
     */
    public function resetMethods_IfNeeded()
    {
        foreach ($this->cart->getConditions() as $condition) {
            if ($condition->getAttributes()['code'] == 'gls') {
                $this->setMethod('shipping', 'gls');
            }
        }

        return $this;
    }


    /**
     * @param string $coupon
     *
     * @return $this
     */
    public function setCoupon(string $coupon): self
    {
        $this->coupon = $coupon;

        foreach ($this->cart->getContent() as $item) {
            $this->remove($item->id);

            $product = Product::query()->find($item->id);

            if ($product) {
                $this->setProduct($product, $item->quantity)
                     ->store();
            }
        }

        $condition = CartCondition::set('coupon', 'total', $this->cart->getTotal(), $this->coupon);
        $this->resetCartCondition('coupon', $condition);

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
        $condition = CartCondition::setItem($this->product);

        $response = [
            'id'              => $this->product->id,
            'name'            => $this->product->name,
            'price'           => $this->product->price,
            'quantity'        => $this->product_quantity,
            'conditions'      => $condition,
            'attributes'      => CartCondition::setItemAttributes($this->product, $condition)
        ];

        return $response;
    }


    /**
     * @param Product|int $product
     * @param int         $quantity
     *
     * @return self
     */
    private function setProduct(Product $product, int $quantity): self
    {
        $this->product = $product;
        $this->product_quantity = $quantity;

        return $this;
    }


    /**
     * @param $condition
     *
     * @return self
     * @throws \Darryldecode\Cart\Exceptions\InvalidConditionException
     */
    private function resetCartCondition(string $type, $condition): self
    {
        $this->cart->removeConditionsByType($type);

        $this->cart->condition($condition);

        return $this;
    }
}
