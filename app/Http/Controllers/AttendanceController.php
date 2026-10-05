<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use Response;
use Illuminate\Http\Request;
use App\Models\HrmAttendance;
use App\Models\HrmLocation;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AttendanceDataProcessController;


class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('attendance.attandance_list')
            ->with('default_user_location',  $default_user_location) ;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function refresh_log($id)
    {


        if($id){
            DB::beginTransaction();
            try {

                   DB::SELECT("DELETE FROM tmp_employee_shift WHERE hrm_location_id=$id");

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $message = $e->getMessage();
            } 
        }

        return redirect()->back();
    }




    public function attendancelistdata(Request $request){

        $condition    = "";
        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));

        if ($request->location != 0){
          $condition  = " AND cc.hrm_location_id = ".$request->location;
        }else{
          $condition  = " AND cc.hrm_location_id = 0";
        }

        $data   = DB::select("SELECT
                                aa.hrm_employee_id,
                                concat(hh.employee_name ,' | ',ifnull(cc.employee_code, ' ') ) as employee_name,
                                ee.depertment_name,
                                ff.designation_name,
                                gg.location_name,
                                ii.punche_date,
                                ii.in_time,
                                ii.out_time,
                                xx.attendance_status,
                                dd.shift_name,
                                GROUP_CONCAT(CONCAT(aa.in_datetime) order by aa.in_datetime ASC SEPARATOR '<br>') AS indatetime,
                                GROUP_CONCAT(CONCAT(aa.out_datetime) order by aa.in_datetime ASC SEPARATOR '<br>') AS outdatetime,
                                GROUP_CONCAT(CONCAT(aa.in_datetime) order by aa.in_datetime ASC SEPARATOR '<br>') AS intime,
                                GROUP_CONCAT(CONCAT(aa.out_datetime) order by aa.in_datetime ASC SEPARATOR '<br>') AS outtime,
                                GROUP_CONCAT(CONCAT(aa.working_hour) order by aa.in_datetime ASC SEPARATOR '<br>') AS working_hour
                            FROM

                                log aa
                                     JOIN
                                hrm_employee_job_info cc ON aa.hrm_employee_id=cc.hrm_employee_id AND
                                cc.id in (SELECT max(id) FROM hrm_employee_job_info Group By hrm_employee_id)
                                    JOIN
                                hrm_attendance ii ON aa.hrm_employee_id = ii.hrm_employee_id
                                    AND ii.punche_date = aa.punch_date
                                    AND ii.punche_date = '$date'
                                    $condition
                                    JOIN
                                hrm_attendance_status xx ON ii.attendance_status = xx.id
                                    JOIN
                                hrm_shift dd ON ii.hrm_shift_id = dd.id
                                    JOIN
                                hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                                    JOIN
                                hrm_designation ff ON cc.hrm_designation_id = ff.id
                                    JOIN
                                hrm_location gg ON cc.hrm_location_id = gg.id
                                    JOIN
                                hrm_employee hh ON cc.hrm_employee_id = hh.id

                            GROUP BY aa.hrm_employee_id ,hh.employee_name, ee.depertment_name , ff.designation_name , gg.location_name , dd.shift_name,
                            ii.punche_date , ii.in_time , ii.out_time,cc.employee_code
                            ");
        return json_encode(array('data' => $data));

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    public function machinedataupload()
    {
          return view('attendance.attendance_data_upload');
    }



    public function attendancedataupload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'location_id'               =>'required',
            'hrm_device_information_id' =>'required',
            'import_file'               =>'required',
        ]);

        if( $validator->fails() ) {
            return response()->json(array(
                 'success'   => false,
                 'messages'  => 'Missing Information'
            ));
        }


        $url        = $request->file('import_file')->getRealPath();
        $filedata   = file($url, FILE_IGNORE_NEW_LINES);
        $check_data = explode(' ',  $filedata[0]);
        $arrays     = array();


        DB::beginTransaction();
        try {
            $insert = [];
           
            //START ZK Native software data processing
            // <<=========================================================================================================================
            // if($request->hrm_device_information_id==1){

          
            //     if($check_data[1]=='Department'){

            //         $count_row=count($filedata);
            //         unset($filedata[0]);
            //         unset($filedata[1]);
            //         // dd( $count_row);
                   
            //         if (!empty($count_row)){
            //             foreach ($filedata as $keys ) {
            //                 $trimmed_array=(array_map('trim',array_filter(explode(' ',$keys))));
                           
            //                 $arra = array();
            //                 foreach ($trimmed_array as $i => $value) {
            //                     $arra[] = $value;
            //                 }
                           
            //                 if (!empty($arra)) {
            //                     $ampm = $arra[5];
                               
            //                     if($ampm=='PM') {
            //                        $punchtime = date("H:i:s", strtotime("$arra[4] PM"));
            //                     } else {
            //                        $punchtime = date("H:i:s", strtotime("$arra[4] AM"));
            //                     }

            //                     // dd($punchtime);

            //                     $employee_code      = $arra[2];
            //                     $punch_date         = date('Y-m-d', strtotime(str_replace('/', '-',$arra[3])));
            //                     $punch_time         = $punchtime;
            //                     // $device_no          = $arra[6];
            //                     $device_no          = 1;
            //                     $is_new             = 1;
            //                     $data_from          = 1;
            //                     $hrm_location_id    = $request->location_id;
                               
                               
            //                     $employee_id        = DB::select("SELECT b.hrm_employee_id FROM hrm_employee_card_code a JOIN hrm_employee_job_info b
            //                                                     ON a.hrm_employee_job_info_id = b.id
            //                                                     and a.card_code='$employee_code' and b.hrm_location_id= $hrm_location_id
            //                                                     and b.employee_activity = 1");

            //                     //   $employee_id        = DB::select("SELECT hrm_employee_id FROM hrm_employee_job_info 
            //                     //                                 where employee_code='$employee_code' and hrm_location_id= $hrm_location_id
            //                     //                                 and employee_activity = 1");

            //                         // dd($employee_id);

                                                             
                               
            //                     $isExitDatabase = null;
            //                     if (!empty($employee_id)){
            //                        $hrm_employee_id = $employee_id[0]->hrm_employee_id;

            //                        // $isExitDatabase  = DB::select("SELECT id from hrm_attendance_raw_data  Where punch_date='$punch_date' AND punch_time='$punch_time'  AND hrm_employee_id = $hrm_employee_id AND employee_code = $employee_code and hrm_location_id = $hrm_location_id ");

            //                         $isExitDatabase  = DB::select("SELECT id from hrm_attendance_raw_data  Where punch_date='$punch_date' AND punch_time='$punch_time'  AND hrm_employee_id = $hrm_employee_id AND employee_code = '$employee_code' and hrm_location_id = $hrm_location_id ");
                                   
            //                     }

            //                      // dd($isExitDatabase);

            //                     if(isset($arrays[$employee_code][$punch_date])){
                                  
            //                         if($arrays[$employee_code][$punch_date]['punch_time'] != $punch_time){
            //                             if (!empty($employee_id)) {
                                           
            //                                 if (empty($isExitDatabase)) {
            //                                     $insert[] = [
            //                                         'employee_code'  =>$employee_code,
            //                                         'punch_date'     =>$punch_date,
            //                                         'punch_time'     =>$punch_time,
            //                                         'device_no'      =>$device_no,
            //                                         'is_new'         =>$is_new,
            //                                         'data_from'      =>$data_from,
            //                                         'hrm_employee_id'=>$hrm_employee_id,
            //                                         'hrm_location_id'=>$hrm_location_id,
            //                                     ];
            //                                 }
            //                             }
            //                         }
            //                     } else {
            //                         if (!empty($employee_id)) {

            //                             if (empty($isExitDatabase)) {

            //                                 $insert[] = [
            //                                     'employee_code'  =>$employee_code,
            //                                     'punch_date'     =>$punch_date,
            //                                     'punch_time'     =>$punch_time,
            //                                     'device_no'      =>$device_no,
            //                                     'is_new'         =>$is_new,
            //                                     'data_from'      =>$data_from,
            //                                     'hrm_employee_id'=>$hrm_employee_id,
            //                                     'hrm_location_id'=>$hrm_location_id
            //                                 ];
            //                             }
            //                         }
            //                     }

            //                     $arrays[$employee_code][$punch_date]['punch_time'] = $punch_time;
            //                 }
                           
            //             }
                        
            //         }

            //     }
            // }


            if($request->hrm_device_information_id==1){

          
                if($check_data[1]=='Department'){

                    $count_row=count($filedata);
                    unset($filedata[0]);
                    unset($filedata[1]);
                    // dd( $count_row);
                   
                    if (!empty($count_row)){
                        foreach ($filedata as $index=> $keys ) {
                            $trimmed_array=(array_map('trim',array_filter(explode(' ',$keys))));
                           
                            $arra = array();
                            foreach ($trimmed_array as $i => $value) {
                                $arra[] = $value;
                            }
                           
                           
                            if (!empty($arra)) {
                                $ampm = $arra[5];
                                
                                if($ampm=='PM') {
                                   $punchtime = date("H:i:s", strtotime("$arra[4] PM"));
                                } elseif($ampm=='AM') {
                                   $punchtime = date("H:i:s", strtotime("$arra[4] AM"));
                                }else{
                                    $punchtime = date("H:i:s", strtotime("$arra[4]"));
                                }

                                // dd($punchtime);

                                $employee_code      = $arra[2];
                                $punch_date         = date('Y-m-d', strtotime(str_replace('/', '-',$arra[3])));
                                $punch_time         = $punchtime;
                                // $device_no          = $arra[6];
                                $device_no          = 1;
                                $is_new             = 1;
                                $data_from          = 1;
                                $hrm_location_id    = $request->location_id;
                               
                                $employee_id        = DB::select("SELECT b.hrm_employee_id FROM hrm_employee_card_code a JOIN hrm_employee_job_info b
                                                                ON a.hrm_employee_job_info_id = b.id
                                                                and a.card_code='$employee_code' and b.hrm_location_id= $hrm_location_id
                                                                and b.employee_activity = 1");

                                //   $employee_id        = DB::select("SELECT hrm_employee_id FROM hrm_employee_job_info 
                                //                                 where employee_code='$employee_code' and hrm_location_id= $hrm_location_id
                                //                                 and employee_activity = 1");

                                    // dd($employee_id);

                                                             
                               
                                $isExitDatabase = null;
                                if (!empty($employee_id)){
                                   $hrm_employee_id = $employee_id[0]->hrm_employee_id;

                                   // $isExitDatabase  = DB::select("SELECT id from hrm_attendance_raw_data  Where punch_date='$punch_date' AND punch_time='$punch_time'  AND hrm_employee_id = $hrm_employee_id AND employee_code = $employee_code and hrm_location_id = $hrm_location_id ");

                                    $isExitDatabase  = DB::select("SELECT id from hrm_attendance_raw_data  Where punch_date='$punch_date' AND punch_time='$punch_time'  AND hrm_employee_id = $hrm_employee_id AND employee_code = '$employee_code' and hrm_location_id = $hrm_location_id ");
                                   
                                }

                                 // dd($isExitDatabase);

                                if(isset($arrays[$employee_code][$punch_date])){
                                  
                                    if($arrays[$employee_code][$punch_date]['punch_time'] != $punch_time){
                                        if (!empty($employee_id)) {
                                           
                                            if (empty($isExitDatabase)) {
                                                $insert[] = [
                                                    'employee_code'  =>$employee_code,
                                                    'punch_date'     =>$punch_date,
                                                    'punch_time'     =>$punch_time,
                                                    'device_no'      =>$device_no,
                                                    'is_new'         =>$is_new,
                                                    'data_from'      =>$data_from,
                                                    'hrm_employee_id'=>$hrm_employee_id,
                                                    'hrm_location_id'=>$hrm_location_id,
                                                ];
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($employee_id)) {

                                        if (empty($isExitDatabase)) {

                                            $insert[] = [
                                                'employee_code'  =>$employee_code,
                                                'punch_date'     =>$punch_date,
                                                'punch_time'     =>$punch_time,
                                                'device_no'      =>$device_no,
                                                'is_new'         =>$is_new,
                                                'data_from'      =>$data_from,
                                                'hrm_employee_id'=>$hrm_employee_id,
                                                'hrm_location_id'=>$hrm_location_id
                                            ];
                                        }
                                    }
                                }

                                $arrays[$employee_code][$punch_date]['punch_time'] = $punch_time;
                            }
                           
                        }
                        
                    }

                }
            }

            
            // dd($insert);
           
            //END ZK Native software data processing
            // ===============================================>>
          


            // <<===============================================
            //START ANVIZ SKD data processing
            if($request->hrm_device_information_id==2){

                    $count_row  = count($filedata);

                    if (!empty($count_row)){
                            foreach ($filedata as $keys ) {
                                $array =  explode(' ', $keys);
                                $employee_code  = $array[0];
                                $punchdate      = $array[1];
                                $punch_date     = date('Y-m-d', strtotime(str_replace('/', '-',$punchdate)));
                                $punch_time     = $array[2];
                                $device_no      = $array[3];
                                $serial_id      = $array[5];
                                $is_new         = 1;
                                $data_from      = 1;
                                $hrm_location_id= $request->location_id;
                                $employee_id    = DB::select("SELECT b.hrm_employee_id FROM `hrm_employee_card_code` a JOIN hrm_employee_job_info b
                                                                ON a.hrm_employee_job_info_id=b.id
                                                                WHERE a.card_code='$employee_code' and b.hrm_location_id= $hrm_location_id AND b.employee_activity = 1 ");

                                if (!empty($employee_id)){
                                   $hrm_employee_id=$employee_id[0]->hrm_employee_id;
                                   $isExitDatabase =DB::select("SELECT id from hrm_attendance_raw_data  Where punch_date='$punch_date' AND punch_time='$punch_time'  AND hrm_employee_id=$hrm_employee_id AND employee_code='$employee_code' and hrm_location_id=$hrm_location_id ");
                                }

                                if(isset($arrays[$employee_code][$punch_date])){
                                    if($arrays[$employee_code][$punch_date]['punch_time'] != $punch_time){
                                        if (!empty($employee_id)){

                                            if (empty($isExitDatabase)){

                                                $hrm_employee_id=$employee_id[0]->hrm_employee_id;

                                                $insert[] = ['employee_code'  =>$employee_code,
                                                             'punch_date'     =>$punch_date,
                                                             'punch_time'     =>$punch_time,
                                                             'device_no'      =>$device_no,
                                                             'serial_id'      =>$serial_id,
                                                             'is_new'         =>$is_new,
                                                             'data_from'      =>$data_from,
                                                             'hrm_employee_id'=>$hrm_employee_id,
                                                             'hrm_location_id'=>$hrm_location_id
                                                            ];
                                            }
                                        }
                                    }
                                }else{
                                    if (!empty($employee_id)){

                                        if (empty($isExitDatabase)){

                                            $hrm_employee_id=$employee_id[0]->hrm_employee_id;

                                            $insert[] = ['employee_code'  =>$employee_code,
                                                         'punch_date'     =>$punch_date,
                                                         'punch_time'     =>$punch_time,
                                                         'device_no'      =>$device_no,
                                                         'serial_id'      =>$serial_id,
                                                         'is_new'         =>$is_new,
                                                         'data_from'      =>$data_from,
                                                         'hrm_employee_id'=>$hrm_employee_id,
                                                         'hrm_location_id'=>$hrm_location_id
                                                        ];
                                        }
                                    }
                                }
                                $arrays[$employee_code][$punch_date]['punch_time'] = $punch_time;

                            }
                    }
            }
            //END ANVIZ SKD data processing
            // ===============================================>>


           // <<===============================================
            //START HUNDURE data processing
            if($request->hrm_device_information_id==3){

                    unset($filedata[0]);

                    $count_row  = count($filedata);


                    if (!empty($count_row)){
                            foreach ($filedata as $keys ) {



                                $array =  explode(',', $keys);
                                $employee_code  = $array[0];
                                $punchdate      = $array[1];
                                $punch_date     = date('Y-m-d', strtotime(str_replace('/', '-',$punchdate)));
                                $punch_time     = $array[2];
                                // $device_no      = $array[3];
                                $device_no      = 1;
                                // $serial_id      = $array[5];
                                $serial_id      = 1;
                                $is_new         = 1;
                                $data_from      = 1;
                                $hrm_location_id= $request->location_id;
                                $employee_id    = DB::select("SELECT b.hrm_employee_id FROM `hrm_employee_card_code` a JOIN hrm_employee_job_info b
                                                                ON a.hrm_employee_job_info_id=b.id
                                                                WHERE a.card_code='$employee_code' and b.hrm_location_id= $hrm_location_id  AND b.employee_activity = 1 ");
                                if (!empty($employee_id)){
                                   $hrm_employee_id=$employee_id[0]->hrm_employee_id;
                                   $isExitDatabase =DB::select("SELECT id from hrm_attendance_raw_data  Where punch_date='$punch_date' AND punch_time='$punch_time'  AND hrm_employee_id=$hrm_employee_id AND employee_code='$employee_code' and hrm_location_id=$hrm_location_id ");
                                }else{
                                     continue;
                                }


                                if(isset($arrays[$employee_code][$punch_date])){
                                    if($arrays[$employee_code][$punch_date]['punch_time'] != $punch_time){
                                        if (!empty($employee_id)){

                                            if (empty($isExitDatabase)){

                                                $hrm_employee_id=$employee_id[0]->hrm_employee_id;

                                                $insert[] = ['employee_code'  =>$employee_code,
                                                             'punch_date'     =>$punch_date,
                                                             'punch_time'     =>$punch_time,
                                                             'device_no'      =>$device_no,
                                                             'serial_id'      =>$serial_id,
                                                             'is_new'         =>$is_new,
                                                             'data_from'      =>$data_from,
                                                             'hrm_employee_id'=>$hrm_employee_id,
                                                             'hrm_location_id'=>$hrm_location_id
                                                            ];
                                            }
                                        }
                                    }
                                }else{
                                    if (!empty($employee_id)){

                                        if (empty($isExitDatabase)){

                                            $hrm_employee_id=$employee_id[0]->hrm_employee_id;

                                            $insert[] = ['employee_code'  =>$employee_code,
                                                         'punch_date'     =>$punch_date,
                                                         'punch_time'     =>$punch_time,
                                                         'device_no'      =>$device_no,
                                                         'serial_id'      =>$serial_id,
                                                         'is_new'         =>$is_new,
                                                         'data_from'      =>$data_from,
                                                         'hrm_employee_id'=>$hrm_employee_id,
                                                         'hrm_location_id'=>$hrm_location_id
                                                        ];
                                        }
                                    }
                                }
                                $arrays[$employee_code][$punch_date]['punch_time'] = $punch_time;

                            }
                    }
            }
            //END HUNDURE Native data processing
            // ===============================================>>


        
            if(!empty($insert)) {
               
                HrmAttendance::insert($insert);
                $this->DeleteData($hrm_location_id);
                // return back()->with('success','Insert Record successfully.');
            }
            else {
                return response()->json(array(
                    'success'   => false,
                    'messages'  => 'No new data for insert'
                ));
            }

            DB::commit();

            // $this->recordActivity(
            //      1,
            //      'New Attendance Data Upload',
            //      null,
            //      $hrm_location_id,
            //      'hrm_attendance_raw_data'
            // );

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => "insert problem !! " . $e->getMessage()
            ));
        }

        return response::json(array(
            'success'   => true,
            'messages'  => 'Insert Record successfully.'
        ));


    }


    public function DeleteData($location){
         // dd($location);

        $employee = DB::SELECT("SELECT
                                    hrm_employee_id
                                FROM
                                    hrm_attendance_raw_data
                                WHERE
                                    hrm_location_id = $location
                                    AND is_new = 1  AND valid=1
                                GROUP BY hrm_employee_id
                                ORDER BY hrm_employee_id");

        foreach ($employee as $emp ){
            $log_insert = array();
            $start_time = '';

            $log = DB::SELECT("SELECT *,(CONCAT(punch_date, ' ', punch_time)) AS punch_date_time
                               FROM hrm_attendance_raw_data
                               WHERE is_new = 1 AND valid=1 AND hrm_employee_id = $emp->hrm_employee_id
                               ORDER BY (CONCAT(punch_date, ' ', punch_time))");
            // dd($log);

            foreach ($log as $logs ){
                if($start_time != ''){
                    $dteStart = new DateTime($logs->punch_date_time);
                    $diff     = $start_time->diff($dteStart);
                    $diff_sec = $diff->format('%r').( // prepend the sign - if negative, change it to R if you want the +, too
                                ($diff->s)+ // seconds (no errors)
                                (60*($diff->i))+ // minutes (no errors)
                                (60*60*($diff->h))+ // hours (no errors)
                                (24*60*60*($diff->d))+ // days (no errors)
                                (30*24*60*60*($diff->m))+ // months (???)
                                (365*24*60*60*($diff->y)) // years (???)
                                );

                    if(180 >= $diff_sec){
                        DB::update("UPDATE hrm_attendance_raw_data SET valid=0 WHERE id = $logs->id");
                    }

                    $start_time = new DateTime($logs->punch_date_time);
                }else{
                    $start_time = new DateTime($logs->punch_date_time);
                }
            }
        }

    }



    public function rawdatacheck()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id ");

        return view('attendance.rawdatacheck')
           -> with('default_user_location',  $default_user_location) ;
    }


    public function pull_attendance()
    {
          return view('attendance.attendance_data_pull');
    } 


    public function data_sync(Request $request)
    {

         

            $process_date         = date('Y-m-d', strtotime(str_replace('/', '-',$request->process_date)));

            // $location  = HrmLocation::where('valid',1)->pluck('id')->toArray();
            //     foreach($location as $keys){
            //     $hrm_employee_id  =  HrmEmployeeJobInfo::where('hrm_location_id', $keys)->where('employee_activity','=',1)->pluck('hrm_employee_id')->toArray();
            //          (new AttendanceDataProcessController)
            //             ->attandanceProcess(
            //                 $keys,
            //                 $process_date,
            //                 implode(',', $hrm_employee_id) 
            //             );
            // }
            // // dd("Precessed");


            // return response::json(array(
            //     'success'   => true,
            //     'messages'  => 'successfully attendance data sync!'
            // ));


            // Data Receive From ZK Master Software ===================  19-07-2023
            $data= Http::withHeaders([
                'Authorization' => 'token a7308629767b3097159702c8aa9291e2d0b47a9c',
                'Content-Type' => 'application/json'
            ])
            ->get("http://45.112.74.163:8088/iclock/api/transactions/?emp_code=&start_time=$process_date 00:00:00&end_time=$process_date 23:59:59&page_size=12000")['data'];

            $count_row  =  count($data); 
            if (!empty($count_row)){

                foreach($data as $keys ) {

                           $arra = array();
                           $employee_code      = $keys['emp_code'];
                           $getInfo = DB::select("SELECT b.hrm_employee_id,b.hrm_location_id FROM hrm_employee_card_code a 
                                    JOIN hrm_employee_job_info b
                                    ON a.hrm_employee_job_info_id = b.id
                                    and a.card_code='$employee_code'
                                    and b.employee_activity = 1 LIMIT 1");


                           if(!empty($getInfo)){

                            $punch_date         = date('Y-m-d', strtotime(str_replace('/', '-',$keys['punch_time'])));
                            $punch_time         = date('H:i:s', strtotime(str_replace('/', '-',$keys['punch_time'])));
                            $device_no          = 1;
                            $is_new             = 1;
                            $data_from          = 1;
                            $hrm_location_id    = $getInfo[0]->hrm_location_id;
                            $hrm_employee_id    = $getInfo[0]->hrm_employee_id;


                            if (!empty($hrm_employee_id)){
                                $isExitDatabase  = DB::select("SELECT id from hrm_attendance_raw_data  Where punch_date='$punch_date' AND punch_time='$punch_time'  AND hrm_employee_id = $hrm_employee_id AND employee_code = '$employee_code' and hrm_location_id = $hrm_location_id ");

                            }

                            if(isset($arrays[$employee_code][$punch_date])){
                                if($arrays[$employee_code][$punch_date]['punch_time'] != $punch_time){
                                    if (!empty($hrm_employee_id)) {

                                        if (empty($isExitDatabase)) {
                                            $insert[] = [
                                                'employee_code'  =>$employee_code,
                                                'punch_date'     =>$punch_date,
                                                'punch_time'     =>$punch_time,
                                                'device_no'      =>$device_no,
                                                'is_new'         =>$is_new,
                                                'data_from'      =>$data_from,
                                                'hrm_employee_id'=>$hrm_employee_id,
                                                'hrm_location_id'=>$hrm_location_id,
                                            ];
                                        }
                                    }
                                }
                            } else {
                                if (!empty($hrm_employee_id)) {

                                    if (empty($isExitDatabase)) {

                                        $insert[] = [
                                            'employee_code'  =>$employee_code,
                                            'punch_date'     =>$punch_date,
                                            'punch_time'     =>$punch_time,
                                            'device_no'      =>$device_no,
                                            'is_new'         =>$is_new,
                                            'data_from'      =>$data_from,
                                            'hrm_employee_id'=>$hrm_employee_id,
                                            'hrm_location_id'=>$hrm_location_id
                                        ];
                                    }
                                }
                            }

                            $arrays[$employee_code][$punch_date]['punch_time'] = $punch_time;


                       }else{
                        continue ;
                       }

                    
                }
            }


            if(!empty($insert)) {
                HrmAttendance::insert($insert);
                $this->DeleteData($hrm_location_id);
            }
            // else {
            //     return response()->json(array(
            //         'success'   => false,
            //         'messages'  => 'No new data for insert'
            //     ));
            // }

            // DB::commit();



            // Data Receive From PinTime  ===================  19-07-2023

                $user = Http::withHeaders([
                    "Accept" => "application/json"
                ])->post("http://157.245.108.27/ibs_hrm/api/login", [
                    'email' => 'nomandiu1450@gmail.com',
                    'password' => '12345678'
                ])->object();

                $response = Http::withHeaders([
                    "Authorization" => $user->accessToken,
                    "Accept" => "application/json"
                ])->get("http://157.245.108.27/ibs_hrm/api/attendancerawdata?date_from=$process_date 00:00:00&date_to=$process_date 23:59:59&hrm_location_id=269")['data'];


            $count_row1  =  count($response); 
            if (!empty($count_row1)){

                foreach($response as $keys ) {

                           $arra = array();
                           $employee_code      = $keys['employee_code'];
                           $getInfo = DB::select("SELECT b.hrm_employee_id,b.hrm_location_id FROM hrm_employee_card_code a 
                                    JOIN hrm_employee_job_info b
                                    ON a.hrm_employee_job_info_id = b.id
                                    and a.card_code='$employee_code'
                                    and b.employee_activity = 1 LIMIT 1");


                           if(!empty($getInfo)){

                            $punch_date         = date('Y-m-d', strtotime(str_replace('/', '-',$keys['attendace_date'])));
                            $punch_time         = date('H:i:s', strtotime(str_replace('/', '-',$keys['attendace_date'])));
                            $device_no          = 1;
                            $is_new             = 1;
                            $data_from          = 1;
                            $hrm_location_id    = $getInfo[0]->hrm_location_id;
                            $hrm_employee_id    = $getInfo[0]->hrm_employee_id;


                            if (!empty($hrm_employee_id)){
                                $isExitDatabase  = DB::select("SELECT id from hrm_attendance_raw_data  Where punch_date='$punch_date' AND punch_time='$punch_time'  AND hrm_employee_id = $hrm_employee_id AND employee_code = '$employee_code' and hrm_location_id = $hrm_location_id ");

                            }

                            if(isset($arrays[$employee_code][$punch_date])){
                                if($arrays[$employee_code][$punch_date]['punch_time'] != $punch_time){
                                    if (!empty($hrm_employee_id)) {

                                        if (empty($isExitDatabase)) {
                                            $insert[] = [
                                                'employee_code'  =>$employee_code,
                                                'punch_date'     =>$punch_date,
                                                'punch_time'     =>$punch_time,
                                                'device_no'      =>$device_no,
                                                'is_new'         =>$is_new,
                                                'data_from'      =>$data_from,
                                                'hrm_employee_id'=>$hrm_employee_id,
                                                'hrm_location_id'=>$hrm_location_id,
                                            ];
                                        }
                                    }
                                }
                            } else {
                                if (!empty($hrm_employee_id)) {

                                    if (empty($isExitDatabase)) {

                                        $insert[] = [
                                            'employee_code'  =>$employee_code,
                                            'punch_date'     =>$punch_date,
                                            'punch_time'     =>$punch_time,
                                            'device_no'      =>$device_no,
                                            'is_new'         =>$is_new,
                                            'data_from'      =>$data_from,
                                            'hrm_employee_id'=>$hrm_employee_id,
                                            'hrm_location_id'=>$hrm_location_id
                                        ];
                                    }
                                }
                            }

                            $arrays[$employee_code][$punch_date]['punch_time'] = $punch_time;


                       }else{
                        continue ;
                       }

                    
                }
            }


            if(!empty($insert)) {
                HrmAttendance::insert($insert);
                $this->DeleteData($hrm_location_id);
            }
            // else {
            //     return response()->json(array(
            //         'success'   => false,
            //         'messages'  => 'No new data for insert'
            //     ));
            // }

            // DB::commit();

            
            if (($count_row+$count_row1)>0){
                  
                $location  = HrmLocation::where('valid',1)->pluck('id')->toArray();
                foreach($location as $keys){
                    $hrm_employee_id  =  HrmEmployeeJobInfo::where('hrm_location_id', $keys)->where('employee_activity','=',1)->pluck('hrm_employee_id')->toArray();

                    if(!empty($hrm_employee_id)){

                        (new AttendanceDataProcessController)
                        ->attandanceProcess(
                            $keys,
                            $process_date,
                            implode(',', $hrm_employee_id) 
                        );
                    }                    
                }
            }


        return response::json(array(
            'success'   => true,
            'messages'  => 'successfully attendance data sync!'
        ));


    }





    public function rawdatalist(Request $request){

        $ldate   = date('Y-m-d H:i:s');
        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));
        $condition="";
        if ($request->location != 0){
          $condition  = " AND b.hrm_location_id = ".$request->location  ;
          $condition  =  $condition . " AND b.hrm_employee_id = ".$request->employee_id ;
        }




        $data   = DB::select("SELECT
                                b.id,
                                concat(a.employee_name,' | ',ifnull(b.employee_code, ' ')) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                '' as shift_name,
                                i.punch_date,
                                i.punch_time,
                                h.card_code,
                                i.is_new,
                                if(i.valid=1,'Accept','-') as valid,
                                if(i.data_from=1,'From Device','Manual') as data_from
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id
                                AND b.id IN (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  '$date'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                 $condition
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employee_card_code h ON h.hrm_employee_job_info_id = b.id
                                    JOIN
                                hrm_attendance_raw_data i ON a.id=i.hrm_employee_id AND i.punch_date='$date'
                                UNION
                                SELECT
                                b.id,
                                concat(a.employee_name,' | ', ifnull(b.employee_code, ' ')) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                '' as shift_name,
                                i.punch_date,
                                i.punch_time,
                                h.card_code,
                                i.is_new,
                                if(i.valid=1,'Accept','-') as valid,
                                if(i.data_from=1,'From Device','Manual') as data_from
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id
                                AND b.id IN (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  '$date'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                                 $condition
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employee_card_code h ON h.hrm_employee_job_info_id = b.id
                                    JOIN
                                hrm_attendance_raw_data i ON a.id=i.hrm_employee_id AND i.punch_date='$date'");

        return json_encode(array('data' => $data));

    }



    public function delete_duplicate_data($location){
         // dd($location);

        $employee = DB::SELECT("SELECT
                                    hrm_employee_id
                                FROM
                                    hrm_attendance_raw_data
                                WHERE
                                    hrm_location_id = $location
                                    AND is_new = 0  AND valid=1 AND punch_date='2020-12-13'
                                GROUP BY hrm_employee_id
                                ORDER BY hrm_employee_id");

        foreach ($employee as $emp ){
            $log_insert = array();
            $start_time = '';

            $log = DB::SELECT("SELECT *,(CONCAT(punch_date, ' ', punch_time)) AS punch_date_time
                               FROM hrm_attendance_raw_data
                               WHERE is_new = 0 AND valid=1 AND punch_date='2020-12-13' AND hrm_employee_id = $emp->hrm_employee_id
                               ORDER BY (CONCAT(punch_date, ' ', punch_time))");
            // dd($log);

            foreach ($log as $logs ){
                if($start_time != ''){
                    $dteStart = new DateTime($logs->punch_date_time);
                    $diff     = $start_time->diff($dteStart);
                    $diff_sec = $diff->format('%r').( // prepend the sign - if negative, change it to R if you want the +, too
                                ($diff->s)+ // seconds (no errors)
                                (60*($diff->i))+ // minutes (no errors)
                                (60*60*($diff->h))+ // hours (no errors)
                                (24*60*60*($diff->d))+ // days (no errors)
                                (30*24*60*60*($diff->m))+ // months (???)
                                (365*24*60*60*($diff->y)) // years (???)
                                );

                    if(180 >= $diff_sec){
                        DB::update("UPDATE hrm_attendance_raw_data SET valid=0 WHERE id = $logs->id");
                    }

                    $start_time = new DateTime($logs->punch_date_time);
                }else{
                    $start_time = new DateTime($logs->punch_date_time);
                }
            }
        }

        var_dump("done");
    }








    }
