@extends('front.layouts.app')

@push('css_after')
@endpush

@section('content')
    <form action="{{ route('checkout') }}" method="GET" enctype="multipart/form-data" novalidate>
        {{--@csrf--}}

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

                        <!-- Payment method -->
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle fs-sm fw-semibold lh-1 flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">3</div>
                            <div class="w-100 ps-3 ps-md-4">
                                <h2 class="h5 mb-0">Način plaćanja</h2>
                                <div class="mb-4" id="paymentMethod" role="list">
                                    <!-- Payment methods -->
                                    @foreach ($payment_methods as $method)
                                        <div class="mt-4">
                                            <div class="form-check mb-0" role="listitem">
                                                <label class="form-check-label w-100 text-dark-emphasis fw-semibold">
                                                    <input type="radio" class="form-check-input fs-base me-2 me-sm-3" name="payment_method" onclick="paymentChange(this);" value="{{ $method->code }}"
                                                           @if( ! empty(session()->get('selected_payment')) && session()->get('selected_payment')->code == $method->code) checked @endif>
                                                    {{ $method->title }}
                                                    <span class="fw-normal ms-auto">{{ ($method->data->price) ? price($method->data->price, true) : '' }}</span>
                                                </label>
                                                <p class="fw-lighter fs-sm" style="margin-left: 9px;">{{ $method->data->short_description }}</p>
                                            </div>
                                        </div>
                                    @endforeach

                                    @error('payment_method')
                                    <div class="alert fs-sm alert-danger d-flex" role="alert">
                                        <div class="alert-icon me-2">
                                            <i class="ci-close-circle"></i>
                                        </div>
                                        <div>Greška..! Morate odabrati način plaćanja</div>
                                    </div>
                                    @enderror
                                </div>

                                <!-- Additional comments -->
                                <textarea class="form-control form-control-lg mb-4" name="comment" rows="3" placeholder="Dodatni komentar"></textarea>

                                <div class="form-check mb-lg-4">
                                    <input type="checkbox" class="form-check-input" id="accept-terms" name="terms_conditions" >


                                    <label for="accept-terms" class="form-check-label nav align-items-center">
                                        Slažem se sa
                                        <a class="nav-link text-decoration-underline fw-normal ms-1 p-0" href="{{ route('catalog.route.page', ['page' => 'uvjeti-prodaje']) }}">Općim uvjetima</a>
                                    </label>

                                </div>
                                @error('terms_conditions')
                                    <div class="alert fs-sm alert-danger d-flex" role="alert">
                                        <div class="alert-icon me-2">
                                            <i class="ci-close-circle"></i>
                                        </div>
                                        <div>Greška..! Morate se složiti s općim uvjetima</div>
                                    </div>
                                @enderror
                                <!-- Pay button visible on screens > 991px wide (lg breakpoint) -->
                                <button type="submit" class="btn btn-lg btn-primary w-100 d-flex">Provjeri narudžbu</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart view aside -->
                @livewire('front.checkout.cart-view-aside')

            </div>
        </div>

    </form>

@endsection

@push('js_after')
    <script>
        function paymentChange(radio) {
            Livewire.emit('paymentUpdated', radio.value);
        }
    </script>
@endpush
