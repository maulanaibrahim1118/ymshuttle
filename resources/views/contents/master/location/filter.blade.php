<div class="card card-stats card-round">
    <div class="card-body pb-0">
        <div class="accordion accordion-flush" id="accordionExample">
            <div class="accordion-item mx-2">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <h5 class="card-title mb-0"><i class="fas fa-filter me-3"></i>Filter</h5>
                    </button>
                </h2>
            </div>
        </div>
    </div>
    <div class="border-top"></div>
    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
        <div class="accordion-body">
            <div class="card-body">
                <form id="filter-form" class="row px-3 pb-3">
                    @csrf
                    <div class="form-group col-md-4">
                        <select name="location_id" class="form-select select2" id="location_id">
                            <option selected value="">ALL LOCATIONS</option>
                            @foreach ($locations as $location)
                            <option value="{{ encrypt($location->id) }}">{{ strtoupper($location->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select name="city" class="form-select select2" id="city">
                            <option selected value="">ALL CITIES</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->city }}">{{ strtoupper($city->city) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <select name="active" class="form-select select2" id="active">
                            <option selected value="">ALL STATUSES</option>
                            <option value="1">ACTIVE</option>
                            <option value="0">INACTIVE</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <select name="support" class="form-select select2" id="support">
                            <option selected value="">ALL DC SUPPORTS</option>
                            @foreach($supports as $support)
                                <option value="{{ $support['name'] }}">{{ strtoupper($support['name']) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <p class="border-bottom mt-2 mb-0"></p>
                    </div>
                    
                    <div class="col-md-12 mt-3">
                        <button type="submit" id="filterBtn" class="btn btn-warning text-light"><i class="fas fa-filter me-1"></i> Filter</button>
                    </div>
                </form><!-- End Input Form -->
            </div>
        </div>
    </div>
</div><!-- End Info Card -->