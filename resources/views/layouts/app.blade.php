<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Document Tracker</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    
</head>
<body>
    <div id="app">
        <Navbar></Navbar>
        <main role="main" class="container">
            <div class="my-3 p-3 bg-white rounded box-shadow">
                <div class="media text-muted pt-3">
                    @yield('content')
                </div>
                <small class="d-block text-right mt-3">
                {{-- <a href="#">All updates</a> --}}
                </small>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ mix('js/manifest.js') }}" ></script>
    <script src="{{ mix('js/vendor.js') }}" ></script>
    <script src="{{ mix('js/app.js') }}" ></script>
    @yield('scripts')
    
</body>
</html>
 