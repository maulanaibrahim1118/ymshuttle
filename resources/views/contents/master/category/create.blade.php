@can('category-add')
<div class="card card-stats card-round">
    <div class="card-body pb-0">
        <div class="accordion accordion-flush" id="accordionExample">
            <div class="accordion-item mx-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <h5 class="card-title mb-0"><i class="fas fa-plus me-3"></i>Add Category</h5>
                    </button>
                </h2>
            </div>
        </div>
    </div>
    <div class="border-top"></div>
    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
        <div class="accordion-body">
            <div class="card-body">
                <form id="addForm" class="row g-3 p-2" action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="id" name="id" value="{{ encrypt(0) }}">

                    <div class="form-group unique-name @error('name') has-error @enderror col-md-4">
                        <label class="form-label">Category Name*</label>
                        <input type="text" name="name" class="form-control alert-warning text-uppercase" id="name" value="{{ old('name') }}" maxlength="50" required>
                        <label id="name-taken" style="display: none;">Category Name exists!</label>
                    </div>

                    <div class="col-md-12">
                        <p class="border-bottom mt-2 mb-0"></p>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-6">
                                <p>(*) Mandatory</p>
                            </div>
                            <div class="col-6">
                                <button type="submit" id="submitBtn" class="btn btn-warning text-light float-end ms-2" disabled><i class="fas fa-plus me-1"></i> Add</button>
                            </div>
                        </div>
                    </div>
                </form><!-- End Input Form -->
            </div>
        </div>
    </div>
</div><!-- End Info Card -->
@endcan