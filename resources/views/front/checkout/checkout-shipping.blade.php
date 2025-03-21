@extends('front.layouts.app')

@push('css_after')
@endpush

@section('content')



        <!-- Delivery date and time offcanvas -->
        <div class="offcanvas offcanvas-end pb-sm-2 px-sm-2" id="deliveryDateTime" tabindex="-1" aria-labelledby="deliveryDateTimeLabel" style="width: 500px">
            <form action="{{ route('info-podaci') }}" method="POST" enctype="multipart/form-data">
                @csrf
            <!-- Header with nav tabs -->
            <div class="offcanvas-header py-3 pt-lg-4">
                <h4 class="offcanvas-title" id="deliveryDateTimeLabel">Odaberite termin montaže</h4>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Zatvori"></button>
            </div>

            <!-- Body -->
            <div class="offcanvas-body py-3">

                <div class="d-flex justify-content-between gap-3 overflow-auto pb-3">
                    <button type="button" class="btn btn-icon btn-sm btn-outline-secodary ms-n2" id="courierTimePrev" aria-label="Prev">
                        <i class="ci-chevron-left fs-lg"></i>
                    </button>
                    <div class="swiper swiper-load pt-2" data-swiper='{
                                "slidesPerView": 4,
                                "spaceBetween": 14,
                                "navigation": {
                                  "prevEl": "#courierTimePrev",
                                  "nextEl": "#courierTimeNext"
                                },
                                "breakpoints": {
                                  "600": {
                                    "slidesPerView": 4,
                                    "spaceBetween": 12
                                  },
                                  "768": {
                                    "slidesPerView": 4,
                                    "spaceBetween": 12
                                  },
                                  "991": {
                                    "slidesPerView": 4,
                                    "spaceBetween": 12
                                  },
                                  "1100": {
                                    "slidesPerView": 5,
                                    "spaceBetween": 12
                                  },
                                  "1250": {
                                    "slidesPerView": 6,
                                    "spaceBetween": 12
                                  }
                                }
                              }'>
                        <div class="swiper-wrapper">
                            <div class="swiper-slide text-center">
                                <div class="text-center">
                                    <div class="fs-sm pb-1 mb-2">Pon</div>
                                    <input type="radio" class="btn-check" name="day" id="mon">
                                    <label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="mon">24</label>
                                </div>

                            </div>
                            <div class="swiper-slide text-center">
                                <div class="text-center">
                                    <div class="fs-sm pb-1 mb-2">Uto</div>
                                    <input type="radio" class="btn-check" name="day" id="tue">
                                    <label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="tue">25</label>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="text-center">
                                    <div class="fs-sm pb-1 mb-2">Sri</div>
                                    <input type="radio" class="btn-check" name="day" id="wed" checked>
                                    <label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="wed">26</label>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="text-center">
                                    <div class="fs-sm pb-1 mb-2">Čet</div>
                                    <input type="radio" class="btn-check" name="day" id="thu">
                                    <label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="thu">27</label>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="text-center">
                                    <div class="fs-sm pb-1 mb-2">Pet</div>
                                    <input type="radio" class="btn-check" name="day" id="fri">
                                    <label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="fri">28</label>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="text-center">
                                    <div class="fs-sm pb-1 mb-2">Sub</div>
                                    <input type="radio" class="btn-check" name="day" id="sat">
                                    <label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="sat">29</label>
                                </div>
                            </div>
                            <div class="swiper-slide text-center">
                                <div class="text-center">
                                    <div class="fs-sm pb-1 mb-2">Ned</div>
                                    <input type="radio" class="btn-check" name="day" id="sun">
                                    <label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="sun">30</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-icon btn-sm btn-outline-secodary me-n2" id="courierTimeNext" aria-label="Next">
                        <i class="ci-chevron-right fs-lg"></i>
                    </button>
                </div>
                <!-- Time -->
                <div class="form-check border-bottom py-4 m-0">
                    <input type="radio" class="form-check-input" id="delivery-time-1" name="delivery-time" disabled>
                    <div class="d-flex w-100">
                        <label for="delivery-time-1" class="form-check-label text-dark-emphasis fw-semibold me-3"><s>08:00 - 09:00</s></label>
                        <span class="fs-sm ms-auto">Zauzeto</span>
                    </div>
                </div>
                <div class="form-check border-bottom py-4 m-0">
                    <input type="radio" class="form-check-input" id="delivery-time-2" name="delivery-time" checked>
                    <div class="d-flex w-100">
                        <label for="delivery-time-2" class="form-check-label text-dark-emphasis fw-semibold me-3">09:00 - 10:00</label>
                        <span class="fs-sm ms-auto">Dostupno</span>
                    </div>
                </div>
                <div class="form-check border-bottom py-4 m-0">
                    <input type="radio" class="form-check-input" id="delivery-time-3" name="delivery-time">
                    <div class="d-flex w-100">
                        <label for="delivery-time-3" class="form-check-label text-dark-emphasis fw-semibold me-3">10:00 - 11:00</label>
                        <span class="fs-sm ms-auto">Dostupno</span>
                    </div>
                </div>
                <div class="form-check border-bottom py-4 m-0">
                    <input type="radio" class="form-check-input" id="delivery-time-4" name="delivery-time">
                    <div class="d-flex w-100">
                        <label for="delivery-time-4" class="form-check-label text-dark-emphasis fw-semibold me-3">11:00 - 12:00</label>
                        <span class="fs-sm ms-auto">Dostupno</span>
                    </div>
                </div>
                <div class="form-check border-bottom py-4 m-0">
                    <input type="radio" class="form-check-input" id="delivery-time-5" name="delivery-time">
                    <div class="d-flex w-100">
                        <label for="delivery-time-5" class="form-check-label text-dark-emphasis fw-semibold me-3">12:00 - 13:00</label>
                        <span class="fs-sm ms-auto">Dostupno</span>
                    </div>
                </div>
                <div class="form-check border-bottom py-4 m-0">
                    <input type="radio" class="form-check-input" id="delivery-time-6" name="delivery-time">
                    <div class="d-flex w-100">
                        <label for="delivery-time-6" class="form-check-label text-dark-emphasis fw-semibold me-3">13:00 - 14:00</label>
                        <span class="fs-sm ms-auto">Dostupno</span>
                    </div>
                </div>
                <div class="form-check border-bottom py-4 m-0">
                    <input type="radio" class="form-check-input" id="delivery-time-7" name="delivery-time">
                    <div class="d-flex w-100">
                        <label for="delivery-time-7" class="form-check-label text-dark-emphasis fw-semibold me-3">14:00 - 15:00</label>
                        <span class="fs-sm ms-auto">Dostupno</span>
                    </div>
                </div>
                <div class="form-check border-bottom py-4 m-0">
                    <input type="radio" class="form-check-input" id="delivery-time-8" name="delivery-time">
                    <div class="d-flex w-100">
                        <label for="delivery-time-8" class="form-check-label text-dark-emphasis fw-semibold me-3">15:00 - 16:00</label>
                        <span class="fs-sm ms-auto">Dostupno</span>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="offcanvas-header">
                <button type="submit" class="btn btn-lg btn-primary w-100 rounded-pill" data-bs-dismiss="offcanvas">Potvrdi termin</button>
            </div>
            </form>
        </div>

        <form action="{{ route('info-podaci') }}" method="POST" enctype="multipart/form-data">
            @csrf

        <div class="container py-5">
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
                                                        <input type="radio" class="form-check-input fs-base me-2 me-sm-3" name="payment-method" id="{{ $method->code }}">
                                                        {{ $method->title }}
                                                        @if ($method->code == 'pickup')
                                                            <div class="ms-auto">
                                                                <label class="btn btn-dark " for="other-date" data-bs-toggle="offcanvas" data-bs-target="#deliveryDateTime" aria-controls="deliveryDateTime"><i class="ci-schedule me-2"></i> Odaberite slobodan termin</label>
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
                <!--@livewire('front.checkout.cart-view-aside')-->
            </div>
        </div>

        </form>

@endsection

@push('js_after')
@endpush
