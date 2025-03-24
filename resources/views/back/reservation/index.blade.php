@extends('back.layouts.backend')

@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endpush

@section('content')

    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Rezervacije</h1>
                <a class="btn btn-hero-success my-2" href="{{ route('reservations.create') }}">
                    <i class="far fa-fw fa-plus-square"></i><span class="d-none d-sm-inline ml-1"> Nova rezervacija</span>
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
                <h3 class="block-title">Lista rezervacija <small class="font-weight-light">{{ $reservations->total() }}</small></h3>
                <div class="block-options">
                    <div class="dropdown">
                        <button type="button" class="btn btn-light" id="dropdown-ecom-filters" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Filtriraj
                            <i class="fa fa-angle-down ml-1"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-ecom-filters">
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:setPageURL('status', 0)">
                                Sve rezervacije
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
                <form action="{{ route('reservations') }}" method="GET">
                    <div class="form-group">
                        <div class="form-group">
                            <div class="input-group flex-nowrap">
                                <input type="text" class="form-control py-3 text-center" name="search" id="search-input" value="{{ request()->input('search') }}" placeholder="Pretraži po broju rezervacije, imenu, prezimenu ili emailu kupca...">
                                <button type="submit" class="btn btn-primary fs-base" onclick="setPageURL('search', $('#search-input').val());"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{--<div class="block-content">
                <!-- All Orders Table -->
                <div class="table-responsive">
                    <table class="table table-borderless table-striped table-vcenter font-size-sm">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 36px;">Br.</th>
                            <th class="text-center">Vrijeme</th>
                            <th>Status</th>
                            <th>Kupac</th>
                            <th class="text-right font-size-sm" style="width: 100px;">Detalji</th>
                        </tr>
                        </thead>
                        <tbody>

                        @php
                            $last_date = null;
                        @endphp
                        @forelse ($reservations as $reservation)
                            @if ( $last_date != $reservation->reservation_date )
                                <tr>
                                    <td class="text-center" colspan="7"><strong>{{ \Illuminate\Support\Carbon::make($reservation->reservation_date)->locale('hr')->translatedFormat('l') }}
                                            - {{ \Illuminate\Support\Carbon::make($reservation->reservation_date)->format('d.m.Y ') }}</strong></td>
                                </tr>
                            @endif
                            @php
                                $last_date = $reservation->reservation_date;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <a class="font-w600" href="{{ route('reservations.edit', ['reservation' => $reservation]) }}">
                                        <strong>{{ $reservation->id }}</strong>
                                    </a>
                                </td>

                                <td class="text-center">{{ $reservation->time}}</td>

                                <td class="font-size-base">
                                    <span class="badge badge-pill badge-{{ $reservation->status }}">{{ $reservation->status }}</span>
                                </td>

                                <td>
                                    <a class="font-w600" href="{{ route('reservations.edit', ['reservation' => $reservation]) }}">{{ $reservation->order->payment_fname }} {{ $reservation->order->payment_lname }}</a>
                                </td>

                                <td class="text-right">
                                    --}}{{--<a class="btn btn-sm btn-alt-secondary" href="{{ route('orders.show', ['order' => $reservation]) }}">
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>--}}{{--
                                    <a class="btn btn-sm btn-alt-info" href="{{ route('reservations.edit', ['reservation' => $reservation]) }}">
                                        <i class="fa fa-fw fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center font-size-sm" colspan="5">
                                    <label>Nema rezervacija...</label>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                {{ $reservations->links() }}
            </div>--}}

            <div class="block-content">
                <table class="js-table-sections table table-hover table-vcenter">
                    <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th style="width: 30%">Vrijeme</th>
                        <th style="width: 30%;">Kupac</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 25%;">Akcije</th>
                    </tr>
                    </thead>
                    @forelse ($reservations as $date => $items)
                        <tbody class="js-table-sections-header">
                        <tr>
                            <td class="text-center"><i class="fa fa-angle-right text-muted"></i></td>
                            <td class="fw-semibold">
                                <a href="#">{{ \Illuminate\Support\Str::ucfirst(carbon($date)->locale('hr')->getTranslatedDayName()) }}</a>
                                <span class="fw-light ml-3">{{ carbon($date)->locale('hr')->isoFormat('D. MMMM YYYY.') }}</span>
                            </td>
                            <td></td>
                            <td class="d-none d-sm-table-cell"></td>
                            <td class="d-none d-sm-table-cell"></td>
                        </tr>
                        </tbody>
                        <tbody class="fs-sm">

                        @foreach ($items as $reservation)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}.</td>
                                <td class="fw-semibold">{{ $reservation->time }}</td>
                                <td><a href="#">{{ $reservation->order->payment_fname }} {{ $reservation->order->payment_lname }}</a></td>
                                <td class="d-none d-sm-table-cell">
                                    @include('back.layouts.partials.status', ['status' => $reservation->status])
                                </td>
                                <td class="text-right">
                                    {{--<a class="btn btn-sm btn-alt-secondary" href="{{ route('orders.show', ['order' => $reservation]) }}">
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>--}}
                                    <a class="btn btn-sm btn-alt-info" href="{{ route('reservations.edit', ['reservation' => $reservation]) }}">
                                        <i class="fa fa-fw fa-edit"></i>
                                    </a>
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    @empty

                    @endforelse
                </table>
            </div>
        </div>
    </div>

@endsection

@push('js_after')
    <script>
        $(() => {
            dmTableToolsSections();
        });
        /**
         * Table sections functionality
         *
         */
        function dmTableToolsSections() {
            let tables = document.querySelectorAll(
                ".js-table-sections:not(.js-table-sections-enabled)"
            );

            tables.forEach((table) => {
                // Add .js-table-sections-enabled class to tag it as activated
                table.classList.add("js-table-sections-enabled");

                // When a row is clicked in tbody.js-table-sections-header
                table.querySelectorAll(".js-table-sections-header > tr").forEach((tr) => {
                    tr.addEventListener("click", (e) => {
                        if (
                            e.target.type !== "checkbox" &&
                            e.target.type !== "button" &&
                            e.target.tagName.toLowerCase() !== "a" &&
                            e.target.parentNode.nodeName.toLowerCase() !== "a" &&
                            e.target.parentNode.nodeName.toLowerCase() !== "button" &&
                            e.target.parentNode.nodeName.toLowerCase() !== "label" &&
                            !e.target.parentNode.classList.contains("custom-control")
                        ) {
                            let tbody = tr.parentNode;
                            let tbodyAll = table.querySelectorAll("tbody");

                            if (!tbody.classList.contains("show")) {
                                if (tbodyAll) {
                                    tbodyAll.forEach((tbodyEl) => {
                                        tbodyEl.classList.remove("show");
                                        tbodyEl.classList.remove("table-active");
                                    });
                                }
                            }

                            tbody.classList.toggle("show");
                            tbody.classList.toggle("table-active");
                        }
                    });
                });
            });
        }
    </script>
@endpush
