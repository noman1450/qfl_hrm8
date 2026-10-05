<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\MobileLog;
use Illuminate\Support\Facades\DB;
use App\Helpers\Functions;
use App\Http\Controllers\AttendanceDataProcessController;

class Attendance extends Controller
{
    public $successStatus = 200;

    public function attendance_status(Request $request)
    {
        $request = $request->all();

        if($request){
            $employees_id = $request['employees_id'];

            $AttendanceInfo = DB::SELECT("SELECT *
                                        FROM mobile_log
                                        WHERE hrm_employee_id = $employees_id AND date(created_at) = date(now())
                                        ORDER BY id DESC, created_at DESC
                                        LIMIT 1");

            if(!empty($AttendanceInfo)){
                $AttendanceInfo = $AttendanceInfo[0];
                $data = ['attendance_status' => $AttendanceInfo->status == 1 ? 2 : 1];

            }else{
                $data = ['attendance_status' => 1];
            }


            $status = true;
            $message = 'Success';
            $error = '';
            $error_code = 200;
        } else {
            $status = false;
            $message = 'Failed';
            $data = '';
            $error = 'Employee ID not found';
            $error_code = 404;
        }
        //-----------
        $response = [
            'success' => $status,
            'message' => $message,
            'data'    => $data,
            'error'   => $error,
            'error_code' => $error_code,
        ];
        return response()->json($response);
    }


    public function store(Request $request)
    {
        $return = static::in_out($request);

        if($return['status']){
            $response = [
                'success' => $return['status'],
                'message' => $return['message'],
                'data'    => $return['data'],
                'error'   => $return['error'],
                'error_code' => 200,
            ];
        }else{
            $response = [
                'success' => $return['status'],
                'message' => $return['message'],
                'data'    => $return['data'],
                'error'   => $return['error'],
               'error_code' => 404,
            ];
        }
        return response()->json($response);
    }

    static function in_out($request)
    {


        $status = false;
        $validator = Validator::make($request->all(), [
            // 'employees_id' => ['required', 'integer', 'exists:hrm_employee,id'],
            'employees_id'    => ['required', 'integer'],
            'attendance_date' => ['required', 'date'],
            'lat' => ['required', 'string'],
            'lon' => ['required', 'string'],
            'status' => ['required', 'integer'],
            'attachment' => ['nullable']
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = "Form Validation Failed..!";
        }


        $hrm_employee_id = $request->employees_id;
        $oldDate         = date('Y-m-d', strtotime("-1 months", strtotime($request->attendance_date)));
        $findOldData     = DB::SELECT("SELECT GROUP_CONCAT(id) id,GROUP_CONCAT(attachment) attachment  FROM mobile_log WHERE hrm_employee_id=$hrm_employee_id AND date(created_at)='$oldDate' AND attachment<>'' ");

        // dd($findOldData);

        if(!empty($findOldData[0]->attachment)){

            $files = explode(",",$findOldData[0]->attachment);

            if (!empty($files)) {
                array_map('unlink', $files);
                DB::UPDATE("UPDATE mobile_log SET attachment='' Where date(created_at)='$oldDate' and hrm_employee_id=$hrm_employee_id ");
            };

        };


        // dist/img/default_profile_picture.png


        $employees_id  = $request->employees_id;
        $AttendanceInfo = DB::SELECT("SELECT status
                                    FROM mobile_log
                                    WHERE hrm_employee_id = $employees_id AND date(created_at) = date(now())
                                    ORDER BY id DESC, created_at DESC
                                    LIMIT 1");


        if(!empty($AttendanceInfo[0]->status)) {

            if($AttendanceInfo[0]->status==$request->status){

                $status = false;
                $message = "Sorry Already Added";

                return [
                    "status" => $status,
                    "message" => $message ?? '',
                    "data" => '',
                    "error" => ''
                ];
            }
        }


        $fileUrl = null;
        $fileUnlink = 'Y';

        if($request['attachment']){
            $image_parts = explode(";base64,", $request['attachment']);

            if (count($image_parts) > 1) {
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
            } else {
                $image_base64 = base64_decode($image_parts[0]);
                $image_type = 'png';
            }

            $fileName = 'attendance-' . uniqid() . '.'.$image_type;
            $file = public_path().'/uploads/'.$fileName;
            file_put_contents($file, $image_base64);
            $fileUrl = 'uploads/'.$fileName;
        }


        DB::beginTransaction();
        try{
            $insert = new MobileLog;
            $insert->attachment      = $fileUrl;
            $insert->date            = date('Y-m-d H:i:s');
            $insert->hrm_employee_id = $request->employees_id;
            $insert->lat             = $request->lat;
            $insert->lon             = $request->lon;
            $insert->status          = $request->status;
            $insert->valid           = 1;
            $insert->user_id         = auth()->user()->id;
            $insert->save();

            $status = true;
            $message = "Your Attendance Information Added Successfully";
            
            DB::commit();
            $attendance = new AttendanceDataProcessController();
            $x =  $attendance->attandanceProcess(8,date('Y-m-d'),$request->employees_id);
            //dd($x);
        } catch (\Exception $e) {
            @unlink(public_path().'/'.$fileUrl);
            $message = "Your Attendance Information Added Faield";
        }

        return [
            "status" => $status,
            "message" => $message ?? '',
            "data" => $data ?? '',
            "error" => $error ?? ''
        ];
    }
    
    public function attendance_details(Request $request)
    {
        $status     = false;
        $message    = "";
        $error      = "";
        $data       = "";
        //--
        $employee_id    = $request->employee_id;
        $month_id       = $request->month_id;
        $year_id        = $request->year_id;
        
        try {
            
            $data = DB::SELECT("SELECT 
                                        a.punche_date,
                                        a.in_time,
                                        a.out_time,
                                        a.early_out_time,
                                        a.late_time,
                                        a.overtime_time,
                                        b.alies attendance_status
                                    FROM
                                        hrm_attendance a
                                            JOIN
                                        hrm_attendance_status b ON b.id = a.attendance_status
                                    WHERE
                                        a.hrm_employee_id = $employee_id
                                            AND MONTH(a.punche_date) = '$month_id'
                                            AND YEAR(a.punche_date) = '$year_id'");

            return Functions::response(true, 'success', $error, $data);
        } catch (Exception $ex) {
            return Functions::response($status, 'failed', $error, $data);
        }
        
    }
}
