@extends('front.layouts.app')

@push('css_after')
@endpush

@section('content')

    <form action="#" method="POST" enctype="multipart/form-data">
        @csrf

        <h4 class="offcanvas-title" id="deliveryDateTimeLabel">Vaši info podaci</h4>

    </form>

@endsection

@push('js_after')
@endpush
