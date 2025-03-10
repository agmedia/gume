<footer class="footer position-relative bg-dark border-top">
    <span class="position-absolute top-0 start-0 w-100 h-100 bg-body d-none d-block-dark"></span>
    <div class="container position-relative z-1 pt-sm-2 pt-md-3 pt-lg-4" data-bs-theme="dark">



        <!-- Columns with links that are turned into accordion on screens < 500px wide (sm breakpoint) -->
        <div class="accordion py-5" id="footerLinks">
            <div class="row">
                <div class="col-md-4 d-sm-flex flex-md-column align-items-center align-items-md-start pb-3 mb-sm-4">
                    <h4 class="mb-sm-0 mb-md-4 me-4">
                        <a class="text-dark-emphasis text-decoration-none" href="{{ route('index') }}"><img style="max-width:180px" src="{{ asset('assets/images/pneu-max.svg') }}" alt ="PNEU-MAX | Auto gume i ugradnja"/></a>


                    </h4>
                    <p class="text-body fs-lg text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4"><span class="animate-underline"><a class="animate-target text-dark-emphasis fw-medium text-decoration-none" href="tel:+38515613930">+385 1 561 3930</a></span></p>
                    <p class="text-body fs-sm text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                        <strong>Adresa</strong> <br>Severinska 4
                        10000 Zagreb </p>
                    <p class="text-body fs-sm text-sm-end text-md-start mb-sm-0 mb-md-3 ms-0 ms-sm-auto ms-md-0 me-4">
                        <strong> Radno vrijeme</strong><br>
                        Ponedjeljak - petak: 08:00 - 18:00<br>
                        Subota: 08:00 - 13:00
                    </p>

                    <div class="d-flex gap-3 mt-2">
                        <a class="btn btn-icon btn-secondary rounded-circle" href="#!" aria-label="Instagram">
                            <i class="ci-instagram fs-base"></i>
                        </a>
                        <a class="btn btn-icon btn-secondary rounded-circle" href="#!" aria-label="Facebook">
                            <i class="ci-facebook fs-base"></i>
                        </a>
                        <a class="btn btn-icon btn-secondary rounded-circle" href="#!" aria-label="YouTube">
                            <i class="ci-youtube fs-base"></i>
                        </a>
                        <a class="btn btn-icon btn-secondary rounded-circle" href="#!" aria-label="Telegram">
                            <i class="ci-telegram fs-base"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row row-cols-1 row-cols-sm-3 gx-3 gx-md-4">
                        <div class="accordion-item col border-0">
                            <h6 class="accordion-header" id="companyHeading">
                                <span class="text-dark-emphasis d-none d-sm-block">Informacije</span>
                                <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#companyLinks" aria-expanded="false" aria-controls="companyLinks">Informacije</button>
                            </h6>
                            <div class="accordion-collapse collapse d-sm-block" id="companyLinks" aria-labelledby="companyHeading" data-bs-parent="#footerLinks">
                                <ul class="nav flex-column gap-2 pt-sm-3 pb-3 mt-n1 mb-1">
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">O nama</a>
                                    </li>

                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Montažni partneri</a>
                                    </li>
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Česta pitanja</a>
                                    </li>
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Kontaktirajte nas</a>
                                    </li>
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Novosti</a>
                                    </li>
                                </ul>
                            </div>
                            <hr class="d-sm-none my-0">
                        </div>
                        <div class="accordion-item col border-0">
                            <h6 class="accordion-header" id="accountHeading">
                                <span class="text-dark-emphasis d-none d-sm-block">Uvjeti kupnje</span>
                                <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#accountLinks" aria-expanded="false" aria-controls="accountLinks">Uvjeti kupnje</button>
                            </h6>
                            <div class="accordion-collapse collapse d-sm-block" id="accountLinks" aria-labelledby="accountHeading" data-bs-parent="#footerLinks">
                                <ul class="nav flex-column gap-2 pt-sm-3 pb-3 mt-n1 mb-1">

                                    @foreach ($uvjeti_kupnje as $page)
                                        <li class="d-flex w-100 pt-1"><a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="{{ route('catalog.route.page', ['page' => $page]) }}">{{ $page->title }}</a></li>
                                    @endforeach

                                </ul>
                            </div>
                            <hr class="d-sm-none my-0">
                        </div>
                        <div class="accordion-item col border-0">
                            <h6 class="accordion-header" id="customerHeading">
                                <span class="text-dark-emphasis d-none d-sm-block">Web shop</span>
                                <button type="button" class="accordion-button collapsed py-3 d-sm-none" data-bs-toggle="collapse" data-bs-target="#customerLinks" aria-expanded="false" aria-controls="customerLinks">Web shop</button>
                            </h6>
                            <div class="accordion-collapse collapse d-sm-block" id="customerLinks" aria-labelledby="customerHeading" data-bs-parent="#footerLinks">
                                <ul class="nav flex-column gap-2 pt-sm-3 pb-3 mt-n1 mb-1">
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Auto gume</a>
                                    </li>
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Naplatci / Felge</a>
                                    </li>
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Moto gume</a>
                                    </li>
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Scooter gume</a>
                                    </li>
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Hotel za gume - B2C</a>
                                    </li>
                                    <li class="d-flex w-100 pt-1">
                                        <a class="nav-link animate-underline animate-target d-inline fw-normal text-truncate p-0" href="#!">Dodatna oprema</a>
                                    </li>
                                </ul>
                            </div>
                            <hr class="d-sm-none my-0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category / tag links -->


        <!-- Copyright + Payment methods -->
        <div class="d-md-flex align-items-center border-top py-4">
            <div class="d-flex gap-2 gap-sm-3 justify-content-center ms-md-auto mb-4 mb-md-0 order-md-2">
                <div>
                    <img src="{{ asset('assets/img/payment-methods/visa-dark-mode.svg') }}" alt="Visa">
                </div>
                <div>
                    <img src="{{ asset('assets/img/payment-methods/mastercard.svg') }}" alt="Mastercard">
                </div>
                <div>
                    <img src="{{ asset('assets/img/payment-methods/maestro.svg') }}" alt="Maestro">
                </div>
                <div>
                    <img src="{{ asset('assets/img/payment-methods/amex.svg') }}" alt="Amex">
                </div>

            </div>
            <p class="text-body fs-xs text-center text-md-start mb-0 me-4 order-md-1">&copy; Sva prava pridržana. Web by <span class="animate-underline"><a class="animate-target text-dark-emphasis fw-medium text-decoration-none" href="https://www.agmedia.hr" target="_blank" rel="noreferrer">AG media</a></span></p>
        </div>
    </div>
</footer>
