@extends('front.layouts.app')

@if (isset($group) && $group)
    @if ($group && ! $cat && ! $subcat)
        @section ( 'title',  \Illuminate\Support\Str::ucfirst($group). ' - ZuZi Shop' )
    @endif
    @if ($cat && ! $subcat)
        @section ( 'title',  $cat->title . ' - ZuZi Shop' )
        @section ( 'description', $cat->meta_description )
    @elseif ($cat && $subcat)
        @section ( 'title', $subcat->title . ' - ZuZi Shop' )
        @section ( 'description', $cat->meta_description )
    @endif
@endif

@if (isset($brand) && $brand)
    @section ('title',  $seo['title'])
    @section ('description', $seo['description'])
@endif

@if (isset($publisher) && $publisher)
    @section ('title',  $seo['title'])
    @section ('description', $seo['description'])
@endif

@if (isset($meta_tags))
    @push('meta_tags')
        @foreach ($meta_tags as $tag)
            <meta name={{ $tag['name'] }} content={{ $tag['content'] }}>
        @endforeach
    @endpush
@endif


@section('content')

    <div class="container pb-5 mb-2 mb-sm-3 mb-lg-4 mb-xl-5">
        @if (isset($group) && $group)
        <!-- Breadcrumb -->
        <nav class="position-relative  my-3 " aria-label="breadcrumb" style="z-index: 1021">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Naslovnica</a></li>

                @if ($group && ! $cat && ! $subcat)
                    <li class="breadcrumb-item" aria-current="page">{{ \Illuminate\Support\Str::ucfirst($group) }}</li>
                @elseif ($group && $cat)
                    <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $group]) }}">{{ \Illuminate\Support\Str::ucfirst($group) }}</a></li>
                @endif
                @if ($cat && ! $subcat)
                    <li class="breadcrumb-item" aria-current="page">{{ $cat->title }}</li>
                @elseif ($cat && $subcat)
                    <li class="breadcrumb-item" aria-current="page"><a class="text-nowrap" href="{{ route('catalog.route', ['group' => $group, 'cat' => $cat]) }}">{{ $cat->title }}</a></li>
                    <li class="breadcrumb-item" aria-current="page">{{ $subcat->title }}</li>
                @endif
            </ol>
        </nav>
        <!-- Page title -->
            @if ($group && ! $cat && ! $subcat)
                <h1 class="h3 position-relative pb-0" style="z-index: 1021">{{ \Illuminate\Support\Str::ucfirst($group) }}</h1>

            @endif
            @if ($cat && ! $subcat)
                <h1 class="h3 position-relative pb-0" style="z-index: 1021">{{ $cat->title }}</h1>

            @elseif ($cat && $subcat)
                <h1 class="h3 position-relative pb-0" style="z-index: 1021">{{ $subcat->title }}</h1>

            @endif

        @endif




        <!-- Product grid -->



            <products-view ids="{{ isset($ids) ? $ids : null }}"
                           group="{{ isset($group) ? $group : null }}"
                           cat="{{ isset($cat) ? $cat['id'] : null }}"
                           subcat="{{ isset($subcat) ? $subcat['id'] : null }}"
                           brand="{{ isset($brand) ? $brand['slug'] : null }}">
            </products-view>














    </div>




@endsection

@push('js_after')
    <script type="application/ld+json">
        {!! collect($crumbs)->toJson() !!}
    </script>
@endpush
