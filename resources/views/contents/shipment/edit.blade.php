@extends('layouts.app')
@section('content')

<div class="page-inner">
    @include('layouts.breadcrumb')
    
    <div class="row">
        <div class="col-12">
            <div class="card card-stats card-round">
                <div class="card-header">
                    <h5 class="card-title">
                        <a href="javascript:history.back()"><i class="fas fa-arrow-left"></i></a>
                        <span class="ms-4">{{ $title }}</span>
                    </h5>
                </div>

                <div class="card-body">
                    <form id="createShipment"
                          class="row g-3 p-3"
                          action="{{ route('shipments.update', encrypt($shipment->id)) }}"
                          method="POST"
                          novalidate>
                        @csrf
                        @method('PUT')

                        <div class="form-group col-md-12 mt-0 px-3 py-0">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">Sender*</label>
                                    <input type="text"
                                           class="form-control text-uppercase alert-warning"
                                           value="{{ auth()->user()->name }}"
                                           disabled>
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="form-label">Sender PIC*</label>
                                    <input type="text"
                                           name="sender_pic"
                                           class="form-control text-uppercase alert-warning"
                                           value="{{ old('sender_pic', $shipment->sender_pic) }}"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4 mt-0">
                            <label class="form-label">Destination*</label>
                            <select name="destination" class="form-select select2 alert-warning" required>
                                <option disabled></option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->code }}"
                                        {{ old('destination', $shipment->destination) == $location->code ? 'selected' : '' }}>
                                        {{ strtoupper($location->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4 mt-0">
                            <label class="form-label">Destination PIC*</label>
                            <input type="text" name="destination_pic"
                                   class="form-control text-uppercase alert-warning"
                                   value="{{ old('destination_pic', $shipment->destination_pic) }}"
                                   required>
                        </div>

                        <div class="form-group col-md-4 mt-0">
                            <label class="form-label">Category*</label>
                            <select name="category_id" class="form-select select2 alert-warning" required>
                                <option disabled></option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $shipment->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ strtoupper($category->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-12 border-bottom mt-0">
                            <p class="text-muted mt-2 mb-0">
                                <i class="text-warning fas fa-boxes me-3"></i>Item Details
                            </p>
                        </div>

                        <div class="form-group pt-0 mt-3">
                            <div id="items_container">
                                @foreach(old('items', $shipment->shipment_detail ?? []) as $i => $item)
                                <div class="item-row border rounded p-3 mb-2">
                                    <input type="hidden"
                                        name="items[{{ $i }}][id]"
                                        value="{{ is_array($item) ? ($item['id'] ?? '') : $item->id }}">

                                    <div class="row g-3 px-2">
                                        <div class="col-md-4 px-2">
                                            <input type="text"
                                                name="items[{{ $i }}][name]"
                                                placeholder="Item Name*"
                                                class="form-control text-uppercase alert-warning"
                                                value="{{ is_array($item) ? $item['name'] : $item->item_name }}"
                                                required>
                                        </div>

                                        <div class="col-md-1 px-2">
                                            <input type="number"
                                                step="0.01"
                                                name="items[{{ $i }}][qty]"
                                                class="form-control alert-warning"
                                                placeholder="Qty*"
                                                value="{{ is_array($item) ? $item['quantity'] : $item->quantity }}"
                                                required>
                                        </div>

                                        <div class="col-md-2 px-2">
                                            <select name="items[{{ $i }}][uom]" class="form-control select2 alert-warning" required>
                                                @foreach($uoms as $uom)
                                                    <option value="{{ $uom }}"
                                                        {{ (is_array($item) ? $item['uom'] : $item->uom) == $uom ? 'selected' : '' }}>
                                                        {{ strtoupper($uom) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2 px-2">
                                            <select name="items[{{ $i }}][condition]" class="form-control select2 alert-warning" required>
                                                <option value="good" {{ (is_array($item) ? $item['condition'] : $item->condition) == 'good' ? 'selected' : '' }}>GOOD</option>
                                                <option value="broken" {{ (is_array($item) ? $item['condition'] : $item->condition) == 'broken' ? 'selected' : '' }}>BROKEN</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2 px-2">
                                            <input type="text"
                                                name="items[{{ $i }}][label]"
                                                class="form-control text-uppercase alert-warning"
                                                placeholder="No Label (optional)"
                                                value="{{ is_array($item) ? $item['label'] : $item->label }}">
                                        </div>

                                        <div class="col-md-1 px-2 d-flex justify-content-end">
                                            <button type="button" class="btn btn-label-danger btn-sm deleteRow w-100">
                                                <i class="fas fa-trash-alt me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <button type="button" id="addRow" class="btn btn-label-primary btn-sm mt-2">
                                <i class="fas fa-plus me-1"></i> Add Item
                            </button>

                            <div class="border-top my-3"></div>

                            <div class="form-group col-md-2 py-0">
                                <label class="form-label">Item Packing</label>
                                <div class="input-group mb-3">
                                    <input type="number" name="packing" class="form-control alert-warning" value="{{ old('packing', $shipment->packing) }}">
                                    <span class="input-group-text" id="basic-addon2"><i class="fas fa-box me-2"></i> Carton</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-top mb-3 my-0"></div>

                        <div class="form-group col-md-3 mt-0">
                            <label class="form-label">Handling Level*</label>
                            <div id="handlingGroup" class="selectgroup w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="handling_level" value="1" class="selectgroup-input" {{ old('handling_level', $shipment->handling_level) == 1 ? 'checked' : '' }} />
                                    <span class="selectgroup-button selectgroup-button-icon">
                                        <i class="fas fa-check-circle me-2"></i>Normal
                                    </span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="handling_level" value="2" class="selectgroup-input" {{ old('handling_level', $shipment->handling_level) == 2 ? 'checked' : '' }} />
                                    <span class="selectgroup-button selectgroup-button-icon">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Fragile
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6 mt-0">
                            <label class="form-label">Delivery By*</label>
                            <div id="shipmentGroup" class="selectgroup selectgroup-warning w-100">
                                <label class="selectgroup-item">
                                    <input type="radio" name="shipment_by" value="1" class="selectgroup-input" {{ old('shipment_by', $shipment->shipment_by) == 1 ? 'checked' : '' }} />
                                    <span class="selectgroup-button selectgroup-button-icon">
                                        <i class="fas fa-user me-2"></i>Personally
                                    </span>
                                </label>
                                <label class="selectgroup-item" {{ $area == 'ho' ? '' : 'hidden' }}>
                                    <input type="radio" name="shipment_by" value="2" class="selectgroup-input" {{ old('shipment_by', $shipment->shipment_by) == 2 ? 'checked' : '' }} />
                                    <span class="selectgroup-button selectgroup-button-icon">
                                        <i class="fas fa-truck me-2"></i>Shuttle
                                    </span>
                                </label>
                                <label class="selectgroup-item">
                                    <input type="radio" name="shipment_by" value="3" class="selectgroup-input" {{ old('shipment_by', $shipment->shipment_by) == 3 ? 'checked' : '' }} />
                                    <span class="selectgroup-button selectgroup-button-icon">
                                        <i class="fas fa-user-secret me-2"></i>Messenger
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-12 mt-0">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control text-uppercase alert-warning" rows="5">{{ old('notes', $shipment->notes) }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-label-primary btn-round float-end">
                                <i class="fas fa-save me-1"></i> Update
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customScripts')
<script>
    var uoms = @json($uoms);
</script>
<script src="{{ asset('dist/js/validation/shipment-transaction.js') }}?v={{ config('asset.version') }}"></script>
@endsection
