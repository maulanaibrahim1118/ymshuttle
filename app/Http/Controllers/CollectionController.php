<?php

namespace App\Http\Controllers;

use App\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    public function index()
    {
        $data['title'] = "Collection Lists";
        $data['breadcrumbs'] = [
            ['label' => 'Collection'],
        ];

        $data['categories'] = DB::table('categories')->select('id', 'name')->orderBy('name', 'ASC')->get();
        $data['locations'] = DB::table('locations')
            ->select('code', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        return view('contents.collection.index', $data);
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
            // WAJIB: status shipment on delivery
            ->whereIn('status', ['2','3'])

            // WAJIB: ledger terakhir status 3 oleh user login
            ->whereHas('shipment_ledger', function ($q) use ($user, $date) {
                $q->where('status', '3')
                ->where('created_by', $user->username)

                // FILTER DATE (jika ada)
                ->when($date, function ($qq) use ($date) {
                    $qq->whereDate('created_at', $date);
                })

                ->whereRaw("
                    shipment_ledgers.created_at = (
                        SELECT MAX(sl.created_at)
                        FROM shipment_ledgers sl
                        WHERE sl.no_shipment = shipment_ledgers.no_shipment
                    )
                ");
            })

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

            // ambil created_at ledger terakhir (untuk ditampilkan)
            ->addSelect([
                'ledger_created_at' => function ($q) use ($user, $date) {
                    $q->from('shipment_ledgers as sl')
                    ->select('sl.created_at')
                    ->whereColumn('sl.no_shipment', 'shipments.no_shipment')
                    ->where('sl.status', '3')
                    ->where('sl.created_by', $user->username)
                    ->when($date, function ($qq) use ($date) {
                        $qq->whereDate('sl.created_at', $date);
                    })
                    ->orderByDesc('sl.created_at')
                    ->limit(1);
                }
            ]);

        // ORDER BY HARUS PAKAI SUBQUERY (BUKAN ALIAS)
        $query = $baseQuery
            ->orderByDesc(DB::raw("(
                SELECT sl.created_at
                FROM shipment_ledgers sl
                WHERE sl.no_shipment = shipments.no_shipment
                AND sl.status = '3'
                AND sl.created_by = '" . $user->username . "'
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