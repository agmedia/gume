@extends('front.layouts.app')
@section ('title', $meta['product']['title'])
@section ('description', $meta['product']['description'])
@push('meta_tags')

    <link rel="canonical" href="{{ env('APP_URL')}}/{{ $data->product->url }}" />
    <meta property="og:locale" content="hr_HR" />
    <meta property="og:type" content="product" />
    <meta property="og:title" content="{{ $meta['product']['title'] }}" />
    <meta property="og:description" content="{{ $meta['product']['description']  }}" />
    <meta property="og:url" content="{{ env('APP_URL')}}/{{ $data->product->url }}"  />
    <meta property="og:site_name" content="PNEU-MAX" />
    <meta property="og:updated_time" content="{{ $data->product->updated_at  }}" />
    <meta property="og:image" content="{{ asset($data->product->image) }}" />
    <meta property="og:image:secure_url" content="{{ asset($data->product->image) }}" />
    <meta property="og:image:width" content="640" />
    <meta property="og:image:height" content="480" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:alt" content="{{ $data->product->image_alt }}" />
    <meta property="product:price:amount" content="{{ number_format($data->product->price, 2) }}" />
    <meta property="product:price:currency" content="EUR" />
    <meta property="product:availability" content="instock" />
    <meta property="product:retailer_item_id" content="{{ $data->product->sku }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $meta['product']['title'] }}" />
    <meta name="twitter:description" content="{{ $meta['product']['description'] }}" />
    <meta name="twitter:image" content="{{ asset($data->product->image) }}" />

@endpush

@if (isset($meta['gdl']))
    @section('google_data_layer')
        <script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                'event': 'view_item',
                'ecommerce': {
                    'items': [<?php echo json_encode($meta['gdl']); ?>]
                } });
        </script>
    @endsection
