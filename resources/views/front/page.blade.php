@extends('front.layouts.app')
@if (request()->routeIs(['index']))
    @section ( 'title', 'PNEU-MAX | Auto gume i ugradnja' )
@section ( 'description', 'Veliki Izbor Auto Guma po Najpovoljnijim Cijenama,Besplatna Montaža na Vozilo!' )


@push('meta_tags')

    <link rel="canonical" href="{{ env('APP_URL')}}" />
    <meta property="og:locale" content="hr_HR" />
    <meta property="og:type" content="product" />
    <meta property="og:title" content="PNEU-MAX | Auto gume i ugradnja" />
    <meta property="og:description" content="Veliki Izbor Auto Guma po Najpovoljnijim Cijenama,Besplatna Montaža na Vozilo!" />
    <meta property="og:url" content="{{ env('APP_URL')}}"  />
    <meta property="og:site_name" content="PNEU-MAX | Auto gume i ugradnja" />
    <meta property="og:image" content="{{ asset('assets/images/03.jpg') }}" />
    <meta property="og:image:secure_url" content="{{ asset('assets/images/03.jpg') }}" />
    <meta property="og:image:width" content="1920" />
    <meta property="og:image:height" content="720" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:alt" content="PNEU-MAX | Auto gume i ugradnja" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="PNEU-MAX | Auto gume i ugradnja" />
    <meta name="twitter:description" content="Veliki Izbor Auto Guma po Najpovoljnijim Cijenama,Besplatna Montaža na Vozilo!" />
    <meta name="twitter:image" content="{{ asset('assets/images/03.jpg') }}" />

@endpush

@else
    @section ( 'title', $page->title. ' - PNEU-MAX' )
@section ( 'description', $page->meta_description )
@endif

@section('content')

    @if (request()->routeIs(['index']))





        {!! $page->description !!}

   {{--@include('front.layouts.partials.otkupwidget') --}}



    @else

        <main class="content-wrapper">
            <div class="container py-5 pt-0 mb-2 mt-n2 mt-sm-1 my-md-3 my-lg-4 mb-xl-5">

                <div class="row justify-content-center">
                    <div class="col-lg-11 col-xl-10 col-xxl-9">
                        <nav class="pt-0 my-3  mt-1" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('index') }}">Naslovnica</a></li>

                                <li class="breadcrumb-item active" aria-current="page">{{ $page->title }}</li>
                            </ol>
                        </nav>
                        <h1 class="h2 pb-2 pb-sm-3 pb-lg-4">{{ $page->title }}</h1>
                        <hr class="mt-0">
                        {!! $page->description !!}
                    </div>
                </div>
            </div>
        </main>





    @endif

@endsection
