<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <link href="https://fonts.googleapis.com/css?family=Kalam&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fontcss/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontcss/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontcss/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontcss/responsive.css') }}">
    <style>
        body {
            background-image: url('frontend/image/reemlogo.jpeg');
            background-repeat: no-repeat;
            background-color: #fbf5b9
            /* background-color: #ffffff; */
        }

        
    </style>




</head>

<body>
    <div id="main">

        <div class="container">
            <div class="signup-content">


            </div>




            @yield('forntlogin')

        </div>
    </div>
    <!-- JS -->
    <script src="{{ asset('js/fontjs/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('js/fontjs/popper.min.js') }}"></script>
    <script src="{{ asset('js/fontjs/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/fontjs/custom.js') }}"></script>

</body>

</html>
