<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <script>
            var faceRegination = {!! json_encode($arr) !!};
            var arrName = {!! json_encode($arrName) !!};
        </script>

        <script defer src="{{ asset('assets/face-api/face-api.min.js') }}"></script>
        <script defer src="{{ asset('assets/face-api/script-face-recognition.js') }}"></script>

        <title>Face Recognition</title>
        <style>
            body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column
            }

            canvas {
            position: absolute;
            top: 0;
            left: 0;
            }
        </style>
    </head>

    <body>
        <p id="url-image" hidden>{{asset('')}}</p>
        <p id="url-face-api" hidden>{{ asset('assets/face-api') }}</p>
        <input type="file" id="imageUpload">
    </body>
</html>
