<aside class="col-lg-4 pt-4 pt-lg-0 pe-xl-5">
    <div class=" rounded-3 shadow-lg pt-1 mb-5 mb-lg-0">
        <div class="d-md-flex justify-content-between align-items-center text-center text-md-start p-4">
            <div class="d-md-flex align-items-center">
                <div class="img-thumbnail rounded-circle position-relative flex-shrink-0 mx-auto mb-2 mx-md-0 mb-md-0" style="width: 6.375rem;"><img class="rounded-circle" src="{{ config('settings.images_domain') . 'assets/app-icons/apple-touch-icon.png' }}" alt="{{ $user->details->fname ? $user->details->fname . ' ' . $user->details->lname: $user->name }}"></div>
                <div class="ps-md-3">
                    <h3 class="fs-base mb-0">{{ $user->details->fname ? $user->details->fname . ' ' . $user->details->lname: $user->name }}</h3><span class="text-accent fs-sm">{{ $user->email }}</span>
                </div>
            </div><a class="btn btn-primary d-lg-none mb-2 mt-3 mt-md-0" href="#account-menu" data-bs-toggle="collapse" aria-expanded="false"><i class="ci-menu me-2"></i>Navigacija</a>
        </div>
        <div class="d-lg-block collapse" id="account-menu">

            <nav class="list-group list-group-borderless p-3">

                    <a class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('moj-racun') ? 'active' : '' }}" href="{{ route('moj-racun') }}">
                        <i class="ci-user fs-base opacity-75 me-2"></i>Moji podaci
                    </a>



                    <a class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('moje-narudzbe') ? 'active' : '' }}" href="{{ route('moje-narudzbe') }}">
                        <i class="ci-shopping-bag fs-base opacity-75 me-2"></i> Narudžbe
                    </a>

                <a class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('hoteli') ? 'active' : '' }}" href="{{ route('hoteli') }}">
                    <i class="ci-box fs-base opacity-75 me-2"></i> Hotel za gume
                </a>



                    <a class="list-group-item list-group-item-action d-flex align-items-center" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="ci-log-out fs-base opacity-75 me-2"></i> Odjava
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

            </nav>
        </div>
    </div>
</aside>
