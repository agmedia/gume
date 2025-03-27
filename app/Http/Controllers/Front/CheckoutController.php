<?php

namespace App\Http\Controllers\Front;

use App\Helpers\OrderHelper;
use App\Http\Controllers\FrontController;
use App\Models\Back\Settings\Settings;
use App\Models\Front\Cart\CartSession;
use App\Models\Front\Checkout\Checkout;
use App\Models\Front\Checkout\PaymentMethod;
use App\Models\Front\Checkout\Reservation;
use App\Models\Front\Checkout\ShippingMethod;
use App\Models\TagManager;
use Illuminate\Http\Request;

/**
 *
 */
class CheckoutController extends FrontController
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function cart(Request $request)
    {
        return view('front.checkout.cart');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function coupon(Request $request)
    {
        $request->validate([
            'coupon' => 'required',
        ]);

        session()->put(config('session.cart') . '_coupon', $request->input('coupon'));

        if (session()->has(config('session.cart') . '_coupon')) {
            CartSession::resolve()->setCoupon($request->input('coupon'));

            return back()->with('success', 'Kupon je uspješno dodan u košaricu.');
        }

        return back()->with('error', 'Greška sa ubacivanjem kupona u košaricu.');
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkoutShipping(Request $request)
    {
        if ($this->isCartEmpty()) {
            return redirect()->route('kosarica');
        }

        $ship             = new ShippingMethod();
        $shipping_methods = $ship->findGeo(1)->sortBy('sort_order');

        //dd(Reservation::getHoursList());

        return view('front.checkout.checkout-shipping', compact('shipping_methods'));
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function customerInfoData(Request $request)
    {
        if ($this->isCartEmpty()) {
            return redirect()->route('kosarica');
        }

        if ( ! session()->has('selected_shipping')) {
            $request->validate([
                'shipping_method' => 'required',
            ]);
        }

        $selected_reservation = null;
        $selected_shipping    = session()->get('selected_shipping');

        if (session()->has('selected_reservation')) {
            $selected_reservation = session()->get('selected_reservation');
        }

        if ($request->has('day') && $request->has('time')) {
            session()->put('selected_reservation', [
                'day'  => $request->input('day'),
                'hour' => $request->input('time'),
            ]);
        }

        if ($request->has('shipping_method')) {
            $selected_shipping = Settings::get('shipping', 'list.' . $request->input('shipping_method'))->first();
            session()->put('selected_shipping', $selected_shipping);
        }

        $user = $this->resolveUser();

        return view('front.checkout.checkout-customer-info', compact('selected_shipping', 'selected_reservation', 'user'));
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function checkoutPayment(Request $request)
    {
        if ($this->isCartEmpty()) {
            return redirect()->route('kosarica');
        }

        if ( ! session()->has('customer_info')) {
            $request->validate([
                'fname'   => 'required',
                'lname'   => 'required',
                'email'   => 'required|email',
                'phone'   => 'required',
                'city'    => 'required',
                'zip'     => 'required',
                'address' => 'required'
            ]);
        }

        if ( ! session()->has('selected_shipping')) {
            return redirect()->route('kosarica');
        }

        if ( ! session()->has('customer_info')) {
            session()->put('customer_info', $request->all());
        }

        //
        $selected_reservation = session()->get('selected_reservation');
        $selected_shipping    = session()->get('selected_shipping');
        $user                 = $this->resolveUser();
        $payment_methods      = (new PaymentMethod())->findGeo(1)->resolve()->sortBy('sort_order');

        //dd($payment_methods);

        return view('front.checkout.checkout-payment', compact('selected_shipping', 'selected_reservation', 'user', 'payment_methods'));
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function checkoutFinalView(Request $request)
    {
        if ($this->isCartEmpty()) {
            return redirect()->route('kosarica');
        }

        $request->validate([
            'payment_method'   => 'required',
            'terms_conditions' => 'required',
        ]);

        if ( ! session()->has('selected_shipping') || ! session()->has('customer_info')) {
            return redirect()->route('kosarica');
        }

        $selected_reservation = session()->get('selected_reservation');
        $selected_shipping    = session()->get('selected_shipping');
        $user                 = $this->resolveUser();
        $selected_payment     = Settings::get('payment', 'list.' . $request->input('payment_method'))->first();
        $cart                 = CartSession::resolve()->get();

        $checkout = new Checkout($selected_payment, $selected_shipping, $user, $selected_reservation, $request->input('comment'), $cart);

        $payment_form = $checkout->recordUnfinishedOrder()
                                 ->resolvePaymentForm();

        //dd($request->all(), session()->all(), $payment_form);

        if ( ! $payment_form) {
            return redirect()->route('kosarica');
        }

        session()->put('order_id', $checkout->getOrderId());

        //dd($selected_payment);

        return view('front.checkout.checkout-final-view', compact('selected_shipping', 'selected_reservation', 'user', 'selected_payment', 'cart', 'payment_form'));
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function checkoutResponseVerification(Request $request)
    {
        $checkout = new Checkout();

        $checkout->resolveResponseBody($request)->setOrder();

        if ($checkout->isApproved()) {
            $order = OrderHelper::get($checkout->getOrderId());

            if ($order->isValid()) {
                $data['order'] = $order->sendEmails()
                                       ->decreaseCartItems()
                                       ->getOrder();

                $checkout->flush();

                return view('front.checkout.success', compact('data'));
            }
        }

        return view('front.checkout.error');
    }

    /*******************************************************************************
     *                                Copyright : AGmedia                           *
     *                              email: filip@agmedia.hr                         *
     *******************************************************************************/

    /**
     * @return bool
     */
    private function isCartEmpty(): bool
    {
        $cart = CartSession::resolve()->get();

        if ( ! $cart['count']) {
            return true;
        }

        return false;
    }


    /**
     * @return array|string[]
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function resolveUser(): array
    {
        if (session()->has('customer_info')) {
            return [
                'fname'   => session()->get('customer_info.fname'),
                'lname'   => session()->get('customer_info.lname'),
                'email'   => session()->get('customer_info.email'),
                'phone'   => session()->get('customer_info.phone'),
                'city'    => session()->get('customer_info.city'),
                'zip'     => session()->get('customer_info.zip'),
                'address' => session()->get('customer_info.address'),
                'company' => '',
                'oib'     => ''
            ];
        }

        if (auth()->check()) {
            $user = auth()->user();

            return [
                'fname'   => $user->details->fname,
                'lname'   => $user->details->lname,
                'email'   => $user->email,
                'phone'   => $user->details->phone,
                'city'    => $user->details->city,
                'zip'     => $user->details->zip,
                'address' => $user->details->address,
                'company' => '',
                'oib'     => ''
            ];
        }

        return [
            'fname'   => '',
            'lname'   => '',
            'email'   => '',
            'phone'   => '',
            'city'    => '',
            'zip'     => '',
            'address' => '',
            'company' => '',
            'oib'     => ''
        ];
    }

}
