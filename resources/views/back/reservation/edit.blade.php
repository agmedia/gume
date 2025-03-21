@extends('back.layouts.backend')
@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endpush

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Rezervacija edit</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('products') }}">Rezervacije</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>


    <!-- Page Content -->
    <div class="content">
        @include('back.layouts.partials.session')

        <form action="{{ isset($reservation) ? route('reservations.update', ['reservation' => $reservation]) : route('reservations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($reservation))
            {{ method_field('PATCH') }}
        @endif

            <!-- Log Messages -->
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">...</h3>
                    <div class="block-options">

                    </div>
                </div>

                <div class="block-content">
                    ...
                </div>
            </div>

            <div class="block">
                <div class="block-content">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-hero-success mb-3">
                                <i class="fas fa-save mr-1"></i> Snimi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
    <!-- END Page Content -->

@endsection

@push('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(() => {
            $('#status-select').select2({});
        });
    </script>

@endpush
