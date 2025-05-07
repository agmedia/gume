<!-- {"title": "Slider Index", "description": "Index main slider."} -->
<!-- Hero slider -->


<section class="position-relative">
    <div class="swiper position-absolute top-0 start-0 w-100 h-100" data-swiper='{
          "effect": "fade",
          "loop": true,
          "speed": 400,
          "pagination": {
            "el": ".swiper-pagination",
            "clickable": true
          },
          "autoplay": {
            "delay": 5500,
            "disableOnInteraction": false
          }
        }' data-bs-theme="dark">
        <div class="swiper-wrapper">
            @foreach($data as  $widget)
            <!-- Slide -->
            <div class="swiper-slide" style="background-color: #000">
                <div class="position-absolute d-flex align-items-center w-100 h-100 z-2">
                    <div class="container mt-lg-n4">
                        <div class="row">
                            <div class="col-9 col-sm-8 col-md-7 col-lg-6">

                              <!--  <h2 class="display-5 pb-2 ">{{ $widget['title'] }}</h2>
                                <p class="fs-lg text-white mb-4 pb-2">{{ $widget['subtitle'] }}</p>
                                <a class="btn btn-lg btn-primary rounded-pill" href="{{ url($widget['url']) }}">Prijavi se</a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <img src="{{ $widget['image'] }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover rtl-flip" alt="Image">
            </div>
            @endforeach
            <!-- Slide -->






        </div>

        <!-- Slider pagination (Bullets) -->
        <div class="swiper-pagination pb-sm-2"></div>
    </div>
    <div class="d-md-none" style="height: 380px"></div>
    <div class="d-none d-md-block d-lg-none" style="height: 420px"></div>
    <div class="d-none d-lg-block d-xl-none" style="height: 500px"></div>
    <div class="d-none d-xl-block d-xxl-none" style="height: 560px"></div>
    <div class="d-none d-xxl-block" style="height: 624px"></div>
</section>

