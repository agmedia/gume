<!-- Delivery date and time offcanvas -->
<div class="offcanvas offcanvas-end pb-sm-2 px-sm-2" id="deliveryDateTime" tabindex="-1" aria-labelledby="deliveryDateTimeLabel" style="width: 500px" wire:ignore>

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
                    @foreach ($days as $day)
                        <div class="swiper-slide text-center">
                            <div class="text-center">
                                <div class="fs-sm pb-1 mb-2">{{ $day['title'] }}</div>
                                <input type="radio" class="btn-check" name="day" value="{{ $day['date'] }}" id="day-{{ $day['day'] }}" wire:model="selected_day">
                                <label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="day-{{ $day['day'] }}">{{ $day['day'] }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <button type="button" class="btn btn-icon btn-sm btn-outline-secodary me-n2" id="courierTimeNext" aria-label="Next">
                <i class="ci-chevron-right fs-lg"></i>
            </button>
        </div>
        <!-- Time -->
        @foreach ($hours as $hour)
            <div class="form-check border-bottom py-4 m-0">
                <input type="radio" class="form-check-input" id="time-{{ $hour['from'] }}" value="{{ $hour['from'] }} - {{ $hour['to'] }}" name="time" wire:model="selected_hour">
                <div class="d-flex w-100">
                    <label for="time-{{ $hour['from'] }}" class="form-check-label text-dark-emphasis fw-semibold me-3">{{ $hour['from'] }} - {{ $hour['to'] }}</label>
                    <span class="fs-sm ms-auto">Dostupno</span>
                </div>
            </div>
        @endforeach

    </div>

    <!-- Footer -->
    <div class="offcanvas-header" wire:ignore>
        <button type="button" class="btn btn-lg btn-primary w-100 rounded-pill" {{--data-bs-dismiss="offcanvas"--}} wire:click="createReservationSession()">Potvrdi termin</button>
    </div>

</div>