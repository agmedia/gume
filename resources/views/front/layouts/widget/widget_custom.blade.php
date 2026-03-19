<!-- {"title": "Slider Index", "description": "Index main slider."} -->
<!-- Hero slider -->

@once
    <style>
        .widget-custom-hero__spacer--xs {
            height: 380px;
        }

        .widget-custom-hero__spacer--md {
            height: 420px;
        }

        .widget-custom-hero__spacer--lg {
            height: 500px;
        }

        .widget-custom-hero__spacer--xl {
            height: 560px;
        }

        .widget-custom-hero__spacer--xxl {
            height: 624px;
        }

        @media (min-width: 1400px) and (max-height: 600px) {
            .widget-custom-hero__container {
                max-width: 1140px;
            }

            .widget-custom-hero__content {
                max-width: 520px;
            }

            .widget-custom-hero__spacer--xxl {
                height: 560px;
            }
        }
    </style>
@endonce


<section class="position-relative widget-custom-hero">
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
                    <div class="container mt-lg-n4 widget-custom-hero__container">
                        <div class="row">
                            <div class="col-9 col-sm-8 col-md-7 col-lg-6 widget-custom-hero__content">

                              <!--  <h2 class="display-5 pb-2 ">{{ $widget['title'] }}</h2>
                                <p class="fs-lg text-white mb-4 pb-2">{{ $widget['subtitle'] }}</p>
                                <a class="btn btn-lg btn-primary rounded-pill" href="{{ url($widget['url']) }}">Prijavi se</a> -->
                            </div>
                        </div>
                    </div>
                </div>
                <img src="{{ $widget['image'] }}" class="position-absolute d-block top-0 start-0 w-100 h-100 object-fit-cover rtl-flip" alt="Image">




            </div>
            @endforeach
            <!-- Slide -->






        </div>

        <!-- Slider pagination (Bullets) -->
        <div class="swiper-pagination pb-sm-2"></div>
    </div>
    <div class="d-md-none widget-custom-hero__spacer--xs"></div>
    <div class="d-none d-md-block d-lg-none widget-custom-hero__spacer--md"></div>
    <div class="d-none d-lg-block d-xl-none widget-custom-hero__spacer--lg"></div>
    <div class="d-none d-xl-block d-xxl-none widget-custom-hero__spacer--xl"></div>
    <div class="d-none d-xxl-block widget-custom-hero__spacer--xxl"></div>
</section>

<!--<section class="container pt-4 pb-5 mt-2 mt-sm-3 mt-lg-4">
    <div class="col-12 align-items-center justify-content-between border-bottom pb-3 pb-md-4">
        <div class="alert alert-danger d-flex" role="alert">
            <div class="alert-icon">
                <i class="ci-close-circle me-4"></i>
            </div>
            Poštovani kupci i korisnici PNEU-MAX usluga.<br>
            Servis za ugradnju auto guma zbog godišnjeg odmora biti će zatvoren od 25.07-10.08 2025<br>
            Auto gume kupljene na našem web shop-u biti će isporučene  ili ugrađene iza 10.08.2025
        </div>
    </div>
</section>-->
