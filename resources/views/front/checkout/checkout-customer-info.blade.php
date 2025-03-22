@extends('front.layouts.app')

@push('css_after')
@endpush

@section('content')

    <form action="{{ route('naplata') }}" method="GET" enctype="multipart/form-data">
        {{--@csrf--}}

        <div class="container py-5">
            @include('front.layouts.partials.session')

            <div class="row pt-1 pt-sm-3 pt-lg-4 pb-2 pb-md-3 pb-lg-4 pb-xl-5">
                <div class="col-lg-8 col-xl-7 mb-5 mb-lg-0">
                    <div class="accordion d-flex flex-column gap-5 pe-lg-4 pe-xl-0" id="checkout">

                        <!-- Delivery info overview + Edit button -->
                        <div class="accordion-item d-flex align-items-start border-0">
                            <div class="d-flex align-items-center justify-content-center bg-body-secondary text-dark-emphasis rounded-circle flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">
                                <i class="ci-check fs-base"></i>
                            </div>
                            @include('front.checkout.partials.selected-shipping')
                        </div>

                        <!-- Shipping address form -->
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle fs-sm fw-semibold lh-1 flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">2</div>
                            <div class="w-100 ps-3 ps-md-4">
                                <h1 class="h5 mb-md-4">Vaši podaci</h1>
                                <form class="needs-validation" novalidate>
                                    <div class="row row-cols-1 row-cols-sm-2 g-3 g-sm-4 mb-4">
                                        <div class="col">
                                            <label for="shipping-fn" class="form-label">Ime <span class="text-danger">*</span></label>
                                            <input type="text" name="fname" value="{{ auth()->user() ? auth()->user()->details->fname : old('fname') }}" class="form-control form-control-lg" id="shipping-fn" required>
                                        </div>
                                        <div class="col">
                                            <label for="shipping-ln" class="form-label">Prezime <span class="text-danger">*</span></label>
                                            <input type="text" name="lname" value="{{ auth()->user() ? auth()->user()->details->lname : old('lname') }}" class="form-control form-control-lg" id="shipping-ln" required>
                                        </div>
                                        <div class="col">
                                            <label for="shipping-email" class="form-label">Email adresa <span class="text-danger">*</span></label>
                                            <input type="email" name="email" value="{{ auth()->user() ? auth()->user()->email : old('email') }}" class="form-control form-control-lg" id="shipping-email" required>
                                        </div>
                                        <div class="col">
                                            <label for="shipping-mobile" class="form-label">Broj mobitela</label>
                                            <input type="text" name="phone" value="{{ auth()->user() ? auth()->user()->details->phone : old('phone') }}" class="form-control form-control-lg" id="shipping-mobile">
                                        </div>
                                        <div class="col">
                                            <label class="form-label">Grad <span class="text-danger">*</span></label>
                                            <input type="text" name="city" value="{{ auth()->user() ? auth()->user()->details->city : old('city') }}" class="form-control form-control-lg" id="shipping-city" required>
                                        </div>
                                        <div class="col">
                                            <label for="shipping-postcode" class="form-label">Poštanski broj <span class="text-danger">*</span></label>
                                            <input type="text" name="zip" value="{{ auth()->user() ? auth()->user()->details->zip : old('zip') }}" class="form-control form-control-lg" id="shipping-postcode" required>
                                        </div>
                                        <input type="hidden" name="company">
                                        <input type="hidden" name="oib">
                                    </div>
                                    <div class="mb-3">
                                        <label for="shipping-address" class="form-label">Adresa <span class="text-danger">*</span></label>
                                        <input type="text" name="address" value="{{ auth()->user() ? auth()->user()->details->address : old('address') }}" class="form-control form-control-lg" id="shipping-address" required>
                                    </div>

                                    <button class="btn btn-lg btn-primary w-100 d-none d-lg-flex" type="submit">
                                        Nastavi dalje
                                        <i class="ci-chevron-right fs-lg ms-1 me-n1"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Payment -->
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center justify-content-center bg-body-secondary text-body-secondary rounded-circle fs-sm fw-semibold lh-1 flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">3</div>
                            <h2 class="h5 text-body-secondary ps-3 ps-md-4 mb-0">Plaćanje</h2>
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
@endpush
