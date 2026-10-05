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

use App\Models\HrmLeaveType;
use App\Models\HrmLeaveYear;
use App\Models\HrmLeaveTypeYear;

class LeaveTypeYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct(){
        $this->middleware('auth');
    }


    public function index()
    {
        return view('leavetype_year.leavetypeyear_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $leavetype=DB::SELECT("SELECT * FROM hrm_employee_leave_type WHERE valid=1");

       return view('leavetype_year.create_leavetypeyear')
              ->with('leavetype', $leavetype)
              ->with('leavetypeyear',HrmLeaveYear::all());

              // ->with('leavetype',HrmLeaveType::where('valid',1)->first())

        // $employeejobinfo = HrmEmployeeJobInfo::where('hrm_employee_id', $request->employee_name)->first();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function leavetypeyearlist(Request $request){

       $leavetypeyearlist=DB::SELECT("SELECT a.id,
                                     b.leave_type,
                                     c.leave_year,
                                     a.leave_days
                                     FROM  hrm_leave_type_year a JOIN   hrm_employee_leave_type b ON a.hrm_employee_leave_type_id=b.id
                                     JOIN  hrm_leave_years c ON a.hrm_leave_years_id=c.id
                                     WHERE c.active_status=1 ORDER BY c.id desc");


        return json_encode(array('data' =>$leavetypeyearlist));

    }




    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_year'           => 'required',
            'leave_type'           => 'required',
            'days'                 => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('leavetypeyear/create')
                        ->withErrors($validator)
                        ->withInput();
        }



        $insert = new HrmLeaveTypeYear;
        $insert->hrm_leave_years_id          = $request->leave_year;
        $insert->hrm_employee_leave_type_id  = $request->leave_type;
        $insert->leave_days                  = $request->days;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Leave Type Year Name',
             null,
             $insert->id,
             'hrm_leave_type_year'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('leavetypeyear');
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
        $edit_data=DB::SELECT("SELECT
                                    a.id,
                                    b.id as leave_type_id,
                                    b.leave_type,
                                    c.id AS leave_year_id,
                                    c.leave_year,
                                    a.leave_days
                                FROM
                                    hrm_leave_type_year a
                                        JOIN
                                    hrm_employee_leave_type b ON a.hrm_employee_leave_type_id = b.id
                                        JOIN
                                    hrm_leave_years c ON a.hrm_leave_years_id = c.id
                                WHERE
                                    c.active_status = 1 AND a.id = $id");

           return view('leavetype_year.edit_leavetypeyear')
                    ->with('leavetype',HrmLeaveType::all())
                    ->with('leaveyear',HrmLeaveYear::all())
                    ->with('edit_data',$edit_data);
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
          $validator = Validator::make($request->all(), [
            'leave_year'           => 'required',
            'leave_type'           => 'required',
            'days'                 => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('leavetypeyear')
                        ->withErrors($validator)
                        ->withInput();
        }



        $insert = HrmLeaveTypeYear::find($id);
        $insert->hrm_leave_years_id          = $request->leave_year;
        $insert->hrm_employee_leave_type_id  = $request->leave_type;
        $insert->leave_days                  = $request->days;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Leave Type Year Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_leave_type_year'
        );

        $request->session()->flash('alert-success', 'data has been successfully Updated!');
        return Redirect::to('leavetypeyear');
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
       dd("working");
    }
}
