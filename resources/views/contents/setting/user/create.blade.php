@can('user-add')
<div class="card card-stats card-round">
    <div class="card-body pb-0">
        <div class="accordion accordion-flush" id="accordionExample">
            <div class="accordion-item mx-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <h5 class="card-title mb-0"><i class="fas fa-plus me-3"></i>Add User</h5>
                    </button>
                </h2>
            </div>
        </div>
    </div>
    <div class="border-top"></div>
    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
        <div class="accordion-body">
            <div class="card-body">
                <form id="addForm" class="row g-3 p-2" action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id" name="id" value="{{ encrypt(0) }}">

                    <div class="form-group @error('name') has-error @enderror col-md-3">
                        <label class="form-label">Full Name*</label>
                        <input type="text" name="name" class="form-control alert-warning text-uppercase" id="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group unique-username @error('username') has-error @enderror col-md-2">
                        <label class="form-label">Username*</label>
                        <input type="text" name="username" class="form-control alert-warning text-uppercase" id="username" value="{{ old('username') }}" maxlength="10" required>
                        <label id="username-taken" style="display: none;">Username exists!</label>
                    </div>

                    <div class="form-group pb-0 @error('location_code') has-error @enderror col-md-4">
                        <label class="form-label">Location*</label>
                        <select name="location_code" class="form-select select2 alert-warning" id="location" required>
                            <option selected disabled></option>
                            @foreach($locations as $location)
                                @if(old('location_code') == $location->code)
                                <option selected value="{{ $location->code }}">{{ strtoupper($location->name) }}</option>
                                @else
                                <option value="{{ $location->code }}">{{ strtoupper($location->name) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group pb-0 @error('role') has-error @enderror col-md-3">
                        <label class="form-label">Role*</label>
                        <select name="role" class="form-select alert-warning select2" id="role" required>
                            <option selected disabled></option>
                            @foreach($roles as $role)
                                @if(old('role') == $role->name)
                                <option selected value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                @else
                                <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <p class="border-bottom mt-2 mb-0"></p>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-6">
                                <p class="text-muted">(*) Mandatory</p>
                            </div>
                            <div class="col-6">
                                <button type="submit" id="submitBtn" class="btn btn-label-warning btn-round float-end ms-2" disabled><i class="fas fa-plus me-1"></i> Add</button>
                            </div>
                        </div>
                    </div>
                </form><!-- End Input Form -->
            </div>
        </div>
    </div>
</div><!-- End Info Card -->
@endcan