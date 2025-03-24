@extends('back.layouts.backend')
@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}">
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
    <div class="content content-full">
        @include('back.layouts.partials.session')

        <form action="{{ isset($reservation) ? route('reservations.update', ['reservation' => $reservation]) : route('reservations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($reservation))
            {{ method_field('PATCH') }}
        @endif

            <!-- Log Messages -->
            <div class="block">
                <div class="block-header block-header-default">
                    <a class="btn btn-light" href="{{ route('reservations') }}">
                        <i class="fa fa-arrow-left mr-1"></i> Povratak
                    </a>
                    <div class="block-options">
                        <div class="dropdown">
                            <div class="custom-control custom-switch custom-control-success block-options-item ml-4">
                                <input type="checkbox" class="custom-control-input" id="product-switch" name="status"{{ (isset($reservation->status) and $reservation->status) ? 'checked' : '' }}>
                                <label class="custom-control-label pt-1" for="product-switch">Aktiviraj</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                 <div class="row justify-content-center push">
                    <div class="col-md-12">
                        <div class="form-group row items-push mb-3">
                            <div class="col-md-4">
                                <label for="title-input">Datum termina</label>
                                    <div class="input-daterange input-group" data-date-format="mm/dd/yyyy" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    <input type="text" class="form-control" id="reservation_date_input" name="reservation_date" placeholder="do" value="{{ (isset($reservation->reservation_date) && $reservation->reservation_date != '0000-00-00 00:00:00') ? \Carbon\Carbon::make($reservation->reservation_date)->format('d.m.Y') : '' }}" data-week-start="1" data-autoclose="true" data-today-highlight="true">
                                    </div>
                            </div>
                            <div class="col-md-4">
                                 <label for="title-input">Vrijeme termina</label>
                                    <select class="js-select2 form-control" id="vrijeme-select" name="time" style="width: 100%;">
                                        @foreach (config('settings.vrijeme_termina') as $vrijeme)
                                            <option value="{{ $vrijeme }}">{{ $vrijeme }} </option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="col-md-4">
                                <label for="title-input">Korisnik</label>
                                <select class="js-select2 form-control" id="user-select" name="user" style="width: 100%;">
                                   <option value="Tomislav Jureša">Tomislav Jureša </option>
                                </select>
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <label for="dm-post-edit-slug">Napomena</label>
                                <textarea id="message-editor" name="message">{!! isset($reservation) ? $reservation->message : old('message') !!}</textarea>
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
            $('#status-select').select2({});
        });
    </script>

            <script>
                $(() => {
                    $('#vrijeme-select').select2({
                        placeholder: 'Odaberite termin',
                        tags: true
                    });

                    $('#user-select').select2({
                        placeholder: 'Odaberite korisnika',
                        tags: true
                    });

                    ClassicEditor
                        .create( document.querySelector('#message-editor'))
                        .then( editor => {
                            console.log(editor);
                        } )
                        .catch( error => {
                            console.error(error);
                        } );



                })
            </script>

@endpush
