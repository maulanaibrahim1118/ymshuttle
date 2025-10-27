<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function index()
    {
        $data['title'] = "Location Lists";
        $data['breadcrumbs'] = [
            ['label' => 'Master Data'],
            ['label' => 'Location'],
        ];

        $data['locations'] = DB::table('locations')
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();

        $data['wilayahs'] = DB::table('locations')
            ->select('wilayah')
            ->distinct()
            ->orderBy('wilayah', 'ASC')
            ->get();

        $data['regionals'] = DB::table('locations')
            ->select('regional')
            ->distinct()
            ->orderBy('regional', 'ASC')
            ->get();

        $data['areas'] = DB::table('locations')
            ->select('area')
            ->distinct()
            ->orderBy('area', 'ASC')
            ->get();

        $data['cities'] = DB::table('locations')
            ->select('city')
            ->distinct()
            ->orderBy('city', 'ASC')
            ->get();
        $data['supports'] = [
            ["name" => "d48"],
            ["name" => "d53"],
            ["name" => "dmr"],
            ["name" => "gbg"]
        ];

        return view('contents.master.location.index', $data);
    }

    // Ajax Function
    public function list(Request $request)
    {
        $query = Location::query()->with(['creator', 'updater']);

        if ($request->filled('id')) {
            $query->where('id', decrypt($request->id));
        }

        if ($request->filled('city')) {
            $query->where('city', strtolower($request->city));
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->active);
        }
        
        if ($request->filled('support')) {
            $query->where('dc_support', $request->support);
        }

        return DataTables::of($query)
            ->editColumn('created_by', function ($row) {
                return strtoupper(optional($row->creator)->name ?? $row->created_by);
            })
            ->editColumn('created_at', function ($row) {
                return strtoupper(optional($row->created_at)->format('d-M-Y H:i:s'));
            })
            ->editColumn('updated_by', function ($row) {
                return strtoupper(optional($row->updater)->name ?? $row->updated_by);
            })
            ->editColumn('updated_at', function ($row) {
                return strtoupper(optional($row->updated_at)->format('d-M-Y H:i:s'));
            })
            ->make(true);
    }
}