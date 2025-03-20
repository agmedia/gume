@extends('front.layouts.app')

@if (isset($gdl))
    @section('google_data_layer')
        <script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ ecommerce: null });
            window.dataLayer.push({
                'event': 'view_cart',
                'ecommerce': {'items': <?php echo json_encode($gdl); ?>}
            });
        </script>
    @endsection
@endif

@section('content')
    <div class="container pb-5 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
        <!-- Breadcrumb -->
        <nav class="position-relative my-3" aria-label="breadcrumb" style="z-index: 1021">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Naslovnica</a></li>
                <li class="breadcrumb-item" aria-current="page">Košarica</li>
            </ol>
        </nav>
        <h1 class="h3 position-relative pb-0" style="z-index: 1021">Košarica</h1>

        @livewire('front.checkout.cart')
    </div>
@endsection
