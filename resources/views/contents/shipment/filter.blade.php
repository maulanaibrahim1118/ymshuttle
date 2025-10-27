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
                    <div class="form-group col-md-3">
                        <select name="category" class="form-select select2" id="category">
                            <option selected value="">ALL CATEGORIES</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ strtoupper($category->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select name="sender" class="form-select select2" id="sender">
                            <option selected value="">ALL SENDERS</option>
                            @foreach ($locations as $location)
                            <option value="{{ encrypt($location->code) }}">{{ strtoupper($location->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select name="destination" class="form-select select2" id="destination">
                            <option selected value="">ALL DESTINATIONS</option>
                            @foreach ($locations as $location)
                            <option value="{{ encrypt($location->code) }}">{{ strtoupper($location->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select name="status" class="form-select select2" id="status">
                            <option selected value="">ALL STATUSES</option>
                            <option value="1">CREATED</option>
                            <option value="2">AWAITING SHIPMENT</option>
                            <option value="3">ON DELIVERY</option>
                            <option value="4">DELIVERED</option>
                            <option value="5">RECEIVED</option>
                            <option value="6">CANCELLED</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <p class="border-bottom mt-2 mb-0"></p>
                    </div>
                    
                    <div class="col-md-12 mt-3">
                        <button type="submit" id="filterBtn" class="btn btn-label-info btn-round"><i class="fas fa-filter me-1"></i> Filter</button>
                    </div>
                </form><!-- End Input Form -->
            </div>
        </div>
    </div>
</div><!-- End Info Card -->