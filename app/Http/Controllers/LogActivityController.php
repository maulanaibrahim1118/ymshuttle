<?php

namespace App\Http\Controllers;

use App\Log_activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LogActivityController extends Controller
{
    public function index()
    {
        $data['title'] = "Log Activity Lists";
        $data['breadcrumbs'] = [
            ['label' => 'Log Activity'],
        ];

        $data['subjects'] = DB::table('log_activities')
            ->select('subject as name')
            ->distinct()
            ->orderBy('subject', 'ASC')
            ->get();

        $data['users'] = DB::table('users')->select('username', 'name')->orderBy('name', 'ASC')->get();
        
        return view('contents.logActivity', $data);
    }

    public function list(Request $request)
    {
        $query = Log_activity::with(['creator']);

        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }

        if ($request->filled('username')) {
            $query->where('created_by', $request->username);
        }

        return DataTables::of($query)
            ->editColumn('created_at', function ($row) {
                return strtoupper(optional($row->created_at)->format('d-M-Y H:i:s'));
            })
            ->editColumn('created_by', function ($row) {
                return strtoupper(optional($row->creator)->name ?? $row->created_by);
            })
            ->editColumn('subject', function ($row) {
                return strtoupper(str_replace('-', ' ', $row->subject));
            })
            ->editColumn('error', function ($row) {
                if (empty($row->error)) {
                    return '';
                }

                $escapedError = $row->error ?? '-';
                return '
                    <a href="#" class="view-error" data-error="' . nl2br($escapedError) . '" data-bs-toggle="modal" data-bs-target="#errorModal">
                        <i class="fas fa-comment-dots me-1"></i>View Details
                    </a>
                ';
            })
            ->rawColumns(['error'])
            ->make(true);
    }
}