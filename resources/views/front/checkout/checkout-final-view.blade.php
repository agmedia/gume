@extends('front.layouts.app')

@push('css_after')
@endpush

@section('content')

    @include('front.layouts.partials.session')

    <div class="container py-5">
        <div class="row pt-1 pt-sm-3 pt-lg-4 pb-2 pb-md-3 pb-lg-4 pb-xl-5">
            <div class="col-lg-12 col-xl-7 position-relative z-2 mb-5 mb-lg-0">

                @dump($user)
                @dump($selected_shipping)
                @dump($selected_payment)
                @dump($selected_reservation)
                @dump($cart)

                {!! $payment_form !!}

            </div>
        </div>
    </div>

@endsection

@push('js_after')
@endpush
