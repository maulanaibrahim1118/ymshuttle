@extends('layouts.app')

@section ('content')
<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">
            <div class="card card-stats card-round">
                <div class="card-header alert-warning pb-3">
                    <div class="row">
                        <div class="col-9">
                            <h5 class="card-title mb-0"><i class="fas fa-user-cog me-3"></i> Account Setting</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stats card-round">
                <div class="card-header pb-3">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <h5 class="card-title mb-0">Profile Picture</h5>
                                </button>
                            </h2>
                        </div>
                    </div>
                </div>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="card-body">
                            <div class="row px-4 py-2">
                                <div class="mb-4 d-flex justify-content-center">
                                    <div class="avatar-xxl">
                                        <span class="avatar-title rounded-circle border border-white">
                                            {{ collect(explode(' ', Auth::user()->name))
                                                ->map(function($word) {
                                                    return strtoupper(substr($word, 0, 1));
                                                })
                                                ->join('') }}
                                        </span>
                                    </div>
                                    {{-- <img src="{{ asset('/dist/img/pp2.png') }}" class="rounded w-100" alt="..."> --}}
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-sm btn-label-warning btn-round w-100"><i class="fas fa-edit"></i> Change</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-stats card-round">
                <div class="card-header">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                    <h5 class="card-title mb-0">Account Information</h5>
                                </button>
                            </h2>
                        </div>
                    </div>
                </div>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="card-body">
                            <div class="row px-4 pb-3 text-muted">
                                <div class="col-12 col-md-12">
                                    <h3 class="text-warning fw-bold">{{ ucwords($user->name) }}</h3>
                                </div>
                                <div class="col-4 col-md-3 col-form-label">
                                    <p class="fw-bold mb-0">Username</p>
                                    <p class="fw-bold mb-0">Location</p>
                                    <p class="fw-bold mb-0">Registered</p>
                                    <p class="fw-bold mb-0">Last Login</p>
                                </div>
                                <div class="col-8 col-md-6 col-form-label">
                                    <p class="mb-0">: {{ $user->username }}</p>
                                    <p class="mb-0">: {{ ucwords($user->location->name) }}</p>
                                    <p class="mb-0">: {{ \Carbon\Carbon::parse($user->created_at)->format('d-M-Y') }}</p>
                                    <p class="mb-0">: {{ \Carbon\Carbon::parse($user->last_login_at)->format('d-M-Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stats card-round">
                <div class="card-header pb-3">
                    <div class="accordion accordion-flush" id="accordionExample">
                        <div class="accordion-item mx-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                    <h5 class="card-title mb-0">Change Password</h5>
                                </button>
                            </h2>
                        </div>
                    </div>
                </div>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <div class="card-body">
                            <form class="px-2" method="POST" action="{{ route('password.change') }}">
                                @csrf
                                <div class="row mb-2">
                                    {{-- <label for="current_password" class="col-5 col-form-label">Current Password</label> --}}
                                    <div class="col-12 d-flex">
                                        {{-- <span class="mt-2 me-2">:</span> --}}
                                        <input type="password" class="form-control alert-warning" name="current_password" id="current_password" placeholder="Current Password" required>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    {{-- <label for="new_password" class="col-5 col-form-label">New Password</label> --}}
                                    <div class="col-12 d-flex">
                                        {{-- <span class="mt-2 me-2">:</span> --}}
                                        <input type="password" class="form-control alert-warning" name="new_password" id="new_password" placeholder="New Password" required>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    {{-- <label for="new_password_confirmation" class="col-5 col-form-label">Confirm Password</label> --}}
                                    <div class="col-12 d-flex">
                                        {{-- <span class="mt-2 me-2">:</span> --}}
                                        <input type="password" class="form-control alert-warning" name="new_password_confirmation" id="new_password_confirmation" placeholder="New Password Confirmation" required>
                                    </div>
                                </div>
                                <div class="row mb-2 px-3">
                                    <button type="submit" class="col-sm-12 btn btn-label-primary btn-round"><i class="fas fa-save me-1"></i> Save Change</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customScripts')
@if ($errors->any())
    <script>
        let errors = [
            @foreach ($errors->all() as $error)
                "{{ $error }}",
            @endforeach
        ];

        function showErrors(index) {
            if (index < errors.length) {
                $.notify({
                    icon: 'icon-bell',
                    title: 'Failed',
                    message: errors[index],
                },{
                    type: 'danger',
                    placement: {
                        from: "top",
                        align: "right"
                    },
                    delay: 5000, // Durasi notifikasi tampil
                });

                // Tunggu sebelum memunculkan error berikutnya
                setTimeout(() => showErrors(index + 1), 500);
            }
        }

        showErrors(0);
    </script>
@endif
@endsection