@endif
@section('content')
    <!-- Page content -->

        <div class="container pb-5 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
            <!-- Breadcrumb -->
            <nav class="position-relative  my-3 ms-3" aria-label="breadcrumb" style="z-index: 1021">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Naslovnica</a></li>

                    @if ($data->group)
                        @if ($data->group && ! $data->category && ! $data->subcategory)
                            <li class="breadcrumb-item" aria-current="page">{{ \Illuminate\Support\Str::ucfirst($data->group) }}</li>
                        @elseif ($data->group && $data->category)
                            <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $data->group]) }}">{{ \Illuminate\Support\Str::ucfirst($data->group) }}</a></li>
                        @endif

                        @if ($data->category && ! $data->subcategory)
                            @if ($data->product)
                                <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $data->group, 'cat' => $data->category]) }}">{{ $data->category->title }}</a></li>
                            @else
                                <li class="breadcrumb-item" aria-current="page">{{ $data->category->title }}</li>
                            @endif
                        @elseif ($data->category && $data->subcategory)
                            <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $data->group, 'cat' => $data->category]) }}">{{ $data->category->title }}</a></li>
                            @if ($data->product)
                                @if ($data->category && ! $data->subcategory)
                                    <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $data->group, 'cat' => $data->category]) }}">{{ \Illuminate\Support\Str::limit($data->product->name, 50) }}</a></li>
                                @else
                                    <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $data->group, 'cat' => $data->category, 'subcat' => $data->subcategory]) }}">{{ $data->subcategory->title }}</a></li>
                                @endif
                            @endif
                        @endif
                    @endif

                    <li class="breadcrumb-item" aria-current="page">{{ \Illuminate\Support\Str::limit($data->product->name, 50) }}</li>

                </ol>
            </nav>
            <!-- Product gallery and details -->
            <section class=" my-3 ms-3">
                <div class="row">
                    <!-- Gallery -->
                    <div class="col-md-6 pb-4 pb-md-0 mb-2 mb-sm-3 mb-md-0">
                        <div class="d-flex" style="padding-top: 8px">
                            <!-- Thumbnails -->
                            <div class="swiper swiper-load swiper-thumbs d-none d-lg-block w-100 me-xl-3" id="thumbs" data-swiper='{
                                "direction": "vertical",
                                "spaceBetween": 12,
                                "slidesPerView": 4,
                                "watchSlidesProgress": true
                              }' style="max-width: 96px; height: 420px;">
                                <div class="swiper-wrapper flex-column">
                                    @if ($data->product->images->count())
                                        @if ( ! empty($data->product->thumb))
                                            <div class="swiper-slide swiper-thumb">
                                                <div class="ratio ratio-1x1" style="max-width: 94px">
                                                    <img src="{{ asset($data->product->thumb) }}" class="swiper-thumb-img" alt="{{ $data->product->name }}">
                                                </div>
                                            </div>
                                        @endif
                                        @foreach ($data->product->images as $key => $image)
                                                <div class="swiper-slide swiper-thumb">
                                                    <div class="ratio ratio-1x1" style="max-width: 94px">
                                                        <img src="{{ url('cache/thumb?size=100x100&src=' . $image->thumb) }}" class="swiper-thumb-img" alt="{{ $image->alt }}">
                                                    </div>
                                                </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <!-- Preview (Large image) -->
                            <div class="swiper w-100" data-swiper='{
                                    "loop": false,
                                    "thumbs": {
                                      "swiper": "#thumbs"
                                    },
                                    "pagination": {
                                      "el": ".swiper-pagination",
                                      "clickable": true
                                    }
                                  }'>
                                <div class="swiper-wrapper">
                                    @if ( ! empty($data->product->image))
                                        <div class="swiper-slide">
                                            <a class="ratio ratio-1x1 d-block rounded cursor-zoom-in" href="{{ asset($data->product->image) }}" data-glightbox data-gallery="product-gallery">
                                                <img src="{{ asset($data->product->image) }}" class="rounded" alt="{{ $data->product->name }}">
                                            </a>
                                        </div>
                                    @endif
                                    @if ($data->product->images->count())
                                        @foreach ($data->product->images as $key => $image)
                                                <div class="swiper-slide">
                                                    <a class="ratio ratio-1x1 d-block rounded cursor-zoom-in" href="{{ asset($image->image) }}" data-glightbox data-gallery="product-gallery">
                                                        <img src="{{ asset($image->image) }}" class="rounded" alt="{{ $image->alt }}">
                                                    </a>
                                                </div>
                                        @endforeach
                                    @endif
                                </div>
                                <!-- Slider pagination (Bullets) visible on screens > 991px wide (lg breakpoint) -->
                                <div class="swiper-pagination mb-n3 d-lg-none"></div>
                            </div>
                        </div>
                    </div>
                    <!-- Product details and options -->
                    <div class="col-md-6">
                        <div class="ps-md-4 ps-xl-5">
                            <!-- Reviews -->
                            <a href="#content-scroll" onclick="ClickReviews()" class="d-none d-md-flex align-items-center gap-2 text-decoration-none mb-3" >
                                <div class="d-flex gap-1 fs-sm">
                                    <i class="ci-star-filled text-warning"></i>
                                    <i class="ci-star-filled text-warning"></i>
                                    <i class="ci-star-filled text-warning"></i>
                                    <i class="ci-star-filled text-warning"></i>
                                    <i class="ci-star text-body-tertiary opacity-75"></i>
                                </div>
                                <span class="text-body-tertiary fs-sm">4 mišljenja</span>
                            </a>
                            <img src="https://cdn.tiresleader.com/static/img/brand/bridgestone.jpg" class="img-fluid mb-1" alt="Bridgestone" width="125" height="37" loading="lazy">
                            <!-- Title -->
                            <h1 class="h3">{{ $data->product->name }}</h1>

                            <p class="fs-sm fw-normal mb-0">Klasa Premium</p>

                            <p><strong>195/65  R15  91  T</strong></p>

                            <p class="criteria-icons fs-sm fw-normal "> <span><i class="fa-solid fa-gas-pump"></i> C <span class="icon-separator">|</span></span> <span><i class="fa-solid fa-cloud-showers-heavy"></i> A <span class="icon-separator">|</span></span> <span><i class="fa-solid fa-volume-high"></i> B 71dB </span> </p>
                            <!-- Description -->
                            @if ($data->product->main_price > $data->product->main_special)
                                <div class="h4 d-flex align-items-center mt-4 mb-2">
                                    {{ $data->product->main_special_text }}
                                    <del class="fs-sm fw-normal text-body-tertiary ms-2">{{ $data->product->main_price_text }}</del>
                                </div>
                                <p class="fs-sm fw-normal mb-4">Najniža cijena u zadnjih 30 dana je: {{ $data->product->main_price_text }}</p>
                            @else
                                <div class="h4 d-flex align-items-center mt-4 mb-2">
                                    {{ $data->product->main_price_text }}
                                </div>

                            @endif


                            <!-- Count input + Add to cart button -->

                            <add-to-cart-btn id="{{ $data->product->id }}" available="{{ $data->product->quantity }}"></add-to-cart-btn>


                            <!-- Stock status -->
                            <div class="d-flex flex-wrap justify-content-between fs-sm mb-3">
                                <span class="fw-medium text-dark-emphasis me-2">🔥 Besplatna montaža i postava na vozilo!</span>
                                <span><i class="fa-solid fa-calendar-days me-2"></i> Odabir termina prilikom kupnje!</span>
                            </div>
                            <div class="progress" role="progressbar" aria-label="Left in stock" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height: 4px">
                                <div class="progress-bar rounded-pill" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <a id="content-scroll"></a>
            <!-- Product details tabs -->
            <section  class="container pt-5 pb-3 mt-2 mt-sm-3 mt-lg-4 mt-xl-5">

                <!-- Nav tabs -->
                <ul class="nav nav-underline flex-nowrap border-bottom" role="tablist">
                    <li class="nav-item me-md-1" role="presentation">
                        <button type="button" class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description-tab-pane" role="tab" aria-controls="description-tab-pane" aria-selected="true">
                            Opis <span class="d-none d-md-inline">&nbsp;artikla</span>
                        </button>
                    </li>
                 <!--   <li class="nav-item me-md-1" role="presentation">
                        <button type="button" class="nav-link" id="washing-tab" data-bs-toggle="tab" data-bs-target="#washing-tab-pane" role="tab" aria-controls="washing-tab-pane" aria-selected="false">
                            Specifikacije
                        </button>
                    </li>-->
                    <li class="nav-item me-md-1" role="presentation">
                        <button type="button" class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery-tab-pane" role="tab" aria-controls="delivery-tab-pane" aria-selected="false">
                            Montaža<span class="d-none d-md-inline">&nbsp;i dostava</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-tab-pane" role="tab" aria-controls="reviews-tab-pane" aria-selected="false">
                            Mišljenja<span class="d-none d-md-inline">&nbsp;(4)</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-4 mt-sm-1 mt-md-3">

                    <!-- Description tab -->
                    <div class="tab-pane fade show active" id="description-tab-pane" role="tabpanel" aria-labelledby="description-tab">
                        <div class="row">
                            <div class="col-lg-6 fs-sm">

                                {!! $data->product->description !!}
                            </div>
                            <div class="col-lg-6 col-xl-5 offset-xl-1">
                                <div class="row  g-4 my-0 my-lg-n2">
                                    <!-- Table with striped rows -->
                                    <div class="table-responsive">
                                        <table class="table table-striped fs-sm">

                                            <tbody>
                                            @foreach($data->product->attributes as $attribute)
                                            <tr>
                                                <th scope="row">{{$attribute->attribute->title }} </th>
                                                <td>{{$attribute->value }}</td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                     <!--   <div onclick="ClickSpec()" style="cursor:pointer;">
                                            <p class="fs-sm fw-normal mb-0"> <u>Pogledajte detaljnije specifikacije</u></p>
                                        </div> -->

                                    </div>
                                    <!-- Dark table with striped rows -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- additional spec -->
                <!--    <div class="tab-pane fade fs-sm" id="washing-tab-pane" role="tabpanel" aria-labelledby="washing-tab">

                        <div class="row row-cols-1 row-cols-md-2">
                            <div class="col mb-3 mb-md-0">

                                <div class="table-responsive">
                                    <table class="table table-striped fs-sm">

                                        <tbody>
                                        <tr>
                                            <th scope="row">Kartica proizvoda: </th>
                                            <td>https://eprel.ec.europa.eu/qr/381887</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">EU naljepnica:</th>
                                            <td>Da</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tip vozila:</th>
                                            <td>Osobna vozila, SUV / 4x4</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Širina:</th>
                                            <td> 185</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Profil:</th>
                                            <td>65</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Promjer:</th>
                                            <td> 15</td>
                                        </tr>

                                        <tr>
                                            <th scope="row">Sezona:</th>
                                            <td> Zimska</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Klasa:</th>
                                            <td>  Premium</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <div class="col">

                                <div class="table-responsive">
                                    <table class="table table-striped fs-sm">

                                        <tbody>
                                        <tr>
                                            <th scope="row">Kartica proizvoda: </th>
                                            <td>https://eprel.ec.europa.eu/qr/381887</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">EU naljepnica:</th>
                                            <td>Da</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Tip vozila:</th>
                                            <td>Osobna vozila, SUV / 4x4</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Širina:</th>
                                            <td> 185</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Profil:</th>
                                            <td>65</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Promjer:</th>
                                            <td> 15</td>
                                        </tr>

                                        <tr>
                                            <th scope="row">Sezona:</th>
                                            <td> Zimska</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Klasa:</th>
                                            <td>  Premium</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>



                            </div>
                        </div>
                    </div> -->

                    <!-- Delivery and returns tab -->
                    <div class="tab-pane fade fs-sm" id="delivery-tab-pane" role="tabpanel" aria-labelledby="delivery-tab">

                        <p class="mb-4">Dvije opcije dostave su vam dostupne prilikom narudžbe:</p>
                        <div class="row row-cols-1 row-cols-md-2">
                            <div class="col mb-3 mb-md-1">
                                <div class="pe-lg-2 pe-xl-3">


                                    <h6>Montaža sa rezervacijom termina</h6>
                                    <p>Montaža guma ključan je korak za sigurnu i udobnu vožnju.</p>
                                    <p> Prilikom kupnje guma na našoj web stranici, možete jednostavno <span class="text-dark-emphasis fw-semibold">rezervirati termin montaže</span> tijekom procesa naplate (checkouta). </p>
                                    <p>Odaberite željeni datum i vrijeme, a naš stručni tim pobrinut će se za brzu i kvalitetnu ugradnju.</p><p> Osigurajte si bezbrižnu vožnju uz profesionalnu uslugu montaže!</p>
                                </div>
                            </div>

                            <div class="col">
                                <div class="ps-lg-2 ps-xl-3">
                                    <h6>Dostava</h6>
                                    <p>Nastojimo isporučiti vašu narudžbu što je brže moguće. Naše procijenjeno vrijeme dostave je sljedeće:</p> <ul class="list-unstyled"> <li>Standardna dostava: <span class="text-dark-emphasis fw-semibold">U roku od 3-7 radnih dana</span></li> <li>Ekspresna dostava: <span class="text-dark-emphasis fw-semibold">U roku od 1-3 radna dana</span></li> </ul> <p>Imajte na umu da vrijeme dostave može varirati ovisno o vašoj lokaciji te trenutnim promocijama ili praznicima. Svoju narudžbu možete pratiti pomoću dostavljenog broja za praćenje nakon što je paket otpremljen.</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Reviews tab -->
                    <div class="tab-pane fade" id="reviews-tab-pane" role="tabpanel" aria-labelledby="reviews-tab">

                        <!-- Heading + Add review button -->
                        <div class="d-sm-flex align-items-center justify-content-between border-bottom pb-2 pb-sm-3">
                            <div class="mb-3 me-sm-3">
                                <h2 class="h5 pb-2 mb-1">Mišljenja kupaca </h2>
                                <div class="d-flex align-items-center text-body-secondary fs-sm">
                                    <div class="d-flex gap-1 me-2">
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star text-body-tertiary opacity-75"></i>
                                    </div>
                                    Zasnovano na 4 mišljenja
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-dark mb-3" data-bs-toggle="modal" data-bs-target="#reviewForm">Ostavi mišljenje</button>
                        </div>

                        <!-- Review -->
                        <div class="border-bottom py-4">
                            <div class="row py-sm-2">
                                <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                                    <div class="d-flex h6 mb-2">
                                        Marko S.
                                        <i class="ci-check-circle text-success mt-1 ms-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" title="Registrirani kupac"></i>
                                    </div>
                                    <div class="fs-sm mb-2 mb-md-3">Lipanj 25, 2024</div>
                                    <div class="d-flex gap-1 fs-sm">
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star text-body-tertiary opacity-75"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9 ">
                                    <p class="mb-md-4 fs-sm">Koristim Blizzak LM005 već drugu zimu i prezadovoljan sam. Odlična kontrola na snijegu i mokroj cesti, a kočenje je odlično čak i na zaleđenoj podlozi. Definitivno preporučujem!</p>
                                    <div class="d-sm-flex justify-content-between">
                                        <div class="d-flex align-items-center fs-sm fw-medium text-dark-emphasis pb-2 pb-sm-0 mb-1 mb-sm-0">
                                            <i class="ci-check fs-base me-1" style="margin-top: .125rem"></i>
                                            Da, preporučam ovaj artikl
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom py-4">
                            <div class="row py-sm-2">
                                <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                                    <div class="d-flex h6 mb-2">
                                        Ana M.
                                        <i class="ci-check-circle text-success mt-1 ms-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" title="Registrirani kupac"></i>
                                    </div>
                                    <div class="fs-sm mb-2 mb-md-3">Ožujak 25, 2024</div>
                                    <div class="d-flex gap-1 fs-sm">
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <p class="mb-md-4 fs-sm">Vozim uglavnom po gradu, ali i na dužim relacijama, i ove gume su se pokazale kao odličan izbor. Tihe su, udobne i drže cestu i po kiši i po snijegu. Bridgestone opet nije razočarao!</p>
                                    <div class="d-sm-flex justify-content-between">
                                        <div class="d-flex align-items-center fs-sm fw-medium text-dark-emphasis pb-2 pb-sm-0 mb-1 mb-sm-0">
                                            <i class="ci-check fs-base me-1" style="margin-top: .125rem"></i>
                                            Da, preporučam ovaj artikl
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-bottom py-4">
                            <div class="row py-sm-2">
                                <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                                    <div class="d-flex h6 mb-2">
                                        Petra L.
                                        <i class="ci-check-circle text-success mt-1 ms-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" title="Registrirani kupac"></i>
                                    </div>
                                    <div class="fs-sm mb-2 mb-md-3">Veljača 25, 2024</div>
                                    <div class="d-flex gap-1 fs-sm">
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <p class="mb-md-4 fs-sm">Vrhunske performanse na kiši! Ove gume su nevjerojatne na mokrim cestama! Čak i pri jačoj kiši nema aquaplaninga, a kočenje je sigurno i precizno. Preporučujem svima koji traže pouzdanu zimsku gumu!</p>
                                    <div class="d-sm-flex justify-content-between">
                                        <div class="d-flex align-items-center fs-sm fw-medium text-dark-emphasis pb-2 pb-sm-0 mb-1 mb-sm-0">
                                            <i class="ci-check fs-base me-1" style="margin-top: .125rem"></i>
                                            Da, preporučam ovaj artikl
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-bottom py-4">
                            <div class="row py-sm-2">
                                <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                                    <div class="d-flex h6 mb-2">
                                        Luka D.
                                        <i class="ci-check-circle text-success mt-1 ms-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" title="Registrirani kupac"></i>
                                    </div>
                                    <div class="fs-sm mb-2 mb-md-3">Lipanj 25, 2024</div>
                                    <div class="d-flex gap-1 fs-sm">
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                        <i class="ci-star-filled text-warning"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <p class="mb-md-4 fs-sm">Kvaliteta je odlična, prianjanje na snijegu i ledu bez zamjerke, ali cijena bi mogla biti malo povoljnija. Ipak, sigurnost je najvažnija, pa vrijedi svakog eura.</p>
                                    <div class="d-sm-flex justify-content-between">
                                        <div class="d-flex align-items-center fs-sm fw-medium text-dark-emphasis pb-2 pb-sm-0 mb-1 mb-sm-0">
                                            <i class="ci-check fs-base me-1" style="margin-top: .125rem"></i>
                                            Da, preporučam ovaj artikl
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>





                        <!-- Pagination -->
                        <nav class="mt-3 pt-2 pt-md-3" aria-label="Reviews pagination">
                            <ul class="pagination">
                                <li class="page-item active" aria-current="page">
                  <span class="page-link">
                    1
                    <span class="visually-hidden">(trenuntno)</span>
                  </span>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#!">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#!">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#!">4</a>
                                </li>
                                <li class="page-item">
                                    <span class="page-link pe-none">...</span>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#!">6</a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                </div>
            </section>

            <!-- Bought together carousel -->
            <section class="container pt-5 pb-0 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-3 pb-md-4 mb-3 mb-md-4">
                    <h2 class="h4 mb-0">Dodatna ponuda</h2>

                    <!-- Slider prev/next buttons -->
                    <div class="d-flex gap-2 ms-3">
                        <button type="button" class="btn btn-icon btn-outline-secondary animate-slide-start rounded-circle me-1" id="prevBtn" aria-label="Prev">
                            <i class="ci-chevron-left fs-lg animate-target"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-outline-secondary animate-slide-end rounded-circle" id="nextBtn" aria-label="Next">
                            <i class="ci-chevron-right fs-lg animate-target"></i>
                        </button>
                    </div>
                </div>

                <!-- Slider -->
                <div class="swiper pt-3 pt-sm-4" data-swiper='{
            "slidesPerView": 2,
            "spaceBetween": 40,
            "loop": true,
            "navigation": {
            "prevEl": "#prevBtn",
            "nextEl": "#nextBtn"
          },
            "breakpoints": {
              "768": {
                "slidesPerView": 3
              },
              "992": {
                "slidesPerView": 4
              }
              ,
              "1280": {
                "slidesPerView": 5
              }
            }
          }'>
                    <div class="swiper-wrapper">



                        @foreach ($data->category->products()->get()->unique()->take(10) as $data->category_product)
                            @if ($data->category_product->id  != $data->product->id)
                                <div>
                                    @include('front.catalog.category.product', ['product' => $data->category_product])
                                </div>
                            @endif
                        @endforeach



                    </div>
                </div>
            </section>

        </div>





@endsection

@push('js_after')
    <script type="application/ld+json">
        {!! collect($crumbs)->toJson() !!}
    </script>
@endpush
