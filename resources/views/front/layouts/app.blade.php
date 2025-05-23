<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" data-bs-theme="light" data-pwa="false">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">
    <title> @yield('title') </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="@yield('description')">
    <meta name="author" content="pneumax">
    @stack('meta_tags')
    <!-- Viewport-->



    <link rel="icon" type="image/png" href="{{ asset('/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/apple-touch-icon.png') }}" />
    <meta name="apple-mobile-web-app-title" content="PNEU-MAX" />
    <link rel="manifest" href="{{ asset('/site.webmanifest') }}" />



    <!-- Theme switcher (color modes) -->
    <script src="{{ asset('assets/js/theme-switcher.js') }}"></script>
    <!-- Preloaded local web font (Inter) -->
    <link rel="preload" href="{{ asset('assets/fonts/inter-variable-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <!-- Font icons -->
    <link rel="preload" href="{{ asset('assets/icons/cartzilla-icons.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('assets/icons/cartzilla-icons.min.css') }}">
    <!-- Vendor styles -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/simplebar/dist/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/choices.js/public/assets/styles/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/glightbox/dist/css/glightbox.min.css') }}">
    <!-- Bootstrap + Theme styles -->
    <link rel="preload" href="{{ asset('assets/css/theme.min.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/css/theme.rtl.min.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" id="theme-styles">
    @livewireStyles
    @livewireScripts

    @if (config('app.env') == 'production')

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-TZRDVQ8C');</script>
        <!-- End Google Tag Manager -->


        @yield('google_data_layer')
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-X8ZP5E2BYT"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-X8ZP5E2BYT');
        </script>

    @endif

    @stack('css_after')

    @if (config('app.env') == 'production')
        <!-- Facebook Pixel Code -->
    <!--    <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', 'xxxxxxxxxxx');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                       src="https://www.facebook.com/tr?id=xxxxxx&ev=PageView&noscript=1"
            /></noscript> -->
    @endif

    <style>
        [v-cloak] { display:none !important; }
    </style>

</head>
<!-- Body-->
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TZRDVQ8C"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->




<div id="agapp">
    @include('front.layouts.partials.header')
            <main class="content-wrapper">


            @yield('content')


            </main>

    @include('front.layouts.partials.footer')

</div>

<!-- Back to top button -->
<div class="floating-buttons position-fixed top-50 end-0 z-sticky me-3 me-xl-4 pb-4">
    <a class="btn-scroll-top btn btn-sm bg-body border-0 rounded-pill shadow animate-slide-end" href="#top">
        Vrh
        <i class="ci-arrow-right fs-base ms-1 me-n1 animate-target"></i>
        <span class="position-absolute top-0 start-0 w-100 h-100 border rounded-pill z-0"></span>
        <svg class="position-absolute top-0 start-0 w-100 h-100 z-1" viewBox="0 0 62 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x=".75" y=".75" width="60.5" height="30.5" rx="15.25" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"/>
        </svg>
    </a>
</div>


<!-- Vendor scripts -->
<script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/simplebar/dist/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/vendor/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ asset('assets/vendor/glightbox/dist/js/glightbox.min.js') }}"></script>
{{--<script src="{{ asset('js/cart.js?v=1.4') }}"></script>--}}

{{--<script src="https://cdn.jsdelivr.net/npm/axios@1.8.1/dist/axios.min.js"></script>
<script src="{{ asset('assets/cart.js?v=1.0') }}"></script>--}}

<script src="https://kit.fontawesome.com/62acfcc394.js" crossorigin="anonymous"></script>
<!-- Bootstrap + Theme scripts -->

<script type="text/javascript">
    function ClickSpec()
    {
        document.getElementById("washing-tab").click();
    }
    function ClickReviews()
    {
        document.getElementById("reviews-tab").click();
    }
</script>

<!-- Bootstrap + Theme scripts -->
<script src="{{ asset('assets/js/theme.min.js') }}"></script>


@if (config('app.env') == 'production')
    <!-- Messenger Chat Plugin Code -->

@endif

{{--<script>
    document.addEventListener('livewire:load', function () {
        // Initialize Choices.js when the page loads
        let conf = {classNames: {containerInner: ["form-select", "filter-select", "rounded-pill"]}}

        var programSelect = new Choices('#program-select', conf);

        // Listen for Livewire update event
        Livewire.hook('element.updated', (el, component) => {
            // Check if the updated element is one of your select inputs
            if (el.id === 'program-select' || el.id === 'courses-select') {
                // Destroy the old Choices instance
                if (el.id === 'program-select') {
                    programSelect.destroy();
                }

                // Re-initialize Choices on the updated select element
                new Choices(el, conf);
            }
        });
    });
</script>--}}

@stack('js_after')

</body>
</html>
