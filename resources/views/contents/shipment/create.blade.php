@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">
            <div class="card card-stats card-round">
                <div class="card-header">
                    <h5 class="card-title"><a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a><span class="ms-4">{{ $title }}</span></h5>
                </div>
                <div class="card-body">
                    <form id="createShipment" class="row g-3 p-3" action="{{ route('shipments.store') }}" method="POST" novalidate>
                        @csrf
                        <div class="form-group col-md-12 mt-0 px-3 py-0">
                            <div class="row">
                                <div class="form-group col-md-4 ">
                                    <label for="sender" class="form-label">Sender*</label>
                                    <input type="text" name="sender" class="form-control text-uppercase alert-warning" id="sender" value="{{ old('sender', auth()->user()->name) }}" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="sender_pic" class="form-label">Sender PIC*</label>
                                    <input type="text" name="sender_pic" class="form-control text-uppercase alert-warning" id="sender_pic" value="{{ old('sender_pic') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group col-md-4 mt-0">
                            <label for="destination" class="form-label">Destination*</label>
                            <select name="destination" class="form-select select2 alert-warning" id="destination" required>
                                <option selected disabled></option>
                                @foreach($locations as $location)
                                    @if(old('destination') == $location->code)
                                    <option selected value="{{ $location->code }}">{{ strtoupper($location->name) }}</option>
                                    @else
                                    <option value="{{ $location->code }}">{{ strtoupper($location->name) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 mt-0">
                            <label for="destination_pic" class="form-label">Destination PIC*</label>
                            <input type="text" name="destination_pic" class="form-control text-uppercase alert-warning" id="destination_pic" value="{{ old('destination_pic') }}" required>
                        </div>
                        <div class="form-group col-md-4 mt-0">
                            <label for="category_id" class="form-label">Category*</label>
                            <select name="category_id" class="form-select select2 alert-warning" id="category_id" required>
                                <option selected disabled></option>
                                @foreach($categories as $category)
                                    @if(old('category_id') == $category->id)
                                    <option selected value="{{ $category->id }}">{{ strtoupper($category->name) }}</option>
                                    @else
                                    <option value="{{ $category->id }}">{{ strtoupper($category->name) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12 border-bottom mt-0">
                            <p class="text-muted mt-2 mb-0"><i class="text-warning fas fa-boxes me-3"></i>Item Details</p>
                        </div>

                        <div class="form-group pt-0 mt-3">
                            <div id="items_container">
                                <!-- First item row -->
                                <div class="item-row border rounded p-3 mb-2">
                                    <div class="row g-3 px-2">
                                        <div class="col-12 col-md-4 px-2">
                                            <input type="text" name="items[0][name]" class="form-control text-uppercase alert-warning" placeholder="Item Name*" required>
                                        </div>
                                        <div class="col-6 col-md-2 px-2">
                                            <input type="text" name="items[0][label]" class="form-control text-uppercase alert-warning" placeholder="Label Number">
                                        </div>
                                        <div class="col-6 col-md-2 px-2">
                                            <select name="items[0][condition]" class="form-control select2 alert-warning text-uppercase" required>
                                                <option value="" disabled selected>CONDITION*</option>
                                                <option value="good">GOOD</option>
                                                <option value="broken">BROKEN</option>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-1 px-2">
                                            <input type="number" name="items[0][qty]" class="form-control text-uppercase alert-warning" placeholder="Qty*" required>
                                        </div>
                                        <div class="col-6 col-md-2 px-2">
                                            <input type="text" name="items[0][uom]" class="form-control text-uppercase alert-warning" placeholder="UOM*" required>
                                        </div>
                                        <div class="col-12 col-md-1 px-2 d-flex justify-content-end">
                                            <button type="button" class="btn btn-label-danger btn-sm deleteRow w-100"><i class="fas fa-trash-alt me-1"></i> Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="addRow" class="btn btn-label-primary btn-sm mt-2"><i class="fas fa-plus me-1"></i> Add Item</button>
                        </div>

                        <div class="border-top mb-3 mb-0"></div>

                        <div class="form-group col-md-3 mt-0">
                            <label class="form-label">Item Type*</label>
                            <div id="handlingGroup" class="selectgroup selectgroup-warning w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="is_asset" value="0" class="selectgroup-input" />
                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-cube me-2"></i>Non Asset</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="is_asset" value="1" class="selectgroup-input" />
                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-gem me-2"></i>Asset</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-3 mt-0">
                            <label class="form-label">Handling Level*</label>
                            <div id="handlingGroup" class="selectgroup w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="handling_level" value="1" class="selectgroup-input" />
                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-check-circle me-2"></i>Normal</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="handling_level" value="2" class="selectgroup-input" />
                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-exclamation-triangle me-2"></i>Fragile</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6 mt-0">
                            <label class="form-label">Shipment By*</label>
                            <div id="shipmentGroup" class="selectgroup selectgroup-warning w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="shipment_by" value="1" class="selectgroup-input" />
                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-user me-2"></i>Personally</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="shipment_by" value="2" class="selectgroup-input" />
                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-truck me-2"></i>Shuttle</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="shipment_by" value="3" class="selectgroup-input" />
                                    <span class="selectgroup-button selectgroup-button-icon"><i class="fas fa-user-secret me-2"></i>Messenger</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-12 mt-0">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" class="form-control text-uppercase alert-warning" id="notes" value="{{ old('notes') }}" rows="5"></textarea>
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
                                    <button type="submit" id="submitBtn" class="btn btn-label-warning btn-round float-end ms-2"><i class="fab fa-telegram-plane me-1"></i> Submit</button>
                                </div>
                            </div>
                        </div>
                    </form><!-- End Input Form -->
                </div>
            </div><!-- End Info Card -->
        </div>
    </div>
</div>
@endsection

@section('customScripts')
@if ($errors->any())
<script>
    swal("Failed!", "Transaction has not been created.", "warning", {
        timer: 3000
    });
</script>
@endif
<script>
    
</script>
{{-- <script>
    const claimPage = {
        isEdit: {{ isset($benefit) ? 'true' : 'false' }},
        selectedemployeeNumber: @json($benefit->employee_number ?? null),
        selectedClaimType: @json($benefit->claim_type_id ?? null),
        selectedClaimFor: @json($benefit->claim_for ?? null)
    };
</script> --}}
{{-- <script src="{{ asset('dist/js/validation/transaction-rule.js') }}?v={{ config('asset.version') }}"></script> --}}
<script src="{{ asset('dist/js/validation/create-shipment.js') }}?v={{ config('asset.version') }}"></script>
@endsection