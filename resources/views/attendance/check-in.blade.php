<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Face Detection</title>
    <script>
        var faceRegination = {!! json_encode($arr) !!};
    </script>

    <script defer src="{{ asset('assets/face-api/face-api.min.js') }}"></script>
    <script defer type="module" src="{{ asset('assets/face-api/script-face-detection.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:ital,wght@0,400;0,500;0,700;1,400;1,700&family=Nunito+Sans:wght@400;600;700&family=Roboto:wght@500&display=swap"
        rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    {{-- <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/js/bootstrap.js') }}"> --}}
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100vw;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        canvas {
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
</head>

<body style="background-image:url('{{ asset('assets/face-api/background.jpg') }}')">

    <p id="url-face-api" hidden>{{ asset('assets/face-api') }}</p>
    <p id="url-image" hidden>{{ asset('') }}</p>

    {{-- <video id="video" width="720" height="560" autoplay muted></video> --}}
    <div id="container">
        <div id="alert-div" style="width: 730px;height: 45px;margin-bottom:5vh;">
            <p id="alert-message" style="border-radius: 5px;line-height: 45px;padding-left: 30px;color: white;"></p>
        </div>

        <div id="webcam">
            <video id="video" width="720" height="560" autoplay muted></video>
            <h2 id="text-loading">Loading...</h2>
        </div>

        <div id="id-input">
            {{-- <button id="btn-shot" style="background: none;
                color: white;
                font-size: 18px;
                border: 2px solid;
                height: 45px;
                width: 300px;
                border-radius: 5px;
                font-weight: 500;
                margin-bottom:30px">Screenshot
            </button> --}}
            <button id="btn-inp"
                style="background: none;
                color: white;
                font-size: 18px;
                border: 2px solid;
                height: 45px;
                width: 300px;
                border-radius: 5px;
                font-weight: 500;">Enter
                ID
            </button>
            <div class="id-field" id="div-inp" style="display: none">
                <form id="checkin-form" action="{{ route('attendance') }}">
                    <input id="inp-id" type="password" maxlength="8">
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/modal.js') }}"></script>
    <script>
    </script>
</body>

</html>

{{-- <div id="alert-div" style="width: 730px;height: 45px;margin-bottom:5vh;background-color: green;border-radius: 5px;">
    <p id="alert-message" style="border-radius: 5px;line-height: 45px;padding-left: 30px;color: white;display: inline-block;width: 90%;"></p>
    <button style="
        display: inline-block;
        height: 45px;
        width: 60px;
        background: none;
        color: red;
    ">CANCEL</button>
</div> --}}
