@extends('back.layouts.backend')

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Brandovi</h1>
                <a class="btn btn-hero-success my-2" href="{{ route('brands.create') }}">
                    <i class="far fa-fw fa-plus-square"></i><span class="d-none d-sm-inline ml-1"> Novi brand</span>
                </a>
            </div>
        </div>
    </div>



    <div class="content">
    @include('back.layouts.partials.session')
    <!-- All Products -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Svi Brandovi <small class="font-weight-light">{{ $brands->total() }}</small></h3>
                <div class="block-options">
                    <!-- Search Form -->
                    <form action="{{ route('brands') }}" method="GET">
                        <div class="block-options-item">
                            <input type="text" class="form-control" id="search-input" name="search" placeholder="Pretraži autore" value="{{ request()->query('search') }}">
                        </div>
                        <div class="block-options-item">
                            <a href="{{ route('brands') }}" class="btn btn-hero-sm btn-secondary"><i class="fa fa-search-minus"></i> Očisti</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="block-content">
                <!-- All Products Table -->
                <div class="table-responsive">
                    <table class="table table-borderless table-striped table-vcenter">
                        <thead>
                        <tr>
                            <th >Naziv</th>
                            <th style="width: 100px;" class="text-right">Status</th>
                            <th style="width: 100px;" class="text-right">Uredi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($brands as $brand)
                            <tr>
                                <td class="font-size-sm">{{ $brand->title }}</td>
                                <td class="text-right">
                                    @if ($brand->status)
                                        <span class="badge badge-success">Aktivan</span>
                                    @else
                                        <span class="badge badge-secondary">Neaktivan</span>
                                    @endif
                                </td>
                                <td class="text-right font-size-sm">
                                    <a href="{{ route('brands.edit', ['brand' => $brand]) }}" class="btn btn-sm btn-secondary js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Uredi">
                                        <i class="fa fa-pencil-alt"></i>
                                    </a>
                                    <button class="btn btn-sm btn-alt-danger" onclick="event.preventDefault(); deleteItem({{ $brand->id }}, '{{ route('brands.destroy.api') }}');"><i class="fa fa-fw fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    Nema brandova.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $brands->links() }}
            </div>
        </div>
        <!-- END All Products -->
    </div>
@endsection

@push('js_after')
    <script>
        function Search(event) {
            console.log(event.key)
        }
    </script>
@endpush
