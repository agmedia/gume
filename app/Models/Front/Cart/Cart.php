<?php

namespace App\Models\Front\Cart;

use App\Helpers\Helper;
use App\Models\Back\Marketing\Action;
use App\Models\Front\Catalog\Product;
use App\Models\TagManager;
use Darryldecode\Cart\CartCondition;
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

    private $product;
    private $product_quantity;


    public function __construct(string $id)
    {
        $this->cart_id     = $id;
        $this->cart        = CartFacade::session($id);
        $this->session_key = config('session.cart') ?: 'agm';
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


    /*public function getItems()
    {
        return $this->cart->getContent();
    }*/

    /*public function getSubtotal()
    {
        return $this->cart->getSubTotal();
    }

    public function getTotal()
    {
        return $this->cart->getTotal();
    }*/

    /*public function getCount()
    {
        return $this->cart->getTotalQuantity();
    }*/


    public function add(Product $product, int $quantity)
    {
        $this->product = $product;
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
        $response = [
            'id'              => $this->product->id,
            'name'            => $this->product->name,
            'price'           => $this->product->price,
            'quantity'        => $this->product_quantity,
            'associatedModel' => $this->product,
            'conditions'      => $this->structureCartItemConditions(),
            'attributes'      => $this->structureCartItemAttributes()
        ];

        return $response;
    }


    /**
     * @param $product
     *
     * @return string[]
     */
    private function structureCartItemAttributes()
    {
        return [
            'thumb' => $this->product->thumb,
            'path' => $this->product->url,
            'org_price' => $this->product->price,
            'tax'  => [],
            'tax_amount' => 0,
        ];
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
        //dd($this->product->special());
        if ($this->product->special()) {
            /*$coupon = $this->product->coupon();

            if ($coupon != '') {
                return new CartCondition([
                    'name'   => 'Kupon akcija',
                    'type'   => 'coupon',
                    'target' => $coupon,
                    'value'  => -($this->product->price - $this->product->special())
                ]);
            }*/

            return new CartCondition([
                'name'   => 'Akcija',
                'type'   => 'promo',
                'target' => '',
                'value'  => -($this->product->price - $this->product->special())
            ]);
        }

        // Ako nema akcije na artiklu.
        // Ako nije ispravan kupon.
        return false;
    }
}