@extends('front.layouts.app')

@push('css_after')
@endpush

@section('content')
    <form action="{{ route('info-podaci') }}" method="GET" enctype="multipart/form-data" novalidate>
        {{--@csrf--}}

        @livewire('front.checkout.reservation-selection')

        <div class="container py-5">
            @include('front.layouts.partials.session')

            <div class="row pt-1 pt-sm-3 pt-lg-4 pb-2 pb-md-3 pb-lg-4 pb-xl-5">

                <!-- Delivery info (Step 1) -->
                <div class="col-lg-8 col-xl-7 mb-5 mb-lg-0">
                    <div class="d-flex flex-column gap-5 pe-lg-4 pe-xl-0">
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle fs-sm fw-semibold lh-1 flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">1</div>
                            <div class="flex-grow-0 flex-shrink-0 ps-3 ps-md-4" style="width: calc(100% - 2rem)">
                                <h1 class="h5 mb-md-4">Odabir montaže ili dostave</h1>
                                <div class="ms-n5 ms-sm-0">
                                    <p class="fs-sm mb-3">Za sve gume kupljene kod nas montaža je besplatna!</p>
                                    <div class="mb-lg-4" id="shippingMethod" role="list">

                                        @foreach ($shipping_methods as $method)
                                            <div class="border-bottom">
                                                <div class="form-check mb-4" role="listitem">
                                                    <label class="form-check-label d-flex align-items-center text-dark-emphasis fw-semibold pt-3">
                                                        <input type="radio" class="form-check-input fs-base me-2 me-sm-3" name="shipping_method" onclick="shippingChange(this);" value="{{ $method->code }}">
                                                        {{ $method->title }}
                                                        @if ($method->code == 'pickup')
                                                            <div class="ms-auto">
                                                                <label class="btn btn-dark" for="other-date" data-bs-toggle="offcanvas" data-bs-target="#deliveryDateTime" aria-controls="deliveryDateTime"><i class="ci-schedule me-2"></i> Odaberite slobodan termin</label>
                                                            </div>
                                                        @endif
                                                        <span class="fw-normal ms-auto">{{ $method->data->price != '0' ? price($method->data->price, true) : '' }}</span>
                                                    </label>
                                                    <p class="fw-lighter fs-sm" style="margin-left: 9px;">{{ $method->data->short_description }}</p>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                    <button class="btn btn-lg btn-primary w-100 d-none d-lg-flex" type="submit">
                                        Nastavi dalje
                                        <i class="ci-chevron-right fs-lg ms-1 me-n1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping address -->
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center justify-content-center bg-body-secondary text-body-secondary rounded-circle fs-sm fw-semibold lh-1 flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">2</div>
                            <h2 class="h5 text-body-secondary ps-3 ps-md-4 mb-0">Vaši podaci</h2>
                        </div>

                        <!-- Payment -->
                        <div class="d-flex align-items-start">
                            <div class="d-flex align-items-center justify-content-center bg-body-secondary text-body-secondary rounded-circle fs-sm fw-semibold lh-1 flex-shrink-0" style="width: 2rem; height: 2rem; margin-top: -.125rem">3</div>
                            <h2 class="h5 text-body-secondary ps-3 ps-md-4 mb-0">Način plaćanja</h2>
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
        function shippingChange(radio) {
            console.log(radio.value);
        }
    </script>
@endpush
