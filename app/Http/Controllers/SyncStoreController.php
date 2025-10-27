<?php

namespace App\Http\Controllers;

use Exception;
use App\Location;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncStoreController extends Controller
{
    public function sync(Request $request)
    {
        $username = 'system';
        $data = $request->input('store', []);
        if (is_object($data)) {
            $data = (array) $data;
        }
        $action = $request->input('action');

        try {
            DB::beginTransaction();

            if ($action === 'deleted') {
                Location::where('code', $data['code'])->delete();

                LogActivity::log(
                    "sync-location",
                    "Location ({$data['site']} - {$data['name']}) has been deleted!",
                    "Synced from YDC App",
                    $username
                );

                DB::commit();
                return response()->json(['status' => 'deleted']);
            }

            Location::updateOrCreate(
                ['code' => $data['code']],
                [
                    'site'       => $data['site'],
                    'initial'    => $data['initial'],
                    'name'       => $data['name'],
                    'wilayah'    => $data['wilayah'],
                    'regional'   => $data['regional'],
                    'area'       => $data['area'],
                    'address'    => $data['address'],
                    'city'       => $data['city'],
                    'email'      => $data['email'],
                    'dc_support' => $data['dc_support'],
                    'telp'       => $data['telp'],
                    'is_active'  => $data['is_active'],
                    'created_by' => $data['created_by'],
                    'updated_by' => $data['updated_by'],
                    'updated_at' => $data['updated_at'],
                ]
            );

            LogActivity::log(
                'sync-location',
                "Location ({$data['site']} - {$data['name']}) successfully {$action}!",
                "Synced from YDC App",
                $username
            );

            DB::commit();
            return response()->json(['status' => 'synced']);
        } catch (Exception $e) {
            DB::rollBack();

            LogActivity::log(
                'sync-location',
                "Failed to sync location ({$data['site']} - {$data['name']})",
                $e->getMessage(),
                $username
            );

            return response()->json([
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}