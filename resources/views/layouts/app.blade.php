<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ config('app.title') }} | {{ $title }}</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('dist/img/favicon1.ico') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script rel="preload" src="{{ asset('dist/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["/dist/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/kaiadmin.min.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('dist/css/select2.min.css') }}" /> --}}
</head>
<body>
  <div class="loading-bar" id="loadingBar"></div>

    <!--  Body Wrapper -->
    <div class="wrapper">

        @include('layouts.sidebar')

        <!--  Main wrapper -->
        <div class="main-panel">
        
            @include('layouts.header')

            <div class="container" id="fade-in">

                @yield('content')
                
            </div>

            <footer class="footer" id="fade-in">
                <div class="container-fluid d-flex justify-content-between">
                  <nav class="pull-left">
                  </nav>
                  <div class="copyright">
                    Copyright &copy; {{ config('app.year_created') }} <a href="#" target="_blank" class="pe-1 text-primary">{{ config('app.company') }}</a>. All Rights Reserved
                  </div>
                  <div>
                  </div>
                </div>
            </footer>
        </div>
    </div>
    <!--   Core JS Files   -->
    <script src="{{ asset('dist/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('dist/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('dist/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('dist/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('dist/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('dist/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('dist/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    {{-- <script src="{{ asset('dist/js/plugin/select2/select2.min.js') }}"></script> --}}

    <!-- Kaiadmin JS -->
    <script src="{{ asset('dist/js/kaiadmin.min.js') }}"></script>
    <script src="{{ asset('dist/js/loading-page.js') }}"></script>

    @if(session()->has('success'))
    <script>
        swal("Success!", "{{ session('success') }}", "success", {
            timer: 3000
        });
    </script>
    @endif

    @if(session()->has('error'))
    <script>
        swal("Failed!", "{{ session('error') }}", "warning", {
            timer: 3000
        });
    </script>
    @endif

    @yield('customScripts')
</body>
</html>