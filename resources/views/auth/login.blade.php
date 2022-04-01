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


  <style>
    body {
      padding-top: unset;
    }
    div.main {
      background: #0264d6;
      /* Old browsers */
      background: -moz-radial-gradient(center, ellipse cover, #754306 1%, #1c2b5a 150%);
      /* FF3.6+ */
      background: -webkit-gradient(radial, center center, 0px, center center, 150%, color-stop(1%, #0264d6), color-stop(100%, #1c2b5a));
      /* Chrome,Safari4+ */
      background: -webkit-radial-gradient(center, ellipse cover, #754306 1%, #1c2b5a 150%);
      /* Chrome10+,Safari5.1+ */
      background: -o-radial-gradient(center, ellipse cover, #754306 1%, #1c2b5a 150%);
      /* Opera 12+ */
      background: -ms-radial-gradient(center, ellipse cover, #754306 1%, #1c2b5a 150%);
      /* IE10+ */
      background: radial-gradient(ellipse at center, #754306 1%, #1c2b5a 150%);
      /* W3C */
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#754306 ', endColorstr='#1c2b5a',
      GradientType=1);
      /* IE6-9 fallback on horizontal gradient */
      height: calc(100vh);
      width: 100%;
    }

    [class*="fontawesome-"]:before {
      font-family: 'FontAwesome', sans-serif;
    }

.modal-full {
    min-width: 100%;
    margin: 0;
}

    /* ---------- GENERAL ---------- */

    * {
      box-sizing: border-box;
      margin: 0px auto;

      &:before,
      &:after {
        box-sizing: border-box;
      }

    }

    body {

      color: #606468;
      font: 87.5%/1.5em 'Open Sans', sans-serif;
      margin: 0;
    }

    a {
      color: #eee;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    input {
      border: none;
      font-family: 'Open Sans', Arial, sans-serif;
      font-size: 14px;
      line-height: 1.5em;
      padding: 0;
      -webkit-appearance: none;
    }

    p {
      line-height: 1.5em;
    }

    .clearfix {
      *zoom: 1;

      &:before,
      &:after {
        content: ' ';
        display: table;
      }

      &:after {
        clear: both;
      }

    }

    .container {
      left: 50%;
      position: fixed;
      top: 50%;
      transform: translate(-50%, -50%);
    }

    /* ---------- LOGIN ---------- */

    #login form {
      width: 250px;
    }

    #login,
    .logo {
      display: inline-block;
      width: 40%;
    }

    #login {
      border-right: 1px solid #fff;
      padding: 0px 22px;
      width: 59%;
    }

    .logo {
      color: #fff;
      font-size: 50px;
      line-height: 125px;

    }

    .logo img {
      width: 10rem;
    }

    #login form span.fa {
      background-color: #fff;
      border-radius: 3px 0px 0px 3px;
      color: #000;
      display: block;
      float: left;
      height: 50px;
      font-size: 24px;
      line-height: 50px;
      text-align: center;
      width: 50px;
    }

    #login form input {
      height: 50px;
    }

    fieldset {
      padding: 0;
      border: 0;
      margin: 0;

    }

    #login form input[type="text"],
    input[type="password"],
    input[type="email"] {
      background-color: #fff;
      border-radius: 0px 3px 3px 0px;
      color: #000;
      margin-bottom: 1em;
      padding: 0 16px;
      width: 200px;
    }

    #login form input[type="submit"] {
      border-radius: 3px;
      -moz-border-radius: 3px;
      -webkit-border-radius: 3px;
      background-color: #000000;
      color: #eee;
      font-weight: bold;
      /* margin-bottom: 2em; */
      text-transform: uppercase;
      padding: 5px 10px;
      height: 30px;
    }

    #login form input[type="submit"]:hover {
      background-color: #d44179;
    }

    #login>p {
      text-align: center;
    }

    #login>p span {
      padding-left: 5px;
    }

    .middle {
      display: flex;
      width: 600px;
    }

    .header-title {
      padding: 0 0 40px 0 !important;
      margin-top: -40px;
      color: #fff;
      text-transform: uppercase;
    }


    .modal {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      overflow: hidden;
    }

    .modal-dialog {
      position: fixed;
      margin: 0;
      width: 100%;
      height: 100%;
      padding: 0;
    }

    .modal-content {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      border: 2px solid #3c7dcf;
      border-radius: 0;
      box-shadow: none;
    }

    .modal-header {
      position: absolute;
      top: 0;
      right: 0;
      left: 0;
      height: 50px;
      padding: 10px;
      background: #6598d9;
      border: 0;
      color: #fff;
    }

    .modal-title {
      font-weight: 300;
      font-size: 2em;
      color: #fff;
      line-height: 30px;
    }

    .modal-body {
      position: absolute;
      top: 50px;
      bottom: 60px;
      width: 100%;
      font-weight: 300;
      overflow: auto;
    }

    .modal-footer {
      position: absolute;
      right: 0;
      bottom: 0;
      left: 0;
      height: 60px;
      padding: 10px;
      background: #f1f3f5;
    }
  </style>
</head>
<!------ Include the above in your HEAD tag ---------->

<body>

  <div class="main">

    <div class="container">

      <center>
        <h1 class="header-title">Document Tracking</h1>
        <div class="middle">
          <div id="login">
            <form method="POST" action="{{ route('login') }}">
              @csrf
              <fieldset class="clearfix">

                <p><span class="text-white pr-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    class="bi bi-person" viewBox="0 0 16 16">
                    <path
                      d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                  </svg>
                </span>
                  <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus></p>

                @if ($errors->has('email'))
                <span class="invalid-feedback">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span> @endif

                <p><span class="text-white pr-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-key"
                    viewBox="0 0 16 16">
                    <path
                      d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8zm4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5z" />
                    <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                  </svg> 
                </span>
                  <input id="password" type="password" name="password" required> @if ($errors->has('password'))
                  <span class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span> @endif
                </p>

                <div>

                  <span style=" text-align:right;  display: inline-block;"><input type="submit"  class="btn btn-default btn-sm" value="Sign In"></span>
                  <span style=" text-align:right;  display: inline-block;"><button type="button" class="btn btn-warning btn-sm"  data-toggle="modal" data-target="#exampleModal">FAST TRACK</button>
                            </div>

          </fieldset>
<div class="clearfix"></div>
        </form>

        <div class="clearfix"></div>

      </div> <!-- end login -->
      <div class="logo"><img src="{{ asset('img/logo.png') }}" alt="">
          
          <div class="clearfix"></div>
      </div>
      
      </div>
</center>
    </div>
</div>
<div id="app">
  <fast-track></fast-track>
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>