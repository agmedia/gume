<div class="w-100 ps-3 ps-md-4">
    <div class="d-flex align-items-center">
        <h2 class="accordion-header h5 mb-0 me-3" id="deliveryInfoHeading">
            @if ($selected_reservation)
                <span class="d-none d-lg-inline">Datum montaže</span>
            @else
                <span class="d-none d-lg-inline">Odabrana dostava</span>
            @endif
            <button type="button" class="accordion-button collapsed fs-5 d-lg-none py-1" data-bs-toggle="collapse" data-bs-target="#deliveryInfo" aria-expanded="false" aria-controls="deliveryInfo">
                <span class="me-2">Datum montaže</span>
            </button>
        </h2>
        <div class="nav ms-auto">
            <a class="nav-link text-decoration-underline p-0" href="{{ route('dostava') }}">Uredi</a>
        </div>
    </div>
    <div class="accordion-collapse collapse d-lg-block" id="deliveryInfo" aria-labelledby="deliveryInfoHeading" data-bs-parent="#checkout">
        <div class="accordion-body p-0 pt-3 pt-md-4">
            @if ($selected_reservation)

                <h3 class="fs-sm mb-2">Izabrani datum montaže</h3>
                <div class="d-flex align-items-center fs-sm">
                    {{ \Illuminate\Support\Carbon::parse($selected_reservation['day'])->locale('hr')->translatedFormat('l, d. F') }}
                    <span class="opacity-40 mx-2">|</span>
                    {{ $selected_reservation['hour'] }}
                </div>
            @else
                <h3 class="fs-sm mb-2">{{ $selected_shipping->title }}</h3>
                <div class="d-flex align-items-center fs-sm">{{ $selected_shipping->data->short_description }}</div>
            @endif
        </div>
    </div>
</div>