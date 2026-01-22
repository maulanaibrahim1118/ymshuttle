<?php

namespace App\Http\Controllers;

use Exception;
use App\Location;
use App\Shipment;
use Carbon\Carbon;
use App\Shipping_note;
use App\Helpers\Cleaner;
use App\Shipment_detail;
use App\Shipment_ledger;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessShipmentImageJob;
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class ShipmentController extends Controller
{
    public function index()
    {
        $data['title'] = "Shipment Lists";
        $data['breadcrumbs'] = [
            ['label' => 'Shipment'],
        ];

        $data['categories'] = DB::table('categories')->select('id', 'name')->orderBy('name', 'ASC')->get();
        $data['locations'] = Location::select('code', 'site', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        return view('contents.shipment.index', $data);
    }

    public function create()
    {
        $data['title'] = "Create Shipment";
        $data['breadcrumbs'] = [
            ['label' => 'Shipment', 'url' => '/shipments'],
            ['label' => 'Create'],
        ];

        $user = Auth::user();
        $userLocation = Location::where('code', $user->location_code)->first();
        $data['area'] = $userLocation->area ?? 'unknown';

        $data['categories'] = DB::table('categories')->select('id', 'name')->orderBy('name', 'ASC')->get();
        $data['locations'] = Location::select('code', 'site', 'name')
            ->whereNotIn('code', [Auth::user()->location_code])
            ->orderBy('name', 'ASC')
            ->get();

        $data['uoms'] = DB::table('uoms')->pluck('name')->sort()->values();

        return view('contents.shipment.create', $data);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $username = $user->username;
        $name = $user->name;
        $sender = $user->location->code;

        $validated = $request->validate([
            'sender_pic'        => 'required|string|max:50',
            'destination'       => 'required|string|max:5',
            'destination_pic'   => 'nullable|string|max:50',
            'category_id'       => 'required|integer',
            'packing'           => 'nullable|string|max:50',
            'handling_level'    => 'required|in:1,2',
            'shipment_by'       => 'required|in:1,2,3',
            'items'             => 'required|array|min:1',
            'items.*.name'      => 'required|string|max:50',
            'items.*.label'     => 'nullable|string|max:50',
            'items.*.condition' => 'required|string|max:10',
            'items.*.qty'       => 'required|numeric|min:0.01',
            'items.*.uom'       => 'required|string|max:15',
            'notes'             => 'nullable|string|max:255',
        ]);

        $cleaned = Cleaner::cleanAll($request->only([
            'sender_pic', 'destination', 'destination_pic', 'category_id',
            'packing', 'handling_level', 'shipment_by', 'notes'
        ]));

        $senderLoc = Location::where('code', $sender)->first();
        $destinationLoc = Location::where('code', $cleaned['destination'])->first();

        $dcSupport  = null;
        $isBranch  = 0;

        if ($destinationLoc) {
            $destinationArea = strtolower($destinationLoc->area);

            if ($cleaned['shipment_by'] == "2") {
                if ($destinationArea == 'ho') {
                    $isBranch  = 0;
                    $dcSupport = $senderLoc->dc_support ?? null;
                } elseif ($destinationArea == 'dc') {
                    $isBranch  = 0;
                    $dcSupport = $destinationLoc->dc_support ?? null;
                } else {
                    $isBranch   = 1;
                    $dcSupport = $destinationLoc->dc_support ?? null;
                }
            } 
        }

        $shipmentData = [
            'description'       => 'Shipment to ' . ucwords($destinationLoc->name),
            'category_id'       => $cleaned['category_id'],
            'sender'            => $sender,
            'sender_pic'        => strtolower($cleaned['sender_pic']),
            'destination'       => $cleaned['destination'],
            'destination_pic'   => strtolower($cleaned['destination_pic'] ?? ''),
            'packing'           => $cleaned['packing'],
            'handling_level'    => $cleaned['handling_level'],
            'shipment_by'       => $cleaned['shipment_by'],
            'is_branch'         => $isBranch,
            'dc_support'        => $dcSupport,
            'status'            => 1,
            'img_path'          => null,
            'notes'             => $cleaned['notes'] ?? null,
            'created_by'        => $username,
            'updated_by'        => $username,
        ];

        try {
            DB::beginTransaction();

            // === INSERT MAIN SHIPMENT ===
            $shipment = Shipment::create($shipmentData);

            // === INSERT ITEM DETAILS ===
            foreach ($validated['items'] as $item) {
                Shipment_detail::create([
                    'no_shipment' => $shipment->no_shipment,
                    'item_name'   => strtolower($item['name']),
                    'label'       => strtolower($item['label']) ?? null,
                    'condition'   => strtolower($item['condition']),
                    'quantity'    => $item['qty'],
                    'uom'         => strtolower($item['uom']),
                    'created_by'  => $username,
                    'updated_by'  => $username,
                ]);
            }

            // === INSERT LEDGER RECORD (CREATE) ===
            Shipment_ledger::create([
                'no_shipment'   => $shipment->no_shipment,
                'description'   => 'shipment created',
                'status'        => 1,
                'status_actor'  => $username,
                'location_point'=> 'HQ',
                'latitude'      => null,
                'longitude'     => null,
                'notes'         => 'dibuat dan disiapkan oleh ' . ucwords($name),
                'img_path'      => null,
                'created_by'    => $username,
                'updated_by'    => $username,
            ]);

            // === LOG ACTIVITY ===
            LogActivity::log(
                'create-shipment',
                "Successfully created shipment {$shipment->no_shipment} to {$shipment->destination}",
                '',
                $username
            );

            DB::commit();
            return redirect()->route('shipments.index')->with('success', "Shipment successfully created!");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating shipment: ' . $e->getMessage());

            LogActivity::log(
                'add-shipment',
                "Failed to create shipment to {$cleaned['destination']}",
                $e->getMessage(),
                $username
            );

            return redirect()->back()->with('error', 'Failed to create shipment!');
        }
    }

    private function getShipmentDetail($encryptedId)
    {
        $id = decrypt($encryptedId);

        return Shipment::with([
            'category',
            'shipment_detail',
            'shipment_ledger',
            'sender_location',
            'receiver_location',
            'creator'
        ])->findOrFail($id);
    }

    public function show($id)
    {
        $data['title'] = "Shipment Details";
        $data['breadcrumbs'] = [
            ['label' => 'Shipment', 'url' => '/shipments'],
            ['label' => 'Details'],
        ];

        $user = auth()->user();
        $userArea = $user->location->area;

        $shipment = $this->getShipmentDetail($id);

        $ledgers = Shipment_ledger::where('no_shipment', $shipment->no_shipment)
            ->where('created_by', $user->username)
            ->orderBy('id', 'DESC')
            ->get(['status']);

        $isSent = $ledgers->contains('status', '2');
        $isCollected = $ledgers->contains('status', '3');
        
        $lastLedgerStatus = Shipment_ledger::where('no_shipment', $shipment->no_shipment)
            ->orderByDesc('created_at')
            ->value('status');
            
        $data['shippingNotes'] = Shipping_note::where('no_shipment', $shipment->no_shipment)->get();

        $qr = Builder::create()
            ->writer(new PngWriter())
            ->data($shipment->no_shipment)
            ->size(300)
            ->margin(10)
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->logoPath(public_path('dist/img/logoym.jpg'))
            ->logoResizeToWidth(85)
            ->build();

        $data['qrCode'] = base64_encode($qr->getString());

        $data['canCollect'] = (in_array($shipment->status, ['2', '3'])
            && $shipment->agent !== $user->username
            && $shipment->shipment_by != 1
            && $shipment->destination !== $user->location_code
            && $lastLedgerStatus == '2'
            && !$isCollected) 
            && (($userArea == 'dc' && $shipment->dc_support == $user->location->code) || ($userArea == 'ho'));

        $data['canReceive'] = in_array($shipment->status, ['2', '3'])
            && $lastLedgerStatus == '2'
            && $shipment->destination === $user->location_code;

        $data['canSend'] = (
            $shipment->status == '1'
            && $shipment->created_by === $user->username
        ) || (
            !$user->hasRole('user')
            && in_array($shipment->status, ['2', '3'])
            && $shipment->agent === $user->username
            && !$isSent
        );

        $data['canDelete'] = $shipment->status == '1'
            && $shipment->created_by === $user->username;

        $data['shipment'] = $shipment;

        return view('contents.shipment.show', $data);
    }

    public function edit($id)
    {
        $data['title'] = "Edit Shipment";
        $data['breadcrumbs'] = [
            ['label' => 'Shipment', 'url' => '/shipments'],
            ['label' => 'Edit'],
        ];

        $user = Auth::user();
        $userLocation = Location::where('code', $user->location_code)->first();
        $data['area'] = $userLocation->area ?? 'unknown';

        // PAKAI QUERY YANG SAMA
        $data['shipment'] = $this->getShipmentDetail($id);

        $data['categories'] = DB::table('categories')
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        $data['locations'] = DB::table('locations')
            ->select('code', 'name')
            ->whereNotIn('code', [Auth::user()->location_code])
            ->orderBy('name', 'ASC')
            ->get();

        $data['uoms'] = DB::table('uoms')
            ->pluck('name')
            ->sort()
            ->values();

        return view('contents.shipment.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $user     = Auth::user();
        $username = $user->username;
        $name     = $user->name;
        $sender   = $user->location->code;

        $id = decrypt($id);

        $shipment = Shipment::where('id', $id)
            ->where('created_by', $username)
            ->where('status', 1)
            ->firstOrFail();

        $validated = $request->validate([
            'sender_pic'        => 'required|string|max:50',
            'destination'       => 'required|string|max:5',
            'destination_pic'   => 'nullable|string|max:50',
            'category_id'       => 'required|integer',
            'packing'           => 'nullable|string|max:50',
            'handling_level'    => 'required|in:1,2',
            'shipment_by'       => 'required|in:1,2,3',
            'items'             => 'required|array|min:1',
            'items.*.name'      => 'required|string|max:50',
            'items.*.label'     => 'nullable|string|max:50',
            'items.*.condition' => 'required|string|max:10',
            'items.*.qty'       => 'required|numeric|min:0.01',
            'items.*.uom'       => 'required|string|max:15',
            'notes'             => 'nullable|string|max:255',
        ]);

        $cleaned = Cleaner::cleanAll($request->only([
            'sender_pic',
            'destination',
            'destination_pic',
            'category_id',
            'packing',
            'handling_level',
            'shipment_by',
            'notes'
        ]));

        $senderLoc = Location::where('code', $sender)->first();
        $destinationLoc = Location::where('code', $cleaned['destination'])->first();

        $dcSupport = null;

        if ($destinationLoc) {
            $area = strtolower($destinationLoc->area);

            if ($area == 'ho' || $area == 'dc') {
                $isBranch  = 0;
                $dcSupport = $senderLoc->dc_support ?? null;
            } else {
                $isBranch   = 1;
                $dcSupport = $destinationLoc->dc_support ?? null;
            }
        }

        $shipmentData = [
            'description'       => 'Shipment to ' . ucwords($destinationLoc->name),
            'category_id'       => $cleaned['category_id'],
            'sender_pic'        => strtolower($cleaned['sender_pic']),
            'destination'       => $cleaned['destination'],
            'destination_pic'   => strtolower($cleaned['destination_pic'] ?? ''),
            'packing'           => $cleaned['packing'],
            'handling_level'    => $cleaned['handling_level'],
            'shipment_by'       => $cleaned['shipment_by'],
            'is_branch'         => $isBranch,
            'dc_support'        => $dcSupport,
            'notes'             => $cleaned['notes'] ?? null,
            'updated_by'        => $username,
        ];

        try {
            DB::beginTransaction();

            // === UPDATE MAIN SHIPMENT ===
            $shipment->update($shipmentData);

            // === RESET ITEM DETAILS ===
            Shipment_detail::where('no_shipment', $shipment->no_shipment)->delete();

            foreach ($validated['items'] as $item) {
                Shipment_detail::create([
                    'no_shipment' => $shipment->no_shipment,
                    'item_name'   => strtolower($item['name']),
                    'label'       => strtolower($item['label']) ?? null,
                    'condition'   => strtolower($item['condition']),
                    'quantity'    => $item['qty'],
                    'uom'         => strtolower($item['uom']),
                    'created_by'  => $username,
                    'updated_by'  => $username,
                ]);
            }

            LogActivity::log(
                'update-shipment',
                "Successfully updated shipment {$shipment->no_shipment}",
                '',
                $username
            );

            DB::commit();
            return redirect()->route('shipments.show', encrypt($shipment->id))->with('success', 'Shipment successfully updated!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating shipment: ' . $e->getMessage());

            LogActivity::log(
                'update-shipment',
                "Failed to update shipment {$shipment->no_shipment}",
                $e->getMessage(),
                $username
            );

            return redirect()->back()->with('error', 'Failed to update shipment!');
        }
    }

    public function destroy($noShipment)
    {
        $user     = Auth::user();
        $username = $user->username;
        $noShipment = decrypt($noShipment);

        $shipment = Shipment::where('no_shipment', $noShipment)->where('status', 1)->firstOrFail();

        try {
            DB::beginTransaction();

            Shipment_ledger::where('no_shipment', $shipment->no_shipment)->delete();

            Shipment_detail::where('no_shipment', $shipment->no_shipment)->delete();

            $shipment->delete();

            LogActivity::log(
                'delete-shipment',
                "Deleted shipment {$shipment->no_shipment}",
                '',
                $username
            );

            DB::commit();
            return redirect()->route('shipments.index')->with('success', 'Shipment successfully deleted!');
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            LogActivity::log(
                'delete-shipment',
                "Failed to delete shipment {$shipment->no_shipment}",
                $e->getMessage(),
                $username
            );

            return redirect()->back()->with('error', 'Failed to delete shipment!');
        }
    }

    public function print($noShipment)
    {
        $copies = request()->get('copies', 1); // default 1 jika tidak diisi

        $data['shipment'] = Shipment::with([
            'category',
            'shipment_detail',
            'shipment_ledger',
            'sender_location',
            'receiver_location',
            'creator'
        ])->where('no_shipment', $noShipment)->firstOrFail();

        $qr = Builder::create()
            ->writer(new PngWriter())
            ->data($noShipment)
            ->size(300)
            ->margin(10)
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->logoPath(public_path('dist/img/logoym.jpg'))
            ->logoResizeToWidth(85)
            ->build();

        $data['qrCode'] = base64_encode($qr->getString());
        $data['copies'] = (int) $copies;

        return view('contents.shipment.print', $data);
    }

    public function scanPage()
    {
        $data['title'] = "Shipment Scan";
        $data['breadcrumbs'] = [
            ['label' => 'Shipment', 'url' => '/shipments' ],
            ['label' => 'Scan'],
        ];

        return view('contents.shipment.scan', $data);
    }

    public function scanProcess(Request $request)
    {
        $no_shipment = $request->input('no_shipment');
        $user = Auth::user();

        $shipment = Shipment::where('no_shipment', $no_shipment)->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not available.'
            ]);
        }

        // jika bukan pembuat shipment DAN status masih 1 → tolak
        if (
            $user->username !== $shipment->created_by &&
            (int) $shipment->status === 1
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment is not ready yet.'
            ]);
        }

        // selain itu → tampilkan
        return response()->json([
            'success' => true,
            'redirect' => route('shipments.show', Crypt::encrypt($shipment->id))
        ]);
    }

    public function send(Request $request, $noShipment)
    {
        $user = Auth::user();
        $username = $user->username;
        $name = ucwords($user->name);

        $noShipment = decrypt($noShipment);

        $userAgent = $request->header('User-Agent');
        $isMobile = preg_match('/Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i', $userAgent);

        if (!$isMobile && $request->hasFile('delivery_image')) {
            return redirect()->back()->with('error', 'Image capture only available on mobile.');
        }

        $validated = $request->validate([
            'delivery_image' => 'nullable|image|mimes:jpeg,png,jpg|max:15360',
            'notes' => 'nullable|string',
        ]);

        $cleaned = Cleaner::cleanAll($request->only(['notes']));

        $shipment = Shipment::where('no_shipment', $noShipment)->firstOrFail();
        $userLocation = Location::where('code', $user->location_code)->first();

        $area = $userLocation->area ?? 'unknown';
        $locPoint  = $userLocation->name;
        $destination = null;
        $statusShipment = 3;
        $statusLedger = 2;

        // Jika pengiriman via personal / user sendiri
        if ($shipment->shipment_by === '1') {
            $destination = $shipment->receiver_location->name;

        // Jika pengiriman via shuttle atau messenger
        } else {
            if ($area === 'ho') {
                if ($user->hasRole('user')) {
                    if ($shipment->shipment_by === '2') {
                        $destination = 'transit griya center';
                        $statusShipment = 2;
                    } else {
                        $destination = 'messenger';
                        $statusShipment = 3;
                    }
                } elseif ($user->hasRole('agent')) {
                    if ($shipment->is_branch === '1') {
                        $destination = $shipment->dc_support === 'd53'
                            ? 'DC 53'
                            : 'DC Gedebage';
                    } else {
                        $destination = $shipment->receiver_location->name;
                    }
                } elseif ($user->hasRole('messenger')) {
                    $destination = $shipment->receiver_location->name;
                    $statusShipment = 4;
                    $statusLedger = 2;
                }

            } elseif ($area === 'dc') {
                if ($user->hasRole('user')) {
                    $destination = 'transit '.ucwords($locPoint);
                    $statusShipment = 2;
                    
                } elseif ($user->hasRole('agent')) {
                    if ($shipment->is_branch === '1') {
                        $destination = $shipment->receiver_location->name;
                    } else {
                        $destination = 'transit griya center';
                    }
                }
                
            } else {
                if ($user->hasRole('user')) {
                    $destination = $userLocation->dc_support === 'd53'
                        ? 'DC 53'
                        : 'DC Gedebage';
                }
            }
        }

        try {
            DB::beginTransaction();

            $shipment->update(['status' => $statusShipment, 'agent' => $username]);

            $imgPath = null;

            if ($user->hasRole('messenger') && $request->hasFile('delivery_image')) {

                $year = now()->year;
                $finalFilename = "shipments/{$year}/send_{$noShipment}_" . uniqid() . ".jpg";

                $tempPath = $request->file('delivery_image')->store('temp');

                // watermark text (sementara waktu saja)
                $watermarkText = now()->format('d-m-Y H:i:s');

                ProcessShipmentImageJob::dispatch(
                    $tempPath,
                    $finalFilename,
                    $watermarkText
                );

                // langsung simpan ke DB
                $imgPath = $finalFilename;
            }

            Shipment_ledger::create([
                'no_shipment'    => $noShipment,
                'description'    => "Send to {$destination}",
                'status'         => $statusLedger,
                'status_actor'   => $username,
                'location_point' => $locPoint,
                'latitude'       => null,
                'longitude'      => null,
                'notes'          => "Dikirim oleh {$name}",
                'img_path'       => $imgPath,
                'created_by'     => $username,
                'updated_by'     => $username,
            ]);

            if ($request->notes) {
                Shipping_note::create([
                    'no_shipment'   => $noShipment,
                    'notes'         => "[send]: ".$cleaned['notes'],
                    'created_by'    => $username,
                    'updated_by'    => $username,
                ]);
            }

            LogActivity::log('send-shipment', "Successfully send shipment: {$shipment->no_shipment}", '', $username);

            DB::commit();

            if ($user->hasRole('user')) {
                return redirect()->route('shipments.index')->with('success', "Shipment sent successfully.");
            } else {
                return redirect()->route('deliveries.index')->with('success', "Shipment sent successfully.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($imgPath && Storage::exists('public/' . $imgPath)) {
                Storage::delete('public/' . $imgPath);
            }
            
            Log::error('Error sent shipment: ' . $e->getMessage());

            LogActivity::log('send-shipment', "Failed to send shipment: {$noShipment}", $e->getMessage(), $username);

            return back()->with('error', 'Failed to send shipment!');
        }
    }

    public function collect(Request $request, $noShipment)
    {
        $user = Auth::user();
        $username = $user->username;
        $name = ucwords($user->name);

        $cleaned = Cleaner::cleanAll($request->only(['notes']));

        $userLoc = Location::where('code', $user->location_code)->first();
        $locPoint = null;

        if ($userLoc) {
            $area = strtolower($userLoc->area);
            
            if ($user->hasRole('agent')) {
                if ($area == 'ho') {
                    $locPoint  = "at transit griya center";
                } else {
                    $locPoint  = "at transit {$userLoc->name}";
                }
            }
            
            if ($user->hasRole('messenger')) {
                $locPoint  = "by messenger";
            }
        }

        try {
            DB::beginTransaction();

            $noShipment = decrypt($noShipment);

            $shipment = Shipment::where('no_shipment', $noShipment)->first();

            if (!$shipment) {
                throw new \Exception("Shipment not found: {$noShipment}");
            }

            if ($shipment->destination == auth()->user()->location_code) {
                $shipment->update(['status' => 5, 'agent' => $username]);
                $ledgerStatus = 5;
                $desc = "Shipment Received";
                $codeAction = "[receive]";
            } elseif ($shipment->status == 2 || $shipment->status == 3) {
                $shipment->update(['agent' => $username]);
                $ledgerStatus = 3;
                $desc = "Collected {$locPoint}";
                $codeAction = "[collect]";
            }

            Shipment_ledger::create([
                'no_shipment'   => $noShipment,
                'description'   => $desc,
                'status'        => $ledgerStatus,
                'status_actor'  => $username,
                'location_point'=> $locPoint,
                'latitude'      => null,
                'longitude'     => null,
                'notes'         => "Diterima oleh {$name}",
                'img_path'      => null,
                'created_by'    => $username,
                'updated_by'    => $username,
            ]);

            if ($request->notes) {
                Shipping_note::create([
                    'no_shipment'   => $noShipment,
                    'notes'         => $codeAction.': '.$cleaned['notes'],
                    'created_by'    => $username,
                    'updated_by'    => $username,
                ]);
            }

            LogActivity::log('collect-shipment', "Successfully collected shipment: {$shipment->no_shipment}", '', $username);

            DB::commit();

            if ($ledgerStatus == 5) {
                return redirect()->route('shipments.index')->with('success', "Shipment received successfully.");
            } else {
                return redirect()->route('collections.index')->with('success', "Shipment collected successfully.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error collected shipment: ' . $e->getMessage());

            LogActivity::log('collect-shipment', "Failed to collect shipment: {$shipment->no_shipment}", $e->getMessage(), $username);

            return back()->with('error', 'Failed to collect shipment!');
        }
    }

    // Ajax Function
    public function list(Request $request)
    {
        $user = Auth::user();
        $search = strtolower($request->input('search'));
        $categoryId = $request->input('category');
        $sender = $request->input('sender');
        $destination = $request->input('destination');
        $status = $request->input('status');
        $date = $request->date ? Carbon::createFromFormat('d/m/Y', $request->date)->toDateString() : null;

        $baseQuery = Shipment::with(['category', 'sender_location', 'receiver_location', 'creator'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->Where('no_shipment', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function ($creatorQuery) use ($search) {
                            $creatorQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('creator', function ($creatorQuery) use ($search) {
                            $creatorQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('sender_location', function ($senderQuery) use ($search) {
                            $senderQuery->whereRaw(
                                "regexp_replace(name, '^(yogya|yomart)\\s*', '', 'i') ILIKE ?",
                                ['%' . $search . '%']
                            );
                        })
                        ->orWhereHas('receiver_location', function ($destinationQuery) use ($search) {
                            $destinationQuery->whereRaw(
                                "regexp_replace(name, '^(yogya|yomart)\\s*', '', 'i') ILIKE ?",
                                ['%' . $search . '%']
                            );
                        });
                });
            })
            ->when(isset($categoryId), function ($q) use ($categoryId) {
                $q->where('category_id', decrypt($categoryId));
            })
            ->when(isset($sender), function ($q) use ($sender) {
                $q->where('sender', decrypt($sender));
            })
            ->when(isset($destination), function ($q) use ($destination) {
                $q->where('destination', decrypt($destination));
            })
            ->when(isset($status), function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when(isset($date), function ($q) use ($date) {
                $q->whereDate('created_at', $date);
            })
            ->when(!$user->hasRole('super admin'), function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('sender', $user->location_code)
                        ->orWhere('destination', $user->location_code);
                });
            });

            $query = $baseQuery
                ->orderBy(DB::raw('DATE(created_at)'), 'DESC')
                ->orderBy('status', 'ASC')
                ->paginate(12);

            $query->getCollection()->transform(function ($item) {
                $item->encrypted_id = encrypt($item->id);
                return $item;
            });

        return response()->json($query);
    }
}