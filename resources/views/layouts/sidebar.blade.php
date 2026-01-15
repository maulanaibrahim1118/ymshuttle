<!-- Sidebar -->
<div class="sidebar sidebar-style-2" data-background-color="white">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="white">
            <a href="/home" class="logo">
                <img src="{{ asset('dist/img/logoym.png') }}" alt="navbar brand" class="navbar-brand" height="28" />
                <h3 class="text-dark mt-2 ms-2"><b>{{ config('app.name') }}</b></h3>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
                </button>
            </div>
            <div class="more">
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
                        <div class="u-text mt-2">
                            <h4>{{ ucwords(auth()->user()->name) }}</h4>
                            <p class="text-muted">{{ ucwords(auth()->user()->location->name) }}</p>
                        </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('account.index') }}" style="line-height: 20px"><i class="fas fa-user-cog me-2"></i><span>{{ __('Account Setting') }}</span></a>
                        <div class="dropdown-divider"></div>
                        <form id="logout-form" action="{{ route('logout') }}" method="post">
                            @csrf
                            <a class="dropdown-item" style="line-height: 20px">
                                <button type="submit" class="dropdown-item ps-0">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                <span>{{ __('Logout') }}</span>
                                </button>
                            </a>
                        </form>
                    </li>
                    </div>
                </ul>
            </div>
        </div>
        <!-- End Logo Header -->
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-warning mt-3">
                <li class="nav-item {{ Request::is('home*') ? 'active' : '' }}">
                    <a href="/home">
                        <i class="fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>
                
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Apps & Pages</h4>
                </li>

                @canany(['location-view', 'category-view'])
                <li class="nav-item {{ Request::is('master*') ? 'active submenu' : '' }}">
                    <a data-bs-toggle="collapse" href="#master">
                        <i class="fas fa-database"></i>
                        <p>Master Data</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ Request::is('master*') ? 'show' : '' }}" id="master">
                        <ul class="nav nav-collapse">
                            @can('location-view')
                            <li class="{{ Request::is('master-locations*') ? 'active' : '' }}">
                                <a href="/master-locations">
                                <span class="sub-item">Location</span>
                                </a>
                            </li>
                            @endcan
                            @can('category-view')
                            <li class="{{ Request::is('master-categories*') ? 'active' : '' }}">
                                <a href="/master-categories">
                                <span class="sub-item">Category</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @can('shipment-view')
                <li class="nav-item {{ Request::is('shipments*') ? 'active' : '' }}">
                    <a href="/shipments">
                        <i class="fas fa-box"></i>
                        <p>Shipment</p>
                    </a>
                </li>
                @endcan
                
                @can('collection-view')
                <li class="nav-item {{ Request::is('collections*') ? 'active' : '' }}">
                    <a href="/collections">
                        <i class="fas fa-layer-group"></i>
                        <p>Collection</p>
                    </a>
                </li>
                @endcan

                @can('delivery-view')
                <li class="nav-item {{ Request::is('deliveries*') ? 'active' : '' }}">
                    <a href="/deliveries">
                        <i class="fas fa-shipping-fast"></i>
                        <p>Delivery</p>
                    </a>
                </li>
                @endcan

                @canany(['user-view', 'role-view'])
                <li class="nav-item {{ Request::is('setting*') ? 'active submenu' : '' }}">
                    <a data-bs-toggle="collapse" href="#setting">
                        <i class="fas fa-cog"></i>
                        <p>Setting</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ Request::is('setting*') ? 'show' : '' }}" id="setting">
                        <ul class="nav nav-collapse">
                            @can('user-view')
                            <li class="{{ Request::is('setting-users*') ? 'active' : '' }}">
                                <a href="/setting-users">
                                <span class="sub-item">User</span>
                                </a>
                            </li>
                            @endcan
                            @can('role-view')
                            <li class="{{ Request::is('setting-roles*') ? 'active' : '' }}">
                                <a href="/setting-roles">
                                <span class="sub-item">Role & Permission</span>
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endcanany

                @can('logActivity-view')
                <li class="nav-item {{ Request::is('log-activities*') ? 'active' : '' }}">
                    <a href="/log-activities">
                        <i class="fas fa-history"></i>
                        <p>Log Activity</p>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->