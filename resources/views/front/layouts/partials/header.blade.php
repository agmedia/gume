<!-- Shopping cart offcanvas -->
@livewire('front.checkout.cart-drawer')

<!-- Site menu offcanvas -->
<nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
    <div class="offcanvas-header py-3">
        <h5 class="offcanvas-title" id="navbarNavLabel">Navigacija</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0 pb-3">
        <div class="h6 fw-medium py-1 mb-0">
            <a class="d-block animate-underline py-1" href="index.html">
                <span class="d-inline-block animate-target py-1">Naslovnica</span>
            </a>
        </div>

        <!-- Navbar nav -->
        <div class="accordion" id="navigation">
            <!-- Rest of the menu -->

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


            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingPages2">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages2" aria-expanded="false" aria-controls="pages2">
                        <span class="d-block animate-target py-1">Naplatci / Felge</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="pages2" aria-labelledby="headingPages2" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            <li><a class="dropdown-item" href="category.html">Aluminijski naplatci</a></li>
                            <li><a class="dropdown-item" href="category.html">Čelični naplatci</a></li>
                            <li><a class="dropdown-item" href="category.html">Pogledajte sve</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingPages3">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages3" aria-expanded="false" aria-controls="pages3">
                        <span class="d-block animate-target py-1">Moto gume</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="pages3" aria-labelledby="headingPages3" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            <li><a class="dropdown-item" href="category.html">Zimske gume</a></li>
                            <li><a class="dropdown-item" href="category.html">Ljetne gume</a></li>
                            <li><a class="dropdown-item" href="category.html">Pogledajte sve</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingPages4">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages4" aria-expanded="false" aria-controls="pages4">
                        <span class="d-block animate-target py-1">Scooter gume</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="pages4" aria-labelledby="headingPages4" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            <li><a class="dropdown-item" href="category.html">Zimske gume</a></li>
                            <li><a class="dropdown-item" href="category.html">Ljetne gume</a></li>
                            <li><a class="dropdown-item" href="category.html">Pogledajte sve</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingPages5">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages5" aria-expanded="false" aria-controls="pages5">
                        <span class="d-block animate-target py-1">Dodatna oprema</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="pages5" aria-labelledby="headingPages5" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            <li><a class="dropdown-item" href="category.html">Ratkape</a></li>
                            <li><a class="dropdown-item" href="category.html">Šarafi</a></li>
                            <li><a class="dropdown-item" href="category.html">Ključ za kotače</a></li>
                            <li><a class="dropdown-item" href="category.html">Spray za felge</a></li>
                            <li><a class="dropdown-item" href="category.html">Spray za dezinfekciju klime</a></li>
                            <li><a class="dropdown-item" href="category.html">Miris za auto</a></li>
                            <li><a class="dropdown-item" href="category.html">Tekućina za pranje stakla</a></li>
                            <li><a class="dropdown-item" href="category.html">AD Blue</a></li>
                            <li><a class="dropdown-item" href="category.html">Motorna ulja</a></li>
                            <li><a class="dropdown-item" href="category.html">Brisači</a></li>
                            <li><a class="dropdown-item" href="category.html">Pogledajte sve</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr>



            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingPages6">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages6" aria-expanded="false" aria-controls="pages6">
                        <span class="d-block animate-target py-1">Informacije</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="pages6" aria-labelledby="headingPages6" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            <li><a class="dropdown-item" href="category.html">O nama</a></li>
                            <li><a class="dropdown-item" href="category.html">Montažni partneri</a></li>
                            <li><a class="dropdown-item" href="category.html">Česta pitanja</a></li>
                            <li><a class="dropdown-item" href="category.html">Kcntaktirajte nas</a></li>
                        </ul>
                    </div>
                </div>
            </div>


            <div class="accordion-item border-0">
                <div class="accordion-header" id="headingPages7">
                    <button type="button" class="accordion-button animate-underline fw-medium collapsed py-2" data-bs-toggle="collapse" data-bs-target="#pages7" aria-expanded="false" aria-controls="pages7">
                        <span class="d-block animate-target py-1">Uvjeti korištenja</span>
                    </button>
                </div>
                <div class="accordion-collapse collapse" id="pages7" aria-labelledby="headingPages7" data-bs-parent="#navigation">
                    <div class="accordion-body pb-3">
                        <ul class="dropdown-menu show position-static shadow-none">
                            @foreach($uvjeti_kupnje as $page)
                            <li><a class="dropdown-item" href="{{ route('catalog.route.page', ['page' => $page]) }}">{{$page->title}}</a></li>
                            <!--<li><a class="dropdown-item" href="category.html">Dostava robe</a></li>
                            <li><a class="dropdown-item" href="category.html">Jamstveni list</a></li>
                            <li><a class="dropdown-item" href="category.html">Zaštita podataka</a></li>
                            <li><a class="dropdown-item" href="category.html">Kolačići i privatnostt</a></li>
                            <li><a class="dropdown-item" href="category.html">Uvjeti povrata</a></li> -->
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>


        </div>
        <div class="h6 fw-medium py-1 mb-0">
            <a class="d-block animate-underline py-1" href="contact.html">
                <span class="d-inline-block animate-target py-1">Kontaktirajte nas</span>
            </a>
        </div>

    </div>

    <!-- Account button visible on screens < 768px wide (md breakpoint) -->
    <div class="offcanvas-header flex-column align-items-start d-md-none">
        <a class="btn btn-lg btn-outline-secondary w-100 rounded-pill" href="account-signin.html">
            <i class="ci-user fs-lg ms-n1 me-2"></i>
            Account
        </a>
    </div>
