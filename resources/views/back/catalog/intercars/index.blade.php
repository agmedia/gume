@extends('back.layouts.backend')

@push('css_before')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endpush

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">IC API</h1>
            </div>
        </div>
    </div>

    <div class="content">
        @include('back.layouts.partials.session')

        @if ( ! $isConfigured)
            <div class="alert alert-warning d-flex" role="alert">
                <div class="alert-icon">
                    <i class="ci-security-announcement"></i>
                </div>
                <div>Za Inter Cars sinkronizaciju dodaj u <code>.env</code> barem <code>INTERCARS_CLIENT_ID</code> i <code>INTERCARS_CLIENT_SECRET</code>. Po potrebi možeš definirati i <code>INTERCARS_TOKEN_URL</code>, <code>INTERCARS_BASE_URL</code> i <code>INTERCARS_LANGUAGE</code>.</div>
            </div>
        @endif

        @if ($isConfigured && ! $isCatalogLookupReady)
            <div class="alert alert-info d-flex" role="alert">
                <div class="alert-icon">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div>EAN lookup će raditi čim u <code>storage/app/intercars</code> staviš <code>ProductInformation.csv</code> ili <code>ProductInformation.zip</code>, ili u <code>.env</code> definiraš <code>INTERCARS_PRODUCT_INFORMATION_PATH</code>. Dok toga nema, sinkronizacija pada natrag na lokalni <code>sku</code> odnosno <code>product_code</code> iz feeda kao IC <code>index</code>.</div>
            </div>
        @elseif ($isConfigured && $isCatalogLookupReady)
            <div class="alert alert-success d-flex" role="alert">
                <div class="alert-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div>EAN lookup je spreman. ProductInformation katalog učitan je iz: <code>{{ $catalogLookupSource }}</code></div>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Sinkronizacija postojećih artikala</h3>
                    </div>
                    <div class="block-content">
                        <form action="{{ route('catalog.intercars.sync') }}" method="post">
                            @csrf
                            <input type="hidden" name="mode" value="catalog_sync">

                            <div class="form-group">
                                <label for="category-select">Kategorija artikala za sinkronizaciju</label>
                                <select class="js-select2 form-control @error('category_id') is-invalid @enderror" id="category-select" name="category_id" style="width: 100%;" data-placeholder="Odaberi lokalnu kategoriju" required>
                                    <option></option>
                                    @foreach ($categories as $group => $cats)
                                        @foreach ($cats as $id => $category)
                                            <option value="{{ $id }}" {{ (string) $id === (string) old('category_id') ? 'selected' : '' }}>{{ $group . ' >> ' . $category['title'] }}</option>
                                            @if ( ! empty($category['subs']))
                                                @foreach ($category['subs'] as $subId => $subcategory)
                                                    <option value="{{ $subId }}" {{ (string) $subId === (string) old('category_id') ? 'selected' : '' }}>{{ $group . ' >> ' . $category['title'] . ' >> ' . $subcategory['title'] }}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">Sustav će uzeti lokalne artikle iz odabrane kategorije, pokušati ih pronaći po <code>ean</code>, a ako nema ProductInformation mapiranja, koristit će postojeći lokalni <code>sku</code> kao IC <code>index</code>. Kod feed-importiranih artikala to je obično <code>product_code</code>.</div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch custom-control-success">
                                    <input type="checkbox" class="custom-control-input" id="only-active" name="only_active" value="1" {{ old('only_active', '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="only-active">Sinkroniziraj samo aktivne lokalne artikle</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" {{ $isConfigured ? '' : 'disabled' }}>
                                <i class="fa fa-sync-alt mr-1"></i> Sinkroniziraj artikle iz kategorije
                            </button>
                        </form>
                    </div>
                </div>

                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Količina i status za sve artikle</h3>
                    </div>
                    <div class="block-content">
                        <form action="{{ route('catalog.intercars.sync') }}" method="post">
                            @csrf
                            <input type="hidden" name="mode" value="stock_status_all">

                            <div class="alert alert-info">
                                Ova akcija prolazi kroz sve artikle u bazi koji imaju <code>ean</code> ili <code>sku</code> i pokušava ažurirati samo količinu i status. Za velike baze obrada može trajati nekoliko minuta.
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch custom-control-success mb-2">
                                    <input type="checkbox" class="custom-control-input" id="sync-status-all" name="sync_status" value="1" {{ old('sync_status', '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="sync-status-all">Postavi status prema količini s Inter Cars API-ja</label>
                                </div>

                                <div class="custom-control custom-switch custom-control-success">
                                    <input type="checkbox" class="custom-control-input" id="only-active-all" name="only_active" value="1" {{ old('mode') === 'stock_status_all' && old('only_active') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="only-active-all">Obradi samo trenutno aktivne artikle</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" {{ $isConfigured ? '' : 'disabled' }}>
                                <i class="fa fa-boxes mr-1"></i> Ažuriraj količinu i status svih artikala
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Kako radi</h3>
                    </div>
                    <div class="block-content">
                        <p class="mb-3">Nakon odabira kategorije sustav za svaki lokalni artikl prvo pokuša naći odgovarajući Inter Cars kod preko lokalnog <code>ean</code> i ProductInformation kataloga, a ako to nije dostupno koristi lokalni <code>sku</code> kao IC <code>index</code> za dohvat podataka s API-ja.</p>
                        <ul class="pl-3 mb-0">
                            <li>prioritetno se koristi <code>ean</code>, a fallback je lokalni <code>sku</code> / feed <code>product_code</code> kao IC <code>index</code></li>
                            <li>novi artikli se ovim ekranom više ne kreiraju automatski</li>
                            <li>osvježavaju se naziv, opis, brand, EAN, cijena, količina i dodatni IC atributi</li>
                            <li>u masovnom modu za sve artikle osvježavaju se samo količina i status</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if ($report && ! empty($report['results']))
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Zadnji rezultat sinkronizacije</h3>
                </div>
                <div class="block-content">
                    <div class="row text-center mb-4">
                        <div class="col-6 col-md-2">
                            <div class="font-size-h3 font-w600">{{ $report['total'] }}</div>
                            <div class="text-muted">Ukupno</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="font-size-h3 font-w600 text-success">{{ $report['created'] }}</div>
                            <div class="text-muted">Kreirano</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="font-size-h3 font-w600 text-primary">{{ $report['updated'] }}</div>
                            <div class="text-muted">Ažurirano</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="font-size-h3 font-w600 text-warning">{{ $report['skipped'] }}</div>
                            <div class="text-muted">Preskočeno</div>
                        </div>
                        <div class="col-6 col-md-2">
                            <div class="font-size-h3 font-w600 text-danger">{{ $report['failed'] }}</div>
                            <div class="text-muted">Greške</div>
                        </div>
                    </div>

                    @if (($report['activated'] ?? 0) > 0 || ($report['deactivated'] ?? 0) > 0)
                        <div class="alert alert-info">
                            Aktivirani: <strong>{{ $report['activated'] ?? 0 }}</strong> |
                            Deaktivirani: <strong>{{ $report['deactivated'] ?? 0 }}</strong>
                        </div>
                    @endif

                    @if (! empty($report['results_truncated']))
                        <div class="alert alert-warning">
                            Prikazano je prvih 200 redaka izvještaja kako bi stranica ostala responzivna.
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                            <tr>
                                <th>IC SKU</th>
                                <th>Lokalni SKU</th>
                                <th>EAN</th>
                                <th>Status</th>
                                <th>Naziv</th>
                                <th>Poruka</th>
                                <th class="text-right">Uredi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($report['results'] as $result)
                                @php
                                    $badgeClass = 'badge badge-secondary';

                                    if ($result['status'] === 'kreirano') {
                                        $badgeClass = 'badge badge-success';
                                    } elseif ($result['status'] === 'ažurirano') {
                                        $badgeClass = 'badge badge-primary';
                                    } elseif ($result['status'] === 'preskočeno') {
                                        $badgeClass = 'badge badge-warning';
                                    } elseif ($result['status'] === 'greška') {
                                        $badgeClass = 'badge badge-danger';
                                    }
                                @endphp
                                <tr>
                                    <td class="font-w600">{{ $result['sku'] }}</td>
                                    <td>{{ $result['local_sku'] ?: '-' }}</td>
                                    <td>{{ $result['ean'] ?: '-' }}</td>
                                    <td><span class="{{ $badgeClass }}">{{ ucfirst($result['status']) }}</span></td>
                                    <td>{{ $result['name'] ?: '-' }}</td>
                                    <td>{{ $result['message'] }}</td>
                                    <td class="text-right">
                                        @if ($result['product_id'])
                                            <a class="btn btn-sm btn-alt-secondary" href="{{ route('products.edit', ['product' => $result['product_id']]) }}">
                                                <i class="fa fa-fw fa-pencil-alt"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(() => {
            $('#category-select').select2({
                placeholder: 'Odaberi lokalnu kategoriju',
                allowClear: true
            });
        });
    </script>
@endpush
