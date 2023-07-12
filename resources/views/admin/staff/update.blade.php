<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        {{-- <script>
            var faceRegination = {!! json_encode($arr) !!};
            var arrName = {!! json_encode($arrName) !!};
        </script> --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/logo.png') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

        <script defer src="{{ asset('assets/face-api/face-api.min.js') }}"></script>
        <script defer src="{{ asset('assets/face-api/update-face-detection.js') }}"></script>

        <title>Update Recognition</title>
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
        <table style="margin-top: 1000px">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">LABEL</th>
                <th scope="col">LINK</th>
                <th scope="col">DESCRIPTION</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($list as $item)
                    <tr class="row-{{ $item->id_image }}">
                        <th scope="row">{{ $item->id_image }}</th>
                        <td>{{ $item->id }}</td>
                        <td>{{ asset($item->image_url) }}</td>
                        <td>{{ substr($item->description, 0, 100) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-success" disabled id="btn-start">Start</button>
        <button type="button" class="btn btn-danger" disabled id="btn-submit">Submit</button>        <script type="text/javascript" src="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/js/bootstrap.min.js') }}"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    </body>
</html>
