@extends('layouts.app')
@section('content')

<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold">{{ $path }}</h3>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="/">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                {{ $path }}
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card card-stats card-animate card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <a href="#">
                            <button class="btn btn-primary icon-big text-center rounded-3">
                            <i class="fas fa-calendar-check"></i>
                            </button>
                            </a>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                            <p class="card-category text-dark fs-5">Store Visited</p>
                            <h4 class="card-title fs-3 fw-bolder">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('customScripts')
    @if(session()->has('loginSuccess'))
        <script>
            $(document).ready(function() {
                var userName = '{{ $fullname }}';
                
                // Menampilkan notifikasi sapaan
                $.notify({
                    icon: 'icon-check',
                    title: 'Hello, ' + userName,
                    message: 'Welcome back to the YMShuttle App!',
                },{
                    type: 'success',
                    placement: {
                        from: "top",
                        align: "right"
                    },
                    delay: 2000,
                });
            });
        </script>
    @endif
    {{-- <script>
        $(document).ready(function () {
            $("#dashboard1-datatables").DataTable({});
            $("#dashboard2-datatables").DataTable({});
        });
    </script> --}}
@endsection