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
                    <h6 class="fs-base  mb-0">Potvrda o preuzimanju guma/feligu na skladište</h6><a class="btn btn-primary btn-sm" href="{{ route('logout') }}">  <i class="ci-log-out fs-base opacity-75 me-2"></i> Odjava</a>
                </div>
                <!-- Orders list-->

                @foreach($user->hotels as $hotel) @endforeach

                <div class="card  bg-body-tertiary border-0 p-md-2 pb-5">
                    <div class="card-body">
                        <!-- Table with striped columns -->
                        <div class="table-responsive">
                            <table class="table table-bordered border-light-subtle">
                                <tbody>
                                <tr>
                                    <td>Ime kupca</td>
                                    <td>{{ $user->details->fname }} {{ $user->details->lname }}</td>
                                </tr>
                                <tr>
                                    <td>Broj dokumenta</td>
                                    <td>{{ $hotel->invoice }}</td>
                                </tr>
                                <tr>
                                    <td>Datum preuzmanja</td>
                                    <td>{{ \Carbon\Carbon::make($hotel->start_date)->format('d.m.Y')}}</td>
                                </tr>
                                <tr>
                                    <td>Istek roka čuvanja</td>
                                    <td>{{ \Carbon\Carbon::make($hotel->end_date)->format('d.m.Y')}}</td>
                                </tr>
                                <tr>
                                    <td>Mobitel</td>
                                    <td>{{ $user->details->phone }}</td>
                                </tr>
                                <tr>
                                    <td>Proizvođač</td>
                                    <td>{{ $hotel->brand->title}}</td>
                                </tr>
                                <tr>
                                    <td>Dimenzija</td>
                                    <td>{{ $hotel->dimension}}</td>
                                </tr>
                                <tr>
                                    <td>Vrsta gume</td>
                                    <td>{{ $hotel->type}}</td>
                                </tr>
                                <tr>
                                    <td>Broj komada</td>
                                    <td>{{ $hotel->quantity}}</td>
                                </tr>
                                <tr>
                                    <td>Registarska oznaka</td>
                                    <td>{{ $hotel->reg}}</td>
                                </tr>

                                <tr>

                                    <td colspan="2"><p><strong>Napomena</strong></p>{!! $hotel->comment !!} </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>


                    </div>

                </div>






            </section>
        </div>
    </div>

@endsection
