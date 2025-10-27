<div class="main-header">
    <div class="main-header-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="white">
            <a href="/" class="logo">
                {{-- <img src="{{ asset('dist/img/kaiadmin/logo_dark.svg') }}" alt="navbar brand" class="navbar-brand" height="20"/> --}}
                <h3 class="text-light"><b>{{ config('app.name') }}</b></h3>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
        </div>
        <!-- End Logo Header -->
    </div>

    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                <div class="d-flex">
                    <img src="{{ asset('dist/img/griya_center.png') }}" alt="navbar brand" class="navbar-brand mt-1" height="38">
                    <h7 class="text-muted mt-3">.:: PT. Griya Pratama</h7>
                </div>
            </nav>

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar avatar-online">
                            <span class="avatar-title rounded-circle border border-white">
                                {{ collect(explode(' ', Auth::user()->name))
                                    ->map(function($word) {
                                        return strtoupper(substr($word, 0, 1));
                                    })
                                    ->take(2)
                                    ->join('') }}
                            </span>
                            {{-- <img src="{{ asset('dist/img/pp2.png') }}" alt="..." class="avatar-img rounded-circle" /> --}}
                        </div>
                        <span class="profile-username">
                            <span class="op-7">Hi,</span>
                            <span class="fw-bold">{{ ucwords(auth()->user()->name) }}</span>
                            <span class="caret"></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            <div class="user-box">
                            <div class="avatar-lg">
                                <span class="avatar-title rounded-circle border border-white">
                                    {{ collect(explode(' ', Auth::user()->name))
                                        ->map(function($word) {
                                            return strtoupper(substr($word, 0, 1));
                                        })
                                        ->take(2)
                                        ->join('') }}
                                </span>
                                {{-- <img src="{{ asset('dist/img/pp2.png') }}" alt="image profile" class="avatar-img rounded" /> --}}
                            </div>
                            <div class="u-text mt-1">
                                <h4>{{ ucwords(auth()->user()->name) }}</h4>
                                <p class="text-muted">{{ ucwords(auth()->user()->location->name) }}</p>
                            </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('account.index') }}"><i class="fas fa-user-cog me-2"></i><span>{{ __('Account Setting') }}</span></a>
                            <div class="dropdown-divider"></div>
                            <form id="logout-form" action="{{ route('logout') }}" method="post">
                                @csrf
                                <a class="dropdown-item">
                                    <button type="submit" class="dropdown-item ps-0">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    <span>{{ __('Logout') }}</span>
                                    </button>
                                </a>
                            </form>
                        </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>