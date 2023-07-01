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
    <script defer src="{{ asset('assets/face-api/test-recognition.js') }}"></script>


    <title>Test Recognition</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/css/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/js/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:ital,wght@0,400;0,500;0,700;1,400;1,700&family=Nunito+Sans:wght@400;600;700&family=Roboto:wght@500&display=swap"
        rel="stylesheet">
</head>

<body style="padding-top: 10px; padding-bottom:10px">
    <p id="url-image" hidden>{{asset('')}}</p>
    <p id="url-face-api" hidden>{{ asset('assets/face-api') }}</p>

    <div style="display: flex; justify-content: center;">
        <button id="btn-start" type="button" class="btn btn-success btn-lg">Start Test</button>
    </div>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Image</th>
                    <th scope="col">Label</th>
                    <th scope="col">Recognition</th>
                    <th scope="col">Result</th>
                </tr>
            </thead>
            <tbody id="tbl-detail">
            </tbody>
          </table>
    </div>

    <h5 style="text-align: center">Result</h5>
    <div style="display: flex; justify-content: center;">
        <table class="table table-hover" style="max-width: 50%">
            <thead>
                <tr>
                    <th scope="col">Label</th>
                    <th scope="col">Count</th>
                    <th scope="col">True</th>
                    <th scope="col">False</th>
                </tr>
            </thead>
            <tbody id="tbl-result">
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th id="total-count">0</th>
                    <th id="total-true">0</th>
                    <th id="total-false">0</th>
                </tr>

                <tr>
                    <th colspan="2"></th>
                    <th colspan="2" id="arrcuracy-metric">0%</th>
                </tr>
            </tfoot>
        </table>
    </div>



    {{-- <script src="{{ asset('assets/face-api/test-recognition.js') }}"></script> --}}
</body>

</html>
