<div>
    <!-- Filter -->
    <div class="bg-body-tertiary p-3 rounded-3 mb-3">
        <div class="row align-items-center pt-1">
            <div class="col-12 d-md-flex d-block gap-2">
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="brand" wire:change="dropdownFilterSelected('brand', $event.target.value)" data-placeholder="Brand" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]}}' aria-label="Brand">
                        @foreach ($brands as $slug => $title)
                            <option value="{{ $slug }}" @if($slug == $brand) selected @endif>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="sirina" wire:change="dropdownFilterSelected('sirina', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Širina">
                        <option value="">Širina</option>
                        @foreach ($sirine as $item)
                            @if($item !='')

                            <option value="{{ $item }}" @if($item == $sirina) selected @endif>{{ $item }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="visina" wire:change="dropdownFilterSelected('visina', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Visina">
                        <option value="">Visina</option>
                        @foreach ($visine as $item)
                            @if($item !='')
                            <option value="{{ $item }}" @if($item == $visina) selected @endif>{{ $item }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="promjer" wire:change="dropdownFilterSelected('promjer', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Promjer" data-placeholder="Promjer">
                        @foreach ($promjeri as $item)
                            @if($item !='')
                            <option value="{{ $item }}" @if($item == $promjer) selected @endif>{{ $item }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="sort" wire:change="dropdownFilterSelected('sort', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]}}' aria-label="Sortiraj" data-placeholder="Sortiraj">
                        @foreach ($sorting_list as $item)
                            @if($item !='')
                            <option value="{{ $item['value'] }}" @if($item['value'] == $sort) selected @endif>{{ $item['title'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <!-- All filters offcanvas toggle -->
             <!--   <nav class="nav">
                    <a class="nav-link animate-underline px-2" href="#shopFilters" data-bs-toggle="offcanvas" aria-controls="shopFilters">
                        <i class="ci-filter me-1"></i>
                        <span class="animate-target text-nowrap">Svi filteri</span>
                    </a>
                </nav>-->
            </div>
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
            <div class="accordion" id="filters">
                <!-- Category filter -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="headingCategory">
                        <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#sezonaFilter" aria-expanded="false" aria-controls="sezonaFilter">
                            <span class="d-flex align-items-end">Sezona<span class="text-body fs-sm fw-normal ms-1" id="sezonaFilter-drawer"></span></span>
                        </button>
                    </h6>
                    <div class="accordion-collapse collapse" id="sezonaFilter" aria-labelledby="headingCategory" data-bs-parent="#filters">
                        <div class="accordion-body d-flex flex-column gap-2 px-1">
                            @foreach ($sezone as $item)
                                <div class="form-check m-0">
                                    <input type="radio" class="form-check-input fs-base" @if($item['key'] == $sezona) selected @endif wire:model="sezona" value="{{ $item['key'] }}" data-count-id="sezonaFilter-drawer">
                                    <label for="living-room-2" class="form-check-label d-flex align-items-end">
                                        {{ $item['title'] }}
                                        {{--<span class="fs-xs text-body-secondary ps-2 ms-auto">657</span>--}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Brand filter -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="brandCategory">
                        <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#brandFilter" aria-expanded="false" aria-controls="brandFilter">
                            <span class="d-flex align-items-end">Brand<span class="text-body fs-sm fw-normal ms-1" id="brandFilter-drawer"></span></span>
                        </button>
                    </h6>
                    <div class="accordion-collapse collapse" id="brandFilter" aria-labelledby="brandCategory" data-bs-parent="#filters">
                        <div class="accordion-body d-flex flex-column gap-2 px-1">
                            @foreach ($brands as $slug => $title)
                                <div class="form-check m-0">
                                    <input type="radio" class="form-check-input fs-base" @if($slug == $brand) selected @endif wire:model="brand" value="{{ $slug }}" data-count-id="brandFilter-drawer">
                                    <label for="living-room-2" class="form-check-label d-flex align-items-end">
                                        {{ $title }}
                                        {{--<span class="fs-xs text-body-secondary ps-2 ms-auto">657</span>--}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Širina filter -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="sirinaCategory">
                        <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#sirinaFilter" aria-expanded="false" aria-controls="sirinaFilter">
                            <span class="d-flex align-items-end">Širina<span class="text-body fs-sm fw-normal ms-1" id="sirinaFilter-drawer"></span></span>
                        </button>
                    </h6>
                    <div class="accordion-collapse collapse" id="sirinaFilter" aria-labelledby="sirinaCategory" data-bs-parent="#filters">
                        <div class="accordion-body d-flex flex-column gap-2 px-1">
                            @foreach ($sirine as $item)
                                <div class="form-check m-0">
                                    <input type="radio" class="form-check-input fs-base" @if($item == $sirina) selected @endif wire:model="sirina" value="{{ $item }}" data-count-id="sirinaFilter-drawer">
                                    <label for="living-room-2" class="form-check-label d-flex align-items-end">
                                        {{ $item }}
                                        {{--<span class="fs-xs text-body-secondary ps-2 ms-auto">657</span>--}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Visina filter -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="visinaCategory">
                        <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#visinaFilter" aria-expanded="false" aria-controls="visinaFilter">
                            <span class="d-flex align-items-end">Visina<span class="text-body fs-sm fw-normal ms-1" id="visinaFilter-drawer"></span></span>
                        </button>
                    </h6>
                    <div class="accordion-collapse collapse" id="visinaFilter" aria-labelledby="visinaCategory" data-bs-parent="#filters">
                        <div class="accordion-body d-flex flex-column gap-2 px-1">
                            @foreach ($visine as $item)
                                <div class="form-check m-0">
                                    <input type="radio" class="form-check-input fs-base" @if($item == $visina) selected @endif wire:model="visina" value="{{ $item }}" data-count-id="visinaFilter-drawer">
                                    <label for="living-room-2" class="form-check-label d-flex align-items-end">
                                        {{ $item }}
                                        {{--<span class="fs-xs text-body-secondary ps-2 ms-auto">657</span>--}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Promjer filter -->
                <div class="accordion-item">
                    <h6 class="accordion-header" id="promjerCategory">
                        <button type="button" class="accordion-button fw-medium collapsed" data-bs-toggle="collapse" data-bs-target="#promjerFilter" aria-expanded="false" aria-controls="promjerFilter">
                            <span class="d-flex align-items-end">Promjer<span class="text-body fs-sm fw-normal ms-1" id="promjerFilter-drawer"></span></span>
                        </button>
                    </h6>
                    <div class="accordion-collapse collapse" id="promjerFilter" aria-labelledby="promjerCategory" data-bs-parent="#filters">
                        <div class="accordion-body d-flex flex-column gap-2 px-1">
                            @foreach ($promjeri as $item)
                                <div class="form-check m-0">
                                    <input type="radio" class="form-check-input fs-base" @if($item == $promjer) selected @endif wire:model="promjer" value="{{ $item }}" data-count-id="promjerFilter-drawer">
                                    <label for="living-room-2" class="form-check-label d-flex align-items-end">
                                        {{ $item }}
                                        {{--<span class="fs-xs text-body-secondary ps-2 ms-auto">657</span>--}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Price filter -->
             {{--  <div class="accordion-item">
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
                 --}}
                <a class="btn btn-primary btn-block mt-4" wire:click="cleanFilter()">
                    <i class="ci-trash-empty me-1"></i>
                    <span class="animate-target text-nowrap">Očisti</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 0 gy-4" id="productGrid">
        <!-- Toolbar-->
        @foreach($products as $product)
          {{-- dd($product->toArray()) --}}
            <div class="col">
                <div class="card h-100   hover-effect-opacity hover-effect-scale rounded-4 overflow-hidden">
                        <div class="card-img-top  position-relative bg-white overflow-hidden">

                            <a class="  d-block mb-3 pt-3" href="{{ $product->url }}">
                                <img loading="lazy" src="{{ $product->thumb }}" width="300" height="300" alt="{{ $product->name }}" >
                            </a>
                        </div>


                        <div class="card-body p-3 pb-1">
                        <div class="w-100 min-w-0 px-0 pb-1 ">


                            <h3 class="nav min-w-0 mb-0 pb-1 mb-2">
                                <a class="nav-link  p-0" href="{{ $product->url }}">
                                    <span class=" animate-target">{{ $product->name }}</span>
                                </a>
                            </h3>

                            <div class="d-flex align-items-center  mb-0">
                                @if($product->iskoristivost and $product->prijanjanje and $product->buka)
                                    <p class="criteria-icons fs-sm fw-normal "> <span><i class="fa-solid fa-gas-pump"></i> {{$product->iskoristivost }} <span class="icon-separator">|</span></span> <span><i class="fa-solid fa-cloud-showers-heavy"></i> {{ $product->prijanjanje }} <span class="icon-separator">|</span></span> <span><i class="fa-solid fa-volume-high"></i> {{ $product->buka }} </span> </p>
                                @endif
                            </div>

                            @if($product->quantity > 0)
                                <div class="d-flex align-items-center text-success fs-sm ms-auto mb-3">
                                    <i class="ci-check-circle fs-base me-2"></i>
                                    Dostupno: {{$product->quantity}}
                                </div>
                            @else

                                <div class="d-flex align-items-center text-danger fs-sm ms-auto mb-3">
                                    <i class="ci-check-circle fs-base me-2"></i>
                                    Nedostupno: {{$product->quantity}}
                                </div>

                            @endif

                            <div class="d-flex align-items-center justify-content-between">
                                @if ($product->special() && ($product->special() < $product->main_price))
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
