<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    // Fungsi statis untuk mencatat log aktivitas
    public static function log($action, $description, $error, $username = '')
    {
        DB::table('log_activities')->insert([
            'subject'       => $action,
            'description'   => $description,
            'error'         => $error,
            'url'           => request()->fullUrl() ?? '-',
            'agent'         => request()->header('User-Agent'),
            'ip_address'    => request()->ip(),
            'created_by'    => is_array($username) ? json_encode($username) : $username,
            'updated_by'    => is_array($username) ? json_encode($username) : $username,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}