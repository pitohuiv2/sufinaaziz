<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('login/fonts/icomoon/style.css') }}">

    <link rel="stylesheet" href="{{ asset('login/css/owl.carousel.min.css') }}">
    <link rel="icon" href="{!! asset('favicon.png') !!}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('login/css/bootstrap.min.css') }}">

    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('login/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/toastr.css')}}">
    <title>P-Cure Neo</title>
</head>
<style>
    body {
        font-weight: 400;
        font-family: 'Poppins', sans-serif;
        background-color: white;
    }

    h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
        font-family: 'Poppins', sans-serif;
    }

    @media (max-width: 800px) {
        .img-back {
            display: none;
        }
    }


    .btn-primary:hover {
        color: #fff;
        background-color: rgb(50, 125, 255);
        border-color: rgb(73, 180, 225);;
    }
    .control:hover input:not([disabled]):checked ~ .control__indicator, .control input:checked:focus ~ .control__indicator {
        background: rgb(7, 71, 166);
    }

    .control input:checked ~ .control__indicator {
        background: rgb(7, 71, 166);
    }


    .btn-primary {
        color: #212529;
        background-color: rgb(7, 71, 166);
        border-color: rgb(7, 82, 239);
    }

</style>
<body>


<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-6 order-md-2">
                <img src="{{ asset('images/login-back2.png') }}" alt="Image" class="img-fluid img-back">
            </div>
            <div class="col-md-6 contents">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="mb-4" style="text-align:center;">
                            <img src="{{ asset('images/p-cure-logo.png') }}" alt="Image Logo" style="width: 70px;">
                            <h3>P-CURE <strong>NEO</strong></h3>
                            <p class="mb-4" style="font-size:13px">Silahkan masukan username dan password anda</p>
                        </div>
                        <form method="POST" action="{{ route('login_validation') }}">
                            <div class="form-group first">
                                <label for="username">Nama User</label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="{{request()->input('username')}}" required>

                            </div>
                            <div class="form-group last mb-4">
                                <label for="password">Kata Sandi</label>
                                <input type="password" class="form-control" id="password" name="password" required>

                            </div>

                            <div class="d-flex mb-2 align-items-center">
                                <label class="control control--checkbox mb-0"><span class="caption">Ingatkan saya</span>
                                    <input type="checkbox" checked="checked"/>
                                    <div class="control__indicator"></div>
                                </label>
                                <!-- <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span>  -->
                            </div>

                            <input type="submit" value="Log In" class="btn text-white btn-block btn-primary">

                            <!-- <span class="d-block text-left my-4 text-muted"> or sign in with</span>

                            <div class="social-login">
                              <a href="#" class="facebook">
                                <span class="icon-facebook mr-3"></span>
                              </a>
                              <a href="#" class="twitter">
                                <span class="icon-twitter mr-3"></span>
                              </a>
                              <a href="#" class="google">
                                <span class="icon-google mr-3"></span>
                              </a>
                            </div> -->
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


<script src="{{ asset('login/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('login/js/popper.min.js') }}"></script>
<script src="{{ asset('login/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('login/js/main.js') }}"></script>
<script src="{{ asset('node_modules/vendors/toastr/toastr.min.js')}}"></script>
</body>
<script>


    var baseUrl =
        {!! json_encode(url('/')) !!}

        @if(Session::has('message'))
    var type = "{{ Session::get('alert-type', 'info') }}";
    switch (type) {
        case 'info':
            toastr.info("{{ Session::get('message') }}", "Info");
            break;

        case 'warning':
            toastr.warning("{{ Session::get('message') }}", "Info");
            break;

        case 'success':
            toastr.success("{{ Session::get('message') }}", "Info");
            break;

        case 'error':
            toastr.error("{{ Session::get('message') }}", "Info");
            break;
    }
    @endif

</script>
</html>