</nav>

<!-- Shop filters offcanvas -->
<div class="offcanvas offcanvas-end pb-sm-2 px-sm-2" id="shopFilters" tabindex="-1" aria-labelledby="shopFiltersLabel">

    <!-- Header -->
    <div class="offcanvas-header py-3">
        <h5 class="offcanvas-title" id="shopFiltersLabel">Filteri</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="zatvori"></button>
    </div>

    <!-- Body -->
    <div class="offcanvas-body pt-0">

        <!-- Sort by -->
        <select class="form-select form-select-lg form-select-flush text-body-emphasis fw-medium py-3 px-0" id="fil" data-select='{
          "classNames": {
            "containerInner": ["form-select", "form-select-lg", "form-select-flush", "text-body-emphasis", "fw-medium", "py-3", "px-0"]
          }
        }' aria-label="Sorting">
            <option value="">Sortiraj</option>
            <option value="popular">Najpopularnije</option>
            <option value="match">Zadnje dodano</option>
            <option value="price-asc">Cijena (viša - niža) </option>
            <option value="price-desc">Cijena (niža - viša)</option>
        </select>

        <div class="accordion" id="filters">

            <!-- Category filter -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingCategory">
                    <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#categoryFilter" aria-expanded="false" aria-controls="categoryFilter">
                <span class="d-flex align-items-end">
                  Kategorija
                  <span class="text-body fs-sm fw-normal ms-1" id="categoryCount-2"></span>
                </span>
                    </button>
                </h6>
                <div class="accordion-collapse collapse" id="categoryFilter" aria-labelledby="headingCategory" data-bs-parent="#filters">
                    <div class="accordion-body d-flex flex-column gap-2 px-1">
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="living-room-2" checked onclick="updateFilterCount('categoryCount-2')" data-count-id="categoryCount-2">
                            <label for="living-room-2" class="form-check-label d-flex align-items-end">
                                Ljetne gume
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">657</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="bedroom-2"  onclick="updateFilterCount('categoryCount-2')" data-count-id="categoryCount-2">
                            <label for="bedroom-2" class="form-check-label d-flex align-items-end">
                                Zimske gume
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">528</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="kitchen-2" onclick="updateFilterCount('categoryCount-2')" data-count-id="categoryCount-2">
                            <label for="kitchen-2" class="form-check-label d-flex align-items-end">
                                Cjelogodišnje gume
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">342</span>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Brand filter -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingBrand">
                    <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#BrandFilter" aria-expanded="false" aria-controls="BrandFilter">
                <span class="d-flex align-items-end">
                  Brand
                  <span class="text-body fs-sm fw-normal ms-1" id="BrandCount-2"></span>
                </span>
                    </button>
                </h6>
                <div class="accordion-collapse collapse" id="BrandFilter" aria-labelledby="BrandCategory" data-bs-parent="#filters">
                    <div class="accordion-body d-flex flex-column gap-2 px-1">
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="living-room-2" checked onclick="updateFilterCount('categoryCount-2')" data-count-id="categoryCount-2">
                            <label for="living-room-2" class="form-check-label d-flex align-items-end">
                                Barum
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">657</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="bedroom-2"  onclick="updateFilterCount('categoryCount-2')" data-count-id="categoryCount-2">
                            <label for="bedroom-2" class="form-check-label d-flex align-items-end">
                                Bridgestone
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">528</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="kitchen-2" onclick="updateFilterCount('categoryCount-2')" data-count-id="categoryCount-2">
                            <label for="kitchen-2" class="form-check-label d-flex align-items-end">
                                Continental
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">342</span>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Širina filter -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingType">
                    <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#typeFilter" aria-expanded="false" aria-controls="typeFilter">
                <span class="d-flex align-items-end">
                  Širina
                  <span class="text-body fs-sm fw-normal ms-1" id="typeCount-2"></span>
                </span>
                    </button>
                </h6>
                <div class="accordion-collapse collapse" id="typeFilter" aria-labelledby="headingType" data-bs-parent="#filters">
                    <div class="accordion-body d-flex flex-column gap-2 px-1">
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="armchair-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="armchair-2" class="form-check-label d-flex align-items-end">
                                145
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">324</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="sofa-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="sofa-2" class="form-check-label d-flex align-items-end">
                                155
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">275</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="ottoman-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="ottoman-2" class="form-check-label d-flex align-items-end">
                                165
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">117</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="bench-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="bench-2" class="form-check-label d-flex align-items-end">
                                175
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">86</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="bed-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="bed-2" class="form-check-label d-flex align-items-end">
                                185
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">263</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="lamp-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="lamp-2" class="form-check-label d-flex align-items-end">
                                195
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">415</span>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Visina filter -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingVisina">
                    <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#typeVisina" aria-expanded="false" aria-controls="typeVisina">
                <span class="d-flex align-items-end">
                  Visina
                  <span class="text-body fs-sm fw-normal ms-1" id="typeVisina-2"></span>
                </span>
                    </button>
                </h6>
                <div class="accordion-collapse collapse" id="typeVisina" aria-labelledby="headingVisina" data-bs-parent="#filters">
                    <div class="accordion-body d-flex flex-column gap-2 px-1">
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="armchair-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="armchair-2" class="form-check-label d-flex align-items-end">
                                35
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">324</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="sofa-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="sofa-2" class="form-check-label d-flex align-items-end">
                                40
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">275</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="ottoman-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="ottoman-2" class="form-check-label d-flex align-items-end">
                                45
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">117</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="bench-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="bench-2" class="form-check-label d-flex align-items-end">
                                50
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">86</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="bed-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="bed-2" class="form-check-label d-flex align-items-end">
                                55
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">263</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="lamp-2" onclick="updateFilterCount('typeCount-2')" data-count-id="typeCount-2">
                            <label for="lamp-2" class="form-check-label d-flex align-items-end">
                                60
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">415</span>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Price filter -->
            <div class="accordion-item">
                <h6 class="accordion-header" id="headingPrice">
                    <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#priceFilter" aria-expanded="false" aria-controls="priceFilter">
                <span class="d-flex align-items-end">
                  Cijena
                  <span class="text-body fs-sm fw-normal ms-1" id="priceCount"></span>
                </span>
                    </button>
                </h6>
                <div class="accordion-collapse collapse" id="priceFilter" aria-labelledby="headingPrice" data-bs-parent="#filters">
                    <div class="accordion-body d-flex flex-column gap-2 px-1">
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="price-1" onclick="updateFilterCount('priceCount')" data-count-id="priceCount">
                            <label for="price-1" class="form-check-label d-flex align-items-end">
                                0.00 - 49.99€
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">241</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="price-2" onclick="updateFilterCount('priceCount')" data-count-id="priceCount">
                            <label for="price-2" class="form-check-label d-flex align-items-end">
                                50.00 - 99.99€
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">398</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="price-3" onclick="updateFilterCount('priceCount')" data-count-id="priceCount">
                            <label for="price-3" class="form-check-label d-flex align-items-end">
                                100.00 - 149.99€
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">253</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="price-4" onclick="updateFilterCount('priceCount')" data-count-id="priceCount">
                            <label for="price-4" class="form-check-label d-flex align-items-end">
                                150.00 - 199.99€
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">197</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="price-5" onclick="updateFilterCount('priceCount')" data-count-id="priceCount">
                            <label for="price-5" class="form-check-label d-flex align-items-end">
                                200.00 - 249.99€
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">152</span>
                            </label>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input fs-base" id="price-6" onclick="updateFilterCount('priceCount')" data-count-id="priceCount">
                            <label for="price-6" class="form-check-label d-flex align-items-end">
                                250.00€ +
                                <span class="fs-xs text-body-secondary ps-2 ms-auto">138</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- Navigation bar (Page header) -->
