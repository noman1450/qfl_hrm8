<?php

namespace App\Http\Controllers;

use Auth;
use Crypt;
use Config;
use Session;
use App\User;
use Redirect;
use Validator;
use Datatables;
use App\Models\HrmLeaveType;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;



class LeaveTypeController extends Controller
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
          return view('leave_type.leavetype_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('leave_type.create_leavetype');
    }




    public function leavetypelist(Request $request){



       $leavetype=DB::SELECT("SELECT id,
                                     leave_type,
                                     If(leave_status=1,'Company Policy','Holiday Against Leave') as leave_status
                                     FROM  hrm_employee_leave_type
                                     WHERE valid=1");


        return json_encode(array('data' =>$leavetype));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

         $validator = Validator::make($request->all(), [
            'leave_type'   => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('leavetype/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmLeaveType;
        $insert->leave_type          = $request->leave_type;
        $insert->leave_status        = $request->leave_status;
        $insert->valid               = 1;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Leave Type Name',
             null,
             $insert->id,
             'hrm_employee_leave_type'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('leavetype');

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

        $edit      = HrmLeaveType::find($id);

        if (empty($edit)){
            session()->flash('alert-danger', 'Invalid Leave Type !!');
            return Redirect()->back();
        }

        return view('leave_type.edit_leavetype')->with('edit_data',$edit);
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
            'leave_type'    => 'required',
            'leave_status'  => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('leavetype')
                        ->withErrors($validator)
                        ->withInput();
        }


        $update = HrmLeaveType::find($id);
        $update->leave_type       = $request->leave_type;
        $update->leave_status     = $request->leave_status;
        $update->save();

        $this->recordActivity(
             1,
             'Updated Leave Type Name',
             $update->getChanges(),
             $update->id,
             'hrm_employee_leave_type'
        );


        $request->session()->flash('alert-success', 'data has been successfully Updated!');
        return Redirect::to('leavetype');
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

        $cancel = HrmLeaveType::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Leave Type !!');
            return Redirect()->back();
        }


        DB::table('hrm_employee_leave_type')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Leave Type Name',
             null,
             $id,
             'hrm_employee_leave_type'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('leavetype');

    }

}
