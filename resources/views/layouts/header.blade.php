<div class="main-header">
    <div class="main-header-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="white">
            <a href="index.html" class="logo">
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
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>

    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="submit" class="btn btn-search pe-1">
                            <i class="fa fa-search search-icon"></i>
                        </button>
                    </div>
                    <input type="text" placeholder="Search ..." class="form-control" />
                </div>
            </nav>

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none" >
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
                        <i class="fa fa-search"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-search animated fadeIn">
                        <form class="navbar-left navbar-form nav-search">
                            <div class="input-group">
                                <input type="text" placeholder="Search ..." class="form-control" />
                            </div>
                        </form>
                    </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        {{-- <span class="notification">4</span> --}}
                    </a>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar avatar-online">
                            <img src="{{ asset('dist/img/pp.jpg') }}" alt="..." class="avatar-img rounded-circle" />
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
                                <img
                                src="{{ asset('dist/img/pp.jpg') }}"
                                alt="image profile"
                                class="avatar-img rounded"
                                />
                            </div>
                            <div class="u-text mt-2">
                                <h4>{{ ucwords(auth()->user()->name) }}</h4>
                                <p class="text-muted">{{ ucwords(auth()->user()->getRoleNames()->first()) }}</p>
                                {{-- <a
                                href="#"
                                class="btn btn-xs btn-secondary btn-sm"
                                >View Profile</a
                                > --}}
                            </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i><span>{{ __('Account Setting') }}</span></a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <a class="dropdown-item">
                                    <button type="submit" class="dropdown-item ps-0">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    <span>{{ __('Logout') }}</span>
                                    </button>
                                </a>
                            </form>
                            {{-- <a class="dropdown-item" href="#">Logout</a> --}}
                        </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>