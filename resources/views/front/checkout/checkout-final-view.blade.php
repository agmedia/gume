@extends('front.layouts.app')

@push('css_after')
@endpush

@section('content')

    @include('front.layouts.partials.session')

    <div class="container py-5">
        <div class="row pt-1 pt-sm-3 pt-lg-4 pb-2 pb-md-3 pb-lg-4 pb-xl-5">

            <div class="col-lg-8 col-xl-7 position-relative z-2 mb-5 mb-lg-0">


                <div class="accordion d-flex flex-column gap-5 pe-lg-4 pe-xl-0" id="checkout">
                    <!-- Delivery info overview + Edit button -->
                    <div class="accordion-item d-flex align-items-start border-0">
                        <div class="d-flex align-items-center justify-content-center bg-body-secondary text-dark-emphasis rounded-circle flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">
                            <i class="ci-check fs-base"></i>
                        </div>
                        @include('front.checkout.partials.selected-shipping')
                    </div>

                    <!-- Shipping address overview + Edit button -->
                    <div class="accordion-item d-flex align-items-start border-0">
                        <div class="d-flex align-items-center justify-content-center bg-body-secondary text-dark-emphasis rounded-circle flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">
                            <i class="ci-check fs-base"></i>
                        </div>
                        <div class="w-100 ps-3 ps-md-4">
                            <div class="d-flex align-items-center">
                                <h2 class="accordion-header h5 mb-0 me-3" id="shippingAddressHeading">
                                    <span class="d-none d-lg-inline">Vaši podaci</span>
                                    <button type="button" class="accordion-button collapsed fs-5 d-lg-none py-1" data-bs-toggle="collapse" data-bs-target="#shippingAddress" aria-expanded="false" aria-controls="shippingAddress">
                                        <span class="me-2">Vaši podaci</span>
                                    </button>
                                </h2>
                                <div class="nav ms-auto">
                                    <a class="nav-link text-decoration-underline p-0" href="{{ route('info-podaci') }}">Uredi</a>
                                </div>
                            </div>
                            <div class="accordion-collapse collapse d-lg-block" id="shippingAddress" aria-labelledby="shippingAddressHeading" data-bs-parent="#checkout">
                                <ul class="accordion-body list-unstyled fs-sm p-0 pt-3 pt-md-4 mb-0">
                                    <li>{{ $user['fname'] }} {{ $user['lname'] }}</li>
                                    <li>{{ $user['email'] }}</li>
                                    <li>{{ $user['phone'] }}</li>
                                    <li>{{ $user['address'] }}</li>
                                    <li>{{ $user['zip'] }} {{ $user['city'] }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Payment method overview + Edit button -->
                    <div class="accordion-item d-flex align-items-start border-0">
                        <div class="d-flex align-items-center justify-content-center bg-body-secondary text-dark-emphasis rounded-circle flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">
                            <i class="ci-check fs-base"></i>
                        </div>
                        <div class="w-100 ps-3 ps-md-4">
                            <div class="d-flex align-items-center">
                                <h2 class="accordion-header h5 mb-0 me-3" id="paymentMethodHeading">
                                    <span class="d-none d-lg-inline">Odabir plaćanja</span>
                                    <button type="button" class="accordion-button collapsed fs-5 d-lg-none py-1" data-bs-toggle="collapse" data-bs-target="#paymentMethod" aria-expanded="false" aria-controls="shippingAddress">
                                        <span class="me-2">Odabir plaćanja</span>
                                    </button>
                                </h2>
                                <div class="nav ms-auto">
                                    <a class="nav-link text-decoration-underline p-0" href="{{ route('naplata') }}">Uredi</a>
                                </div>
                            </div>
                            <div class="accordion-collapse collapse d-lg-block" id="shippingAddress" aria-labelledby="shippingAddressHeading" data-bs-parent="#checkout">
                                <ul class="accordion-body list-unstyled fs-sm p-0 pt-3 pt-md-4 mb-5">
                                    <li><strong>{{ $selected_payment->title }}</strong> </li>
                                    <li>{{ $selected_payment->data->short_description}}</li>

                                </ul>
                            </div>
                        </div>
                    </div>


                </div>

                {!! $payment_form !!}
            </div>

            <!-- Cart view aside -->
             @livewire('front.checkout.cart-view-aside')


                {{-- @dump($user)
                @dump($selected_shipping)
                @dump($selected_payment)
                @dump($selected_reservation)
                @dump($cart)--}}




        </div>
    </div>

@endsection

@push('js_after')
@endpush
