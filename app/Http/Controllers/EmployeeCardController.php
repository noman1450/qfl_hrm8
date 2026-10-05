<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use Auth;
use DB;
use Validator;

use App\Models\HrmEmployee;
use App\Models\HrmEmployeeCardCode as EmployeeCard;

class EmployeeCardController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('employee_card.employee_card_list')
            -> with('default_user_location',  $default_user_location) ;
    }

    public function create()
    {
        return view('employee_card.create_card_employee');

    }

    public function store(Request $request)
    {
        $device_id = 1;
        $validator = Validator::make($request->all(), [
            'employee_name'  => 'required',
            'card_code'      => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('employeecard/create')
                ->withErrors($validator)
                ->withInput();
        }

        $activity = DB::table('hrm_employee_job_info')
            ->where('hrm_employee_id', $request->employee_name)
            ->where('employee_activity', 1)
            ->first();

       $location_id = $activity->hrm_location_id;


        $isExist = DB::SELECT("SELECT
                                    b.id
                                FROM
                                    hrm_employee_card_code a
                                        JOIN
                                    hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id  AND b.employee_activity=1
                                WHERE
                                      b.hrm_location_id = $location_id
                                      AND a.device_id = $device_id
                                      AND a.card_code = '$request->card_code'");

        if(!empty($isExist)){
            $request->session()->flash('alert-danger', 'Sorry This Card No. Already Exists!');
            return Redirect::to('employeecard');

        }


        $isExistCode= DB::SELECT("SELECT
                                        b.id
                                    FROM
                                        hrm_employee_card_code a
                                            JOIN
                                        hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id AND b.employee_activity=1
                                    WHERE
                                        b.hrm_employee_id = $request->employee_name;");

        if(!empty($isExistCode)){
            $request->session()->flash('alert-danger', 'Sorry This Employee Already Exists!');
            return Redirect::to('employeecard');

        }


        $insert     = new EmployeeCard;
        // $insert->hrm_employee_id          = $request->employee_name;
        $insert->card_code                = $request->card_code;
        $insert->old_code                 = $request->old_code;
        $insert->device_id                = $device_id;
        $insert->hrm_employee_job_info_id = $activity->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Employee Card Information',
             null,
             $insert->id,
             'hrm_employee_card_code'
        );


        $request->session()->flash('alert-success', 'data has been successfully added!');
        return redirect()->to('employeecard');
    }

    public function employeecardlist(Request $request)
    {
        if($request->location==null){
            $location = '';
        }else{
            $location = 'And b.hrm_location_id = '.$request->location;
        }


        $user_id=Auth::user()->id;

        $employeecardlist = DB::select("SELECT
                                a.id,
                                Concat(a.employee_name,' | ',ifnull(b.employee_code,'')) as employee_name,
                                c.card_code,
                                c.device_id,
                                d.designation_name,
                                e.depertment_name,
                                f.location_name,
                                a.Images,
                                c.id as cardtableid
                                FROM hrm_employee a
                                Join hrm_employee_job_info b ON a.id=b.hrm_employee_id AND b.employee_activity=1 $location
                                JOIN hrm_employee_card_code c ON c.hrm_employee_job_info_id = b.id
                                Join hrm_designation d On b.hrm_designation_id=d.id
                                JOIN hrm_depertment e ON b.hrm_depertment_id=e.id
                                Join hrm_location f ON b.hrm_location_id=f.id
                                JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id ");



        return json_encode(array('data' => $employeecardlist));

        // return datatables()->of($employeecardlist)
        // ->addColumn('Link', function ($employeecardlist) {
        //    return
        //    ' <a href="'. url('/employeecard') . '/' .
        //    Crypt::encrypt($employeecardlist->id) .
        //    '/edit' .'"' .
        //    'class="btn  btn-sm block btn-flat"><i class="glyphicon glyphicon-edit" id="customer-confrimed"></i> Edit</a>';
        //  })
        // ->editColumn('id', '{{$id}}')
        // ->setRowId('id')
        // ->rawColumns(['Link'])
        // ->make(true);
    }

    public function edit($id)
    {
        $employee = HrmEmployee::find($id);
        if (empty($employee)){
            session()->flash('alert-danger', 'Invalid employee !!');
            return Redirect()->back();
        }
       $activity   = DB::table('hrm_employee_job_info')->where('hrm_employee_id','=',$id)
                                                       ->where('employee_activity','=',1)->first();
       $employeecard = DB::table('hrm_employee_card_code')->where('hrm_employee_job_info_id',$activity->id)->first();

        return view('employee_card.edit_card_employee')
            ->with('employee',$employee)
            ->with('employeecard',$employeecard);
    }

    public function update(Request $request, $id)
    {
        $device_id = 1;
        $validator = Validator::make($request->all(), [
            'employee_name'     => 'required',
            'card_code'         => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('employeecard')
                ->withErrors($validator)
                ->withInput();
        }

        // $activity   = DB::table('hrm_employee_job_info')->where('hrm_employee_id','=',$id)->first();


        $activity = DB::table('hrm_employee_job_info')
            ->where('hrm_employee_id', $id)
            ->where('employee_activity', 1)
            ->first();


        $employeecard = EmployeeCard::where('hrm_employee_job_info_id', $activity->id)->first();

        // $employeecard->hrm_employee_id          = $request->get('employee_name');
        $employeecard->card_code                = $request->get('card_code');
        $employeecard->old_code                 = $request->get('old_code');
        $employeecard->device_id                = $device_id;
        $employeecard->hrm_employee_job_info_id = $activity->id;

        $employeecard->save();


        $this->recordActivity(
             1,
             'Updated Employee Card Information',
             $employeecard->getChanges(),
             $employeecard->id,
             'hrm_employee_card_code'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('employeecard');
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

    public function cancel(Request $request,$id)
    {
        DB::table('hrm_employee_card_code')->where('id', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Employee Card Information',
             null,
             $id,
             'hrm_employee_card_code'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('employeecard');
    }
}
