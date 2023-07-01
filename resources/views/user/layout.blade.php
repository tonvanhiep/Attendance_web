<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/css/bootstrap.min.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo.png') }}">
    @stack('css')

    <style>
        html, body{
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Open Sans', sans-serif;
            color: #222;
        }

        a{
            text-decoration: none;
        }

        p, li, a{
            font-size: 14px;
        }

        /* GRID */

        .twelve { width: 100%; }
        .eleven { width: 91.53%; }
        .ten { width: 83.06%; }
        .nine { width: 74.6%; }
        .eight { width: 66.13%; }
        .seven { width: 57.66%; }
        .six { width: 49.2%; }
        .five { width: 40.73%; }
        .four { width: 32.26%; }
        .three { width: 23.8%; }
        .two { width: 15.33%; }
        .one { width: 6.866%; }

        /* COLUMNS */

        .col {
            display: block;
            float:left;
            margin: 1% 0 1% 1.6%;
        }

        .col:first-of-type {
            margin-left: 0;
        }

        .container{
            width: 100%;
            max-width: 940px;
            margin: 0 auto;
            position: relative;
            text-align: center;
        }

        /* CLEARFIX */

        .cf:before,
        .cf:after {
            content: " ";
            display: table;
        }

        .cf:after {
            clear: both;
        }

        .cf {
            *zoom: 1;
        }

        /* GENERAL STYLES */

        .pagination{
            padding: 0 0 20px 0;
        }

        .pagination ul{
            margin: 0;
            padding: 0;
            list-style-type: none;
        }

        .pagination a{
            display: inline-block;
            padding: 10px 18px;
            color: #222;
        }

        /* TWO */

        .p2 .is-active li{
            font-weight: bold;
            border-bottom: 3px solid #1E2774;
        }
    </style>
</head>

<body>
    <section class="d-flex" id="wrapper">
        @include('user.components.menu')

        <section id="page-content-wrapper">
            @include('user.components.navigation')
            @yield('content')
        </section>
    </section>

    <script src="http://localhost:6001/socket.io/socket.io.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function() {
            el.classList.toggle("toggled");
        };
    </script>
    <script src="{{asset('assets/js/main.js');}}"></script>
    @stack('js')
</body>
</html>