<header class="navbar navbar-expand navbar-sticky sticky-top d-block bg-body-tertiary z-fixed py-1 py-lg-0 py-xl-1 px-0" data-sticky-element>
    <div class="container justify-content-start py-2 py-lg-3">

        <!-- Offcanvas menu toggler (Hamburger) -->
        <button type="button" class="navbar-toggler d-block flex-shrink-0 me-3 me-sm-4" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar brand (Logo) -->
        <a class="navbar-brand fs-2 p-0 pe-lg-2 pe-xxl-0 me-4 me-sm-3 me-md-4 me-xxl-5 " href="{{ route('index') }}" >
            <img src="{{ asset('assets/images/pneu-max-light.svg') }}" alt ="PNEU-MAX | Auto gume i ugradnja" class="display-light"/>
            <img src="{{ asset('assets/images/pneu-max.svg') }}" alt ="PNEU-MAX | Auto gume i ugradnja" class="display-dark"/>
        </a>



        <!-- Search bar visible on screens > 768px wide (md breakpoint) -->
        <div class="position-relative w-100 d-none d-md-block me-3 me-xl-4">
            <form action="{{ route('pretrazi') }}" id="search-form" method="get">
            <input type="search" class="form-control form-control-lg rounded-pill" placeholder="Primjer upisa 195/65 R15" aria-label="Search" name="{{ config('settings.search_keyword') }}" value="{{ request()->query('pojam') ?: '' }}">
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
            <a class="btn btn-icon fs-lg btn-outline-secondary border-0 rounded-circle animate-shake d-none d-md-inline-flex" href="{{ route('login') }}">
                <i class="ci-user animate-target"></i>
                <span class="visually-hidden">Moj račun</span>
            </a>



            <!-- Cart button -->
            <button type="button" class="btn btn-icon fs-xl btn-outline-secondary position-relative border-0 rounded-circle animate-scale" data-bs-toggle="offcanvas" data-bs-target="#shoppingCart" aria-controls="shoppingCart" aria-label="Shopping cart">
                <span class="position-absolute top-0 start-100 badge fs-xs text-bg-primary rounded-pill ms-n3 z-2" style="--cz-badge-padding-y: .25em; --cz-badge-padding-x: .42em">4</span>
                <i class="ci-shopping-cart animate-target"></i>
            </button>
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
                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="{{ route('catalog.route', ['group' => \App\Helpers\Helper::categoryGroupPath(true) . '/auto-gume']) }}">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
                <img src="{{ asset('assets/images/auto-gume.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">AUTO GUME</span>
                </a>
                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="category.html">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
               <img src="{{ asset('assets/images/felge.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">NAPLATCI / FELGE</span>
                </a>

                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="category.html">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
                       <img src="{{ asset('assets/images/motogume.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">MOTO GUME</span>
                </a>

                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="category.html">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
               <img src="{{ asset('assets/images/scootergume.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">SCOOTER GUME</span>
                </a>

                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="category.html">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
               <img src="{{ asset('assets/images/hotegume.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1"> HOTEL ZA GUME B2C</span>
                </a>


                <a class="nav-link align-items-center animate-underline gap-2 p-0" href="category.html">
              <span class="d-flex align-items-center justify-content-center bg-body-tertiary rounded-circle" style="width: 45px; height: 45px">
                 <img src="{{ asset('assets/images/dodatnaoprema.png') }}" width="35" alt="Image">
              </span>
                    <span class="d-block animate-target fw-bolder text-nowrap ms-1">DODATNA OPREMA</span>
                </a>

            </div>
        </div>
    </div>
</section>
