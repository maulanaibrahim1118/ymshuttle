<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.title') }} | {{ $title }}</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('dist/img/favicon1.ico') }}?v={{ config('asset.version') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <link rel="stylesheet" href="{{ asset('/dist/css/fonts-public-sans.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/css/fonts.min.css') }}">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/flatpickr.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/kaiadmin.min.css') }}?v={{ config('asset.version') }}" />
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
    <div class="wrapper">

        @include('layouts.sidebar')

        <!--  Main wrapper -->
        <div class="main-panel">
        
            @include('layouts.header')

            <div class="container" id="fade-in">

                @yield('content')
                
                <button id="backToTop" class="floating-back-to-top-button btn btn-light rounded-circle px-3 shadow position-fixed">
                    <i class="fas fa-arrow-up"></i>
                </button>
            </div>

            @if (Request::segment(1) && !Request::segment(2))
            <div id="marginFloatingMenu" style="padding:27px 0px;" hidden></div>
            @endif

            <footer class="footer" id="fade-in">
                <div class="container-fluid d-flex justify-content-between">
                    <nav class="pull-left">
                    </nav>
                    <div class="copyright" title="Developed by {{ config('app.creator') }}">
                        Copyright &copy; {{ config('app.year_created') }} <a href="#" target="_blank" class="pe-1 text-primary">{{ config('app.company') }}</a>. All Rights Reserved v{{ config('app.version') }}
                    </div>
                    <div>
                    </div>
                </div>
            </footer>

            @if (Request::segment(1) && !Request::segment(2))
            <div class="my-bottom-nav">
                <a href="/home" class="my-nav-item {{ Request::is('home*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>

                <a href="/shipments" class="my-nav-item {{ Request::is('shipments*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span>Shipment</span>
                </a>

                <!-- TOMBOL SCAN (tengah) -->
                <a href="/shipments/scan" class="my-nav-item my-scan-btn {{ Request::is('/shipments/scan*') ? 'active' : '' }}">
                    <div class="scan-icon-wrapper">
                        <i class="fas fa-qrcode fs-2"></i>
                    </div>
                </a>

                <a href="/account-setting" class="my-nav-item {{ Request::is('account-setting*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>Account</span>
                </a>

                <a href="#" class="my-nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
            @endif
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
    <script src="{{ asset('dist/js/plugin/datepicker/flatpickr.min.js') }}"></script>
    <script src="{{ asset('dist/js/plugin/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('dist/js/plugin/select2/select2.full.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('dist/js/kaiadmin.min.js') }}"></script>
    <script src="{{ asset('dist/js/loading-page.js') }}?v={{ config('asset.version') }}"></script>

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

    @if(session()->has('warning'))
    <script>
        swal("Warning!", "{{ session('warning') }}", "warning", {
            timer: 3000
        });
    </script>
    @endif

    @if ($errors->any())
    <script>
        swal("Failed!", "Data has not been added.", "warning", {
            timer: 3000
        });
    </script>
    @endif

    <script>
        const isMobile = window.innerWidth < 991;

        if (isMobile) {
            $('#marginFloatingMenu').removeAttr('hidden');
            $(".footer").hide();
        }

		$(document).ready(function() {
			// Pastikan Select2 hanya diinisialisasi pada elemen yang sudah ada
			$(".select2").each(function() {
				if (!$(this).hasClass("select2-hidden-accessible")) {
					$(this).select2({
						dropdownParent: $(this).parent()
					});
				}
			});
		});

        window.addEventListener("scroll", function () {
			const backToTop = document.getElementById("backToTop");
			if (window.scrollY > 300) {
				backToTop.classList.add("show");
			} else {
				backToTop.classList.remove("show");
			}
		});

		// Smooth scroll ke atas
		document.getElementById("backToTop").addEventListener("click", function () {
			window.scrollTo({
				top: 0,
				behavior: "smooth",
			});
		});

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
	</script>

    @yield('customScripts')
</body>
</html>