<?php

namespace App\Http\Controllers;

use Auth;
use Redirect;
use Response;
use Validator;
use Illuminate\Http\Request;
use App\Models\HrmDepertment;
use Illuminate\Support\Facades\DB;
use App\Models\HrmKPIDeptHeadMaster;
use App\Models\HrmKPIDeptHeadDetails;

class KPIDeptHeadAssignController extends Controller
{
    public function index()
    {
         return view('kpi_dept_head_assign.kpi_dept_head_assign_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('kpi_dept_head_assign.create_kpi_dept_head_assign')
                ->with('data',HrmDepertment::where('valid',1)->get());
    }

    public function kpi_deptheadassignlistdata(Request $request)
    {
        $where = '';

        if ($hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id) {
            $where = " AND aa.id = {$hrm_kpi_assesment_date_id}";
        }



        $data = DB::select("SELECT
                                    a.id,
                                    concat(bb.description,' | ','From :',aa.start_date,'  To :',aa.end_date) as date_year,
                                    CONCAT(c.employee_name, ' | ', b.employee_code) AS employee_name,
                                    d.depertment_name  as emp_dept_name,
                                    e.designation_name,
                                    h.location_name,
                                    GROUP_CONCAT(CONCAT(g.depertment_name)
                                        ORDER BY g.depertment_name ASC
                                        SEPARATOR '<br>') AS depertment_name
                                FROM
                                    hrm_kpi_dept_head_assign_master a
                                        JOIN
                                    hrm_kpi_assesment_date aa ON aa.id=a.hrm_kpi_assesment_date_id AND a.valid=1
                                        JOIN
                                    hrm_kpi_assesment_year bb ON aa.hrm_kpi_assesment_year_id=bb.id
                                    $where
                                        JOIN
                                    hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_designation e ON b.hrm_designation_id = e.id
                                        JOIN
                                    hrm_kpi_dept_head_assign_details f ON a.id = f.hrm_kpi_dept_head_assign_master_id
                                        JOIN
                                    hrm_depertment g ON f.hrm_depertment_id = g.id
                                        AND g.valid = 1
                                        JOIN
                                    hrm_location h ON a.hrm_location_id=h.id
                                GROUP BY a.id , c.employee_name , b.employee_code , d.depertment_name , e.designation_name,h.location_name,bb.description,aa.start_date,aa.end_date");
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'hrm_kpi_assesment_date_id'   => 'required|unique:hrm_kpi_task_department,description|max:255',
            'hrm_kpi_assesment_date_id'=> 'required',
            'employee_name'            => 'required',
            'id'                       => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('task_department/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $activity   = DB::table('hrm_employee_job_info')->where('hrm_employee_id','=',$request->employee_name)
                                                       ->where('employee_activity','=',1)->first();

       //  $check_data = DB::SELECT("SELECT id FROM hrm_kpi_dept_head_assign_master WHERE hrm_employee_job_info_id=$activity->id AND hrm_kpi_assesment_date_id=$request->hrm_kpi_assesment_date_id AND valid=1");

       // if(!empty($check_data)){
       //          $request->session()->flash('alert-danger', 'Sorry this employee head already assign!');
       //          return Redirect::to('kpi_deptheadassign');
       // }

        DB::beginTransaction();
                try {
                        $insert = new HrmKPIDeptHeadMaster;
                        $insert->hrm_employee_job_info_id  = $activity->id;
                        $insert->hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id;
                        $insert->hrm_location_id           = $request->hrm_location_name;
                        $insert->valid                     = 1;
                        $insert->users_id                  = Auth::user()->id;
                        $insert->save();

                        $count_row  = count($request->id);

                        for($r = 0; $r <$count_row; $r++) {
                            $insert_details = new HrmKPIDeptHeadDetails;
                            $insert_details->hrm_kpi_dept_head_assign_master_id = $insert->id;
                            $insert_details->hrm_depertment_id                  = $request->id[$r];
                            $insert_details->save();

                            $this->recordActivity(
                                1,
                               'Created KPI Dept Head Assign Details',
                                $insert_details,
                                $insert_details->id,
                                'hrm_kpi_dept_head_assign_details'
                            );
                        }

        DB::commit();

        $this->recordActivity(
            1,
           'Created KPI Dept Head Assign',
            $insert,
            $insert->id,
            'hrm_kpi_dept_head_assign_master'
        );

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => "insert problem !! " . $e->getMessage()
            ));
        }
        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('kpi_deptheadassign');

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
                                    aa.id as hrm_kpi_assesment_date_id,
                                    concat(bb.description,' | ','From :',aa.start_date,'  To :',aa.end_date) as date_year,
                                    CONCAT(c.employee_name, ' | ', b.employee_code) AS employee_name,
                                    b.hrm_employee_id,
                                    d.id as hrm_location_id,
                                    d.location_name
                                FROM
                                    hrm_kpi_dept_head_assign_master a
                                        JOIN
                                    hrm_kpi_assesment_date aa ON aa.id=a.hrm_kpi_assesment_date_id AND a.valid=1 AND a.id=$id
                                        JOIN
                                    hrm_kpi_assesment_year bb ON aa.hrm_kpi_assesment_year_id=bb.id
                                        JOIN
                                    hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_location  d ON a.hrm_location_id=d.id ");





          $dtls_data=DB::SELECT("SELECT
                                        a.id, a.depertment_name, b.hrm_depertment_id
                                    FROM
                                        hrm_depertment a
                                            LEFT JOIN
                                        hrm_kpi_dept_head_assign_details b ON a.id = b.hrm_depertment_id
                                            AND b.hrm_kpi_dept_head_assign_master_id = $id WHERE a.valid=1");



          return view('kpi_dept_head_assign.edit_kpi_dept_head_assign')
                ->with('edit_data',$edit_data)
                ->with('dtls_data',$dtls_data);
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
            // 'hrm_kpi_assesment_date_id'   => 'required|unique:hrm_kpi_task_department,description|max:255',
            'hrm_kpi_assesment_date_id'=> 'required',
            'employee_name'            => 'required',
            'id'                       => 'required',
        ]);


       $activity   = DB::table('hrm_employee_job_info')->where('hrm_employee_id','=',$request->employee_name)
                                                       ->where('employee_activity','=',1)->first();


        DB::beginTransaction();
                try {

                    $insert =  HrmKPIDeptHeadMaster::find($id);
                    $insert->hrm_employee_job_info_id  = $activity->id;
                    $insert->hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id;
                    $insert->hrm_location_id           = $request->hrm_location_name;
                    $insert->valid                     = 1;
                    $insert->users_id                  = Auth::user()->id;
                    $insert->save();


                    DB::DELETE("DELETE FROM hrm_kpi_dept_head_assign_details WHERE hrm_kpi_dept_head_assign_master_id=$id ");



                    $count_row  = count($request->id);

                    for($r = 0; $r <$count_row; $r++) {
                        $insert_details = new HrmKPIDeptHeadDetails;
                        $insert_details->hrm_kpi_dept_head_assign_master_id = $insert->id;
                        $insert_details->hrm_depertment_id                  = $request->id[$r];
                        $insert_details->save();

                        $this->recordActivity(
                             1,
                             'Updated KPI Dept. Head Assign Details',
                             $insert_details,
                             $insert_details->id,
                             'hrm_kpi_dept_head_assign_details'
                        );
                    }


        DB::commit();

        $this->recordActivity(
             1,
             'Updated KPI Dept. Head Assign',
             $insert->getChanges(),
             $insert->id,
             'hrm_kpi_dept_head_assign_master'
        );

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => "insert problem !! " . $e->getMessage()
            ));
        }

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('kpi_deptheadassign');
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

        $cancel = HrmKPIDeptHeadMaster::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        // $cancel->valid              = 0;
        // $cancel->users_id           = Auth::user()->id;
        // $cancel->save();

        DB::DELETE("DELETE FROM hrm_kpi_dept_head_assign_details WHERE hrm_kpi_dept_head_assign_master_id=$id ");
        DB::table('hrm_kpi_dept_head_assign_master')->delete($id);

        $this->recordActivity(
             1,
             'Deleted KPI Dept Head Assign',
             null,
             $cancel->id,
             'hrm_kpi_dept_head_assign_master'
        );


        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('kpi_deptheadassign');

    }


}
