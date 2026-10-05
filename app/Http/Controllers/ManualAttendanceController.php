<?php

namespace App\Http\Controllers;

use Auth;
use Crypt;
use Config;
use Session;
use App\User;
use DateTime;
use Redirect;
use DataTables;
use App\Models\HrmShift;
use Illuminate\Http\Request;
use App\Models\HrmAttendance;
use App\Models\HrmAttendanceData;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmAttendanceComment;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\AttendanceDataProcessController;
use App\Models\HrmManualAttendance;
use App\Jobs\ManualAttendanceProcessJob;

class ManualAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
    }


    public function manual_attendance()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

      return view('manual_attendance.manual_attandance_list')
       -> with('default_user_location',  $default_user_location) ;

    }

    public function manual_attendance_entry()
    {
      return view('manual_attendance.manual_attendance_entry');
    }

    public function manual_in()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");


        return view('manual_attendance.manual_attendance_in')
        -> with('default_user_location',  $default_user_location) ;
    }

    public function manual_out()
    {
      return view('manual_attendance.manual_attendance_out');
    }

    public function manual_attendancelistdata(Request $request){

        $condition    = "";
        $date_from         = date('Y-m-d', strtotime(str_replace('-', '-', $request->date_from)));
        $date_to         = date('Y-m-d', strtotime(str_replace('-', '-', $request->date_to)));

        // dd($date_from, $date_to ,$condition);

        if ($request->location != 0) {
            $condition  = " AND cc.hrm_location_id = ".$request->location;
        }

        if (!empty($request->hrm_employee_id)) {
            $condition  = $condition." AND cc.hrm_employee_id = ".$request->hrm_employee_id;
        }

        $auto_approve       = (int) DB::table('company_information')->value('manual_attendance_auto_approved') === 1;
        $approval_condition = $auto_approve
                ? ""
                : "and aa.approved_at is null
                            AND aa.approved_by is null
                            and aa.hrm_attendance_raw_data_id is null";

            $data = DB::select("SELECT
                      aa.id,
                      aa.hrm_employee_id,
                      Concat(hh.employee_name,' | ',ifnull(cc.employee_code, '')) as employee_name,
                      ee.depertment_name,
                      ff.designation_name,
                      gg.location_name,
                      aa.punch_date as punch_date,
                       aa.punch_time,
                       CONCAT(If(aa.entry_status=1,'OSD','Attendance'),
                           CASE
                               WHEN aa.entry_status=1 AND aa.osd_time_status = 0
                               THEN ' -> 00:00'
                               WHEN aa.entry_status=1 AND aa.osd_time_status = 1
                               THEN ' ->From Machine'
                               WHEN aa.entry_status=1 AND aa.osd_time_status = 2
                               THEN ' ->Manually'
                               ELSE ''
                           END
                       ) As typess,
                       aa.comment,
                       CONCAT(uu.name,' | ', aa.created_at) as users_name,
                       'Manual' As source_type
                  FROM

                      hrm_manual_attendance_data aa
                          JOIN
                      hrm_employee_job_info cc ON cc.hrm_employee_id = aa.hrm_employee_id
                            and cc.employee_activity=1
                            and aa.data_from=2
                            $approval_condition
                      -- and aa.punch_date between '$date_from' AND '$date_to'
                      $condition
                          JOIN
                      hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                          JOIN
                      hrm_designation ff ON cc.hrm_designation_id = ff.id
                          JOIN
                      hrm_location gg ON cc.hrm_location_id = gg.id
                          JOIN
                      hrm_employee hh ON cc.hrm_employee_id = hh.id
                         JOIN
                          users uu ON aa.users_id = uu.id
                      GROUP BY aa.id

                     ");

        return json_encode(array('data' => $data));

    }

    public function old_manual_attendancelistdata(Request $request){

        $condition    = "";
        $date_from         = date('Y-m-d', strtotime(str_replace('-', '-', $request->date_from)));
        $date_to         = date('Y-m-d', strtotime(str_replace('-', '-', $request->date_to)));

        // dd($date_from, $date_to ,$condition);

        if ($request->location != 0) {
            $condition  = " AND cc.hrm_location_id = ".$request->location;
        }

        if (!empty($request->hrm_employee_id)) {
            $condition  = $condition." AND cc.hrm_employee_id = ".$request->hrm_employee_id;
        }



        $data = DB::select("SELECT
                                aa.id,
                                aa.hrm_employee_id,
                                Concat(hh.employee_name,' | ',ifnull(cc.employee_code, '')) as employee_name,
                                ee.depertment_name,
                                ff.designation_name,
                                gg.location_name,
                                aa.punch_date as punch_date,
                                aa.punch_time,
                                If(ii.entry_status=1,'OSD','Attendance') As typess,
                                ii.comment
                                ,
                                CONCAT(uu.name,' | ', ii.created_at) as users_name
                            FROM

                                hrm_attendance_raw_data aa
                                    JOIN
                                hrm_employee_job_info cc ON cc.hrm_employee_id = aa.hrm_employee_id and cc.employee_activity=1 and aa.data_from=2
                                and aa.punch_date between '$date_from' AND '$date_to'
                                $condition
                                    JOIN
                                hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                                    JOIN
                                hrm_designation ff ON cc.hrm_designation_id = ff.id
                                    JOIN
                                hrm_location gg ON cc.hrm_location_id = gg.id
                                    JOIN
                                hrm_employee hh ON cc.hrm_employee_id = hh.id
                                    JOIN
                                hrm_attendance_comment ii On aa.id=ii.hrm_attendance_raw_data_id
                                 LEFT JOIN users uu ON ii.users_id = uu.id
                                GROUP BY aa.id
                               ORDER BY aa.id desc");

        return json_encode(array('data' => $data));

    }


    public function manual_inoutlistdata(Request $request)
    {
        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));
        $punch_date   = $request->punch_date;

        $condition    = "AND a.active_status=1 ";

        if ($request->location != 0){
          $condition  = "AND b.hrm_location_id = ".$request->location;
        }

        if ($request->working_shift_id != 0){
          $condition  =  $condition . " AND d.id = ".$request->working_shift_id ;
        }

        if ($request->department_id != 0){
          $condition  =  $condition . " AND b.hrm_depertment_id = ".$request->department_id ;
        }


        if ($request->category_id != 0){
          $condition  =  $condition . " AND b.hrm_category_id = ".$request->category_id ;
        }

        if ($request->designation_id != 0) {
          $condition  =  $condition . " AND b.hrm_designation_id = ".$request->designation_id ;
        }

        if ($request->section_id != 0){
          $condition  =  $condition . " AND b.hrm_section_id = ".$request->section_id ;
        }

        if ($request->employee_name != 0){
          $condition  =  $condition . " AND b.hrm_employee_id = ".$request->employee_name ;
        }



        $plant_contition="";
        if ($request->plant_id != 0){
          // $condition  =  $condition . " AND b.hrm_plant_id = ".$request->plant_id ;
          $plant_contition = " JOIN hrm_plantwise_employee i ON b.id=i.hrm_employee_job_info_id AND b.hrm_plant_id = ".$request->plant_id;

        }

        // if ($request->status!=2){
        //   $status="d.start_time";
        // }else{
        //   $status="d.end_time";
        // }

        $data   = DB::select("SELECT
                                a.id,
                                concat(a.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                '$punch_date' as punch_date,
                                d.start_time,
                                d.end_time,
                                d.shift_name
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
                                    $condition
                                    JOIN
                                hrm_employee_shift c ON c.hrm_employee_job_info_id = b.id And c.end_date IS NULL
                                    AND c.start_date <='$date'
                                    JOIN
                                hrm_shift d ON c.hrm_shift_id = d.id
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employee_card_code h ON h.hrm_employee_job_info_id = b.id
                                $plant_contition

                            UNION
                                SELECT
                                a.id,
                                concat(a.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                '$punch_date' as punch_date,
                                d.start_time,
                                d.end_time,
                                d.shift_name
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
                                $condition
                                    JOIN
                                hrm_employee_shift c ON c.hrm_employee_job_info_id = b.id And c.end_date IS NOT NULL
                                    AND '$date' between c.start_date AND c.end_date
                                    JOIN
                                hrm_shift d ON c.hrm_shift_id = d.id
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                hrm_employee_card_code h ON h.hrm_employee_job_info_id=b.id
                                $plant_contition
                            ");

        return json_encode(array('data' => $data));
    }

    public function manual_attendance_submit(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required',
            'punch_time'    => 'required',
            'punch_date'    => 'required',
            'comment'    => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->to('/manual_attendance_entry')->withErrors($validator)->withInput();
        }

        $punch_from_date = date('Y-m-d', strtotime($request->punch_date));
        $punch_to_date = date('Y-m-d', strtotime($request->punch_to_date ? $request->punch_to_date : $request->punch_date));

        if($punch_from_date > $punch_to_date){
            return redirect()->to('/manual_attendance_entry')->with('alert-danger', 'Punch From Date is Greater Than Punch To Date.!');
        }

        // if (date('H:i:s', strtotime($request->punch_time)) == '00:00:00') {
        //     return redirect()->to('/manual_attendance_entry')->with('alert-danger', 'Punch Time can not be 00:00:00!');
        // }



        // New Added by Noman 03-08-2026
        $hrm_employee_id=$request->employee_name;
        $hrm_month_id    = date('m', strtotime($punch_from_date));
        $year_id         = date('Y', strtotime($punch_from_date));


        $pay_register = DB::table('pay_register as a')
                    ->select('a.*','b.hrm_employee_id')
                    ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                    ->where('b.hrm_employee_id',$hrm_employee_id)
                    ->where('a.hrm_month_id',$hrm_month_id)
                    ->where('a.year_id',$year_id)
                    ->whereIn('a.salary_genarate_type', [1, 2])
                    ->first();



        if (!empty($pay_register)){
            session()->flash('alert-danger', 'This months salary has already been processed. Manual Entry is not allowed. !!');
            return Redirect()->back();
        }
        // End By Noman 03-08-2026

        // dd($pay_register);


        $valid_status = 1;
        if($request->entry_status==2 && $request->punch_time=='00:00'){
            $valid_status = 0;
        }

        if($request->entry_status == "1" && $request->osd_status==1){
            $valid_status = 0;
        }

        $diff= date_diff(date_create($punch_from_date), date_create($punch_to_date))->format("%a") + 1;

        $auto_approve          = (int) DB::table('company_information')->value('manual_attendance_auto_approved') === 1;
        $auto_approved_grouped = [];

        //------
        for ($x = 0; $x < $diff; $x++) {
            $punch_time          = $request->punch_time;
            //$punch_date          = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));
            $punch_date          = date('Y-m-d', strtotime($punch_from_date. ' + '.$x.'days'));
            $punch_date_and_time = $punch_date." $punch_time"  ;
            $employee_id         = $request->employee_name;


            $location  = HrmEmployeeJobInfo::where('hrm_employee_id', $request->employee_name)
                                           ->where('employee_activity','=',1)->first();

            $code      = HrmEmployeeCardCode::where('hrm_employee_job_info_id', $location->id)->first();

            if (empty($code)){
               return redirect()->to('/employeecard')->with('alert-danger', 'This Employee has no finger code!');
            }

            if( $request->entry_status == "1" &&  $request->osd_status == "0"){

                $employee_shift = DB::SELECT(" SELECT c.* FROM hrm_employee_shift a
                                            JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id AND b.hrm_employee_id=$employee_id and a.end_date IS NULL and a.start_date<='$punch_date' JOIN hrm_shift c ON
                                            a.hrm_shift_id=c.id
                                            UNION
                                            SELECT c.* FROM hrm_employee_shift a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id =b.id  AND b.hrm_employee_id=$employee_id and a.end_date IS NOT NULL AND '$punch_date' between a.start_date AND a.end_date JOIN hrm_shift c ON
                                            a.hrm_shift_id=c.id");
                $punch_time = $employee_shift[0]->start_time ;
            }

            $existing = HrmManualAttendance::where('hrm_employee_id', $employee_id)
                                   ->where('punch_date', $punch_date)
                                   ->where('punch_time', $punch_time)
                                   ->where('entry_status', $request->entry_status) // Entry status onujayi (e.g., IN or OUT)
                                   ->where('osd_time_status',$request->osd_status)
                                   ->where('valid',$valid_status)
                                   ->exists();

                // Option A: Duplicate record thakle skipp kora (Recommended for bulk date range)
            if ($existing) {
                session()->flash('alert-danger', 'Already same data exist. !!');
                return Redirect()->back();
            }

            DB::beginTransaction();



            $insert     = new HrmManualAttendance();
            $insert->device_no              = $code->device_id;
            $insert->employee_code          = $code->card_code;
            $insert->punch_date             = $punch_date;
            $insert->punch_time             = $punch_time;
            $insert->hrm_location_id        = $location->hrm_location_id;
            $insert->is_new                 = 1;
            $insert->hrm_employee_id        = $request->employee_name;
            $insert->data_from              = 2;
            $insert->comment                = $request->comment;
            $insert->entry_status           = $request->entry_status;
            $insert->osd_time_status        = $request->osd_status;
            $insert->valid                  = $valid_status;
            $insert->users_id               = auth()->id();
            $insert->created_at             = now();
            $insert->save();
            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Manual Attendance Entry',
                 $insert,
                 $insert->id,
                 'hrm_manual_attendance_data'
            );

            if ($auto_approve) {
                if ($this->isLocationSalaryProcessed($insert->hrm_location_id, $insert->punch_date)) {
                    session()->flash('alert-danger', 'This months salary has already been processed for this location. Manual Entry is not allowed!');
                    return Redirect()->back();
                }

                $this->autoApproveManualAttendance($insert);
                $auto_approved_grouped[$insert->hrm_location_id . '|' . $insert->punch_date][] = $insert->hrm_employee_id;
            }
            //-------Data Process

        }

        if ($auto_approve) {
            $this->dispatchManualAttendanceProcessJob($auto_approved_grouped);
        }

        return redirect()->to('/manual_attendance')->with('alert-success', 'data has been successfully added!');
    }

    public function submitmanual_inout(Request $request)
    {
        // dd(request()->all());

        $count_row  = count($request->id);

        $auto_approve          = (int) DB::table('company_information')->value('manual_attendance_auto_approved') === 1;
        $auto_approved_grouped = [];

        for($r = 0; $r <$count_row; $r++) {

            $start_time             = $request->start_time[$request->id[$r]];
            $end_time               = $request->end_time[$request->id[$r]];
            $punch_date             = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date[$request->id[$r]])));
            $xmasDay                = new DateTime($punch_date.' 1 day');
            $punch_date_plus        = $xmasDay->format('Y-m-d');
            $employee_id            = $request->id[$r];


            // New Added by Noman 03-08-2026
            $hrm_employee_id=$employee_id;
            $hrm_month_id    = date('m', strtotime($punch_date));
            $year_id         = date('Y', strtotime($punch_date));


            $pay_register = DB::table('pay_register as a')
                        ->select('a.*','b.hrm_employee_id')
                        ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                        ->where('b.hrm_employee_id',$hrm_employee_id)
                        ->where('a.hrm_month_id',$hrm_month_id)
                        ->where('a.year_id',$year_id)
                        ->whereIn('a.salary_genarate_type', [1, 2])
                        ->first();



            if (!empty($pay_register)){
                session()->flash('alert-danger', 'This months salary has already been processed. Manual Entry is not allowed. !!');
                return Redirect()->back();
            }
            // End By Noman 03-08-2026




            // $punch_date_and_time    = $punch_date." $punch_time"  ;
            $shift_id               = 0;

            $shift_data  = DB::select("SELECT b.id,b.hrm_shift_id FROM hrm_employee_job_info a
                                          JOIN hrm_employee_shift b ON a.id=b.hrm_employee_job_info_id
                                          WHERE end_date IS NULL AND a.hrm_employee_id =  $employee_id ");
            $shift_id    = $shift_data[0]->id;
            $date_status = Hrmshift::find($shift_id = $shift_data[0]->hrm_shift_id);
            $date_status = $date_status->date_status;

            $location  = HrmEmployeeJobInfo::where('hrm_employee_id', $employee_id)
                                            ->where('employee_activity','=',1)->first();
            $code = HrmEmployeeCardCode::where('hrm_employee_job_info_id', $location->id)->first();

            if (empty($code)) {
                $request->session()->flash('alert-danger', 'Please Check This List,Some Employees has no finger code!');
                return Redirect()->back();
            }

            $existing = HrmManualAttendance::where('hrm_employee_id', $employee_id)
                                   ->where('punch_date', $punch_date)
                                   ->where('entry_status', 2) // Entry status onujayi (e.g., IN or OUT)
                                   ->where('osd_time_status',$request->osd_status)
                                   ->where('is_new',1)
                                   ->whereIn('punch_time', [$start_time, $end_time])
                                   ->exists();

                // Option A: Duplicate record thakle skipp kora (Recommended for bulk date range)
            if ($existing) {
                $employee = DB::table('hrm_employee')->where('id',$employee_id)->first()->employee_name;
                session()->flash('alert-danger', "Attendance data already exists for {$employee} on {$punch_date}!");
                return Redirect()->back();
            }




            $insert_in     = new HrmManualAttendance;
            $insert_in->device_no              = $code->device_id;
            $insert_in->employee_code          = $code->card_code;
            $insert_in->punch_date             = $punch_date;
            $insert_in->punch_time             = $start_time;
            $insert_in->hrm_location_id        = $location->hrm_location_id;
            $insert_in->is_new                 = 1;
            $insert_in->hrm_employee_id        = $employee_id;
            $insert_in->data_from              = 2;
            $insert_in->entry_status           = 2;
            $insert_in->comment                = "Manual Attendance Entry From Multiple-Inout Option";
            $insert_in->created_at             = now();
            $insert_in->users_id               = auth()->id();
            $insert_in->save();

            $this->recordActivity(
                 1,
                 'Created Manual Attendance From Multiple-Inout Option',
                 $insert_in,
                 $insert_in->id,
                 'hrm_manual_attendance_data'
            );

            if ($auto_approve) {
                if ($this->isLocationSalaryProcessed($insert_in->hrm_location_id, $insert_in->punch_date)) {
                    session()->flash('alert-danger', 'This months salary has already been processed for this location. Manual Entry is not allowed!');
                    return Redirect()->back();
                }

                $this->autoApproveManualAttendance($insert_in);
                $auto_approved_grouped[$insert_in->hrm_location_id . '|' . $insert_in->punch_date][] = $insert_in->hrm_employee_id;
            }



            $insert_out     = new HrmManualAttendance;
            $insert_out->device_no              = $code->device_id;
            $insert_out->employee_code          = $code->card_code;
            if($date_status==1){
                $insert_out->punch_date             = $punch_date;
            }else{
                $insert_out->punch_date             = $punch_date_plus;
            }
            $insert_out->punch_time             = $end_time;
            $insert_out->hrm_location_id        = $location->hrm_location_id;
            $insert_out->is_new                 = 1;
            $insert_out->hrm_employee_id        = $employee_id;
            $insert_out->data_from              = 2;
            $insert_out->entry_status           = 2;
            $insert_out->comment                = "Manual Attendance Entry From Multiple-Inout Option";
            $insert_out->users_id               = auth()->id();
            $insert_out->created_at             = now();
            $insert_out->save();

            $this->recordActivity(
                 1,
                 'Created Manual Attendance From Multiple-Inout Option',
                 $insert_out,
                 $insert_out->id,
                 'hrm_manual_attendance_data'
            );

            if ($auto_approve) {
                if ($this->isLocationSalaryProcessed($insert_out->hrm_location_id, $insert_out->punch_date)) {
                    session()->flash('alert-danger', 'This months salary has already been processed for this location. Manual Entry is not allowed!');
                    return Redirect()->back();
                }

                $this->autoApproveManualAttendance($insert_out);
                $auto_approved_grouped[$insert_out->hrm_location_id . '|' . $insert_out->punch_date][] = $insert_out->hrm_employee_id;
            }


        }

        if ($auto_approve) {
            $this->dispatchManualAttendanceProcessJob($auto_approved_grouped);
        }

        return redirect()->to('/manual_attendance')
            ->with('alert-success', 'data has been successfully added!');
    }


    private function isLocationSalaryProcessed($hrmLocationId, $punchDate)
    {
        $pay_register = DB::table('pay_register as a')
                    ->select('a.id')
                    ->where('a.hrm_location_id',$hrmLocationId)
                    ->where('a.hrm_month_id',date('m', strtotime($punchDate)))
                    ->where('a.year_id',date('Y', strtotime($punchDate)))
                    ->whereIn('a.salary_genarate_type', [1, 2])
                    ->first();

        return !empty($pay_register);
    }

    private function autoApproveManualAttendance($manual)
    {
        $manual = HrmManualAttendance::find($manual->id);

        $attendance     = new HrmAttendance;
        $attendance->device_no              = $manual->device_no;
        $attendance->employee_code          = $manual->employee_code;
        $attendance->punch_date             = $manual->punch_date;
        $attendance->punch_time             = $manual->punch_time;
        $attendance->hrm_location_id        = $manual->hrm_location_id;
        $attendance->is_new                 = 1;
        $attendance->hrm_employee_id        = $manual->hrm_employee_id;
        $attendance->data_from              = 2;
        $attendance->valid                  = $manual->valid;
        $attendance->save();

        $this->recordActivity(
            1,
            'Created Manual Attendance (Auto Approved)',
            $attendance,
            $attendance->id,
            'hrm_attendance_raw_data'
        );

        $comment     = new HrmAttendanceComment;
        $comment->comment                    = $manual->comment;
        $comment->hrm_attendance_raw_data_id = $attendance->id;
        $comment->entry_status               = $manual->entry_status;
        $comment->osd_time_status            = $manual->osd_time_status;
        $comment->users_id                   = $manual->users_id;
        $comment->created_at                 = $manual->created_at;
        $comment->save();

        DB::table('hrm_manual_attendance_data')
            ->where('id', $manual->id)
            ->update([
                'hrm_attendance_raw_data_id' => $attendance->id,
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
    }

    private function dispatchManualAttendanceProcessJob(array $grouped)
    {
        if (empty($grouped)) {
            return;
        }

        foreach ($grouped as $key => $hrm_employee_ids) {
            [$hrm_location_id, $punch_date] = explode('|', $key);
            ManualAttendanceProcessJob::dispatch($hrm_location_id, $punch_date, implode(',', $hrm_employee_ids), auth()->id());
        }
    }


    public function pending_manual_attendance()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");
        if(request()->ajax()){
             $auto_approve = (int) DB::table('company_information')->value('manual_attendance_auto_approved') === 1;

             if ($auto_approve) {
                return json_encode(array('data' => []));
             }

             $condition    = "";
        $date_condition = "";
        if (request()->apply_date_range == 1) {
            $date_from         = date('Y-m-d', strtotime(str_replace('-', '-', request()->date_from)));
            $date_to         = date('Y-m-d', strtotime(str_replace('-', '-', request()->date_to)));
            $date_condition = " and aa.punch_date between '$date_from' AND '$date_to'";
        }

        // dd($date_from, $date_to ,$condition);

        if (request()->location != 0) {
            $condition  = " AND cc.hrm_location_id = ".request()->location;
        }

        if (!empty(request()->hrm_employee_id)) {
            $condition  = $condition." AND cc.hrm_employee_id = ".request()->hrm_employee_id;
        }



                        $data = DB::select("SELECT
                                   aa.id,
                                   aa.hrm_employee_id,
                                   Concat(hh.employee_name,' | ',ifnull(cc.employee_code, '')) as employee_name,
                                   ee.depertment_name,
                                   ff.designation_name,
                                   gg.location_name,
                                   aa.punch_date as punch_date,
                                   aa.punch_time,
                                   CONCAT(If(aa.entry_status=1,'OSD','Attendance'),
                                       CASE
                                           WHEN aa.entry_status=1 AND aa.osd_time_status = 0
                                           THEN ' -> 00:00'
                                           WHEN aa.entry_status=1 AND aa.osd_time_status = 1
                                           THEN ' -> From Machine'
                                           WHEN aa.entry_status=1 AND aa.osd_time_status = 2
                                           THEN ' -> Manually'
                                           ELSE ''
                                       END
                                   ) As typess,
                                   aa.comment,
                                   CONCAT(uu.name,' | ', aa.created_at) as users_name

                               FROM

                                   hrm_manual_attendance_data aa
                                       JOIN
                                   hrm_employee_job_info cc ON cc.hrm_employee_id = aa.hrm_employee_id
                                   and cc.employee_activity=1
                                   and aa.data_from=2
                                   and aa.approved_at is null
                                   AND aa.approved_by is null
                                   $date_condition
                                   $condition
                                       JOIN
                                  hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                                      JOIN
                                  hrm_designation ff ON cc.hrm_designation_id = ff.id
                                      JOIN
                                  hrm_location gg ON cc.hrm_location_id = gg.id
                                      JOIN
                                  hrm_employee hh ON cc.hrm_employee_id = hh.id
                                     JOIN
                                  users uu ON aa.users_id = uu.id
                                  GROUP BY aa.id
                                 ORDER BY aa.id desc");

                return json_encode(array('data' => $data));
        }
      return view('manual_attendance.pending_manual_attendance')
       -> with('default_user_location',  $default_user_location) ;

    }

    public function approved_manual_attendance(){

        $user_id = Auth::user()->id;
       $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");
        if(request()->ajax()){
             $condition    = "";
        $date_from         = date('Y-m-d', strtotime(str_replace('-', '-', request()->date_from)));
        $date_to         = date('Y-m-d', strtotime(str_replace('-', '-', request()->date_to)));

        // dd($date_from, $date_to ,$condition);

        if (request()->location != 0) {
            $condition  = " AND cc.hrm_location_id = ".request()->location;
        }

        if (!empty(request()->hrm_employee_id)) {
            $condition  = $condition." AND cc.hrm_employee_id = ".request()->hrm_employee_id;
        }



                            $data = DB::select("SELECT
                                  aa.id,
                                  aa.hrm_employee_id,
                                  Concat(hh.employee_name,' | ',ifnull(cc.employee_code, '')) as employee_name,
                                  ee.depertment_name,
                                  ff.designation_name,
                                  gg.location_name,
                                  aa.punch_date as punch_date,
                                aa.punch_time,
                                   CONCAT(If(aa.entry_status=1,'OSD','Attendance'),
                                       CASE
                                           WHEN aa.entry_status=1 AND aa.osd_time_status = 0
                                           THEN ' -> 00:00'
                                           WHEN aa.entry_status=1 AND aa.osd_time_status = 1
                                           THEN ' ->From Machine'
                                           WHEN aa.entry_status=1 AND aa.osd_time_status = 2
                                           THEN ' ->Manually'
                                           ELSE ''
                                       END
                                   ) As typess,
                                   aa.comment,
                                   CONCAT(uu.name,' | ', aa.created_at) as users_name,
                                   CONCAT(uu2.name,' | ', aa.approved_at) as approved_by

                              FROM

                                  hrm_manual_attendance_data aa
                                      JOIN
                                  hrm_employee_job_info cc ON cc.hrm_employee_id = aa.hrm_employee_id and cc.employee_activity=1 and aa.data_from=2
                                  and aa.punch_date between '$date_from' AND '$date_to'
                                  and aa.valid=1 and aa.approved_at is not null
                                  $condition
                                      JOIN
                                  hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                                      JOIN
                                  hrm_designation ff ON cc.hrm_designation_id = ff.id
                                      JOIN
                                  hrm_location gg ON cc.hrm_location_id = gg.id
                                      JOIN
                                  hrm_employee hh ON cc.hrm_employee_id = hh.id
                                     JOIN
                                  users uu ON aa.users_id = uu.id
                                    JOIN users uu2 ON aa.approved_by = uu2.id

                                 GROUP BY aa.id
                                ORDER BY aa.id desc");

                return json_encode(array('data' => $data));
        }
      return view('manual_attendance.approved_manual_attendance')
       ->with('default_user_location',  $default_user_location) ;
    }


    public function pending_manual_attendance_action(Request $request)
    {
        $ids = $request->ids;

        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'Please select at least one record!']);
        }

        try {
            if ($request->action == 'approve') {
                DB::beginTransaction();

                $datas = HrmManualAttendance::whereIn('id', $ids)->whereNull('approved_at')->get();
                // dd($datas->toArray());
                foreach($datas as $data){

                    $hrm_location_id = $data->hrm_location_id;
                    $hrm_month_id    = date('m', strtotime($data->punch_date));
                    $year_id         = date('Y', strtotime($data->punch_date));


                    $pay_register = DB::table('pay_register as a')
                                ->select('a.*')
                                ->where('a.hrm_location_id',$hrm_location_id)
                                ->where('a.hrm_month_id',$hrm_month_id)
                                ->where('a.year_id',$year_id)
                                ->whereIn('a.salary_genarate_type', [1, 2])
                                ->first();

                    // if (!empty($pay_register)){
                    //     DB::rollBack();
                    //     session()->flash('alert-danger', 'This months salary has already been processed for this location. Manual Entry is not allowed. !!');
                    //     return Redirect()->back();
                    // }
                    if (!empty($pay_register)){
                        DB::rollBack();

                        return response()->json([
                            'status' => 'error',
                            'message' => 'This months salary has already been processed for this location. Manual Entry is not allowed!'
                        ]);
                    }
                    $insert_in     = new HrmAttendance;
                    $insert_in->device_no              = $data->device_no;
                    $insert_in->employee_code          = $data->employee_code;
                    $insert_in->punch_date             = $data->punch_date;
                    $insert_in->punch_time             = $data->punch_time;
                    $insert_in->hrm_location_id        = $data->hrm_location_id;
                    $insert_in->is_new                 = 1;
                    $insert_in->hrm_employee_id        = $data->hrm_employee_id;
                    $insert_in->data_from              = 2;
                    $insert_in->valid                  = $data->valid;
                    $insert_in->save();

                    $this->recordActivity(
                        1,
                        'Created Manual Attandance From Multiple-Inout Option',
                        $insert_in,
                        $insert_in->id,
                        'hrm_attendance_raw_data'
                    );

                    $insert_in_comment     = new HrmAttendanceComment;
                    $insert_in_comment->comment                    = $data->comment;
                    $insert_in_comment->hrm_attendance_raw_data_id = $insert_in->id;
                    $insert_in_comment->entry_status               = $data->entry_status;
                    $insert_in_comment->osd_time_status            = $data->osd_time_status;
                    $insert_in_comment->users_id                   = $data->users_id;
                    $insert_in_comment->created_at                 = $data->created_at;
                    $insert_in_comment->save();


                    DB::table('hrm_manual_attendance_data')
                    ->where('id', $data->id)
                    ->update(['hrm_attendance_raw_data_id' => $insert_in->id,'approved_by' => auth()->id(),'approved_at' => now()]);
                }
                DB::commit();

                $grouped = [];
                foreach ($datas as $data) {
                    $grouped[$data->hrm_location_id . '|' . $data->punch_date][] = $data->hrm_employee_id;
                }

                foreach ($grouped as $key => $hrm_employee_ids) {
                    [$hrm_location_id, $punch_date] = explode('|', $key);
                    ManualAttendanceProcessJob::dispatch($hrm_location_id, $punch_date, implode(',', $hrm_employee_ids), auth()->id());
                }

                $message = 'Attendance has been successfully approved!';
            } elseif ($request->action == 'reject') {
                DB::table('hrm_manual_attendance_data')
                    ->whereIn('id', $ids)
                    ->whereNull('approved_at')
                    ->update(['valid' => 0,'approved_by' => auth()->id(),'approved_at' => now()]);

                $message = 'Attendance has been rejected!';
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid action!']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage()]);
        }

        return response()->json(['status' => 'success', 'message' => $message]);
    }

    public function manual_attendance_submitOld(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'employee_name' => 'required',
            'punch_time'    => 'required',
            'punch_date'    => 'required',
            'comment'    => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->to('/manual_attendance_entry')->withErrors($validator)->withInput();
        }

        $punch_from_date = date('Y-m-d', strtotime($request->punch_date));
        $punch_to_date = date('Y-m-d', strtotime($request->punch_to_date ? $request->punch_to_date : $request->punch_date));

        if($punch_from_date > $punch_to_date){
            return redirect()->to('/manual_attendance_entry')->with('alert-danger', 'Punch From Date is Greater Than Punch To Date.!');
        }



        // New Added by Noman 03-08-2026
        $hrm_employee_id=$request->employee_name;
        $hrm_month_id    = date('m', strtotime($punch_from_date));
        $year_id         = date('Y', strtotime($punch_from_date));


        $pay_register = DB::table('pay_register as a')
                    ->select('a.*','b.hrm_employee_id')
                    ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                    ->where('b.hrm_employee_id',$hrm_employee_id)
                    ->where('a.hrm_month_id',$hrm_month_id)
                    ->where('a.year_id',$year_id)
                    ->whereIn('a.salary_genarate_type', [1, 2])
                    ->first();



        if (!empty($pay_register)){
            session()->flash('alert-danger', 'This months salary has already been processed. Manual Entry is not allowed. !!');
            return Redirect()->back();
        }
        // End By Noman 03-08-2026

        // dd($pay_register);


        $valid_status = 1;
        if($request->entry_status==2 && $request->punch_time=='00:00'){
            $valid_status = 0;
        }

        if($request->entry_status == "1" && $request->osd_status==1){
            $valid_status = 0;
        }

        $diff= date_diff(date_create($punch_from_date), date_create($punch_to_date))->format("%a") + 1;

        //------
        for ($x = 0; $x < $diff; $x++) {
            $punch_time          = $request->punch_time;
            //$punch_date          = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));
            $punch_date          = date('Y-m-d', strtotime($punch_from_date. ' + '.$x.'days'));
            $punch_date_and_time = $punch_date." $punch_time"  ;
            $employee_id         = $request->employee_name;


            $location  = HrmEmployeeJobInfo::where('hrm_employee_id', $request->employee_name)
                                           ->where('employee_activity','=',1)->first();

            $code      = HrmEmployeeCardCode::where('hrm_employee_job_info_id', $location->id)->first();

            if (empty($code)){
               return redirect()->to('/employeecard')->with('alert-danger', 'This Employee has no finger code!');
            }

            if( $request->entry_status == "1" &&  $request->osd_status == "0"){

                $employee_shift = DB::SELECT(" SELECT c.* FROM hrm_employee_shift a
                                            JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id AND b.hrm_employee_id=$employee_id and a.end_date IS NULL and a.start_date<='$punch_date' JOIN hrm_shift c ON
                                            a.hrm_shift_id=c.id
                                            UNION
                                            SELECT c.* FROM hrm_employee_shift a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id =b.id  AND b.hrm_employee_id=$employee_id and a.end_date IS NOT NULL AND '$punch_date' between a.start_date AND a.end_date JOIN hrm_shift c ON
                                            a.hrm_shift_id=c.id");
                $punch_time = $employee_shift[0]->start_time ;
            }

            DB::beginTransaction();


            $insert     = new HrmAttendance;
            $insert->device_no              = $code->device_id;
            $insert->employee_code          = $code->card_code;
            $insert->punch_date             = $punch_date;
            $insert->punch_time             = $punch_time;
            $insert->hrm_location_id        = $location->hrm_location_id;
            $insert->is_new                 = 1;
            $insert->hrm_employee_id        = $request->employee_name;
            $insert->data_from              = 2;
            $insert->valid                  = $valid_status;
            // if($request->entry_status == "1" && $request->osd_status==1){
            // }
            $insert->save();


            $insert_comment     = new HrmAttendanceComment;
            $insert_comment->comment                    = $request->comment;
            $insert_comment->hrm_attendance_raw_data_id = $insert->id;
            $insert_comment->entry_status               = $request->entry_status;
            $insert_comment->osd_time_status            = $request->osd_status;
            $insert_comment->users_id                   = Auth::user()->id;
            $insert_comment->created_at                 = now();
            $insert_comment->save();
            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Manual Attendance Entry',
                 $insert,
                 $insert->id,
                 'hrm_attendance_raw_data'
            );
            //-------Data Process
            $attendance = new AttendanceDataProcessController();
            $attendance->attandanceProcess($location->hrm_location_id,$punch_date,$employee_id);
        }

        return redirect()->to('/manual_attendance')->with('alert-success', 'data has been successfully added!');
    }



    public function submitmanual_inout_old(Request $request)
    {
        //dd("ok..multi in");

        $count_row  = count($request->id);

        for($r = 0; $r <$count_row; $r++) {

            $start_time             = $request->start_time[$request->id[$r]];
            $end_time               = $request->end_time[$request->id[$r]];
            $punch_date             = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date[$request->id[$r]])));
            $xmasDay                = new DateTime($punch_date.' 1 day');
            $punch_date_plus        = $xmasDay->format('Y-m-d');
            $employee_id            = $request->id[$r];


            // New Added by Noman 03-08-2026
            $hrm_employee_id=$employee_id;
            $hrm_month_id    = date('m', strtotime($punch_date));
            $year_id         = date('Y', strtotime($punch_date));


            $pay_register = DB::table('pay_register as a')
                        ->select('a.*','b.hrm_employee_id')
                        ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
                        ->where('b.hrm_employee_id',$hrm_employee_id)
                        ->where('a.hrm_month_id',$hrm_month_id)
                        ->where('a.year_id',$year_id)
                        ->whereIn('a.salary_genarate_type', [1, 2])
                        ->first();



            if (!empty($pay_register)){
                session()->flash('alert-danger', 'This months salary has already been processed. Manual Entry is not allowed. !!');
                return Redirect()->back();
            }
            // End By Noman 03-08-2026




            // $punch_date_and_time    = $punch_date." $punch_time"  ;
            $shift_id               = 0;

            $shift_data  = DB::select("SELECT b.id,b.hrm_shift_id FROM hrm_employee_job_info a
                                          JOIN hrm_employee_shift b ON a.id=b.hrm_employee_job_info_id
                                          WHERE end_date IS NULL AND a.hrm_employee_id =  $employee_id ");
            $shift_id    = $shift_data[0]->id;
            $date_status = Hrmshift::find($shift_id = $shift_data[0]->hrm_shift_id);
            $date_status = $date_status->date_status;

            $location  = HrmEmployeeJobInfo::where('hrm_employee_id', $employee_id)
                                            ->where('employee_activity','=',1)->first();
            $code = HrmEmployeeCardCode::where('hrm_employee_job_info_id', $location->id)->first();

            if (empty($code)) {
                $request->session()->flash('alert-danger', 'Please Check This List,Some Employees has no finger code!');
                return Redirect()->back();
            }



            $insert_in     = new HrmAttendance;
            $insert_in->device_no              = $code->device_id;
            $insert_in->employee_code          = $code->card_code;
            $insert_in->punch_date             = $punch_date;
            $insert_in->punch_time             = $start_time;
            $insert_in->hrm_location_id        = $location->hrm_location_id;
            $insert_in->is_new                 = 1;
            $insert_in->hrm_employee_id        = $employee_id;
            $insert_in->data_from              = 2;
            $insert_in->save();

            $this->recordActivity(
                 1,
                 'Created Manual Attandance From Multiple-Inout Option',
                 $insert_in,
                 $insert_in->id,
                 'hrm_attendance_raw_data'
            );

            $insert_in_comment     = new HrmAttendanceComment;
            $insert_in_comment->comment                    = "Manual Attendance Entry From Multiple-Inout Option";
            $insert_in_comment->hrm_attendance_raw_data_id = $insert_in->id;
            $insert_in_comment->entry_status               = 2;
            $insert_in_comment->users_id                   = Auth::user()->id;
            $insert_in_comment->created_at                 = now();
            $insert_in_comment->save();

            $insert_out     = new HrmAttendance;
            $insert_out->device_no              = $code->device_id;
            $insert_out->employee_code          = $code->card_code;
            if($date_status==1){
                $insert_out->punch_date             = $punch_date;
            }else{
                $insert_out->punch_date             = $punch_date_plus;
            }
            $insert_out->punch_time             = $end_time;
            $insert_out->hrm_location_id        = $location->hrm_location_id;
            $insert_out->is_new                 = 1;
            $insert_out->hrm_employee_id        = $employee_id;
            $insert_out->data_from              = 2;
            $insert_out->save();

            $this->recordActivity(
                 1,
                 'Created Manual Attandance From Multiple-Inout Option',
                 $insert_out,
                 $insert_out->id,
                 'hrm_attendance_raw_data'
            );

            $insert_out_comment     = new HrmAttendanceComment;
            $insert_out_comment->comment                    = "Manual Attendance Entry From Multiple-Inout Option";
            $insert_out_comment->hrm_attendance_raw_data_id = $insert_out->id;
            $insert_out_comment->entry_status               = 2;
            $insert_out_comment->users_id                   = Auth::user()->id;
            $insert_out_comment->created_at                 = now();
            $insert_out_comment->save();
        }

        return redirect()->to('/manual_attendance')
            ->with('alert-success', 'data has been successfully added!');
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



    // public function manual_attendance_delete(Request $request,$id){

    //     // dd("i am here");

    //     $cancel = HrmAttendance::find($id);
    //     $hrm_employee_id=$cancel->hrm_employee_id;
    //     $hrm_month_id    = date('m', strtotime($cancel->punch_date));
    //     $year_id         = date('Y', strtotime($cancel->punch_date));


    //     $pay_register = DB::table('pay_register as a')
    //                 ->select('a.*','b.hrm_employee_id')
    //                 ->join('hrm_employee_job_info as b','b.id','=','a.hrm_employee_job_info_id')
    //                 ->where('b.hrm_employee_id',$hrm_employee_id)
    //                 ->where('a.hrm_month_id',$hrm_month_id)
    //                 ->where('a.year_id',$year_id)
    //                 ->whereIn('a.salary_genarate_type', [1, 2])
    //                 ->first();



    //     if (!empty($pay_register)){
    //         session()->flash('alert-danger', 'This months salary has already been processed. Deletion or editing is not allowed. !!');
    //         return Redirect()->back();
    //     }

    //     // dd($pay_register);



    //     if (empty($cancel)){
    //         session()->flash('alert-danger', 'Invalid Data !!');
    //         return Redirect()->back();
    //     }

    //     $this->recordActivity(
    //          1,
    //          'Deleted Manual Attandance',
    //          $cancel,
    //          $id,
    //          'hrm_attendance_raw_data'
    //     );



    //     DB::table('hrm_attendance_comment')->where('hrm_attendance_raw_data_id', '=', $id)->delete();
    //     DB::table('hrm_attendance_data')->where('row_data_id', '=', $id)->delete();
    //     DB::table('hrm_attendance_raw_data')->where('id', '=', $id)->delete();


    //     //-------Data Process
    //     $attendance = new AttendanceDataProcessController();
    //     $attendance->attandanceProcess($cancel->hrm_location_id,$cancel->punch_date,$cancel->hrm_employee_id);

    //     $request->session()->flash('alert-success', 'successfully deleted !');
    //     return Redirect::to('manual_attendance');
    // }

    public function delete(Request $request, $id)
    {
        $auto_approve = (int) DB::table('company_information')->value('manual_attendance_auto_approved') === 1;

        $manual = $auto_approve
            ? HrmManualAttendance::find($id)
            : HrmManualAttendance::whereNull('hrm_attendance_raw_data_id')->find($id);

        if (empty($manual)) {
            session()->flash('alert-danger', 'Invalid Data !!');
            return Redirect()->back();
        }
        $this->recordActivity(
             1,
             'Deleted Manual Attandance',
             $manual,
             $id,
             'hrm_manual_attendance_data'
        );

        $manual->delete();

        if ($auto_approve && !empty($manual->hrm_attendance_raw_data_id)) {
            DB::table('hrm_attendance_comment')->where('hrm_attendance_raw_data_id', $manual->hrm_attendance_raw_data_id)->delete();
            DB::table('hrm_attendance_raw_data')->where('id', $manual->hrm_attendance_raw_data_id)->delete();

            ManualAttendanceProcessJob::dispatch($manual->hrm_location_id, $manual->punch_date, $manual->hrm_employee_id, auth()->id());
        }

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('manual_attendance');
    }






}
