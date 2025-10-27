<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ config('app.title') }} | Login</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('dist/img/favicon1.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <link rel="stylesheet" href="{{ asset('/dist/css/fonts-public-sans.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/css/fonts.min.css') }}">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/kaiadmin.min.css') }}" />

    <style>
        html,
    body {
        overflow: hidden;
        height: 100%;
        margin: 0;
        padding: 0;
    }
    </style>
</head>
<body>
    <div id="loadingOverlay">
        <div class="loading-logo">
            <img src="{{ asset('dist/img/logoym.png') }}?v={{ config('asset.version') }}" alt="Logo" />
        </div>
        {{-- <div class="wave-loader">
            <span></span>
            <span></span>
            <span></span>
        </div> --}}
        <div class="loading-text">Please Wait</div>
    </div>
    <div class="loading-bar" id="loadingBar"></div>
    <!--  Body Wrapper -->
    <div class="login-wrapper">
        <div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-lg-8 col-xxl-6">
                        <div class="card px-3 py-4 mb-5">
                            <div class="row">
                                <div class="col-md-6 col-12 img-login text-center">
                                    <div class="card-body border-end">
                                        <img class="img-login" src="{{ asset('dist/img/cover.jpg') }}" />
                                    </div>
                                </div>
                                <div class="col-md-6 col-12 form-login">
                                    <div class="card-body pt-0 pb-3">
                                        <a href="/" class="text-nowrap logo-img justify-content-center d-flex w-100 pb-0">
                                            <img class="mt-1 me-3" src="{{ asset('dist/img/logoym.png') }}" height="37" />
                                            <h1 class="text-dark fw-bolder text-center mb-0">{{ config('app.name') }}</h1>
                                        </a>
                                        <p class="text-muted text-center mb-5" style="font-size:13px;">Version {{ config('app.version') }}</p>

                                        <p class="alert-warning mb-3 ps-3">Login to <span class="fw-bolder">{{ config('app.name_alias') }}</span>!</p>
                                        <form class="text-start" action="{{ route('login') }}" method="post">
                                            @csrf
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-user-alt text-muted px-1"></i></span>
                                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Username" required autofocus>
                                            </div>
                                            <div class="input-group mb-1">
                                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock text-muted px-1"></i></span>
                                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <div class="form-check">
                                                <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" onclick="myFunction()">
                                                <label class="form-check-label text-muted" for="flexCheckChecked">
                                                    {{ __('Show Password') }}
                                                </label>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-warning text-light w-100 rounded-3"><i class="fas fa-sign-in-alt me-2"></i> Login</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="copyright text-center" title="Developed by {{ config('app.creator') }}">
                        Copyright &copy; {{ config('app.year_created') }} <a href="#" target="_blank" class="pe-1 text-primary">{{ config('app.company') }}</a>. All Rights Reserved
                    </div>
                </div>
            </div>
        </div>
	</div>
	<!--   Core JS Files   -->
    <script src="{{ asset('dist/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('dist/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('dist/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('dist/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('dist/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('dist/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('dist/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('dist/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('dist/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('dist/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('dist/js/plugin/jsvectormap/world.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('dist/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('dist/js/kaiadmin.min.js') }}"></script>
    <script src="{{ asset('dist/js/loading-page.js') }}"></script>
    
    @if(session()->has('error'))
    <script>
        swal("Login Failed!", "{{ session('error') }}", "warning", {
            timer: 3000
        });
    </script>
    @endif

    <script type="text/javascript">
        // Menampilkan dan menyembunyikan Password
        function myFunction() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
</body>
</html>