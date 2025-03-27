@extends('front.layouts.app')

@section('content')

    <!-- Order Details Modal-->


    @include('front.customer.layouts.header')

    <div class="container pb-5 mb-2 mb-md-4">
        <div class="row">
        @include('front.customer.layouts.sidebar')

            <!-- Content  -->
            <section class="col-lg-8">
                <!-- Toolbar-->
                <div class="d-none d-lg-flex justify-content-between align-items-center pt-lg-3 pb-2 pb-lg-2 mb-lg-3">
                    <h6 class="fs-base text-light mb-0">Pogledajte povijest svoji narudžbi:</h6><a class="btn btn-primary btn-sm" href="{{ route('logout') }}">  <i class="ci-log-out fs-base opacity-75 me-2"></i> Odjava</a>
                </div>
                <!-- Orders list-->
                <div class="table-responsive fs-md mb-4">
                    <table class="table table-hover mb-0">
                        <thead>
                        <tr>
                            <th>Broj narudžbe #</th>
                            <th>Datum</th>
                            <th>Status</th>
                            <th>Ukupno</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>


            </section>
        </div>
    </div>

@endsection
