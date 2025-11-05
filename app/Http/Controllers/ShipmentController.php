<?php

namespace App\Http\Controllers;

use Exception;
use App\Location;
use App\Shipment;
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
use Endroid\QrCode\Encoding\Encoding;
use Illuminate\Support\Facades\Crypt;
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
        $data['locations'] = DB::table('locations')
            ->select('code', 'name')
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

        $data['categories'] = DB::table('categories')->select('id', 'name')->orderBy('name', 'ASC')->get();
        $data['locations'] = DB::table('locations')
            ->select('code', 'name')
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

        $destinationLoc = Location::where('code', $cleaned['destination'])->first();

        $isBranch   = 0;
        $dcSupport  = null;

        if ($destinationLoc) {
            $area = strtoupper($destinationLoc->area ?? '');

            // Kalau area bukan HO dan bukan DC → branch
            if ($area !== 'ho' && $area !== 'dc') {
                $isBranch  = 1;
                $dcSupport = $destinationLoc->dc_support ?? null;
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
            'note'              => $cleaned['notes'] ?? null,
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
                'note'          => 'dibuat dan disiapkan oleh ' . ucwords($name),
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

    public function show($id)
    {
        $data['title'] = "Shipment Details";
        $data['breadcrumbs'] = [
            ['label' => 'Shipment', 'url' => '/shipments' ],
            ['label' => 'Details'],
        ];

        $id = decrypt($id);
        
        $data['shipment'] = Shipment::with([
            'category',
            'shipment_detail',
            'shipment_ledger',
            'sender_location',
            'receiver_location',
            'creator'
        ])->findOrFail($id);

        if ($data['shipment']->status == 1) {
            $data['status'] = "Created";
        } elseif ($data['shipment']->status == 2) {
            $data['status'] = "Awaiting Payment";
        } elseif ($data['shipment']->status == 3) {
            $data['status'] = "On Delivery";
        } elseif ($data['shipment']->status == 4) {
            $data['status'] = "Delivered";
        } elseif ($data['shipment']->status == 5) {
            $data['status'] = "Finished";
        } else {
            $data['status'] = "Cancelled";
        }

        $qr = Builder::create()
            ->writer(new PngWriter())
            ->data($data['shipment']->no_shipment)
            ->size(300)
            ->margin(10)
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh()) // biar tahan gangguan logo besar
            ->logoPath(public_path('dist/img/logoym.jpg'))
            ->logoResizeToWidth(85) // besar, tapi masih aman
            ->build();

        // Buat QR Code Base64
        $data['qrCode'] = base64_encode($qr->getString());

        return view('contents.shipment.show', $data);
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

        $shipment = Shipment::where('no_shipment', $no_shipment)->first();

        if (!$shipment) {
            return response()->json(['success' => false, 'message' => 'Invalid QR Code!']);
        }

        return response()->json([
            'success' => true,
            'redirect' => route('shipments.show', Crypt::encrypt($shipment->id))
        ]);
    }

    public function collect(Request $request, $noShipment)
    {
        $user = Auth::user();
        $username = $user->username;
        $name = ucwords($user->name);

        $agentLoc = Location::where('code', $user->location_code)->first();

        $locPoint = null;

        if ($agentLoc) {
            $area = strtoupper($agentLoc->area ?? '');

            // Kalau area bukan HO dan bukan DC → branch
            if ($area !== 'ho') {
                $locPoint  = "transit griya center";
            } else {
                $locPoint  = "transit {$agentLoc->name}";
            }
        }

        $notes = null;

        if ($request->notes) {
            $notes = "dengan catatan: {$request->notes}";
        }

        try {
            DB::beginTransaction();

            $noShipment = decrypt($noShipment);

            $shipment = Shipment::where('no_shipment', $noShipment)->first();

            if (!$shipment) {
                throw new \Exception("Shipment not found: {$noShipment}");
            }

            if ($shipment->status == 1) {
                $shipment->update(['status' => 2, 'agent' => $username]);
            } elseif ($shipment->status == 3) {
                $shipment->update(['agent' => $username]);
            }

            Shipment_ledger::create([
                'no_shipment'   => $noShipment,
                'description'   => "Collected at {$locPoint}",
                'status'        => 2,
                'status_actor'  => $username,
                'location_point'=> $locPoint,
                'latitude'      => null,
                'longitude'     => null,
                'note'          => "Diterima oleh {$name} {$notes}",
                'img_path'      => null,
                'created_by'    => $username,
                'updated_by'    => $username,
            ]);

            LogActivity::log(
                'collect-shipment',
                "Successfully collected shipment: {$shipment->no_shipment}",
                '',
                $username
            );

            DB::commit();

            return back()->with('success', 'Shipment collected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error collected shipment: ' . $e->getMessage());

            LogActivity::log(
                'collect-shipment',
                "Failed to collect shipment: {$shipment->no_shipment}",
                $e->getMessage(),
                $username
            );

            return back()->with('error', 'Failed to collect shipment!');
        }
    }

    // Ajax Function
    public function list(Request $request)
    {
        $search = strtolower($request->input('search'));
        $categoryId = $request->input('category');
        $sender = $request->input('sender');
        $destination = $request->input('destination');
        $status = $request->input('status');

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
            });

            $query = $baseQuery
                ->orderBy(DB::raw('DATE(created_at)'), 'DESC')
                ->paginate(12);

            $query->getCollection()->transform(function ($item) {
                $item->encrypted_id = encrypt($item->id);
                return $item;
            });

        return response()->json($query);
    }
}