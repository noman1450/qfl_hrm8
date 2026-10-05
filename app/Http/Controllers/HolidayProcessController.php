<?php

namespace App\Http\Controllers;

use Redirect;
use Validator;
use App\Models\HrmLeaveYear;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeHoliday;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\HrmEmployeeHolidayDetails;

class HolidayProcessController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('holiday_process.employee_holiday_list')->with('default_user_location',  $default_user_location);;
    }

    public function create()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('holiday_process.create_employee_holiday')
            ->with('leavetypeyear',HrmLeaveYear::where('active_status', 1)->orderBy('id', 'desc')->get())
            ->with('default_user_location',  $default_user_location) ;
    }


    public function approvedholidaylist()
    {
        $user_id = Auth::id();

        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('holiday_process.holiday_processlist', compact('default_user_location'));
    }

    public function employeeholiday_list()
    {
            $location    = request('location');
            $user_id     = Auth::user()->id;
            $holidaylist = DB::select("SELECT
                                            a.id,
                                            a.date_from,
                                            a.date_to,
                                            (DATEDIFF(a.date_to, a.date_from) + 1) AS days,
                                            a.hrm_holiday_id,
                                            a.`hrm_leave_years_id`,
                                            IF(a.apply_type = 1,
                                                'All Employee',
                                                'Selected Employee') AS apply_type,
                                            b.holiday_name,
                                            c.leave_year,
                                            d.location_name,
                                            GROUP_CONCAT(CONCAT(hh.employee_name, ' | ', h.employee_code)
                                                ORDER BY hh.employee_name ASC
                                                SEPARATOR '<br>') AS employee_name,
                                            GROUP_CONCAT(CONCAT(k.depertment_name)
                                                ORDER BY k.depertment_name ASC
                                                SEPARATOR '<br>') AS department_name,
                                            GROUP_CONCAT(CONCAT(l.designation_name)
                                                ORDER BY l.designation_name ASC
                                                SEPARATOR '<br>') AS designation_name,
                                            GROUP_CONCAT(CONCAT(m.plant_name)
                                                ORDER BY m.plant_name ASC
                                                SEPARATOR '<br>') AS plant_name
                                        FROM
                                            hrm_employee_holiday a
                                                JOIN
                                            hrm_holiday b ON b.id = a.hrm_holiday_id AND a.valid = 1
                                                AND a.hrm_location_id = $location
                                                JOIN
                                            hrm_leave_years c ON c.id = a.hrm_leave_years_id
                                                AND a.process_status = 1
                                                JOIN
                                            hrm_location d ON a.hrm_location_id = d.id
                                                JOIN
                                            user_location e ON a.hrm_location_id = e.hrm_location_id
                                                AND e.users_id = $user_id
                                                LEFT JOIN
                                            hrm_employee_holiday_details f ON a.id = f.hrm_employee_holiday_id
                                                LEFT JOIN
                                            hrm_employee_job_info h ON h.hrm_employee_id = f.hrm_employee_id
                                                AND h.employee_activity = 1
                                                LEFT JOIN
                                            hrm_employee hh ON h.hrm_employee_id = hh.id
                                                LEFT JOIN
                                            hrm_employee_shift i ON h.id = i.hrm_employee_job_info_id
                                                AND i.end_date IS NULL
                                                LEFT JOIN
                                            hrm_shift j ON i.hrm_shift_id = j.id
                                                LEFT JOIN
                                            hrm_depertment k ON h.hrm_depertment_id = k.id
                                                LEFT JOIN
                                            hrm_designation l ON h.hrm_designation_id = l.id
                                                LEFT JOIN
                                            hrm_plant m ON h.hrm_plant_id = m.id
                                        GROUP BY a.id , a.date_from , a.date_to , a.hrm_holiday_id , a.hrm_leave_years_id , b.holiday_name , c.leave_year , a.apply_type , d.location_name ");

            return json_encode(array('data' => $holidaylist));

    }





    public function holidayfor_selectedemployee(Request $request){

        // $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));

        $condition    = "a.active_status=1";
        // $punch_date   = $request->punch_date;

        if ($request->location != 0){
          $condition  = " b.hrm_location_id = ".$request->location;
        }

        if ($request->working_shift_id != 0){
          $condition  =  $condition . " AND d.id = ".$request->working_shift_id ;
        }

        if ($request->department_id != 0){
          $condition  =  $condition . " AND e.id = ".$request->department_id ;
        }

        if ($request->designation_id != 0){
          $condition  =  $condition . " AND f.id = ".$request->designation_id ;
        }

        if ($request->plant_name != 0){
          $condition  =  $condition . " AND b.hrm_plant_id = ".$request->plant_name ;
        }


        if ($request->section_id != 0){
          $condition  =  $condition . " AND b.hrm_section_id = ".$request->section_id ;
        }

        $data   = DB::select("SELECT
                                a.id,
                                concat(a.employee_name,' | ',b.employee_code) as employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                DATE_FORMAT(c.start_date, '%d-%m-%Y') as last_change,
                                d.start_time,
                                d.end_time,
                                d.shift_name
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
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

   public function holiday_processlist()
    {
        $location = request('location');

        $user_id = Auth::id();

        $holidaylist= DB::select("SELECT
                                        a.id,
                                        a.date_from,
                                        a.date_to,
                                        (DATEDIFF(a.date_to, a.date_from)+1) as days,
                                        a.hrm_holiday_id,
                                        a.hrm_leave_years_id,
                                        IF(a.apply_type = 1,
                                            'All Employee',
                                            'Selected Employee') AS apply_type,
                                        b.holiday_name,
                                        c.leave_year,
                                        d.location_name,
                                        GROUP_CONCAT(CONCAT(hh.employee_name, ' | ', h.employee_code)
                                            ORDER BY hh.employee_name ASC
                                            SEPARATOR '<br>') AS employee_name,
                                        GROUP_CONCAT(CONCAT(k.depertment_name)
                                            ORDER BY k.depertment_name ASC
                                            SEPARATOR '<br>') AS department_name,
                                        GROUP_CONCAT(CONCAT(l.designation_name)
                                            ORDER BY l.designation_name ASC
                                            SEPARATOR '<br>') AS designation_name,
                                        GROUP_CONCAT(CONCAT(m.plant_name)
                                            ORDER BY m.plant_name ASC
                                            SEPARATOR '<br>') AS plant_name
                                    FROM
                                        hrm_employee_holiday a
                                            JOIN
                                        hrm_holiday b ON b.id = a.hrm_holiday_id AND a.valid = 1
                                            JOIN
                                        hrm_leave_years c ON c.id = a.hrm_leave_years_id
                                            AND a.process_status = 2
                                            JOIN
                                        hrm_location d ON a.hrm_location_id = d.id
                                            AND d.id = $location
                                            JOIN
                                        user_location e ON a.hrm_location_id = e.hrm_location_id
                                            AND e.users_id = $user_id
                                            LEFT JOIN
                                        hrm_employee_holiday_details f ON a.id = f.hrm_employee_holiday_id
                                            LEFT JOIN
                                        hrm_employee_job_info h ON h.hrm_employee_id = f.hrm_employee_id
                                            AND h.employee_activity = 1
                                            LEFT JOIN
                                        hrm_employee hh ON h.hrm_employee_id = hh.id
                                            LEFT JOIN
                                        hrm_employee_shift i ON h.id = i.hrm_employee_job_info_id
                                            AND i.end_date IS NULL
                                            LEFT JOIN
                                        hrm_shift j ON i.hrm_shift_id = j.id
                                            LEFT JOIN
                                        hrm_depertment k ON h.hrm_depertment_id = k.id
                                            LEFT JOIN
                                        hrm_designation l ON h.hrm_designation_id = l.id
                                            LEFT JOIN
                                        hrm_plant m ON h.hrm_plant_id = m.id
                                    GROUP BY a.id , a.date_from , a.date_to , a.hrm_holiday_id , a.hrm_leave_years_id , b.holiday_name , c.leave_year , a.apply_type , d.location_name");



        return json_encode(array('data' => $holidaylist));
    }

    public function store(Request $request)
    {


        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'location_name'  => 'required',
            'year'           => 'required',
            'holiday_name'   => 'required',
            'date_from'      => 'required',
            'date_to'        => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('holiday_process/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $date_from     = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to       = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));


        $insert = new HrmEmployeeHoliday;
        $insert->hrm_location_id    = $request->location_name;
        $insert->date_from          = $date_from;
        $insert->date_to            = $date_to;
        $insert->hrm_holiday_id     = $request->holiday_name;
        $insert->hrm_leave_years_id = $request->year;
        $insert->valid              = 1;
        $insert->apply_type         = $request->apply_type;
        $insert->process_status     = 1;
        $insert->users_id           = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Holiday Process',
             $insert,
             $insert->id,
             'hrm_employee_holiday'
        );

        if ($request->apply_type==2){
        $count_row  = count($request->id);
        for($r = 0; $r <$count_row; $r++) {

                $id            = $request->id[$r];

                $insert_details = new HrmEmployeeHolidayDetails;
                $insert_details->hrm_employee_holiday_id    = $insert->id;
                $insert_details->hrm_employee_id            = $id;
                $insert_details->save();

                $this->recordActivity(
                     1,
                     'Created Holiday Process',
                     $insert_details,
                     $insert_details->id,
                     'hrm_employee_holiday_details'
                );

            }
        }

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('holiday_process');


    }

    public function holiday_process(Request $request,$id)
    {


        $update = HrmEmployeeHoliday::find($id);
        $details = HrmEmployeeHolidayDetails::where("hrm_employee_holiday_id", $id)->get();

        if (empty($update)){
            session()->flash('alert-danger', 'Invalid Input !!');
            return Redirect()->back();
        }

        $update->process_status     = 2;
        $update->users_id           = Auth::user()->id;
        $update->save();


        $this->recordActivity(
             1,
             'Approved Holiday Process',
             null,
             $id,
             'hrm_employee_holiday'
        );

        //-------Data Process
        $diff= date_diff(date_create($update->date_from), date_create($update->date_to))->format("%a") + 1;

        for ($x = 0; $x < $diff; $x++) {
            $punch_date = date('Y-m-d', strtotime($update->date_from. ' + '.$x.'days'));
            foreach($details as $key=>$value){
                $attendance = new AttendanceDataProcessController();
                $attendance->attandanceProcess($update->hrm_location_id,$punch_date,$value->hrm_employee_id);
            }
        }

        $request->session()->flash('alert-success', 'Successfully Processed!');
        return Redirect::to('holiday_process');

        // DB::insert("INSERT INTO hrm_attendance_data (hrm_employee_id,attandance_date,punch_date,punch_time,hrm_shift_id,row_data_id,data_from)");
    }

    public function edit($id)
    {
        $holidaylist= DB::select("SELECT
                                    a.id,
                                    a.date_from,
                                    a.date_to,
                                    a.hrm_holiday_id,
                                    a.hrm_leave_years_id,
                                    a.apply_type,
                                    IF(a.apply_type = 1,
                                        'All Employee',
                                        'Selected Employee') AS apply_typess,
                                    b.holiday_name,
                                    c.leave_year,
                                    a.hrm_location_id,
                                    d.location_name
                                FROM
                                    hrm_employee_holiday a
                                        JOIN
                                    hrm_holiday b ON b.id = a.hrm_holiday_id
                                        JOIN
                                    hrm_leave_years c ON c.id = a.hrm_leave_years_id
                                        AND a.process_status = 1 AND a.id=$id
                                        JOIN
                                    hrm_location d ON a.hrm_location_id=d.id");

            $dtls_data = "";

            if ($holidaylist[0]->apply_type==2){

                $dtls_data = DB::SELECT("SELECT
                                    b.id,
                                    CONCAT(b.employee_name, ' | ', c.employee_code) AS employee_name,
                                    d.depertment_name,
                                    e.designation_name,
                                    g.shift_name
                                FROM
                                    hrm_employee_holiday_details a
                                        JOIN
                                    hrm_employee b ON a.hrm_employee_id = b.id
                                        AND a.hrm_employee_holiday_id = $id
                                        JOIN
                                    hrm_employee_job_info c ON b.id = c.hrm_employee_id
                                        AND c.employee_activity = 1
                                        JOIN
                                    hrm_depertment d ON c.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_designation e ON c.hrm_designation_id = e.id
                                        JOIN
                                    hrm_employee_shift f ON c.id = f.hrm_employee_job_info_id
                                        AND f.end_date IS NULL
                                        JOIN
                                    hrm_shift g ON f.hrm_shift_id=g.id");

            };

        return view('holiday_process.edit_employee_holiday')
             ->with('leavetypeyear',HrmLeaveYear::all())
             ->with('edit_data',$holidaylist)
             ->with('dtls_data',$dtls_data);
    }

    public function update(Request $request, $id)
    {
                // dd($request->all());
        $validator = Validator::make($request->all(), [
            'year'           => 'required',
            'holiday_name'   => 'required',
            'date_from'      => 'required',
            'date_to'        => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('holiday_process')
                        ->withErrors($validator)
                        ->withInput();
        }

        $date_from     = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to       = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));



        $insert = HrmEmployeeHoliday::find($id);
        $insert->date_from          = $date_from;
        $insert->date_to            = $date_to;
        $insert->hrm_holiday_id     = $request->holiday_name;
        $insert->hrm_leave_years_id = $request->year;
        $insert->valid              = 1;
        $insert->apply_type         = $request->apply_type;
        $insert->process_status     = 1;
        $insert->users_id           = Auth::user()->id;

        $insert->save();

        $this->recordActivity(
                     1,
                     'Updated Employee Holiday',
                     $insert->getChanges(),
                     $insert->id,
                     'hrm_employee_holiday'
                );



        if ($request->apply_type==2){

            DB::DELETE("DELETE FROM hrm_employee_holiday_details WHERE hrm_employee_holiday_id=$id");
            $count_row  = count($request->id);
            for($r = 0; $r <$count_row; $r++) {

                $id            = $request->id[$r];

                $insert_details = new HrmEmployeeHolidayDetails;
                $insert_details->hrm_employee_holiday_id    = $insert->id;
                $insert_details->hrm_employee_id            = $id;
                $insert_details->save();

                $this->recordActivity(
                     1,
                     'Updated Employee Holiday',
                     $insert_details,
                     $insert_details->id,
                     'hrm_employee_holiday_details'
                );

            }
        }




        $request->session()->flash('alert-success', 'data has been successfully Updated!');
        return Redirect::to('holiday_process');
    }

    public function holiday_process_delete(Request $request, $id)
    {
        $update = HrmEmployeeHoliday::find($id);
        $details = HrmEmployeeHolidayDetails::where("hrm_employee_holiday_id", $id)->get();

        if (empty($update)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        $update->valid     = 0;
        $update->users_id  = Auth::user()->id;
        $update->save();

        //-------Data Process
        $diff= date_diff(date_create($update->date_from), date_create($update->date_to))->format("%a") + 1;

        for ($x = 0; $x < $diff; $x++) {
            $punch_date = date('Y-m-d', strtotime($update->date_from. ' + '.$x.'days'));
            foreach($details as $key=>$value){
                $attendance = new AttendanceDataProcessController();
                $attendance->attandanceProcess($update->hrm_location_id,$punch_date,$value->hrm_employee_id);
            }
        }

        $this->recordActivity(
             1,
             'Deleted Employee Holiday',
             null,
             $id,
             'hrm_employee_holiday'
        );

        $request->session()->flash('alert-success', 'Successfully Deleted!');
        return Redirect::to('approvedholidaylist');


    }



    public function deletepending(Request $request, $id)
    {

        DB::DELETE("DELETE FROM hrm_employee_holiday_details WHERE hrm_employee_holiday_id=$id");
        DB::DELETE("DELETE FROM hrm_employee_holiday WHERE id=$id");

        $this->recordActivity(
             1,
             'Deleted Employee Holiday',
             null,
             $id,
             'hrm_employee_holiday'
        );

        $request->session()->flash('alert-success', 'Successfully Deleted!');
        return Redirect::to('holiday_process');


    }

}
