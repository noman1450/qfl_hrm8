<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function jsonResponse($status, $message = '', $data = [], $error = '', $error_code = 200)
    {
        $response = [
            'success' => $status,
            'message' => $message,
            'data'    => $data,
            'error'   => $error,
            'error_code' => $error_code,
        ];

      	return response()->json($response, $error_code);
    }


    public function recordActivity($moduleId, $title, $data = null, $masterTableId, $masterTableName)
    {

        return DB::table('users_logs')
            ->insert([
                'config_modules_id' => $moduleId,
                'screen_from' => $title,

                'change_details' => ! is_null($data) ? json_encode($data) : null,

                'master_table_id' => $masterTableId,
                'master_table_name' => $masterTableName,

                'users_id' => auth()->id(),
                'login_from_ip' => request()->ip(),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString()
            ]);
    }


}
