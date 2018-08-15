<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
         <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Document Tracker</title>
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/landing-page.css') }}" rel="stylesheet">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    </head>
    <body class="text-center">
          
            <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
                    <header class="masthead mb-auto">
                      <div class="inner">
                        <h3 class="masthead-brand"></h3>
                        <nav class="nav nav-masthead justify-content-center">
                          {{-- @if (Route::has('login'))
                              @auth
                                  <a class="nav-link" href="{{ url('/home') }}">Home</a>
                              @else
                                  <a class="nav-link" href="{{ route('login') }}">Login</a>
                              @endauth
                        @endif --}}
                        </nav>
                      </div>
                    </header>
              
                    <main role="main" class="inner cover">
                      <h1 class="cover-heading">Document Tracking System</h1>
                      <p class="lead">Register, route, receive and release documents digitally.</p>
                      <p class="lead">
                        @if (Route::has('login'))
                              @auth
                                  
                              @else
                                  <a  class="btn btn-lg btn-secondary" href="{{ route('login') }}">Login</a>
                              @endauth
                          @else
                         
                        @endif
                        <a  class="btn btn-lg btn-secondary" href="{{ url('documents') }}">Enter</a>
                      </p>
                    </main>
              
                    <footer class="mastfoot mt-auto">
                      <div class="inner">
                        <p>By PAO-ITCDD.</p>
                      </div>
                    </footer>
                  </div>
      
         <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
