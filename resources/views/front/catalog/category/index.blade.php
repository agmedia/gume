@extends('front.layouts.app')

@if (isset($data->group) && $data->group)
    @if ($data->group && ! $data->category && ! $data->subcategory)
        @section ( 'title',  \Illuminate\Support\Str::ucfirst($data->group). ' - PNEU-MAX' )
    @endif
    @if ($data->category && ! $data->subcategory)
        @section ( 'title',  $data->category->title . ' - PNEU-MAX' )
        @section ( 'description', $data->category->meta_description )
    @elseif ($data->category && $data->subcategory)
        @section ( 'title', $data->subcategory->title . ' - PNEU-MAX' )
        @section ( 'description', $data->category->meta_description )
    @endif
@endif

@if (isset($brand) && $brand)
    @section ('title',  $seo['title'])
    @section ('description', $seo['description'])
@endif

@if (isset($meta))
    @push('meta_tags')
        @foreach ($meta as $tag)
            <meta name={{ $tag['name'] }} content={{ $tag['content'] }}>
        @endforeach
    @endpush
@endif


@section('content')

    <div class="container pb-5 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
        @if (isset($data->group) && $data->group)
            <!-- Breadcrumb -->
            <nav class="position-relative  my-3 " aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Naslovnica</a></li>

                    @if ($data->group && ! $data->category && ! $data->subcategory)
                        <li class="breadcrumb-item" aria-current="page">{{ \Illuminate\Support\Str::ucfirst($data->group) }}</li>
                    @elseif ($data->group && $data->category)
                        <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $data->group]) }}">{{ \Illuminate\Support\Str::ucfirst($data->group) }}</a></li>
                    @endif
                    @if ($data->category && ! $data->subcategory)
                        <li class="breadcrumb-item" aria-current="page">{{ $data->category->title }}</li>
                    @elseif ($data->category && $data->subcategory)
                        <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $data->group, 'cat' => $data->category]) }}">{{ $data->category->title }}</a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ $data->subcategory->title }}</li>
                    @endif
                </ol>
            </nav>
            <!-- Page title -->
            @if ($data->group && ! $data->category && ! $data->subcategory)
                <h1 class="h3 position-relative pb-0" >{{ \Illuminate\Support\Str::ucfirst($data->group) }}</h1>
            @endif
            @if ($data->category && ! $data->subcategory)
                <h1 class="h3 position-relative pb-0" >{{ $data->category->title }}</h1>


            @elseif ($data->category && $data->subcategory)
                <h1 class="h3 position-relative pb-0" >{{ $data->subcategory->title }}</h1>
            @endif
        @endif



            @if ($data->category && ! $data->subcategory)

                @if ($data->category->subcategories()->count())


                    <section class="py-2 mb-0">
                        <div class="overflow-auto" data-simplebar>
                            <div class="nav flex-nowrap justify-content-between gap-1 py-2 ">
                                    @foreach ($data->category->subcategories()->get() as $item)

                                        <a href=" {{ route('catalog.route', ['group' => $data->group, 'cat' => $data->category]). '/'. $item->slug }}"
                                           class="btn btn-dark btn-sm mb-2">
                                            <p class=" py-0 mb-0 px-1">{{ $item->title }}</p></a>
                                    @endforeach
                                </div>
                            </div>


                    </section>

                @endif

            @endif

            @livewire('front.catalog.category-products-list', ['route_data' => json_encode($data)])
    </div>



@endsection

@push('js_after')
    <script type="application/ld+json">
        {!! collect($crumbs)->toJson() !!}
    </script>
@endpush
