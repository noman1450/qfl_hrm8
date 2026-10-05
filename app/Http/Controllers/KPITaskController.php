<?php

namespace App\Http\Controllers;

use Auth;
use Crypt;
use Config;
use Entrust;
use Session;
use App\User;
use Redirect;
use Validator;
use Datatables;
use App\Models\HrmKPITask;
use Illuminate\Http\Request;

use App\Models\HrmKPITaskType;
use Illuminate\Support\Facades\DB;


class KPITaskController extends Controller
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
        return view('kpi_task.kpi_task_list')
             ->with('task_type',HrmKPITaskType::all());

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('kpi_task.create_kpi_task')
             // ->with('task_type',HrmKPITaskType::all());
    }

    public function kpi_task_list(Request $request){

        $data = DB::SELECT("SELECT
                                a.id, a.description,b.id as hrm_kpi_task_type_id,b.task_type_name
                            FROM
                                hrm_kpi_task a
                                    JOIN
                                hrm_kpi_task_type b ON a.hrm_kpi_task_type_id = b.id
                            WHERE
                                a.valid = 1");

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
        if(!empty($request->id)){

            $request->validate([
              'task_name'   => 'required|unique:hrm_kpi_task,description,'.$request->id.',id',
            ]);

        }else{

            $validator = Validator::make($request->all(), [
                'task_name'   => 'required|unique:hrm_kpi_task,description|max:255',
            ]);

            if ($validator->fails()) {
                $request->session()->flash('alert-danger', 'Already Exist!');
                return redirect('kpi_task')
                            ->withErrors($validator)
                            ->withInput();
            }

        }


        if(!empty($request->id)){
           $insert = HrmKPITask::find($request->id);
        }else{
           $insert = new HrmKPITask;
        }
        $insert->description          = $request->task_name;
        $insert->hrm_kpi_task_type_id = $request->hrm_kpi_task_type;
        $insert->valid         = 1;
        $insert->users_id      = Auth::user()->id;

        $insert->save();

        $this->recordActivity(
             1,
             $request->id ? 'Updated Kpi Task' : 'Created Kpi Task',
             $request->id ? $insert->getChanges() : $insert,
             $insert->id,
             'hrm_kpi_task'
        );

        $request->session()->flash('alert-success', 'Successfully Insert!');
        return Redirect::to('kpi_task');
    }


    public function kpi_task_type(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'task_type_name'   => 'required|unique:hrm_kpi_task_type,task_type_name|max:255',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Already Exist!');
            return redirect('kpi_task')
                        ->withErrors($validator)
                        ->withInput();
        }

        if(!empty($request->id)){
         $insert = HrmKPITaskType::find($request->id);
        }else{
         $insert = new HrmKPITaskType;
        }
        $insert->task_type_name   = $request->task_type_name;
        $insert->users_id      = Auth::user()->id;
        $insert->save();

        $request->session()->flash('alert-success', 'Successfully Insert!');
        return Redirect::to('kpi_task');
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

    public function destroy($id)
    {
        //
    }

    public function cancel(Request $request,$id){



        $cancel = HrmKPITask::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid designation !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_kpi_task_details')->where('hrm_kpi_task_id','=',$id)
          ->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this task name !! This task Name already used ");
            return Redirect()->back();
        }

        $cancel->valid              = 0;
        $cancel->users_id           = Auth::user()->id;
        $cancel->save();

        $this->recordActivity(
             1,
             'Deleted KPI Task Name',
             $cancel,
             $id,
             'hrm_kpi_task'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('kpi_task');

    }
}
