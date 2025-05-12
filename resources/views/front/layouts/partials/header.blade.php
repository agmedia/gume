<!-- Shopping cart offcanvas -->
{{--@livewire('front.checkout.cart-drawer')--}}

<!-- Site menu offcanvas -->
<nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
    <div class="offcanvas-header py-3">
        <h5 class="offcanvas-title" id="navbarNavLabel">Navigacija</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0 pb-3">
        <div class="h6 fw-medium py-1 mb-0">
            <a class="d-block animate-underline py-1" href="{{ route('index') }}">
                <span class="d-inline-block animate-target py-1">Naslovnica</span>
            </a>
        </div>

        <!-- Navbar nav -->
        <div class="accordion" id="navigation">
            <!-- Categories -->

            @if(isset($category_list))
                @foreach($category_list as $item)
                <div class="accordion-item border-0">
                    <div class="accordion-header" id="headingPages{{$item->id}}">
                        <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages{{$item->id}}" aria-expanded="false" aria-controls="pages{{$item->id}}">
                            <span class="d-block animate-target py-1">{{$item->title}} </span>
                        </button>
                    </div>
                    <div class="accordion-collapse collapse" id="pages{{$item->id}}" aria-labelledby="headingPages{{$item->id}}" data-bs-parent="#navigation">
                        <div class="accordion-body pb-3">
                            <ul class="dropdown-menu show position-static shadow-none">
                                @foreach($item->subcategories as $sub_item)
                                    <li><a class="dropdown-item" href=" {{ route('catalog.route', ['group' => \App\Helpers\Helper::categoryGroupPath(true) . '/'. $item->slug.'/'.$sub_item->slug]) }}">{{$sub_item->title}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
            @endif
            <hr>

            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingPages6000">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages6000" aria-expanded="false" aria-controls="pages6000">
                        <span class="d-block animate-target py-1">Informacije</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="pages6000" aria-labelledby="headingPages6000" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">

                            @if(isset($uvjeti_kupnje))
                            @foreach ($uvjeti_kupnje as $page)
                                <li><a class="dropdown-item" href="{{ route('catalog.route.page', ['page' => $page]) }}">{{ $page->title }}</a></li>
                            @endforeach
                            @endif
                            <li><a class="dropdown-item" href="{{ route('faq') }}">Česta pitanja</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="h6 fw-medium py-1 mb-0">
            <a class="d-block animate-underline py-1" href="{{ route('kontakt') }}">
                <span class="d-inline-block animate-target py-1">Kontaktirajte nas</span>
            </a>
        </div>

    </div>

    <!-- Account button visible on screens < 768px wide (md breakpoint) -->
    <div class="offcanvas-header flex-column align-items-start d-md-none">
        <a class="btn btn-lg btn-outline-secondary w-100 rounded-pill" href="{{ route('login') }}">
            <i class="ci-user fs-lg ms-n1 me-2"></i>
            Account
        </a>
    </div>
</nav>



<!-- Navigation bar (Page header) -->
<header class="navbar navbar-expand navbar-sticky sticky-top d-block bg-body-tertiary z-fixed py-1 py-lg-0 py-xl-1 px-0" data-sticky-element>
    <div class="container justify-content-start py-2 py-lg-3">

        <!-- Offcanvas menu toggler (Hamburger) -->
        <button type="button" class="navbar-toggler d-block flex-shrink-0 me-3 me-sm-4" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar brand (Logo) -->
        <a class="navbar-brand fs-2 p-0 pe-lg-2 pe-xxl-0 me-5 me-sm-3 me-md-4 me-xxl-5 " href="{{ route('index') }}" >
            <img src="{{ asset('assets/images/pneu-max-light.svg') }}" alt ="PNEU-MAX | Auto gume i ugradnja" class="display-light"/>
            <img src="{{ asset('assets/images/pneu-max.svg') }}" alt ="PNEU-MAX | Auto gume i ugradnja" class="display-dark"/>
        </a>

        <!-- Search bar visible on screens > 768px wide (md breakpoint) -->
        <div class="position-relative w-100 d-none d-md-block me-3 me-xl-4">
            <form action="{{ route('pretrazi') }}" id="search-form" method="get">
                <input type="search" class="form-control form-control-lg rounded-pill" placeholder="Primjer upisa 195/65R15" aria-label="Search" name="{{ config('settings.search_keyword') }}" value="{{ request()->query('pojam') ?: '' }}">
                <button type="submit" class="btn btn-icon btn-ghost fs-lg btn-secondary text-bo border-0 position-absolute top-0 end-0 rounded-circle mt-1 me-1" aria-label="Search button">
                    <i class="ci-search"></i>
                </button>
            </form>
        </div>

        <!-- Button group -->
        <div class="d-flex align-items-center gap-md-1 gap-lg-2 ms-auto">

            <!-- Theme switcher (light/dark/auto) -->
            <div class="dropdown">
                <button type="button" class="theme-switcher btn btn-icon btn-outline-secondary fs-lg border-0 rounded-circle animate-scale" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Toggle theme (light)">
              <span class="theme-icon-active d-flex animate-target">
                <i class="ci-sun"></i>
              </span>
                </button>
                <ul class="dropdown-menu" style="--cz-dropdown-min-width: 9rem">
                    <li>
                        <button type="button" class="dropdown-item active" data-bs-theme-value="light" aria-pressed="true">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-sun"></i>
                  </span>
                            <span class="theme-label">Light</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="dark" aria-pressed="false">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-moon"></i>
                  </span>
                            <span class="theme-label">Dark</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-theme-value="auto" aria-pressed="false">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-auto"></i>
                  </span>
                            <span class="theme-label">Auto</span>
                            <i class="item-active-indicator ci-check ms-auto"></i>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Search toggle button visible on screens < 768px wide (md breakpoint) -->
            <button type="button" class="btn btn-icon fs-xl btn-outline-secondary border-0 rounded-circle animate-shake d-md-none" data-bs-toggle="collapse" data-bs-target="#searchBar" aria-controls="searchBar" aria-label="Toggle search bar">
                <i class="ci-search animate-target"></i>
            </button>

            <!-- Account button visible on screens > 768px wide (md breakpoint) -->
            <a class="btn btn-icon fs-lg btn-outline-secondary border-0 rounded-circle animate-shake d-inline-flex" href="{{ route('login') }}">
                <i class="ci-user animate-target"></i>
                <span class="visually-hidden">Moj račun</span>
            </a>

            <!-- Cart button -->
            @livewire('front.checkout.cart-nav-icon')
        </div>
    </div>

    <!-- Search collapse available on screens < 768px wide (md breakpoint) -->
    <div class="collapse d-md-none" id="searchBar">
        <div class="container pt-2 pb-3">
            <div class="position-relative">
                <form action="{{ route('pretrazi') }}" id="search-form-mobile" method="get">
                    <i class="ci-search position-absolute top-50 translate-middle-y d-flex fs-lg ms-3"></i>
                    <input type="search" class="form-control form-icon-start rounded-pill" placeholder="Primjer upisa 195/65 R15" data-autofocus="collapse" name="{{ config('settings.search_keyword') }}" value="{{ request()->query('pojam') ?: '' }}">
                </form>
            </div>
        </div>
    </div>
</header>

<!-- Categories -->
<section class="border-top-dark ">
    <div class="container py-lg-1" >
        <div class="overflow-auto" data-simplebar>
            <div class="nav flex-nowrap justify-content-between gap-4 py-2 ">
                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="{{ route('catalog.route', ['group' => group(true), 'cat' => 'gume']) }}">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
                <img src="{{ asset('assets/images/auto-gume.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">AUTO GUME</span>
                </a>

                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="{{ route('catalog.route', ['group' => group(true), 'cat' => 'suv-4x4-gume']) }}">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
                <img src="{{ asset('assets/images/auto-gume.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">SUV/4x4 GUME</span>
                </a>
              {{--   <a class="nav-link align-items-center animate-underline gap-2 p-0" href="{{ route('catalog.route', ['group' => group(true), 'cat' => 'dodatna-oprema', 'subcat' => 'poklopac-kotaca-ratkape']) }}">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
               <img src="{{ asset('assets/images/felge.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">RATKAPE</span>
                </a>--}}

                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="{{ route('catalog.route', ['group' => group(true), 'cat' => 'moto-i-scooter-gume']) }}">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
                       <img src="{{ asset('assets/images/motogume.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">MOTO I GUME ZA SCOOTER</span>
                </a>

                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="{{ route('catalog.route', ['group' => group(true), 'cat' => 'dodatna-oprema']) }}">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
                 <img src="{{ asset('assets/images/dodatnaoprema.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">DODATNA OPREMA</span>
                </a>

                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="{{ route('login') }}">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
               <img src="{{ asset('assets/images/hotegume.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1"> HOTEL ZA GUME B2C</span>
                </a>



            </div>
        </div>
    </div>



    <div class="toast-container top-0 end-0 p-3" >
        <div class="toast border-success" id="add-cart-toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header mt-2">
                <i class="ci-shopping-cart text-primary fs-base mt-1 me-2"></i>
                <strong class="">Proizvod je uspješno dodan u košaricu.</strong>
                {{--<small class="text-body-secondary">5 mins ago</small>--}}
                <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            {{--<div class="toast-body">
                Hello, world! This is a toast message.
            </div>--}}
            <div class="d-flex pt-1">
                <a href="{{ route('kosarica') }}" class="btn btn-sm btn-primary me-2">Košarica</a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="toast">Zatvori</button>
            </div>
        </div>
    </div>
</section>

@push('js_after')
    <script>
        Livewire.on('addCartItem', () => {
            //console.log(111)
            let addCartToast = document.getElementById('add-cart-toast');

            if (addCartToast) {
                bootstrap.Toast.getOrCreateInstance(addCartToast).show();
            }
        })
    </script>
@endpush
