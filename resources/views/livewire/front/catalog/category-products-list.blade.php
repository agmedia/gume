<div>
    <!-- Filter -->
    <div class="bg-body-tertiary p-3 rounded-3 mb-3">
        <div class="row align-items-center pt-1">
            <div class="col-12 d-md-flex d-block gap-2">
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="sezona" wire:change="dropdownFilterSelected('sezona', $event.target.value)" data-placeholder="Sezona" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]}}' aria-label="Sezona">
                        @foreach ($sezone as $item)
                            <option value="{{ $item['key'] }}" @if($item['key'] == $sezona) selected @endif>{{ $item['title'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="sirina" wire:change="dropdownFilterSelected('sirina', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Širina">
                        <option value="">Širina</option>
                        @foreach ($sirine as $item)
                            <option value="{{ $item }}" @if($item == $sirina) selected @endif>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="visina" wire:change="dropdownFilterSelected('visina', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Visina">
                        <option value="">Visina</option>
                        @foreach ($visine as $item)
                            <option value="{{ $item }}" @if($item == $visina) selected @endif>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="promjer" wire:change="dropdownFilterSelected('promjer', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Promjer" data-placeholder="Promjer">
                        @foreach ($promjeri as $item)
                            <option value="{{ $item }}" @if($item == $promjer) selected @endif>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="sort" wire:change="dropdownFilterSelected('sort', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]}}' aria-label="Sortiraj" data-placeholder="Sortiraj">
                        @foreach ($sorting_list as $item)
                            <option value="{{ $item['value'] }}" @if($item['value'] == $sort) selected @endif>{{ $item['title'] }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- All filters offcanvas toggle -->
                <nav class="nav">
                    <a class="nav-link animate-underline px-2" href="#shopFilters" data-bs-toggle="offcanvas" aria-controls="shopFilters">
                        <i class="ci-filter me-1"></i>
                        <span class="animate-target text-nowrap">Svi filteri</span>
                    </a>
                </nav>
            </div>

            @if ($show_additional_filters)
                <div class="col-12 d-md-flex d-block gap-2 mt-4">
                    <div class="d-block w-100 mb-2 mb-md-0 me-1"></div>
                    <div class="d-block w-100 mb-2 mb-md-0 me-1"></div>
                    <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                        <select class="form-select rounded-pill" wire:model="brand" wire:change="dropdownFilterSelected('brand', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]}}' aria-label="Proizvođači" data-placeholder="Proizvođači">
                            @foreach ($brands as $slug => $title)
                                <option value="{{ $slug }}" @if($slug == $brand) selected @endif>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-block w-100 mb-2 mb-md-0 me-1"></div>
                    <div class="d-block w-100 mb-2 mb-md-0 me-1"></div>
                    <nav class="nav">
                        <a class="nav-link animate-underline px-2" wire:click="cleanFilter()">
                            <i class="ci-trash-empty me-1"></i>
                            <span class="animate-target text-nowrap">Očisti</span>
                        </a>
                    </nav>
                </div>
            @endif
        </div>
    </div>

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
            {{--<select class="form-select form-select-lg form-select-flush text-body-emphasis fw-medium py-3 px-0" id="fil" data-select='{
              "classNames": {
                "containerInner": ["form-select", "form-select-lg", "form-select-flush", "text-body-emphasis", "fw-medium", "py-3", "px-0"]
              }
            }' aria-label="Sorting">
                <option value="">Sortiraj</option>
                <option value="popular">Najpopularnije</option>
                <option value="match">Zadnje dodano</option>
                <option value="price-asc">Cijena (viša - niža) </option>
                <option value="price-desc">Cijena (niža - viša)</option>
            </select>--}}

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

    <!-- Selected filters -->
    {{--<div class="d-flex flex-wrap align-items-center gap-2 text-nowrap mb-5 pb-4 border-bottom">
        <div class="nav ps-1">
            <a class="nav-link fs-xs text-decoration-underline px-0" href="#!">Očisti filtere</a>
        </div>
    </div>--}}

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 0 gy-5" id="productGrid">
        <!-- Toolbar-->
        @foreach($products as $product)
            {{--{{ dd($product->toArray()) }}--}}
            <div class="col">
                <div class="animate-underline">
                    <a class="ratio ratio-1x1 d-block mb-3" href="{{ $product->url }}">
                        <img loading="lazy" src="{{ $product->thumb }}" width="300" height="300" alt="{{ $product->name }}" class="rounded-4">
                    </a>
                    <div class="w-100 min-w-0 px-0 pb-2 pb-sm-3">
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
                            <a class="d-block fs-sm fw-medium" href="{{ $product->url }}">
                                <span class="animate-target">{{ $product->name }}</span>
                            </a>
                        </h3>
                        <div class="d-flex align-items-center justify-content-between">
                            @if ($product->special() != '0.0000')
                                <div class="h5 lh-1 mb-0">{{ $product->main_special_text }} <del class="text-body-tertiary fs-sm fw-normal">{{ $product->main_price_text }}</del></div>
                            @else
                                <div class="h5 lh-1 mb-0"> {{ $product->main_price_text }}</div>
                            @endif

                            <button type="button" wire:click="addToCart('{{ $product->slug }}', 1)" class="product-card-button btn btn-icon btn-primary animate-slide-end ms-2" aria-label="Add to Cart">
                                <i class="ci-shopping-cart fs-base animate-target"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row col">
        <div class="col-md-12">
            <div class="d-flex flex-wrap justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
