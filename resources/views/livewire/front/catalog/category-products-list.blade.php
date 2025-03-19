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
                            <option value="{{ $item }}" @if($item == $sezona) selected @endif>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="visina" wire:change="dropdownFilterSelected('visina', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Visina">
                        <option value="">Visina</option>
                        @foreach ($visine as $item)
                            <option value="{{ $item }}" @if($item == $sezona) selected @endif>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0 me-1" wire:ignore>
                    <select class="form-select rounded-pill" wire:model="promjer" wire:change="dropdownFilterSelected('promjer', $event.target.value)" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Promjer" data-placeholder="Promjer">
                        @foreach ($promjeri as $item)
                            <option value="{{ $item }}" @if($item == $sezona) selected @endif>{{ $item }}</option>
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
                    {{--<a class="nav-link animate-underline px-2" href="#shopFilters" data-bs-toggle="offcanvas" aria-controls="shopFilters">
                        <i class="ci-filter me-1"></i>
                        <span class="animate-target text-nowrap">Svi filteri</span>
                    </a>--}}
                    <a class="nav-link animate-underline px-2" wire:click="showFilter()">
                        <i class="ci-filter me-1"></i>
                        <span class="animate-target text-nowrap">Filteri</span>
                    </a>
                    {{--<a class="nav-link animate-underline px-2" href="#!">Očisti filtere</a>--}}
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
