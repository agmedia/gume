<!-- {"title": "Slider Index", "description": "Index main slider."} -->
<!-- Hero slider -->

@once
    <style>
        .widget-custom-hero {
            width: calc(100% - 1rem);
            max-width: 1320px;
            margin-right: auto;
            margin-left: auto;
            overflow: hidden;
            border-radius: 1.5rem;
            background-color: #fff;
        }

        @media (min-width: 576px) {
            .widget-custom-hero {
                width: calc(100% - 2rem);
            }
        }

        .widget-custom-hero__swiper {
            position: relative;
        }

        .widget-custom-hero__image {
            display: block;
            width: 100% !important;
            height: auto !important;
            max-width: 100%;
            object-fit: unset !important;
        }

        .widget-custom-hero__link {
            display: block;
        }

        .widget-custom-hero__pagination {
            bottom: 0.75rem !important;
        }

        @media (min-width: 1400px) and (max-height: 700px) {
            .widget-custom-hero {
                max-width: 1140px;
            }
        }
    </style>
@endonce


<section class="widget-custom-hero">
    <div class="swiper widget-custom-hero__swiper" data-swiper='{
          "effect": "fade",
          "loop": true,
          "autoHeight": true,
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
            <div class="swiper-slide" style="background-color: #fff">
                @if(! empty($widget['url']) && $widget['url'] !== '/')
                    <a href="{{ url($widget['url']) }}"
                       class="widget-custom-hero__link"
                       aria-label="{{ $widget['title'] ?: 'Otvori ponudu' }}">
                        <img src="{{ $widget['image'] }}"
                             class="widget-custom-hero__image rtl-flip"
                             alt="{{ $widget['title'] ?: 'Banner' }}">
                    </a>
                @else
                    <img src="{{ $widget['image'] }}"
                         class="widget-custom-hero__image rtl-flip"
                         alt="{{ $widget['title'] ?: 'Banner' }}">
                @endif
            </div>
            @endforeach
            <!-- Slide -->
        </div>

        <!-- Slider pagination (Bullets) -->
        <div class="swiper-pagination widget-custom-hero__pagination pb-sm-2"></div>
    </div>
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
