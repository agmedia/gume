@extends('front.layouts.app')

@section('content')

    <!-- Page Title (Light)-->
    <div class="container pb-0 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
                <li class="breadcrumb-item"><a class="text-nowrap" href="/"> Naslovnica</a></li>
                <li class="breadcrumb-item text-nowrap active" aria-current="page">Česta pitanja</li>
            </ol>
        </nav>



    </div>


    <!-- FAQ (Accordion) -->
    <section class="container pt-1 py-5 mb-1 mb-sm-2 mb-md-3 mb-lg-4 mb-xl-5">
        <div class="row pt-xl-2">
            <div class="col-md-4 col-xl-3 mb-4 mb-md-0" style="margin-top: -120px">
                <div class="sticky-md-top text-center text-md-start pe-md-4 pe-lg-5 pe-xl-0" style="padding-top: 120px;">
                    <h2>Česta pitanja</h2>
                    <p class="pb-2 pb-md-3">Za sva dodatna pitanja slobodno nas kontaktirajte!</p>
                    <a class="btn btn-lg btn-primary" href="{{ route('kontakt') }}">Kontaktirajte nas</a>
                </div>
            </div>
            <div class="col-md-8 offset-xl-1">

                <!-- Accordion of questions -->
                <div class="accordion" id="faq">




                        @foreach ($faq as $fa)
                    <!-- Question -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faqHeading-{{ $fa->id }}">
                            <button type="button" class="accordion-button hover-effect-underline collapsed" data-bs-toggle="collapse" data-bs-target="#faqCollapse-{{ $fa->id }}" aria-expanded="false" aria-controls="faqCollapse-{{ $fa->id }}">
                                <span class="me-2">{{ $fa->title }}</span>
                            </button>
                        </h3>
                        <div class="accordion-collapse collapse" id="faqCollapse-{{ $fa->id }}" aria-labelledby="faqHeading-{{ $fa->id }}" data-bs-parent="#faq">
                            <div class="accordion-body">{!! $fa->description !!}</div>
                        </div>
                    </div>
                        @endforeach




                </div>
            </div>
        </div>
    </section>


















@endsection
