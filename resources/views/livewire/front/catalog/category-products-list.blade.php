<div>
    <!-- Filter -->
    <div class="bg-body-tertiary p-3 rounded-3 mb-3" >
        <div class="row align-items-center pt-1">
            <div class="col-12 d-md-flex d-block gap-2 ">
                <div class="d-block w-100 mb-2 mb-md-0 me-1">
                    <select class="form-select rounded-pill" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]}}' aria-label="Kategorija">
                        <option value="">Kategorija</option>
                        <option value="popular" selected>Ljetne gume</option>
                        <option value="match">Zimske gume</option>
                        <option value="new">Cjelogodišnje gume</option>
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0  me-1">
                    <select class="form-select rounded-pill" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Širina">
                        <option value="">Širina</option>
                        <option value="145" >145</option>
                        <option value="155">155</option>
                        <option value="165">165</option>
                        <option value="175" selected>175</option>
                        <option value="185">185</option>
                        <option value="195">195</option>
                        <option value="205">205</option>
                        <option value="215">215</option>
                        <option value="225">225</option>
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0  me-1">
                    <select class="form-select rounded-pill" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Visina">
                        <option value="">Visina</option>
                        <option value="35" >35</option>
                        <option value="40">40</option>
                        <option value="45">45</option>
                        <option value="50">50</option>
                        <option value="55">55</option>
                        <option value="60">60</option>
                        <option value="65">65</option>
                        <option value="70">70</option>
                        <option value="80">80</option>
                    </select>
                </div>
                <div class="d-block w-100 mb-2 mb-md-0  me-1">
                    <select class="form-select rounded-pill" data-select='{"classNames": {"containerInner": ["form-select", "filter-select", "rounded-pill"]},"searchEnabled": true,"searchPlaceholderValue": ["Pretraži"]}' aria-label="Promjer" data-placeholder="Promjer">
                        <option value="10" >R 10</option>
                        <option value="11">R 11</option>
                        <option value="12">R 12</option>
                        <option value="13">R 13</option>
                        <option value="14">R 14</option>
                        <option value="15">R 15</option>
                        <option value="16">R 16</option>
                        <option value="17">R 17</option>
                        <option value="18">R 18</option>
                        <option value="19">R 19</option>
                        <option value="20">R 20</option>
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
        </div>
    </div>
    <!-- Selected filters -->
    <div class="d-flex flex-wrap align-items-center gap-2 text-nowrap mb-5 pb-4 border-bottom ">
        <button type="button" class="btn btn-sm btn-secondary rounded-pill me-1">
            <i class="ci-close fs-sm me-1 ms-n1"></i>
            Ljetne gume
        </button>
        <button type="button" class="btn btn-sm btn-secondary rounded-pill me-1">
            <i class="ci-close fs-sm me-1 ms-n1"></i>
            Širina 175
        </button>
        <div class="nav ps-1">
            <a class="nav-link fs-xs text-decoration-underline px-0" href="#!">Očisti filtere</a>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 0 gy-5" id="productGrid" >
        <!-- Toolbar-->
        @foreach($products as $product)
            <div class="col">
                <div class="animate-underline">
                    <a class=" ratio ratio-1x1 d-block mb-3" href="{{ $product->url }}">
                        <img loading="lazy" src="{{ $product->thumb }}" width="300" height="300" alt="{{ $product->name }}"  class="rounded-4">
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
                            @if ($product->special())
                                <div class="h5 lh-1 mb-0">{{ $product->main_special_text }} <del class="text-body-tertiary fs-sm fw-normal">{{ $product->main_price_text }}</del></div>
                            @else
                                <div class="h5 lh-1 mb-0"> {{ $product->main_price_text }}</div>
                            @endif

                            <button type="button" disabled="{{ ! $product->quantity }}" onclick="" class="product-card-button btn btn-icon btn-primary animate-slide-end ms-2" aria-label="Add to Cart">
                                <i class="ci-shopping-cart fs-base animate-target"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
