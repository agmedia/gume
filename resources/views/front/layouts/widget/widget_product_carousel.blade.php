<!-- {"title": "Product Carousel", "description": "Some description of a Product Carousel."} -->
<section class="container pt-4 pb-5 mt-2 mt-sm-3 mt-lg-4">
    <!-- Heading -->
    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 pb-md-4">
        <h2 class="h4 mb-0 ">{{ $data['title'] }}</h2>
        @if($data['url'] !='/')
            <div class="nav ms-3">
                <a class="nav-link animate-underline px-0 py-2" href="{{ url($data['url']) }}">
                    <span class="animate-target">Pogledajte sve</span>
                    <i class="ci-chevron-right fs-base ms-1"></i>
                </a>
            </div>
        @endif
    </div>

    <!-- Product carousel -->
    <div class="position-relative pb-xxl-3">

        <!-- External slider prev/next buttons visible on screens > 500px wide (sm breakpoint) -->
        <button type="button" class="popular-prev{{ str_replace(' ', '', $data['title']) }} btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-start position-absolute top-50 start-0 z-2 translate-middle mt-n5 d-none d-sm-inline-flex" aria-label="Prev">
            <i class="ci-chevron-left fs-lg animate-target"></i>
        </button>
        <button type="button" class="popular-next{{ str_replace(' ', '', $data['title']) }} btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-end position-absolute top-50 start-100 z-2 translate-middle mt-n5 d-none d-sm-inline-flex" aria-label="Next">
            <i class="ci-chevron-right fs-lg animate-target"></i>
        </button>

        <!-- Slider -->
        <div class="swiper pt-3 pt-sm-4" data-swiper='{
            "slidesPerView": 2,
            "spaceBetween": 20,
            "loop": true,
            "navigation": {
              "prevEl": ".popular-prev{{ str_replace(' ', '', $data['title']) }}",
              "nextEl": ".popular-next{{ str_replace(' ', '', $data['title']) }}"
            },
            "breakpoints": {
              "768": {
                "slidesPerView": 3
              },
              "992": {
                "slidesPerView": 4
              },
              "1280": {
                "slidesPerView": 5
              }
            }
          }'>
            <div class="swiper-wrapper" >

                @foreach ($data['items'] as $product)
                    <!-- Product-->

                        @include('front.catalog.category.product')

                @endforeach
            </div>
        </div>
    </div>

    <!-- External slider prev/next buttons visible on screens < 500px wide (sm breakpoint) -->
    <div class="d-flex justify-content-center gap-2 mt-1 pt-4 d-sm-none">
        <button type="button" class="popular-prev{{ str_replace(' ', '', $data['title']) }} btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-start me-1" aria-label="Prev">
            <i class="ci-chevron-left fs-lg animate-target"></i>
        </button>
        <button type="button" class="popular-next{{ str_replace(' ', '', $data['title']) }} btn btn-icon btn-outline-secondary bg-body rounded-circle animate-slide-end" aria-label="Next">
            <i class="ci-chevron-right fs-lg animate-target"></i>
        </button>
    </div>
</section>
