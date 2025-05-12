@extends('back.layouts.backend')
@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
@endpush

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Hotel guma edit</h1>
                <nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('hotels') }}">Hotel</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>


    <!-- Page Content -->
    <div class="content">
        @include('back.layouts.partials.session')

        <form action="{{ isset($hotel) ? route('hotels.update', ['hotel' => $hotel]) : route('hotels.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if (isset($hotel))
                {{ method_field('PATCH') }}
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="block">
                        <div class="block-header block-header-default">
                            <a class="btn btn-light" href="{{ route('hotels') }}">
                                <i class="fa fa-arrow-left mr-1"></i> Povratak
                            </a>
                            <div class="block-options">
                                <div class="custom-control custom-switch custom-control-success block-options-item ml-4">
                                    <input type="checkbox" class="custom-control-input" id="product-switch" name="active"{{ (isset($hotel->active) and $hotel->active) ? 'checked' : '' }}>
                                    <label class="custom-control-label pt-1" for="product-switch">Aktiviraj</label>
                                </div>
                            </div>
                            <div class="block-options">
                                <select class="js-select2 form-control" id="status-select" name="reservation_status" style="width: 100%;">
                                    <option></option>
                                    @foreach ($statuses as $id => $status)
                                        <option value="{{ $id }}" @if(isset($hotel) && $hotel->status_id == $id) selected @endif>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="block-content">
                            <div class="row justify-content-center push">
                                <div class="col-md-12">
                                    <div class="form-group row items-push mb-2">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="invoice-input">Broj dokumenta</label>
                                                <input type="text" class="form-control" name="invoice" value="{{ isset($hotel) ? $hotel->invoice : old('invoice') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="title-input">Korisnik</label>
                                            @livewire('back.catalog.user-search-input', ['user_id' => isset($hotel) ? $hotel->user_id : 0])
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="reg-input">Registracija</label>
                                                <input type="text" class="form-control" name="reg" value="{{ isset($hotel) ? $hotel->reg : old('reg') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="brand-input">Proizvođač </label>
                                                @livewire('back.catalog.brand-search-input', ['brand_id' => isset($hotel) ? $hotel->brand_id : 0])


                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="dimension-input">Dimenzije</label>
                                                <input type="text" class="form-control" name="dimension" value="{{ isset($hotel) ? $hotel->dimension : old('dimension') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="type-input">Vrsta</label>
                                                <input type="text" class="form-control" name="type" value="{{ isset($hotel) ? $hotel->type : old('type') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="quantity-input">Br. komada</label>
                                                <input type="text" class="form-control" name="quantity" value="{{ isset($hotel) ? $hotel->quantity : old('quantity') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="dm-post-edit-slug">Napomena</label>
                                                <textarea id="message-editor" name="message">{!! isset($hotel) ? $hotel->message : old('message') !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title">Stanje guma</h3>
                                </div>
                                <div class="block-content">
                                    <div class="row items-push mb-2">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reg-input">Lijeva prednja</label>
                                                <select class="js-select2 form-control" id="condition_lp-select" name="condition_lp" style="width: 100%;">
                                                    @foreach ($conditions as $condition)
                                                        <option value="{{ $condition }}" @if(isset($hotel) && $hotel->condition_lp == $condition) selected @endif>{{ $condition }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reg-input">Desna prednja</label>
                                                <select class="js-select2 form-control" id="condition_dp-select" name="condition_dp" style="width: 100%;">
                                                    @foreach ($conditions as $condition)
                                                        <option value="{{ $condition }}" @if(isset($hotel) && $hotel->condition_dp == $condition) selected @endif>{{ $condition }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reg-input">Lijeva zadnja</label>
                                                <select class="js-select2 form-control" id="condition_lz-select" name="condition_lz" style="width: 100%;">
                                                    @foreach ($conditions as $condition)
                                                        <option value="{{ $condition }}" @if(isset($hotel) && $hotel->condition_lz == $condition) selected @endif>{{ $condition }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reg-input">Desna zadnja</label>
                                                <select class="js-select2 form-control" id="condition_dz-select" name="condition_dz" style="width: 100%;">
                                                    @foreach ($conditions as $condition)
                                                        <option value="{{ $condition }}" @if(isset($hotel) && $hotel->condition_dz == $condition) selected @endif>{{ $condition }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="block">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title">Datum skladištenja</h3>
                                </div>
                                <div class="block-content">
                                    <div class="row items-push mb-2">
                                        <div class="col-md-12 mb-4">
                                            <label for="title-input">Datum preuzimanja na čuvanje</label>
                                            <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                                <input type="text" class="form-control" id="start_date_input" name="start_date" placeholder="do" value="{{ (isset($hotel->start_date) && $hotel->start_date != '0000-00-00 00:00:00') ? \Carbon\Carbon::make($hotel->start_date)->format('m/d/Y') : '' }}" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="reg-input">Datum isteka čuvanja</label>
                                                <input type="text" class="form-control"  name="end_date" value="{{ (isset($hotel->start_date) && $hotel->start_date != '0000-00-00 00:00:00') ? \Carbon\Carbon::make($hotel->start_date)->addMonths(6)->format('m/d/Y') : '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    <script src="{{ asset('js/plugins/ckeditor5-classic/build/ckeditor.js') }}"></script>
    <script src="{{ asset('js/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/jquery.maskedinput/jquery.maskedinput.min.js') }}"></script>

    <!-- Page JS Helpers (CKEditor 5 plugins) -->
    <script>jQuery(function(){Dashmix.helpers(['datepicker']);});</script>

    <script>
        $(() => {
            $('#status-select').select2({
                placeholder: 'Odaberite status',
                minimumResultsForSearch: Infinity,
            });

            $('#condition_lp-select').select2({ minimumResultsForSearch: Infinity });
            $('#condition_dp-select').select2({ minimumResultsForSearch: Infinity });
            $('#condition_lz-select').select2({ minimumResultsForSearch: Infinity });
            $('#condition_dz-select').select2({ minimumResultsForSearch: Infinity });

            ClassicEditor.create(document.querySelector('#message-editor'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });



        })
    </script>

@endpush
