<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ config('app.title') }} | Error 503</title>
    <meta
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
        name="viewport"
    />
    <link rel="icon" href="{{ asset('dist/img/favicon1.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <link rel="stylesheet" href="{{ asset('/dist/css/fonts-public-sans.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/css/fonts.min.css') }}">

    <!-- CSS Files -->
    <link
        rel="stylesheet"
        href="{{ asset('dist/css/bootstrap.min.css') }}"
    />
    <link rel="stylesheet" href="{{ asset('dist/css/plugins.min.css') }}" />
    <link
        rel="stylesheet"
        href="{{ asset('dist/css/kaiadmin.min.css') }}"
    />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="{{ asset('dist/css/demo.css') }}" />
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
    <div class="login-wrapper error-page">
        <div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-lg-8 col-xxl-6 text-center">
                        <div class="text-warning fw-bolder" style="font-size: 150px;">503</div>
                        <h1><i class="fas fa-cogs text-warning me-2"></i> Under maintenance.</h1>
                        <p>We are currently performing system maintenance. Please try again later.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright text-center">
            Copyright &copy; {{ config('app.year_created') }} <a href="#" target="_blank" class="pe-1 text-secondary">{{ config('app.company') }}</a>. All Rights Reserved
        </div>
    </div>
    <!--   Core JS Files   -->
    <script src="{{ asset('dist/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('dist/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('dist/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('dist/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('dist/js/kaiadmin.min.js') }}"></script>
    <script src="{{ asset('dist/js/loading-page.js') }}"></script>
</body>
</html>
