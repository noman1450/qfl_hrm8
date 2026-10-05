<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use DB;
use Datatables;
use Crypt;
use Validator;
use Config;
use Session;

use App\Models\HrmHolidayAgainstLeave;


class HolidayAgainstLeaveController extends Controller
{


    function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $month = DB::select("SELECT id,month_name FROM hrm_month");
        return view('holiday_against_leave.holiday_against_leave_list')
            ->with('month',$month);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $month=DB::Select("SELECT id,month_name FROM hrm_month");
        return view('holiday_against_leave.create_holiday_against_leave')
                   ->with('month',$month);
    }

    public function holidayagainstleavehistory()
    {
        $month=DB::Select("SELECT id,month_name FROM hrm_month");
        return view('holiday_against_leave.holiday_against_leave_history')
                   ->with('month',$month);
    }




   public function employee_wise_WorkOnHoliday(Request $request)
    {


        $year=$request->year;
        $month=$request->month;
        $employee_name=$request->employee_name;



        $employeewiseholidaylist=DB::SELECT("SELECT
                                                punche_date,
                                                DAYNAME(punche_date) AS dayName,
                                                in_time,
                                                out_time,
                                                overtime_time
                                            FROM
                                                hrm_attendance
                                            WHERE
                                                    YEAR(punche_date) = $year
                                                    AND MONTH(punche_date) = $month
                                                    AND hrm_employee_id = $employee_name
                                                    AND attendance_status IN (7 , 8)
                                                    AND in_time > '00:00:00'
                                                    AND punche_date NOT IN (SELECT
                                                        holiday_date
                                                    FROM
                                                        hrm_holiday_against_leave
                                                    WHERE
                                                            valid=1
                                                            AND hrm_employee_id = $employee_name
                                                            AND YEAR(holiday_date) = $year
                                                            AND MONTH(holiday_date) = $month)");


        return json_encode(array('data' =>$employeewiseholidaylist));
    }



   public function holidayagainstleavehistorylist()
    {

             $user_id = Auth::user()->id;

             $listdata= DB::SELECT("SELECT
                                        b.id,
                                        b.employee_name,
                                        IF(g.workonholiday IS NULL,
                                            '0',
                                            g.workonholiday) AS WorkOnHoliday,
                                        SUM((DATEDIFF(a.date_to, a.date_from) + 1)) AS takenLeave,
                                        ((IF(g.workonholiday IS NULL,
                                            '0',
                                            g.workonholiday)) - (SUM((DATEDIFF(a.date_to, a.date_from) + 1)))) AS RemainingDays,
                                            h.depertment_name,
                                            i.designation_name
                                    FROM
                                        hrm_leave_application a
                                            JOIN
                                        hrm_employee b ON b.id = a.hrm_employee_id
                                            AND b.active_status = 1
                                            JOIN
                                        hrm_employee_leave_type c ON a.hrm_employee_leave_type_id = c.id
                                            AND leave_status = 2
                                            JOIN
                                        hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                                            AND d.employee_activity = 1
                                            JOIN
                                        user_location e ON d.hrm_location_id = e.hrm_location_id
                                            AND e.users_id = 2
                                            JOIN
                                        hrm_leave_approve f ON f.hrm_leave_application_id = a.id
                                            AND f.action_type = 1
                                            AND f.forward = 2
                                            LEFT JOIN
                                        (SELECT
                                            hrm_employee_id, COUNT(id) AS workonholiday
                                        FROM
                                            hrm_holiday_against_leave
                                        GROUP BY hrm_employee_id) g ON b.id = g.hrm_employee_id
                                            JOIN
                                        hrm_depertment h ON d.hrm_depertment_id=h.id
                                            JOIN
                                        hrm_designation i ON d.hrm_designation_id=i.id
                                    GROUP BY b.id,b.employee_name,h.depertment_name,i.designation_name,g.workonholiday");

        return json_encode(array('data' =>$listdata));
    }




   public function holidayagainstleavelist(Request $request)
    {

// dd($request->all());
        $year=$request->year;
        $month=$request->month;
        $employee_name=$request->employee_name;



        if (!empty($employee_name)){
            $condition= "AND YEAR(a.holiday_date) ='". $year . "'  AND MONTH(a.holiday_date) ='". $month . "'  AND a.hrm_employee_id ='". $employee_name . "' ";

            $condition1= "AND f.hrm_employee_id = '". $employee_name . "'";

        }else{
            $condition="AND YEAR(a.holiday_date) = '". $year . "' AND MONTH(a.holiday_date) = '". $month . "' ";
            $condition1='';
        }


        $holidayagainstleavelist = DB::SELECT("SELECT
                                                a.id,
                                                DAYNAME(a.holiday_date) AS dayName,
                                                a.holiday_date,
                                                b.employee_name,
                                                a.hrm_employee_id,
                                                d.depertment_name,
                                                e.designation_name,
                                                concat(f.in_time, ' - ' ,f.out_time) as in_out_time
                                            FROM
                                                hrm_holiday_against_leave a
                                                    JOIN
                                                hrm_employee b ON a.hrm_employee_id = b.id
                                                    AND a.valid=1
                                                    $condition
                                                JOIN
                                                hrm_employee_job_info c ON a.hrm_employee_id=c.hrm_employee_id
                                                JOIN
                                                hrm_depertment d ON c.hrm_depertment_id=d.id
                                                JOIN
                                                hrm_designation e ON c.hrm_designation_id=e.id
                                                JOIN
                                                hrm_attendance f ON  a.hrm_employee_id = f.hrm_employee_id
                                                    AND a.holiday_date=f.punche_date
                                                    $condition1 ");


        return json_encode(array('data' =>$holidayagainstleavelist));
    }







    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'employee_name'   => 'required',
            'selecteddate'   => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('holidayagainstleave/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $count_row = count($request->selecteddate);


     // DB::beginTransaction();
     //    try{
                if (!empty($count_row)){

                    foreach ($request->selecteddate as $keys ) {

                        $holiday_date       = date('Y-m-d', strtotime(str_replace('/', '-', $keys)));

                        $insert = new HrmHolidayAgainstLeave;
                        $insert->hrm_employee_id        = $request->employee_name;
                        $insert->holiday_date           = $holiday_date;
                        $insert->valid                  = 1;
                        $insert->users_id               = Auth::user()->id;

                        $insert->save();


                        $this->recordActivity(
                             1,
                             'Created Holiday Against Leave',
                             null,
                             $insert->id,
                             'hrm_holiday_against_leave'
                        );

                        $query= DB::SELECT("SELECT overtime_time  FROM hrm_attendance WHERE hrm_employee_id = $request->employee_name AND punche_date='$holiday_date'");

                        $overtime=$query[0]->overtime_time;

                        // dd($overtime);
                        if($overtime>='08:00:00'){
                                DB::update("UPDATE hrm_attendance SET overtime_time= TIMEDIFF('$overtime','08:00:00')
                                WHERE hrm_employee_id = $request->employee_name AND punche_date='$holiday_date'");
                        }else{
                                DB::update("UPDATE hrm_attendance SET overtime_time= '00:00:00'
                                WHERE hrm_employee_id = $request->employee_name AND punche_date='$holiday_date'");
                        }


                    }


                    $request->session()->flash('alert-success', 'data has been successfully added!');
                    return Redirect::to('holidayagainstleave');

                 }
        // DB::commit();
        // }catch (\Exception $e) {
        //     DB::rollback();
        //     $validator->errors()->add('field', $e->getMessage());
        //     return response()->json($validator->errors()->all());
        // }


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



    public function destroy($id)
    {

    }


   public function cancel(Request $request,$id){

        $cancel = HrmHolidayAgainstLeave::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid !!');
            return Redirect()->back();
        }

        $cancel->valid              = 0;
        $cancel->users_id           = Auth::user()->id;
        $cancel->save();


        $this->recordActivity(
             1,
             'Deleted Holiday Against Leave',
             null,
             $id,
             'hrm_holiday_against_leave'
        );


        // DB::table('hrm_holiday_against_leave')->where('id', '=', $id)->delete();

        $request->session()->flash('alert-success', 'Successfully deleted,This Date Attendance Process Again.');
        return Redirect::to('holidayagainstleave');

    }



}
