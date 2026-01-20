<?php

namespace App\Http\Controllers;

use App\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index()
    {
        $data['title'] = "Delivery Lists";
        $data['breadcrumbs'] = [
            ['label' => 'Delivery'],
        ];

        $data['categories'] = DB::table('categories')->select('id', 'name')->orderBy('name', 'ASC')->get();
        $data['locations'] = DB::table('locations')
            ->select('code', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        return view('contents.delivery.index', $data);
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $search = strtolower($request->input('search'));
        $categoryId = $request->input('category');
        $sender = $request->input('sender');
        $destination = $request->input('destination');
        $date = $request->date ? Carbon::createFromFormat('d/m/Y', $request->date)->toDateString() : null;

        $baseQuery = Shipment::with([
                'category',
                'sender_location',
                'receiver_location',
                'creator'
            ])

            // WAJIB: shipment sudah delivery / received
            ->whereIn('status', ['3', '4', '5'])

            // WAJIB: ledger TERAKHIR status SEND (2) oleh user login
            ->whereHas('shipment_ledger', function ($q) use ($user, $date) {
                $q->where('status', '2') // SEND
                ->where('created_by', $user->username)

                ->whereRaw(
                    "
                    shipment_ledgers.created_at = (
                        SELECT MAX(sl.created_at)
                        FROM shipment_ledgers sl
                        WHERE sl.no_shipment = shipment_ledgers.no_shipment
                        AND sl.status = '2'
                        AND sl.created_by = ?
                        " . ($date ? "AND DATE(sl.created_at) = ?" : "") . "
                    )
                    ",
                    $date
                        ? [$user->username, $date]
                        : [$user->username]
                );
            })


            // SEARCH
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('no_shipment', 'like', '%' . $search . '%')
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', '%' . $search . '%');
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

            // ambil created_at SEND terakhir (untuk ditampilkan)
            ->addSelect([
                'ledger_created_at' => function ($q) use ($user, $date) {
                    $q->from('shipment_ledgers as sl')
                    ->select('sl.created_at')
                    ->whereColumn('sl.no_shipment', 'shipments.no_shipment')
                    ->where('sl.status', '2')
                    ->where('sl.created_by', $user->username)
                    ->when($date, function ($q) use ($date) {
                        $q->whereDate('sl.created_at', $date);
                    })
                    ->orderByDesc('sl.created_at')
                    ->limit(1);
                }
            ]);

        // ORDER BY — SUBQUERY (bukan alias)
        $query = $baseQuery
            ->orderByDesc(DB::raw("(
                SELECT sl.created_at
                FROM shipment_ledgers sl
                WHERE sl.no_shipment = shipments.no_shipment
                AND sl.status = '2'
                AND sl.created_by = '{$user->username}'
                " . ($date ? "AND DATE(sl.created_at) = '{$date}'" : "") . "
                ORDER BY sl.created_at DESC
                LIMIT 1
            )"))
            ->paginate(12);

        $query->getCollection()->transform(function ($item) {
            $item->encrypted_id = encrypt($item->id);
            return $item;
        });

        return response()->json($query);
    }
}