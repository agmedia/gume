

<div class="swiper-slide">
    <div class="card h-100   hover-effect-opacity hover-effect-scale rounded-4 overflow-hidden">
        <div class="card-img-top  position-relative bg-white overflow-hidden">
        <a class=" d-block mb-0 pt-0" href="{{ url($product->url) }}">
            <img src="{{ $product->thumb }}"   alt="Product">
        </a>
        </div>
        <div class="card-body p-3 pb-1">
        <div class="w-100 min-w-0 px-0 pb-2  pb-sm-3">
            <h3 class="pb-1 mb-2">
                <a class="d-block fs-sm fw-medium " href="{{ url($product->url) }}">
                    <span class="animate-target">{{ $product->name }}</span>
                </a>
            </h3>

            <div class="d-flex align-items-center  mb-0">
                @if($product->iskoristivost and $product->prijanjanje and $product->buka)
                    <p class="criteria-icons fs-sm fw-normal "> <span><i class="fa-solid fa-gas-pump"></i> {{$product->iskoristivost }} <span class="icon-separator">|</span></span> <span><i class="fa-solid fa-cloud-showers-heavy"></i> {{ $product->prijanjanje }} <span class="icon-separator">|</span></span> <span><i class="fa-solid fa-volume-high"></i> {{ $product->buka }} </span> </p>
                @endif
            </div>

            @if($product->quantity > 0)
                <div class="d-flex align-items-center text-success fs-xs ms-auto mb-1">
                    <i class="ci-check-circle fs-base me-2"></i>
                    Dostupno: {{$product->quantity}}
                </div>
            @else

                <div class="d-flex align-items-center text-danger fs-xs ms-auto mb-1">
                    <i class="ci-check-circle fs-base me-2"></i>
                    Nedostupno: {{$product->quantity}}
                </div>

            @endif


            <div class="  ms-auto mb-1">
                <ul class="list-group list-group-flush">
                    <li class="ps-0 fs-xs list-group-item"><i class="ci-map-pin"></i> Izražena cijena vrijedi za narudžbe putem web shopa</li>
                    <li class="ps-0 fs-xs list-group-item"><i class="ci-percent"></i></i> Mogućnost plaćanja na rate kod nas u poslovnici</li>

                </ul>
            </div>




            <div class="d-flex align-items-center justify-content-between">



                @if ($product->main_price > $product->main_special)
                    <div class="h5 lh-1 mb-0">{{ $product->main_special_text }}  <del class="text-body-tertiary fs-sm fw-normal">{{ $product->main_price_text }}</del></div>
                @else
                    <div class="h5 lh-1 mb-0">{{ $product->main_price_text }}  </div>
                @endif

                    <button type="button" onclick="Livewire.emit('addCartItem', '{{ $product->slug }}', 1)" class="product-card-button btn btn-icon btn-primary animate-slide-end ms-2" aria-label="Add to Cart">
                        <i class="ci-shopping-cart fs-base animate-target"></i>
                    </button>

            </div>
        </div>
    </div>
    </div>
</div>

