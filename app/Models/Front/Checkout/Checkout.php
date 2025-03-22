<?php

namespace App\Models\Front\Checkout;

use App\Helpers\Helper;
use App\Models\Back\Orders\Order;
use App\Models\Back\Orders\OrderHistory;
use App\Models\Back\Orders\OrderProduct;
use App\Models\Back\Orders\OrderTotal;
use App\Models\Back\Settings\Settings;
use App\Models\Front\Cart\CartSession;
use Illuminate\Http\Request;

/**
 *
 */
class Checkout
{

    /**
     * @var int
     */
    protected $order_id = 0;

    /**
     * @var null
     */
    protected $order = null;

    /**
     * @var null
     */
    protected $request = null;


    /**
     * @param string|array|object|null $payment_method
     * @param string|object|null       $shipping_method
     * @param array|null               $customer_info
     * @param array|null               $reservation_data
     * @param string|null              $comment
     * @param array|null               $cart
     */
    public function __construct(
        public string|array|object|null $payment_method = null,
        public string|object|null $shipping_method = null,
        public array|null $customer_info = null,
        public array|null $reservation_data = null,
        public string|null $comment = null,
        public array|null $cart = null
    )
    {
        if (is_array($payment_method)) {
            $this->payment_method   = $this->resolveMethod('payment', $payment_method['payment_method']);
            $this->shipping_method  = $this->resolveMethod('shipping', $payment_method['shipping_method']);
            $this->customer_info    = $payment_method['customer_info'];
            $this->reservation_data = $payment_method['reservation_data'];
            $this->comment          = $payment_method['comment'];
            $this->cart             = $payment_method['cart'];
        }

        $this->payment_method  = $this->resolveMethod('payment', $payment_method);
        $this->shipping_method = $this->resolveMethod('shipping', $shipping_method);
    }


    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->order_id;
    }


    /**
     * @param int|null $order_id
     *
     * @return $this
     */
    public function setOrder(int $order_id = null): self
    {
        if ($order_id) {
            $this->order_id = $order_id;
        }

        $this->order = Order::query()->find($this->order_id);

        return $this;
    }


    /**
     * @return $this
     */
    public function recordUnfinishedOrder(): self
    {
        if ($this->methodsNotSet()) {
            return $this;
        }

        $user_id = auth()->user() ? auth()->id() : 0;

        $this->order_id = Order::insertGetId([
            'user_id'             => $user_id,
            'affiliate_id'        => 0,
            'order_status_id'     => config('order.status.unfinished'),
            'invoice'             => '',
            'total'               => $this->cart['total'],
            'payment_fname'       => $this->customer_info['fname'],
            'payment_lname'       => $this->customer_info['lname'],
            'payment_address'     => $this->customer_info['address'],
            'payment_zip'         => $this->customer_info['zip'],
            'payment_city'        => $this->customer_info['city'],
            'payment_state'       => 'Croatia',
            'payment_phone'       => $this->customer_info['phone'] ?: null,
            'payment_email'       => $this->customer_info['email'],
            'payment_method'      => $this->payment_method->title,
            'payment_code'        => $this->payment_method->code,
            'payment_card'        => '',
            'payment_installment' => '',
            'shipping_fname'      => $this->customer_info['fname'],
            'shipping_lname'      => $this->customer_info['lname'],
            'shipping_address'    => $this->customer_info['address'],
            'shipping_zip'        => $this->customer_info['zip'],
            'shipping_city'       => $this->customer_info['city'],
            'shipping_state'      => 'Croatia',
            'shipping_phone'      => $this->customer_info['phone'] ?: null,
            'shipping_email'      => $this->customer_info['email'],
            'shipping_method'     => $this->shipping_method->title,
            'shipping_code'       => $this->shipping_method->code,
            'company'             => $this->customer_info['company'] ?: null,
            'oib'                 => $this->customer_info['oib'] ?: null,
            'comment'             => $this->comment,
            'created_at'          => now(),
            'updated_at'          => now()
        ]);

        if ($this->order_id) {
            $this->order = Order::query()->find($this->order_id);
            // HISTORY
            OrderHistory::insert([
                'order_id'   => $this->order_id,
                'user_id'    => $user_id,
                'comment'    => config('settings.order.made_text'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Order products
            $this->updateProducts();
            // Order totals
            $this->updateTotals();
        }

        return $this;
    }


    /**
     * @return false|mixed|null
     */
    public function resolvePaymentForm()
    {
        if ($this->isCreated()) {
            $method = new PaymentMethod($this->payment_method->code);

            return $method->resolveForm($this->order);
        }

        return false;
    }


    /**
     * @param Request $request
     *
     * @return $this
     */
    public function resolveResponseBody(Request $request)
    {
        $this->request = $request;

        // Bank, Cod, Pickup
        if ($this->request->has('provjera')) {
            $this->order_id = $this->request->input('provjera');
        }

        // Corvus
        if ($this->request->has('order_number')) {
            $this->order_id = $this->request->input('order_number');
        }

        return $this;
    }


    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        if ($this->isCreated() && $this->request) {
            $method = new PaymentMethod($this->order->payment_code);

            return $method->finish($this->order, $this->request);
        }

        return false;
    }


    /**
     * @return void
     */
    public function flush(): void
    {
        CartSession::resolve()->flush();

        session()->forget('selected_reservation');
        session()->forget('selected_shipping');
        session()->forget('customer_info');
        session()->forget('order_id');
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @return bool
     */
    private function isCreated(): bool
    {
        if ($this->order) {
            return true;
        }

        return false;
    }


    /**
     * @param int $order_id
     *
     * @return bool
     */
    private function updateProducts(int $order_id = 0): bool
    {
        if ( ! $order_id) {
            $order_id = $this->order_id;
        }

        OrderProduct::where('order_id', $order_id)->delete();

        foreach ($this->cart['items'] as $item) {
            $discount = 0;

            if ( ! empty($item->conditions)) {
                $amount = 0;

                foreach ($item->conditions as $condition) {
                    $amount += $condition->parsedRawValue;
                }

                $discount = Helper::calculateDiscount($item->attributes->org_price, ($item->attributes->org_price - $amount));
            }

            $inserted = OrderProduct::query()->insertGetId([
                'order_id'   => $order_id,
                'product_id' => $item->id,
                'name'       => $item->name,
                'quantity'   => $item->quantity,
                'org_price'  => $item->attributes->org_price,
                'discount'   => $discount ? number_format($discount, 2) : 0,
                'price'      => $item->price,
                'total'      => $item->quantity * $item->price,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ( ! $inserted) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param int $order_id
     *
     * @return bool
     */
    private function updateTotals(int $order_id = 0): bool
    {
        if ( ! $order_id) {
            $order_id = $this->order_id;
        }

        OrderTotal::where('order_id', $order_id)->delete();
        $sort_order = 1;

        // SUBTOTAL
        $sub = OrderTotal::query()->insertGetId([
            'order_id'   => $order_id,
            'code'       => 'subtotal',
            'title'      => 'Ukupno',
            'value'      => $this->cart['subtotal'],
            'sort_order' => $sort_order,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $sort_order++;

        // CONDITIONS on Total
        foreach ($this->cart['conditions'] as $name => $condition) {
            if ($condition->getType() == 'payment') {
                OrderTotal::query()->insertGetId([
                    'order_id'   => $order_id,
                    'code'       => $condition->getType(),
                    'title'      => $name,
                    'value'      => $condition->parsedRawValue,
                    'sort_order' => $sort_order,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $sort_order++;
            }
        }

        // Shipping
        if (isset($this->shipping_method->data->price) && $this->shipping_method->data->price != '0') {
            OrderTotal::query()->insertGetId([
                'order_id'   => $order_id,
                'code'       => 'shipping',
                'title'      => $this->shipping_method->title,
                'value'      => $this->shipping_method->data->price,
                'sort_order' => $sort_order,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $sort_order++;
        }

        // Payment
        if (isset($this->payment_method->data->price) && $this->payment_method->data->price != '0') {
            OrderTotal::query()->insertGetId([
                'order_id'   => $order_id,
                'code'       => 'shipping',
                'title'      => $this->payment_method->title,
                'value'      => $this->payment_method->data->price,
                'sort_order' => $sort_order,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $sort_order++;
        }

        // TOTAL
        $tot = OrderTotal::query()->insertGetId([
            'order_id'   => $order_id,
            'code'       => 'total',
            'title'      => 'Sveukupno',
            'value'      => $this->cart['total'],
            'sort_order' => $sort_order,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ( ! $sub || ! $tot) {
            return false;
        }

        return true;
    }


    /**
     * @param string        $target
     * @param string|object $method
     *
     * @return object|mixed|string|null
     */
    private function resolveMethod(string $target, string|object $method): object|null
    {
        if (is_string($method)) {
            $method = Settings::get($target, 'list.' . $method)->first();

            if ( ! $method || ! isset($method->title)) {
                return null;
            }
        }

        return $method;
    }


    /**
     * @return bool
     */
    private function methodsNotSet(): bool
    {
        if ( ! isset($this->payment_method->title) || ! isset($this->shipping_method->title)) {
            return false;
        }

        return true;
    }
}
