<!-- Sidebar -->
<div class="sidebar" data-background-color="white">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="white">
            <a href="index.html" class="logo">
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
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ Request::is('home*') ? 'active' : '' }}">
                    <a href="/home">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                <span class="sidebar-mini-icon">
                    <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Apps & Pages</h4>
                </li>
                <li class="nav-item {{ Request::is('master*') ? 'active submenu' : '' }}">
                    <a data-bs-toggle="collapse" href="#master">
                        <i class="fas fa-database"></i>
                        <p>Master Data</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ Request::is('master*') ? 'show' : '' }}" id="master">
                        <ul class="nav nav-collapse">
                        <li class="{{ Request::is('master-user*') ? 'active' : '' }}">
                            <a href="/master-user">
                            <span class="sub-item">User</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('master-checklist*') ? 'active' : '' }}">
                            <a href="/master-checklist">
                            <span class="sub-item">Checklist</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('master-store*') ? 'active' : '' }}">
                            <a href="/master-store">
                            <span class="sub-item">Store</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('master-wilayah*') ? 'active' : '' }}">
                            <a href="/master-wilayah">
                            <span class="sub-item">Wilayah</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('master-regional*') ? 'active' : '' }}">
                            <a href="/master-regional">
                            <span class="sub-item">Regional</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('master-area*') ? 'active' : '' }}">
                            <a href="/master-area">
                            <span class="sub-item">Area</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('master-cluster*') ? 'active' : '' }}">
                            <a href="/master-cluster">
                            <span class="sub-item">Cluster</span>
                            </a>
                        </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item {{ Request::is('transfer*') ? 'active' : '' }}">
                    <a href="/transfer">
                        <i class="fas fa-chart-bar"></i>
                        <p>Visit Report</p>
                    </a>
                </li>
                {{-- <li class="nav-item {{ Request::is('return*') ? 'active' : '' }}">
                    <a href="/return">
                        <i class="fas fa-undo-alt"></i>
                        <p>Checklist</p>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('return*') ? 'active' : '' }}">
                    <a href="/return">
                        <i class="fas fa-undo-alt"></i>
                        <p>Store</p>
                    </a>
                </li> --}}
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->