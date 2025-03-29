<!-- Delivery date and time offcanvas -->
<div class="offcanvas offcanvas-end pb-sm-2 px-sm-2" id="deliveryDateTime" tabindex="-1" aria-labelledby="deliveryDateTimeLabel" style="width: 500px">

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
                <div class="swiper-wrapper" id="days-swiper">
                    <!-- foreach days -->
                </div>
            </div>
            <button type="button" class="btn btn-icon btn-sm btn-outline-secodary me-n2" id="courierTimeNext" aria-label="Next">
                <i class="ci-chevron-right fs-lg"></i>
            </button>
        </div>
        <!-- Time -->
        <div class="row" id="hours-selector">
            <!-- foreach hours -->
        </div>
    </div>

    <!-- Footer -->
    <div class="offcanvas-header">
        <button onclick="confirmReservation()" type="button" class="btn btn-lg btn-primary w-100 rounded-pill" data-bs-dismiss="offcanvas">Potvrdi termin</button>
    </div>

</div>

<!-- Reservation confirmation toast -->
<div class="toast-container top-0 end-0 p-3">
    <div class="toast text-bg-success border-0 fade mb-3" id="confirm-reservation-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-theme="dark">
        <div class="d-flex">
            <div class="toast-body me-2">
                Vaša rezervacija je potvrđena.
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

@push('js_after')
    <script src="https://cdn.jsdelivr.net/npm/axios@1.8.1/dist/axios.min.js"></script>

    <script>
        window.addEventListener('load', (e) => {
            axios.get('{{ route('api.reservations.days') }}')
            .then((response) => {
                let days_html = '';

                response.data.forEach((item) => {
                    let date = "'" + item.date + "'";

                    days_html += '<div class="swiper-slide text-center"><div class="text-center">';
                    days_html += '<div class="fs-sm pb-1 mb-2">' + item.title + '</div>';
                    days_html += '<input type="radio" class="btn-check" name="day" value="' + item.date + '" id="day-' + item.day + '" onclick="getHoursList(' + date + ');">';
                    days_html += '<label class="btn btn-icon btn-lg btn-outline-secondary fs-sm rounded-circle" for="day-' + item.day + '">' + item.day + '</label>';
                    days_html += '</div></div>';
                });

                document.getElementById('days-swiper').innerHTML = days_html;
            })
            .catch((error) => {
                console.log(error);
            });
        });

        /**
         *
         * @param date
         */
        function getHoursList(date) {
            var datum = new Intl.DateTimeFormat('hr-HR').format(new Date(date));

            axios.get('{{ route('api.reservations.hours') }}' + '?day=' + date)
            .then((response) => {
                let hours_html = '';
                let count = 1;

                response.data.forEach((item) => {
                    let color = '';
                    let strike = false;

                    if (!item.available) {
                        color = 'text-danger-emphasis';
                        strike = true;
                    }

                    hours_html += '<div class="form-check text-center col-6 border-bottom py-4 m-0">';
                    hours_html += '<label for="time-' + count + '" class="form-check-label fw-semibold me-0 ' + color + '">';

                    if (strike) { hours_html += '<s>' }

                    hours_html += item.from + '-' + item.to + ' <input type="radio" class="form-check-input" id="time-' + count + '" value="' + item.from + '-' + item.to + '" name="time">';

                    if (strike) { hours_html += '</s>' }

                    hours_html += '</label></div>';

                    count++;
                });

                if (response.data.length == 0) {
                    hours_html += '<div class="text-center col-12 border-bottom py-5 m-0">';
                    hours_html += '<h4 class="fw-light me-0">' + datum + ' ne radimo.</h4>';
                    hours_html += '</div>';
                }

                document.getElementById('hours-selector').innerHTML = hours_html;

            })
            .catch((error) => {
                console.log(error);
            });
        }

        /**
         *
         */
        function confirmReservation() {
            let day = document.querySelector('input[name="day"]:checked').value;
            let time = document.querySelector('input[name="time"]:checked').value;

            let data = {
                day: day,
                time: time
            };

            axios.post('{{ route('api.reservations.set') }}', data)
            .then((response) => {
                if (response.data.success) {
                    let toast = document.getElementById('confirm-reservation-toast');

                    if (toast) {
                        bootstrap.Toast.getOrCreateInstance(toast).show();
                    }
                }
            })
            .catch((error) => {
                console.log(error);
            });
        }
    </script>

@endpush
