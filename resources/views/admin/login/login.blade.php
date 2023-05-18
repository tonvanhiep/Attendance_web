<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/css/bootstrap.min.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="{{ asset('assets/bootstrap-5.3.0-alpha2-dist/js/bootstrap.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:ital,wght@0,400;0,500;0,700;1,400;1,700&family=Nunito+Sans:wght@400;600;700&family=Roboto:wght@500&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="logo">
            <img style="max-height: 150px; margin-top:30px" src="{{ asset(env('WEB_LOGO')) }}" alt="">
        </div>
        <h1>Login</h1>
        <form action="{{ route('admin.auth.check-login') }}" method="post">
            @csrf
            <div class="text-container">
                <input type="text" name="name" required>
                <span></span>
                <label for="name">Username</label>
            </div>
            <div class="text-container">
                <input type="password" name="password" required>
                <span></span>
                <label for="password">Password</label>
            </div>
            <div class="forgot">Forgot Password?</div>
            @if (session('success'))
                {{ session('success') }}
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" style="display:flex;justify-content:space-between">
                    {{ session('error') }}
                    <a href="#" class="close" data-bs-dismiss="alert" aria-label="close"><i class="fa-solid fa-x"></i></a>
                </div>
            @endif
            <button type="submit">Login</button>
        </form>


    </div>
</body>

</html>
