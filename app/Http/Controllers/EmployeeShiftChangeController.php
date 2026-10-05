<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use DB;
use DataTables;
use Crypt;
use Validator;
use Config;
use Session;
use DateTime;
use Response;

use App\Models\HrmEmployeeShift;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmChangeEmployeeShift;
use App\Http\Controllers\AttendanceDataProcessController;





class EmployeeShiftChangeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function change_employeeshift()
    {
        return view('employee_shift_change.change_employee_shift');
    }


    public function changeshift_multiple()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id ");

            return view('employee_shift_change.changeshift_multiple')
                  -> with('default_user_location',  $default_user_location) ;
    }


    public function wrongshiftassign()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id ");
          return view('employee_shift_change.wrongshiftassign_list')
          -> with('default_user_location',  $default_user_location) ;
    }


    public function employeeshiftlist()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('employee_shift_change.employee_shift_list')
            ->with('default_user_location',  $default_user_location) ;

    }


    public function employeeshiftlist_old()
    {

        return view('employee_shift_change.employee_old_shift_list');

    }


    public function employeeshiftchange()
    {

        return view('employee_shift_change.pre_employee_shift_list');

    }

    public function pre_changeemployeeshift()
    {

        return view('employee_shift_change.pre_change_employee_shift');

    }


    public function multipleemployee_shiftdata(Request $request){

        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));

        $condition    = "a.active_status=1";
        $punch_date   = $request->punch_date;

        if ($request->location != 0){
          $condition  = " b.hrm_location_id = ".$request->location;
        }

        if ($request->working_shift_id != 0){
          $condition  =  $condition . " AND d.id = ".$request->working_shift_id ;
        }

        if ($request->department_id != 0){
          $condition  =  $condition . " AND e.id = ".$request->department_id ;
        }

        if ($request->employee_id != 0){
          $condition  =  $condition . " AND b.hrm_employee_id = ".$request->employee_id ;
        }

        if ($request->section != 0){
          $condition  =  $condition . " AND b.hrm_section_id = ".$request->section ;
        }

        $data   = DB::select("SELECT
                                b.id,
                                concat(a.employee_name,' | ',ifnull(b.employee_code, ' ')) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                '$punch_date' as punch_date,
                                DATE_FORMAT(c.start_date, '%d-%m-%Y') as last_change,
                                d.start_time,
                                d.end_time,
                                d.shift_name
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity = 1
                                    JOIN
                                hrm_employee_shift c ON c.hrm_employee_job_info_id = b.id And c.end_date IS NULL
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
                                Where
                                    $condition
                            ");

        return json_encode(array('data' => $data));

    }

    public function wrongshiftassign_data(Request $request){

        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));

        $condition    = "a.active_status=1";
        // $punch_date   = $request->punch_date;

        if ($request->location != 0){
          $condition  = " b.hrm_location_id = ".$request->location;
        }

        // if ($request->working_shift_id != 0){
        //   $condition  =  $condition . " AND d.id = ".$request->working_shift_id ;
        // }

        if ($request->department_id != 0){
          $condition  =  $condition . " AND e.id = ".$request->department_id ;
        }

        if ($request->employee_id != 0){
          $condition  =  $condition . " AND b.hrm_employee_id = ".$request->employee_id ;
        }

        if ($request->section != 0){
          $condition  =  $condition . " AND b.hrm_section_id = ".$request->section ;
        }

        $data   = DB::select("SELECT
                                    a.id,
                                    CONCAT(a.employee_name, ' | ', ifnull(b.employee_code, ' ')) AS employee_name,
                                    e.depertment_name,
                                    f.designation_name,
                                    g.location_name,
                                    ii.category_name,
                                    i.in_time,
                                    i.out_time,
                                    concat(d.shift_name,'(', d.start_time,'--',d.end_time,')') as assign_shift,
                                    (SELECT concat(shift_name,'(', start_time,'--',end_time,')') FROM hrm_shift  where valid=1 AND start_time between  SubTime(i.in_time, '00:40:00')
                                    AND AddTime(i.in_time, '00:40:00') limit 1 ) as probable_shift,
                                    (SELECT id FROM hrm_shift  where valid=1 AND start_time between  SubTime(i.in_time, '00:40:00')
                                    AND AddTime(i.in_time, '00:40:00') limit 1 ) as probable_shift_id

                                FROM
                                    hrm_employee a
                                        JOIN
                                    hrm_employee_job_info b ON b.hrm_employee_id = a.id
                                        AND b.employee_activity = 1
                                        JOIN
                                    hrm_attendance i ON i.hrm_employee_id = a.id
                                        AND i.attendance_status = 6
                                        AND i.punche_date='$date'
                                       AND i.late_time>'02:00:00'
                                        JOIN
                                    hrm_shift d ON i.hrm_shift_id = d.id
                                        JOIN
                                    hrm_depertment e ON b.hrm_depertment_id = e.id
                                        JOIN
                                    hrm_designation f ON b.hrm_designation_id = f.id
                                        JOIN
                                    hrm_location g ON b.hrm_location_id = g.id
                                        JOIN
                                    hrm_employee_card_code h ON h.hrm_employee_job_info_id = b.id
                                        JOIN
                                    hrm_category ii ON b.hrm_category_id = ii.id
                                    Where  $condition
                                GROUP BY a.id , a.employee_name , b.employee_code , e.depertment_name , f.designation_name , g.location_name ,
                                i.in_time,i.out_time,d.shift_name, d.start_time,d.end_time

                            ");

        return json_encode(array('data' => $data));

    }


   public function employee_current_shiftlist(Request $request)
    {

           if($request->location==null){
                $location = 0;
              }else{
                $location = $request->location;
            }

            $user_id=Auth::user()->id;


            $currentshiftlist= DB::select("SELECT
                                                a.id,
                                                CONCAT(a.employee_name, ' | ', ifnull(b.employee_code, ' ')) AS employee_name,
                                                e.depertment_name,
                                                f.designation_name,
                                                d.shift_name,
                                                d.start_time,
                                                d.end_time,
                                                d.working_hours,
                                                c.start_date,
                                                if(c.comment='NewJoin','NewJoin','') as new_join_employee,
                                                c.id as hrm_employee_shift_id
                                            FROM
                                                hrm_employee a
                                                    JOIN
                                                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                                    AND b.employee_activity = 1
                                                    AND b.hrm_location_id = $location
                                                    JOIN
                                                hrm_employee_shift c ON c.hrm_employee_job_info_id = b.id
                                                    JOIN
                                                hrm_shift d ON c.hrm_shift_id = d.id
                                                    JOIN
                                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                                    JOIN
                                                hrm_designation f ON b.hrm_designation_id = f.id
                                                    JOIN
                                                hrm_location g ON b.hrm_location_id = g.id
                                                    JOIN
                                                user_location h ON b.hrm_location_id = h.hrm_location_id
                                                    AND h.users_id = $user_id
                                            WHERE
                                                c.end_date IS NULL" );



            // return json_encode(array('data' => $currentshiftlist));

        return datatables()->of($currentshiftlist)
        ->setRowId('id')
        ->make(true);

    }

   public function pre_changeemployeeshiftlist(Request $request)
    {


        $condition    = "";
        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->process_date)));

        if ($request->employee_name != 0){
          $condition  = " AND a.id = $request->employee_name ";
        }



            $user_id=Auth::user()->id;


            $currentshiftlist= DB::select("SELECT c.id,
                a.id as hrm_employee_id,
                concat(a.employee_name,' | ',ifnull(b.employee_code, ' ') ,' ',e.depertment_name,' ',f.designation_name) as employee_name,
                e.depertment_name,
                f.designation_name,
                d.shift_name,
                d.start_time,
                d.end_time,
                d.working_hours,
                c.punch_date,
                g.location_name
                from hrm_employee a
                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id AND b.employee_activity=1
                $condition
                JOIN hrm_change_employee_shift c On c.hrm_employee_id=a.id  AND c.punch_date='$date'
                JOIN hrm_shift d On c.hrm_shift_id=d.id
                JOIN hrm_depertment e ON b.hrm_depertment_id=e.id
                JOIN hrm_designation f ON b.hrm_designation_id=f.id
                Join hrm_location g ON b.hrm_location_id=g.id
                JOIN user_location h ON b.hrm_location_id = h.hrm_location_id AND h.users_id = $user_id
                " );
            // return json_encode(array('data' => $currentshiftlist));
        $currentshiftlist   = collect($currentshiftlist);
        return datatables()->of($currentshiftlist)
        ->addColumn('Link', function ($data) {
           return
           ' <a href="'. url('/pre_changeemployeeshift') . '/' .
           ($data->id) .
           '/cancel' .'"' .
           'onclick="return confirm(\'Do you really want to DELETE?\');" class="btn btn-danger btn-sm btn-flat"><i class="glyphicon glyphicon-trash" id="customer-confrimed"></i> Delete</a>';

         })
        ->editColumn('id', '{{$id}}')
        ->setRowId('id')
        ->rawColumns(['Link'])
        ->make(true);


    }





   public function employee_old_shiftlist(Request $request)
    {


        $condition    = "";
        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->process_date)));
        if ($request->employee_name != 0){
          $activity   = DB::table('hrm_employee_job_info')->where('hrm_employee_id','=',$request->employee_name)->first();
          $condition  = " AND c.hrm_employee_job_info_id = ".$activity->id;
        }

            $user_id=Auth::user()->id;

            $oldshiftlist= DB::select("SELECT a.id,
                concat(a.employee_name,' | ',ifnull(b.employee_code, ' ')) as employee_name,
                e.depertment_name,
                f.designation_name,
                d.shift_name,
                d.start_time,
                d.end_time,
                d.working_hours,
                c.start_date,
                c.end_date
                from hrm_employee a
                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id AND b.id in
                (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE  '$date'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND '$date' >= start_date)
                JOIN hrm_employee_shift c On c.hrm_employee_job_info_id=b.id
                JOIN hrm_shift d On c.hrm_shift_id=d.id
                JOIN hrm_depertment e ON b.hrm_depertment_id=e.id
                JOIN hrm_designation f ON b.hrm_designation_id=f.id
                JOIN hrm_location g ON b.hrm_location_id=g.id
                JOIN user_location h ON b.hrm_location_id = h.hrm_location_id AND h.users_id = $user_id
                WHERE c.end_date is not Null AND '$date' BETWEEN c.start_date AND c.end_date  $condition " );

            return json_encode(array('data' => $oldshiftlist));

    }




    public function wrong_to_correct_shift(Request $request)
    {

        // dd("working...");

        $validator = Validator::make($request->all(), [
            'id'  => 'required',
            'shift'  => 'required',
            'attendance_date'     => 'required|date'
        ]);

        if ($validator->fails()) {
            return response::json(array(
                'success'    => false,
                'messages'   => 'Information Required'
            ));
        }


        $punch_date           = date('Y-m-d', strtotime(str_replace('/', '-', $request->attendance_date)));

        $isExist=DB::select("SELECT id FROM hrm_change_employee_shift WHERE hrm_employee_id=$request->id AND punch_date='$punch_date' ");

        if (!empty($isExist)){
           return response::json(array(
                'success'    => false,
                'messages'   => 'Duplicate Entry!!Please Check.'
            ));

        }

        $employee_jobinfo     = HrmEmployeeJobInfo::where('hrm_employee_id',$request->id)
                                                  ->where('employee_activity',1)->first();


  DB::beginTransaction();

        try {

            $insert_shift  = new HrmChangeEmployeeShift;
            $insert_shift->hrm_employee_id          = $request->id;
            $insert_shift->hrm_shift_id             = $request->shift;
            $insert_shift->punch_date               = $punch_date;

            $insert_shift->save();

            DB::commit();

            $this->attentanceCurrentProcess($employee_jobinfo->hrm_location_id,$punch_date,$employee_jobinfo->hrm_employee_id,1);

            } catch (\Exception $e) {
                DB::rollback();
                return Response::json(array(
                    'success'           => false,
                    'error_messages'    => true,
                    'messages'          => "insert problem !! " . $e->getMessage()
                ));
            }




            return response::json(array(
                    'success'    => true,
                    'messages'   => 'Employee Shift successfully Changed!'
            ));



    }



    public function wrong_to_correct_shift_permanent(Request $request)
    {

         $validator = Validator::make($request->all(), [
            'id'  => 'required',
            'shift'  => 'required',
            'attendance_date'     => 'required|date'
        ]);

        if ($validator->fails()) {
            // return redirect('wrongshiftassign')
            //             ->withErrors($validator)
            //             ->withInput();
            return response::json(array(
                'success'    => false,
                'messages'   => 'Information Required'
            ));
        }




        $user_id = Auth::user()->id;
        $ldate   = date('Y-m-d H:i:s');

        $start_date           = date('Y-m-d', strtotime(str_replace('/', '-', $request->attendance_date)));
        $xmasDay              = new DateTime($start_date.'- 1 day');
        $end_date             = $xmasDay->format('Y-m-d');
        $employee_jobinfo     = HrmEmployeeJobInfo::where('hrm_employee_id',$request->id)
                                                  ->where('employee_activity',1)->first();



        if (empty($employee_jobinfo)){
             return response::json(array(
                'success'    => false,
                'messages'   => 'Invalid active employee!'
            ));
        }

        $check = DB::select("SELECT * FROM hrm_employee_shift
                                WHERE hrm_employee_job_info_id = $employee_jobinfo->id
                                AND start_date = '$start_date' ");

        if (!empty($check)){
             return response::json(array(
                'success'    => false,
                'messages'   => 'Already Assign This date !'
            ));
        }

        $check_data = DB::select("SELECT * FROM hrm_employee_shift
                                WHERE hrm_employee_job_info_id = $employee_jobinfo->id
                                AND end_date is null");


          if($end_date<$check_data[0]->start_date){
             return response::json(array(
                'success'    => false,
                'messages'   => 'Sorry you can not give back date,Check current shift list!'
            ));

          }

  DB::beginTransaction();

        try {


            DB::update("UPDATE hrm_employee_shift
                        SET hrm_employee_shift.end_date     = '$end_date',
                            hrm_employee_shift.comment      = 'WrongToCorrectShiftPermanent',
                            hrm_employee_shift.users_id     = $user_id,
                            hrm_employee_shift.updated_at   = '$ldate'
                            WHERE hrm_employee_shift.end_date is null
                            AND   hrm_employee_shift.hrm_employee_job_info_id = $employee_jobinfo->id");


            $insert_shift  = new HrmEmployeeShift;
            $insert_shift->hrm_employee_job_info_id = $employee_jobinfo->id;
            $insert_shift->hrm_shift_id             = $request->shift;
            $insert_shift->start_date               = $start_date;
            $insert_shift->valid                    = 1;
            $insert_shift->comment                  = 'WrongToCorrectShiftPermanent';
            $insert_shift->users_id                 = Auth::user()->id;
            $insert_shift->save();



            DB::commit();



            // if(date('Y-m-d')>= $start_date){

            //     $attendance = new AttendanceDataProcessController();
            //     $attendance->attandanceProcess($employee_jobinfo->hrm_location_id,$start_date,$employee_jobinfo->hrm_employee_id);

            // }

            $fdate = $start_date;
            $tdate = date('Y-m-d');;
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime1->diff($datetime2);
            $days_total = $interval->format('%a');
            $duration = $days_total+1;

            $this->attentanceCurrentProcess($employee_jobinfo->hrm_location_id,$start_date,$employee_jobinfo->hrm_employee_id,$duration);





            } catch (\Exception $e) {
                DB::rollback();
                return Response::json(array(
                    'success'           => false,
                    'error_messages'    => true,
                    'messages'          => "insert problem !! " . $e->getMessage()
                ));
            }
            return response::json(array(
                    'success'    => true,
                    'messages'   => 'Employee Shift successfully Changed!'
            ));

    }







   public function changeemployeeshift(Request $request)
    {


        $user_id = Auth::user()->id;
        $ldate   = date('Y-m-d H:i:s');

        $validator = Validator::make($request->all(), [
            'employee_name'  => 'required',
            'working_shift'  => 'required',
            'start_date'     => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect('change_employeeshift')
                        ->withErrors($validator)
                        ->withInput();
        }

        $start_date           = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
        $xmasDay              = new DateTime($start_date.'- 1 day');
        $end_date             = $xmasDay->format('Y-m-d');
        $employee_jobinfo     = HrmEmployeeJobInfo::where('hrm_employee_id',$request->employee_name)
                                                  ->where('employee_activity',1)->first();



        if (empty($employee_jobinfo)){


            return response::json(array(
                'success'   => false,
                'messages'  => 'Invalid active employee!'
            ));

        }

        $check = DB::select("SELECT * FROM hrm_employee_shift
                                WHERE hrm_employee_job_info_id = $employee_jobinfo->id
                                AND start_date = '$start_date' ");

        if (!empty($check)){

            return response::json(array(
                'success'   => false,
                'messages'  => 'Already Assign This date !'
            ));


        }

        $check_data = DB::select("SELECT * FROM hrm_employee_shift
                                WHERE hrm_employee_job_info_id = $employee_jobinfo->id
                                AND end_date is null");


          if($end_date<$check_data[0]->start_date){
            return response::json(array(
                'success'   => false,
                'messages'  => 'Sorry you can not give back date,Check current shift list!'
            ));

          }

        DB::beginTransaction();

        try {

            DB::update("UPDATE hrm_employee_shift
                        SET hrm_employee_shift.end_date     = '$end_date',
                            hrm_employee_shift.comment      = 'from shift change',
                            hrm_employee_shift.users_id     = $user_id,
                            hrm_employee_shift.updated_at   = '$ldate'
                            WHERE hrm_employee_shift.end_date is null
                            AND   hrm_employee_shift.hrm_employee_job_info_id = $employee_jobinfo->id");


            $insert_shift  = new HrmEmployeeShift;
            $insert_shift->hrm_employee_job_info_id = $employee_jobinfo->id;
            $insert_shift->hrm_shift_id             = $request->working_shift;
            $insert_shift->start_date               = $start_date;
            $insert_shift->valid                    = 1;
            $insert_shift->comment                  = 'changeemployeeshift';
            $insert_shift->users_id                 = Auth::user()->id;
            $insert_shift->save();


            DB::commit();


            $this->recordActivity(
                 1,
                 'Created Change Employee Shift',
                 $insert_shift,
                 $insert_shift->id,
                 'hrm_employee_shift'
            );

            // if(date('Y-m-d')>= $start_date){

            //     $attendance = new AttendanceDataProcessController();
            //     $attendance->attandanceProcess($employee_jobinfo->hrm_location_id,$start_date,$employee_jobinfo->hrm_employee_id);

            // }


            $fdate = $start_date;
            $tdate = date('Y-m-d');;
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime1->diff($datetime2);
            $days_total = $interval->format('%a');
            $duration = $days_total+1;

            $this->attentanceCurrentProcess($employee_jobinfo->hrm_location_id,$start_date,$employee_jobinfo->hrm_employee_id,$duration);





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
                'messages'  => 'Successfully shift change'
            ));


    }




   public function multipleshiftchange(Request $request)
    {
       // dd($request->all());

        $user_id   = Auth::user()->id;
        $ldate   = date('Y-m-d H:i:s');

        $validator = Validator::make($request->all(), [
            'id'       => 'required',
            'shift'    => 'required',
            'eff_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response::json(array(
                'success'    => false,
                'messages'   => 'Please Check Input Option!'
            ));
        }

        $start_date           = date('Y-m-d', strtotime(str_replace('/', '-', $request->eff_date)));
        $xmasDay              = new DateTime($start_date.'- 1 day');
        $end_date             = $xmasDay->format('Y-m-d');
        $employee_jobinfo     =  HrmEmployeeJobInfo::find($request->id);

        if (empty($employee_jobinfo)){
            $request->session()->flash('alert-danger', 'Invalid active employee!');
            return redirect('changeshift_multiple')
                        ->withErrors($validator)
                        ->withInput();
        }

        $check = DB::select("SELECT * FROM hrm_employee_shift
                            WHERE hrm_employee_job_info_id = $request->id
                            AND start_date = '$start_date' ");

        if (!empty($check)){
            return response::json(array(
                'success'    => false,
                'messages'   => 'Already Assign This date !'
            ));
        }

        $check_data = DB::select("SELECT * FROM hrm_employee_shift
                            WHERE hrm_employee_job_info_id = $request->id
                            AND end_date is null");

        if($end_date<$check_data[0]->start_date){
            return response::json(array(
                'success'    => false,
                'messages'   => 'Sorry you can not give back date,Check current shift'
            ));
        }


        DB::beginTransaction();

        try {


                DB::update("UPDATE hrm_employee_shift
                            SET end_date = '$end_date',
                                comment='from multiple shift change single',
                                users_id=$user_id,
                                updated_at='$ldate'
                            WHERE hrm_employee_job_info_id = $request->id
                            AND end_date is null");


                $insert_shift  = new HrmEmployeeShift;
                $insert_shift->hrm_employee_job_info_id = $request->id;
                $insert_shift->hrm_shift_id             = $request->shift;
                $insert_shift->start_date               = $start_date;
                $insert_shift->valid                    = 1;
                $insert_shift->comment                  = 'multipleshiftchange';
                $insert_shift->users_id                 = Auth::user()->id;
                $insert_shift->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 'Updated Employee Shift Changed',
                 $insert_shift,
                 $insert_shift->id,
                 'hrm_employee_shift'
            );


            // if(date('Y-m-d')>= $start_date){

            //     $attendance = new AttendanceDataProcessController();
            //     $attendance->attandanceProcess($employee_jobinfo->hrm_location_id,$start_date,$employee_jobinfo->hrm_employee_id);

            // }



            $fdate = $start_date;
            $tdate = date('Y-m-d');;
            $datetime1 = new DateTime($fdate);
            $datetime2 = new DateTime($tdate);
            $interval = $datetime1->diff($datetime2);
            $days_total = $interval->format('%a');
            $duration = $days_total+1;

            $this->attentanceCurrentProcess($employee_jobinfo->hrm_location_id,$start_date,$employee_jobinfo->hrm_employee_id,$duration);


            } catch (\Exception $e) {
                DB::rollback();
                return Response::json(array(
                    'success'           => false,
                    'error_messages'    => true,
                    'messages'          => "insert problem !! " . $e->getMessage()
                ));
            }


        // $request->session()->flash('alert-success', 'Employee Shift successfully Changed!');
        // return Redirect::to('employeeshiftlist');

        return response::json(array(
            'success'    => true,
            'messages'   => 'Successfully Changed'
        ));


    }

   public function submitmultiple_shiftchange(Request $request)
    {

        // $user_id   = Auth::user()->id;
        // $ldate   = date('Y-m-d H:i:s');

        // $validator = Validator::make($request->all(), [
        //     'id'  => 'required',

        // ]);

        // if ($validator->fails()) {
        //     return redirect('changeshift_multiple')
        //                 ->withErrors($validator)
        //                 ->withInput();
        // }

        // $count_row  = count($request->id);

        // for($r = 0; $r <$count_row; $r++) {


        //     $id            = $request->id[$r];
        //     $effect_date   = $request->effect_date[$request->id[$r]];
        //     $new_shift       =$request->new_shift[$r];


        //     $start_date           = date('Y-m-d', strtotime(str_replace('/', '-', $effect_date)));
        //     $xmasDay              = new DateTime($start_date.'- 1 day');
        //     $end_date             = $xmasDay->format('Y-m-d');
        //     $employee_jobinfo     = HrmEmployeeJobInfo::where('id',$id)
        //                                               ->where('employee_activity',1)->first();

        //     if (empty($employee_jobinfo)){
        //         $request->session()->flash('alert-danger', 'Invalid active employee!');
        //         return redirect('changeshift_multiple')
        //                     ->withErrors($validator)
        //                     ->withInput();
        //     }

        //     $check = DB::select("SELECT * FROM hrm_employee_shift
        //                             WHERE hrm_employee_job_info_id = $id
        //                             AND start_date = '$start_date' ");

        //     if (!empty($check)){
        //         $request->session()->flash('alert-danger', 'Already Assign This date !');
        //         return redirect('changeshift_multiple')
        //                     ->withErrors($validator)
        //                     ->withInput();
        //     }

        //     $check_data = DB::select("SELECT * FROM hrm_employee_shift
        //                             WHERE hrm_employee_job_info_id = $id
        //                             AND end_date is null");

        //   if($end_date<$check_data[0]->start_date){
        //      $request->session()->flash('alert-danger', 'Sorry you can not give back date,Check current shift list!');
        //         return redirect('employeeshiftlist')
        //                     ->withErrors($validator)
        //                     ->withInput();
        //   }




        //     DB::update("UPDATE hrm_employee_shift
        //             SET end_date = '$end_date',
        //                 comment  = 'from multiple shift change',
        //                 users_id = $user_id,
        //                 updated_at = '$ldate'
        //             WHERE hrm_employee_job_info_id = $id
        //             AND end_date is null");


        //     $insert_shift  = new HrmEmployeeShift;
        //     $insert_shift->hrm_employee_job_info_id = $id;
        //     $insert_shift->hrm_shift_id             = $new_shift;
        //     $insert_shift->start_date               = $start_date;
        //     $insert_shift->valid                    = 1;
        //     $insert_shift->comment                  = 'submitmultiple_shiftchange';
        //     $insert_shift->users_id                 = Auth::user()->id;
        //     $insert_shift->save();




        // }

        // $request->session()->flash('alert-success', 'data has been successfully added!');
        // return Redirect::to('employeeshiftlist');

    }



   public function submitprechangeemployeeshift(Request $request)
    {


         $validator = Validator::make($request->all(), [
            'employee_name'  => 'required',
            'working_shift'  => 'required',
            'start_date'     => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect('employeeshiftchange')
                        ->withErrors($validator)
                        ->withInput();
        }

            $punch_date           = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));

            $isExist=DB::select("SELECT id FROM hrm_change_employee_shift WHERE hrm_employee_id=$request->employee_name AND punch_date='$punch_date' ");

            if (!empty($isExist)){
                session()->flash('alert-danger', 'Duplicate Entry!!Please Check.');
                return Redirect::to('employeeshiftchange');
            }

        DB::beginTransaction();

        try {


                $insert_shift  = new HrmChangeEmployeeShift;
                $insert_shift->hrm_employee_id          = $request->employee_name;
                $insert_shift->hrm_shift_id             = $request->working_shift;
                $insert_shift->punch_date               = $punch_date;

                $insert_shift->save();



            DB::commit();

            $employee_jobinfo     = HrmEmployeeJobInfo::where('hrm_employee_id',$request->employee_name)
                                                  ->where('employee_activity',1)->first();

            // if(date('Y-m-d')>= $punch_date){

            //     $attendance = new AttendanceDataProcessController();
            //     $attendance->attandanceProcess($employee_jobinfo->hrm_location_id,$punch_date,$employee_jobinfo->hrm_employee_id);

            // }


            $this->attentanceCurrentProcess($employee_jobinfo->hrm_location_id,$punch_date,$employee_jobinfo->hrm_employee_id,1);


            } catch (\Exception $e) {
                DB::rollback();
                return Response::json(array(
                    'success'           => false,
                    'error_messages'    => true,
                    'messages'          => "insert problem !! " . $e->getMessage()
                ));
            }

            $this->recordActivity(
                 1,
                 'Created Previous Employee Shift Change',
                 $insert_shift,
                 $insert_shift->id,
                 'hrm_change_employee_shift'
            );

            $request->session()->flash('alert-success', 'Employee Shift successfully Changed!');
            return Redirect::to('employeeshiftchange');

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


    public function cancel(Request $request,$id){

       // DB::table('hrm_change_employee_shift')->where('id', '=', $id)->delete();

        $preShift = HrmChangeEmployeeShift::query()->findOrFail($id);

        $preShift->delete();

        $this->recordActivity(
             1,
             'Deleted Previous Employee Shift Change',
             $preShift,
             $id,
             'hrm_change_employee_shift'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('employeeshiftchange');

    }





    public function attentanceCurrentProcess($hrm_location_id,$processDate,$hrm_employee_id,$duration){

        // var_dump($duration);

        for ($x = 0; $x < $duration; $x++) {
            $pushProcess = date('Y-m-d', strtotime($processDate. ' + '.$x.'days'));
            if(date('Y-m-d')< $pushProcess){
                break;
            }
            $attendance = new AttendanceDataProcessController();
            $attendance->attandanceProcess($hrm_location_id,$pushProcess,$hrm_employee_id);

            // var_dump($pushProcess);
            // var_dump($x);
        }


    }


}
