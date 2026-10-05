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

use App\Models\HrmDepertment;
use App\Models\HrmKPITaskDepartment;
use App\Models\HrmKPITaskDepartmentDetails;

class KPITaskDepartmentController extends Controller
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
         return view('kpi_task_department.task_department_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('kpi_task_department.create_task_department')
                ->with('data',HrmDepertment::where('valid',1)->get());
    }





    public function task_departmentlistdata(Request $request){

        // $condition    = "";
        // $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));

        // if ($request->location != 0){
        //   $condition  = " AND cc.hrm_location_id = ".$request->location;
        // }else{
        //   $condition  = " AND cc.hrm_location_id = 0";
        // }

        $data   = DB::select("SELECT
                                a.id,
                                a.description,
                                GROUP_CONCAT(CONCAT(c.depertment_name) order by c.depertment_name ASC SEPARATOR '<br>') AS depertment_name
                            FROM

                                hrm_kpi_task_department a
                                     JOIN
                                hrm_kpi_task_department_details b ON a.id=b.hrm_kpi_task_department_id
                                AND a.valid=1
                                    JOIN
                                hrm_depertment c ON b.hrm_depertment_id = c.id WHERE c.valid=1

                            GROUP BY a.id,a.description");
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

        $validator = Validator::make($request->all(), [
            'task_department_name'   => 'required|unique:hrm_kpi_task_department,description|max:255',
            'id'                     => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('task_department/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        DB::beginTransaction();
                try {

                        $insert = new HrmKPITaskDepartment;
                        $insert->description            = $request->task_department_name;
                        $insert->valid                  = 1;
                        $insert->users_id               = Auth::user()->id;
                        $insert->save();

                        $count_row  = count($request->id);

                        for($r = 0; $r <$count_row; $r++) {

                            // dd($request->id[$r]);
                            $insert_details = new HrmKPITaskDepartmentDetails;
                            $insert_details->hrm_kpi_task_department_id = $insert->id;
                            $insert_details->hrm_depertment_id          = $request->id[$r];
                            $insert_details->save();
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
        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('task_department');

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
          $edit_data=DB::SELECT("SELECT id,description FROM hrm_kpi_task_department WHERE valid=1 AND id=$id");
          $dtls_data=DB::SELECT("SELECT
                                        a.id, a.depertment_name, b.hrm_depertment_id
                                    FROM
                                        hrm_depertment a
                                            LEFT JOIN
                                        hrm_kpi_task_department_details b ON a.id = b.hrm_depertment_id
                                            AND b.hrm_kpi_task_department_id = $id WHERE a.valid=1");



          return view('kpi_task_department.edit_task_department')
                ->with('edit_data',$edit_data)
                ->with('dtls_data',$dtls_data);
                // ->with('data',HrmDepertment::where('valid',1)->get());
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

        // dd($request->all());

        $request->validate([
            'task_department_name'   => 'required|unique:hrm_kpi_task_department,description,'.$id.',id',
            'id'                     => 'required',
        ]);



        DB::beginTransaction();
                try {

                    $insert =  HrmKPITaskDepartment::find($id);
                    $insert->description            = $request->task_department_name;
                    $insert->valid                  = 1;
                    $insert->users_id               = Auth::user()->id;
                    $insert->save();


                    DB::DELETE("DELETE FROM hrm_kpi_task_department_details WHERE hrm_kpi_task_department_id=$id ");

                    $count_row  = count($request->id);

                    for($r = 0; $r <$count_row; $r++) {

                        // dd($request->id[$r]);
                        $insert_details = new HrmKPITaskDepartmentDetails;
                        $insert_details->hrm_kpi_task_department_id = $insert->id;
                        $insert_details->hrm_depertment_id          = $request->id[$r];
                        $insert_details->save();
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

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('task_department');
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
}
