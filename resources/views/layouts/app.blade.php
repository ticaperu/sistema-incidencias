<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,700" rel="stylesheet" type="text/css">
    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/fontastic.css') }}" rel="stylesheet">

    <link href="{{ asset(mix('css/all.css')) }}" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
          integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

</head>
<body>
<div class="page" id="app">
    <!-- Main Navbar-->
    @include('layouts.partials.header')
    @guest
        <div class="page-content d-flex align-items-stretch">
            <div class="content-inner">
                <div class="projects" style="margin-top: 15px">
                    @yield('content')
                </div>
            </div>
        </div>
    @else
        <div class="page-content d-flex align-items-stretch">
            <!-- Side Navbar -->
            @include('layouts.partials.sidebar')
            <div class="content-inner">
                <!-- Page Header-->
                <div class="projects" style="margin-top: 15px">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
                <!-- Page Footer-->
                <footer class="main-footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <p>Your company &copy; 2017-2019</p>
                            </div>
                            <div class="col-sm-6 text-right">
                                <p>Design by <a href="https://bootstrapious.com/admin-templates" class="external">Bootstrapious</a>
                                </p>
                                <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)-->
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    @endguest
</div>

<script src="//maps.google.com/maps/api/js?libraries=geometry&key={{env('MAPS_API_KEY')}}"
        type="text/javascript"></script>
<script src="{{ asset('js/gmaps.min.js') }}"></script>
@routes
<!-- Scripts -->
<script src="{{ asset(mix('js/app.js')) }}"></script>
<script src="{{ asset(mix('js/all.js')) }}"></script>
</body>
</html>
