<?php

namespace App\Http\Controllers;

use Crypt;
use Config;
use Session;
use App\User;
use DateTime;
use Redirect;
use Response;
use Validator;
use Datatables;
use App\Models\HrmShiftRole;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeShift;

use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use App\Models\HrmShiftRoleDuration;
use App\Models\HrmShiftRoleEmployee;
use Illuminate\Support\Facades\Auth;

//

class ShiftRoleEmployeeController extends Controller
{


    function __construct()
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
        return view('shiftrole_employee.shiftrole_employee');
    }



    public function remaining_shiftrole_data(Request $request)
    {


        if ($request->shiftrole == null) {
            $hrm_location_id = 0;
        } else {
            $shiftrole       = $request->shiftrole;
            $hrm_shift_role  =  HrmShiftRole::find($shiftrole);
            $hrm_location_id = $hrm_shift_role->hrm_location_id;
        }


        $list           = DB::select("SELECT
                                a.id,
                                a.employee_name,
                                e.depertment_name,
                                f.designation_name,
                                g.location_name,
                                d.start_time,
                                d.end_time,
                                d.shift_name,
                                b.employee_code as card_code
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
                                    AND a.id NOT IN (SELECT hrm_employee_id FROM hrm_shift_role_employee)
                                    AND b.hrm_location_id = $hrm_location_id
                                    LEFT JOIN
                                hrm_employee_card_code h ON b.id=h.hrm_employee_job_info_id
                                    JOIN
                                hrm_employee_shift c ON c.hrm_employee_job_info_id = b.id And c.end_date is null
                                    JOIN
                                hrm_shift d ON c.hrm_shift_id = d.id
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id");
        return json_encode(array('data' => $list));
    }


    public function url_to_delete_rows(Request $request)
    {

        dd("NOMAN");
    }


    public function shiftrolewise_employeelist(Request $request)
    {

        $user_id = Auth::user()->id;

        if ($request->shiftrole == null) {
            $condition = "";
        } else {
            $condition = " And h.hrm_shift_role_id=$request->shiftrole ";
        }

        $list           = DB::select("SELECT
                                h.id,
                                a.employee_name,
                                e.depertment_name,
                                f.alis as designation_name,
                                g.location_name,
                                concat(d.start_time, '-' , d.end_time) as shift_time,
                                hh.shift_role_name,
                                d.shift_name,
                                b.employee_code as card_code
                            FROM
                                hrm_employee a
                                    JOIN
                                hrm_employee_job_info b ON b.hrm_employee_id = a.id and b.employee_activity=1
                                    JOIN
                                hrm_employee_card_code i ON b.id=i.hrm_employee_job_info_id
                                    JOIN
                                hrm_shift_role_employee h ON a.id = h.hrm_employee_id
                                $condition
                                    JOIN
                                hrm_shift_role hh ON h.hrm_shift_role_id=hh.id
                                    JOIN
                                hrm_employee_shift c ON c.hrm_employee_job_info_id = b.id And c.end_date is null
                                    JOIN
                                hrm_shift d ON c.hrm_shift_id = d.id
                                    JOIN
                                hrm_depertment e ON b.hrm_depertment_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                hrm_location g ON b.hrm_location_id = g.id
                                    JOIN
                                user_location j ON g.id = j.hrm_location_id AND j.users_id = $user_id");
        return json_encode(array('data' => $list));
    }



    public function shiftrole_currentshift(Request $request)
    {

        $user_id = Auth::user()->id;

        $list = DB::select("SELECT a.hrm_shift_role_id,
                                   a.hrm_shift_id,
                                   c.shift_name,
                                   concat(b.shift_role_name,' || ',d.location_name) as shift_role_name,
                                   concat(c.start_time, '  -  ' ,c.end_time) as shift_time,
                                   a.start_date,
                                   c.start_time ,
                                   c.end_time
                                   FROM
                                   hrm_shift_role_duration a
                                   JOIN hrm_shift_role b ON a.hrm_shift_role_id=b.id
                                   JOIN hrm_shift c ON a.hrm_shift_id=c.id
                                   JOIN hrm_location d ON b.hrm_location_id=d.id
                                   JOIN user_location e ON d.id = e.hrm_location_id AND e.users_id = $user_id
                                   WHERE a.end_date is null");

        return json_encode(array('data' => $list));
    }
    public function previou_shiftrole_assign(Request $request)
    {
        return view('shiftrole_employee.previous_assignrole_list');
    }

    public function previous_shiftrole_assign_list(Request $request)
    {


        // dd($request->all());

        $condition = "";

        if ($request->shiftrole <> '') {
            $condition  =  $condition . " AND a.hrm_shift_role_id = " . $request->shiftrole;
        }
        if ($request->start_date <> '') {
            $start_date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
            $condition  =  $condition . " AND a.start_date = '$start_date'";
        }
        //
        if ($request->end_date <> '') {
            $end_date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->end_date)));
            $condition  =  $condition . " AND a.end_date = '$end_date'";
        }


        $user_id = Auth::user()->id;

        $list = DB::select("SELECT a.hrm_shift_role_id,
                                   a.hrm_shift_id,
                                   c.shift_name,
                                   concat(b.shift_role_name,' || ',d.location_name) as shift_role_name,
                                   concat(c.start_time, '  -  ' ,c.end_time) as shift_time,
                                   a.start_date,
                                   a.end_date
                                   FROM
                                   hrm_shift_role_duration a
                                   JOIN hrm_shift_role b ON a.hrm_shift_role_id=b.id AND  a.end_date is not null
                                   $condition
                                   JOIN hrm_shift c ON a.hrm_shift_id=c.id
                                   JOIN hrm_location d ON b.hrm_location_id=d.id
                                   JOIN user_location e ON d.id = e.hrm_location_id AND e.users_id = $user_id

                                   ");

        return json_encode(array('data' => $list));
    }




    public function shiftroleassigntoemployee(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'shiftrole'       => 'required',
            'working_shift'   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('shiftroleassign')
                ->withErrors($validator)
                ->withInput();
        }

        $user_id = Auth::user()->id;
        $ldate   = date('Y-m-d H:i:s');



        $apply_date   = date('Y-m-d', strtotime(str_replace('/', '-', $request->apply_date)));
        $xmasDay      = new DateTime($apply_date . '- 1 day');
        $end_date     = $xmasDay->format('Y-m-d');
        $shift        = DB::table('hrm_shift_role_employee')->where('hrm_shift_role_id', $request->shiftrole)->first();

        if (empty($shift)) {
            $request->session()->flash('alert-danger', "No Employee found this shift role !!");
            return Redirect::to('shiftroleassign');
        }

        $check = DB::SELECT("SELECT id FROM hrm_shift_role_duration WHERE hrm_shift_role_id = $request->shiftrole AND  start_date='$apply_date' AND end_date is Null ");

        if (!empty($check)) {
            $request->session()->flash('alert-danger', "Sorry this role already assign in the selected date !!");
            return Redirect::to('shiftroleassign');
        }

        $check_data = DB::SELECT("SELECT start_date FROM hrm_shift_role_duration WHERE hrm_shift_role_id = $request->shiftrole  AND end_date is Null ");
        if (!empty($check_data)) {
            if ($end_date < $check_data[0]->start_date) {
                $request->session()->flash('alert-danger', "Sorry back date not accepted,please check list!!");
                return Redirect::to('shiftroleassign');
            }
        }



        DB::update("UPDATE hrm_shift_role_duration SET end_date = '$end_date'
                    WHERE hrm_shift_role_id = $request->shiftrole AND end_date is null");


        DB::beginTransaction();

        try {


            $insert     = new HrmShiftRoleDuration;
            $insert->hrm_shift_role_id   = $request->shiftrole;
            $insert->hrm_shift_id        = $request->working_shift;
            $insert->start_date          = $apply_date;
            $insert->valid               = 1;
            $insert->save();


            $query = DB::SELECT("SELECT b.id,b.employee_code
                             FROM  hrm_shift_role_employee a
                             JOIN hrm_employee_job_info b
                             ON a.hrm_employee_id=b.hrm_employee_id and b.employee_activity=1
                             WHERE a.hrm_shift_role_id = $request->shiftrole");

            $count_row  = count($query);


            for ($r = 0; $r < $count_row; $r++) {

                $return = 0;


                $row = $query[$r]->id;
                $code = $query[$r]->employee_code;
                // $row = $query[5]->id;
                // $code = $query[5]->employee_code;

                // dd($row);
                $check = DB::select("SELECT * FROM hrm_employee_shift
                                WHERE hrm_employee_job_info_id = $row
                                AND start_date = '$apply_date' ");
                if (!empty($check)) {
                    $return = 1;
                }


                $check_data = DB::select("SELECT * FROM hrm_employee_shift
                                    WHERE hrm_employee_job_info_id =  $row
                                    AND end_date is null");

                if ($end_date < $check_data[0]->start_date) {
                    $return = 1;
                }


                if ($return == 0) {


                    DB::update("UPDATE hrm_employee_shift
                            SET end_date = '$end_date',
                            comment='from shift role',
                            users_id= $user_id,
                            updated_at='$ldate'
                            WHERE hrm_employee_job_info_id =  $row
                            AND end_date is null");



                    $insert_shift  = new HrmEmployeeShift;
                    $insert_shift->hrm_employee_job_info_id = $row;
                    $insert_shift->hrm_shift_id             = $request->working_shift;
                    $insert_shift->start_date               = $apply_date;
                    $insert_shift->valid                    = 1;
                    $insert_shift->comment                  = 'Shift Role New Assign';
                    $insert_shift->users_id                 = Auth::user()->id;

                    $insert_shift->save();
                }

                $this->recordActivity(
                    1,
                    'Created Shift Role Assign',
                    $insert_shift,
                    $insert_shift->id,
                    'hrm_employee_shift'
                );
            }


            DB::commit();


        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => "insert problem !! " . $e->getMessage()
            ));
        }

        $request->session()->flash('alert-success', 'Data has been successfully Process !');
        return Redirect::to('shiftroleassign');
    }

    public function shiftroleemployeelist()
    {
        return view('shiftrole_employee.shiftroleemployee_list');
    }


    public function getcurrentshift(Request $request)
    {

        $shift = DB::SELECT("SELECT
                        concat(c.shift_name,' | ',c.start_time) AS shift_name,c.id
                    FROM
                        hrm_shift_role a
                        JOIN
                        hrm_shift_role_duration b ON a.id = b.hrm_shift_role_id
                        AND a.id = $request->shiftrole AND b.end_date IS NULL
                        JOIN
                        hrm_shift c ON b.hrm_shift_id = c.id");
        return json_encode($shift);
    }


    public function shiftroleassign()
    {
        return view('shiftrole_employee.assignrole_list');
    }

    public function shiftrole_assign()
    {
        return view('shiftrole_employee.shiftroleassign');
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
        // dd($request->all());


        $count_row  = count($request->id);

        if (empty($count_row)) {
            // $request->session()->flash('alert-success', 'data has been successfully save!');
            $request->session()->flash('alert-danger', "you can't submit without employee !!");
            return Redirect::to('shiftroleemployee');
        }

        for ($r = 0; $r < $count_row; $r++) {

            $employee_id    = $request->id[$r];
            $insert         = new HrmShiftRoleEmployee;
            $insert->hrm_employee_id          = $employee_id;
            $insert->hrm_shift_role_id        = $request->shiftrole;
            $insert->save();

            // dd($insert->save());
        }


        $this->recordActivity(
            1,
            'Created Employee Shift Role',
            $insert,
            $insert->id,
            'hrm_shift_role_employee'
        );

        $request->session()->flash('alert-success', 'data has been successfully saved!');
        return Redirect::to('shiftroleemployee');
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
    public function cancel($id)
    {
        //dd($id);

        //DB::table('hrm_shift_role_employee')->where('id', '=', $id)->delete();

        // $request->session()->flash('alert-success', 'successfully deleted !');
        // return Redirect::to('shiftroleemployeelist');

        $RoleEmployee = HrmShiftRoleEmployee::query()->findOrFail($id);
        $RoleEmployee->delete();

        $this->recordActivity(
            1,
            'Deleted Shift Role Wise Employee',
            $RoleEmployee,
            $RoleEmployee->id,
            'hrm_shift_role_employee'
        );
        return Response::json(array('massages' => true));
    }

    public function delete_all(Request $request)
    {

        //dd($request->all());

        $count_row  = count($request->id);
        if (empty($count_row)) {
            $request->session()->flash('alert-danger', "you can't submit without employee !!");
            return Redirect::to('shiftroleemployee');
        }

        for ($r = 0; $r < $count_row; $r++) {
            $id = $request->id[$r];
            DB::table('hrm_shift_role_employee')->where('id', '=', $id)->delete();

            /*$RoleEmployee = HrmShiftRoleEmployee::query()->findOrFail($request->id);
            $RoleEmployee->delete();*/
        }
        $this->recordActivity(
            1,
            'Deleted Multiple Shift Role Wise Employee',
            null,
            $request->id,
            'hrm_shift_role_employee'
        );

        $request->session()->flash('alert-success', 'data has been successfully deleted!');
        return Redirect::to('shiftroleemployeelist');
    }
}
