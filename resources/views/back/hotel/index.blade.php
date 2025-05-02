@extends('back.layouts.backend')

@push('css_before')
@endpush

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Hotel za gume</h1>
                <a class="btn btn-hero-success my-2" href="{{ route('hotels.create') }}">
                    <i class="far fa-fw fa-plus-square"></i><span class="d-none d-sm-inline ml-1"> Novi upis</span>
                </a>

            </div>
        </div>
    </div>


    <!-- Page Content -->
    <div class="content">
    @include('back.layouts.partials.session')
    <!-- All Orders -->
        <div class="block block-rounded">
            <div class="block-header block-header-default">
                <h3 class="block-title">Lista hotelskih rezervacija <small class="font-weight-light">{{ $hotels->total() }}</small></h3>
                <div class="block-options">
                    <div class="dropdown">
                        <button type="button" class="btn btn-light" id="dropdown-ecom-filters" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Filtriraj
                            <i class="fa fa-angle-down ml-1"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-ecom-filters">
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:setPageURL('status', 0)">
                                Svi statusi
                            </a>
                            @foreach ($statuses as $status)
                                <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:setPageURL('status', {{ $status->id }})">
                                    <span class="badge badge-pill badge-{{ $status->color }}">{{ $status->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-content bg-body-dark">
                <!-- Search Form -->
                <form action="{{ route('hotels') }}" method="GET">
                    <div class="form-group">
                        <div class="form-group">
                            <div class="input-group flex-nowrap">
                                <input type="text" class="form-control py-3 text-center" name="search" id="search-input" value="{{ request()->input('search') }}" placeholder="Pretraži po broju dokumenta, korisniku ili registarskom broju...">
                                <button type="submit" class="btn btn-primary fs-base" onclick="setPageURL('search', $('#search-input').val());"><i class="fa fa-search"></i> </button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END Search Form -->
            </div>
            <div class="block-content">
                <!-- All Orders Table -->
                <div class="table-responsive">
                    <table class="table table-borderless table-striped table-vcenter font-size-sm">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 36px;">Br.</th>
                            <th class="text-center">Datum od-do</th>
                            <th class="text-center">Količina</th>
                            <th>Status</th>
                            <th>Kupac</th>
                            <th class="text-right font-size-sm" style="width: 100px;">Akcije</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($hotels->sortByDesc('id') as $hotel)
                            <tr>
                                <td class="text-center">
                                    <a class="font-w600" href="{{ route('hotels.edit', ['hotel' => $hotel]) }}">
                                        <strong>{{ $hotel->id }}</strong>
                                    </a>
                                </td>
                                <td class="text-center">{{ \Illuminate\Support\Carbon::make($hotel->start_date)->format('d.m.Y') }} - {{ \Illuminate\Support\Carbon::make($hotel->end_date)->format('d.m.Y') }}</td>
                                <td class="text-center">{{ $hotel->quantity }}</td>
                                <td class="font-size-base">
                                    <span class="badge badge-pill badge-{{ $hotel->status->color }}">{{ $hotel->status->title }}</span>
                                </td>
                                <td>
                                    <a class="font-w600" href="{{ route('users.edit', ['user' => $hotel->user]) }}">{{ $hotel->username }}</a>
                                </td>
                                <td class="text-right">
                                    {{--<a class="btn btn-sm btn-alt-secondary" href="{{ route('hotels.show', ['hotel' => $hotel]) }}">
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>--}}
                                    <a class="btn btn-sm btn-alt-info" href="{{ route('hotels.edit', ['hotel' => $hotel]) }}">
                                        <i class="fa fa-fw fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-alt-danger" onclick="event.preventDefault(); deleteItem({{ $hotel->id }}, '{{ route('hotels.destroy.api') }}');"><i class="fa fa-fw fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center font-size-sm" colspan="6">
                                    <label>Nema rezervacija hotela...</label>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                {{ $hotels->links() }}
            </div>
        </div>
        <!-- END All Orders -->
    </div>

@endsection

@push('js_after')

@endpush
