

<div class="swiper-slide">
    <div class="animate-underline">
        <a class=" ratio ratio-1x1 d-block mb-3" href="{{ url($product->url) }}">
            <img src="{{ $product->thumb }}"  class="rounded-4" alt="Product">

        </a>



        <div class="w-100 min-w-0 px-0 pb-2  pb-sm-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="d-flex gap-1 fs-xs">
                    <i class="ci-star-filled text-warning"></i>
                    <i class="ci-star-filled text-warning"></i>
                    <i class="ci-star-filled text-warning"></i>
                    <i class="ci-star-filled text-warning"></i>
                    <i class="ci-star text-body-tertiary opacity-75"></i>
                </div>
                <span class="text-body-tertiary fs-xs">(2)</span>
            </div>
            <h3 class="pb-1 mb-2">
                <a class="d-block fs-sm fw-medium " href="{{ url($product->url) }}">
                    <span class="animate-target">{{ $product->name }}</span>
                </a>
            </h3>
            <div class="d-flex align-items-center justify-content-between">

                @if ($product->main_price > $product->main_special)
                    <div class="h5 lh-1 mb-0">{{ $product->main_special_text }}  <del class="text-body-tertiary fs-sm fw-normal">{{ $product->main_price_text }}</del></div>
                @else
                    <div class="h5 lh-1 mb-0">{{ $product->main_price_text }}  </div>
                @endif

                    <add-to-cart-btn-simple id="{{ $product->id }}" available="{{ $product->quantity }}"></add-to-cart-btn-simple>

            </div>
        </div>
    </div>
</div>

