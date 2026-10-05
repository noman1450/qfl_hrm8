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

use App\Models\HrmBloodGroup;
class BloodgroupController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {

 
        return view('bloodgroup.bloodgroup_list');
    }

    public function create()
    {
        return view('bloodgroup.create_bloodgroup');
    }

    public function bloodgrouplist(){
        return json_encode(array('data' => HrmBloodGroup::all()));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name'    => 'required|unique:hrm_blood_group,blood_group|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('bloodgroup/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmBloodGroup;
        $insert->blood_group = $request->group_name;
        $insert->save();

        $this->recordActivity(
             1,
             'Create Blood Group',
              null,
             $insert->id,
             'hrm_blood_group'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('bloodgroup');
    }

    public function edit($id)
    {
        $bloodgroup = HrmBloodGroup::find($id);

        if (empty($bloodgroup)){
            session()->flash('alert-danger', 'Invalid blood group !!');
            return Redirect()->back();
        }

        return view('bloodgroup.edit_bloodgroup')->with('bloodgroup',$bloodgroup);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            // 'group_name'    => 'required|unique:hrm_blood_group,blood_group|max:255',
            'group_name'    => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmBloodGroup::find($id);
        $insert->blood_group = $request->group_name;
        $insert->save();

        // dd($insert->getChanges());

       $this->recordActivity(
             1,
             'Update Blood Group',
             $insert->getChanges(),
             $insert->id,
             'hrm_blood_group'
        );


        $request->session()->flash('alert-success', 'successfully updated!');
        return Redirect::to('bloodgroup');
    }

    public function cancel(Request $request,$id)
    {
        $cancel = HrmBloodGroup::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid blood group !!');
            return back();
        }

        $activity   = DB::table('hrm_employee')->where('hrm_blood_group_id', $id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this blood group !! This blood group use to employees ");
            return back();
        }

        DB::table('hrm_blood_group')->where('id', '=', $id)->delete();

        $this->recordActivity(
                 1,
                 'Deleted Blood Group',
                  null,
                 $id,
                 'hrm_blood_group'
            );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return redirect()->to('bloodgroup');

    }
}
