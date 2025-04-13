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
                    <h6 class="fs-base  mb-0">Potvrda o preuzimanju guma/feligu na skladište</h6><a class="btn btn-primary btn-sm" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">  <i class="ci-log-out fs-base opacity-75 me-2"></i> Odjava</a>
                </div>
                <!-- Orders list-->

                @if( isset($user->hotels) and $user->hotels)

                @foreach($user->hotels as $hotel)

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


                            <table class="table table-bordered border-white" >
                                <tbody>
                                <tr>
                                    <td colspan="3" style="vertical-align: middle;">
                                <h5>Stanje guma</h5>
                                <span class="badge bg-success fs-md">Odlično</span> <span class="badge bg-warning fs-md">Srednje</span> <span class="badge bg-danger fs-md">Loše / oštećeno</span>
                                    </td>
                                </tr>



                                <tr>
                                    <td style="vertical-align: middle;">
                                        @if($hotel->condition_lp == 'Odlično')
                                             <span class="badge bg-success fs-md">Lijeva prednja</span>
                                        @elseif($hotel->condition_lp == 'Srednje')
                                            <span class="badge bg-warning fs-md">Lijeva prednja</span>
                                        @elseif($hotel->condition_lp ='Loše/oštećeno')
                                            <span class="badge bg-danger fs-md">Lijeva prednja</span>
                                        @else
                                            <span class="badge bg-secondary fs-md">Nije upisano</span>
                                        @endif

                                    </td>
                                    <td  style="vertical-align: middle;text-align:center" rowspan="3" >
                                        <img src="{{ asset('media/img/auto-tlocrt.jpg') }}" style="max-width:250px"/></td>
                                    <td style="vertical-align: middle;">
                                        @if($hotel->condition_dp == 'Odlično')
                                            <span class="badge bg-success fs-md">Desna prednja</span>
                                        @elseif($hotel->condition_dp == 'Srednje')
                                            <span class="badge bg-warning fs-md">Desna prednja</span>
                                        @elseif($hotel->condition_dp == 'Loše/oštećeno')
                                            <span class="badge bg-danger fs-md">Desna prednja</span>
                                        @else
                                            <span class="badge bg-secondary fs-md">Nije upisano</span>
                                        @endif


                                    </td>
                                </tr>

                                <tr>
                                    <td style="vertical-align: middle;">

                                        @if($hotel->condition_lz == 'Odlično')
                                            <span class="badge bg-success fs-md">Lijeva zadnja</span>
                                        @elseif($hotel->condition_lz == 'Srednje')
                                            <span class="badge bg-warning fs-md">Lijeva zadnja</span>
                                        @elseif($hotel->condition_lz == 'Loše/oštećeno')
                                            <span class="badge bg-danger fs-md">Lijeva zadnja</span>
                                        @else
                                            <span class="badge bg-secondary fs-md">Nije upisano</span>
                                        @endif


                                    </td>
                                    <td style="vertical-align: middle;">

                                        @if($hotel->condition_dz == 'Odlično')
                                            <span class="badge bg-success fs-md">Desna zadnja</span>
                                        @elseif($hotel->condition_dz == 'Srednje')
                                            <span class="badge bg-warning fs-md">Desna zadnja</span>
                                        @elseif($hotel->condition_dz == 'Loše/oštećeno')
                                            <span class="badge bg-danger fs-md">Desna zadnja</span>
                                        @else
                                            <span class="badge bg-secondary fs-md">Nije upisano</span>
                                        @endif


                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>


                    </div>

                </div>


                @endforeach
                @else
                    <div class="card  bg-body-tertiary border-0 p-md-2 pb-5">
                        <div class="card-body">
                            <p>Nemate guma na čuvanju.</p>
                        </div>
                    </div>




                @endif



            </section>
        </div>
    </div>

@endsection
