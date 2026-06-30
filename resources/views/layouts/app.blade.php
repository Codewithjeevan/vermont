<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Retail Sales') }}</title>

    <!-- Styles -->
    <link href="{{ asset("css/theme.css") }}" rel="stylesheet">
    <link href="{{ asset("css/plugin.css") }}" rel="stylesheet">
    <link href="{{ asset("css/app.css") }}" rel="stylesheet">
    <link href="{{ asset('css/plugin-extend.css') }}" rel="stylesheet">

    <link href="{{ asset('fonts/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('fonts/ionicons-2.0.1/css/ionicons.min.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="{{ asset("js/preload.js") }}"></script>
      
    <style>
        @yield('style')
    </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
    <div id="app" class="wrapper">
        @include('layouts.partials.header')
        @include('layouts.partials.sidebar')
        <div class="content-wrapper">
        {!! Toastr::message() !!}
            <section class="content-header">
                <h1>
                    @yield('page-title')
                    <small> @yield('page-subtitle') </small>
                </h1>
                @yield('breadcrumb')
            </section>
            <section class="content">

                @yield('content')
            </section>
        </div>
        @include('layouts.partials.footer')
        
    </div>

    <script src="{{ asset('js/plugins.js') }}"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
    <script src="{{ asset('js/app.js') }}?v=2"></script>

</body>
</html>

