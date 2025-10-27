@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-md-12">
            <div class="card card-annoucement card-round">
                <div class="card-body px-5 py-5">
                    <div class="card-opening">Welcome to <span class="fw-bold">YMShuttle</span>, <span class="fw-bolder text-warning">{{ ucwords(Auth::user()->name) }}! 🎉</span></div>
                    <div class="card-desc text-muted">
                        A simple system to monitor shipment and delivery processes, track statuses, and ensure goods arrive safely and on time.
                    </div>
                    <div class="card-detail mt-3">
                        <div class="card-tools d-flex">
                            <a href="#" class="btn btn-label-warning btn-round me-2" title="User Guide">
                                <span>View Details</span>
                                <span class="ms-1">
                                    <i class="fas fa-chevron-right"></i>
                                </span> 
                            </a>
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
            var userName = '{{ $name }}';

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
                delay: 10000,
            });
        });
    </script>
@endif
@endsection