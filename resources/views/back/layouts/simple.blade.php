<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>PNEU-MAX</title>

    <meta name="description" content="Zuzi shop">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="noindex, nofollow">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Icons -->

    <link rel="icon" type="image/png" href="{{ config('settings.images_domain') . 'assets/app-icons/favicon-96x96.png' }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ config('settings.images_domain') . 'assets/app-icons/favicon.svg' }}" />
    <link rel="shortcut icon" href="{{ config('settings.images_domain') . 'assets/app-icons/favicon.ico' }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ config('settings.images_domain') . 'assets/app-icons/apple-touch-icon.png' }}" />
    <link rel="manifest" href="{{ config('settings.images_domain') . '/manifest.json' }}" />

    <!-- Fonts and Styles -->
    @stack('css_before')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" id="css-main" href="{{ asset('css/dashmix.css') }}">

    <!-- You can include a specific file from public/css/themes/ folder to alter the default color theme of the template. eg: -->
<!-- <link rel="stylesheet" id="css-theme" href="{{ asset('css/themes/xwork.css') }}"> -->
@stack('css_after')

<!-- Scripts -->
    <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
</head>
<body>

<div id="page-container">
    <main id="main-container">
        @yield('content')
    </main>
</div>

<!-- Dashmix Core JS -->
<script src="{{ asset('/js/dashmix.app.js') }}"></script>

<!-- Laravel Original JS -->
<script src="{{ asset('/js/laravel.app.js') }}"></script>

@stack('js_after')
</body>
</html>
