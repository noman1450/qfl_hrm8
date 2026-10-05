<?php

namespace App\Http\Controllers;

use Auth;
use Crypt;
use Config;
use Session;
use App\User;
use Redirect;
use Datatables;
use Illuminate\Http\Request;
use App\Models\HrmKPITaskDetails;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;

class KPITaskDetailsController extends Controller
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
         return view('kpi_task_details.kpi_task_details_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('kpi_task_details.create_kpi_task_details');
    }

    public function kpi_task_details_list(Request $request){

        // $data   = DB::select("SELECT
        //                         a.id,
        //                         a.description,
        //                         CONCAT(e.description,
        //                                       ' | ',
        //                                     'From :',
        //                                     d.start_date,
        //                                     '  To :',
        //                                     d.end_date) AS date_year,
        //                         GROUP_CONCAT(CONCAT(c.description) order by c.description ASC SEPARATOR '<br>') AS task_name
        //                     FROM

        //                         hrm_kpi_task_department a
        //                              JOIN
        //                         hrm_kpi_task_details b ON a.id=b.hrm_kpi_task_department_id
        //                         AND a.valid=1 AND b.valid=1
        //                             JOIN
        //                         hrm_kpi_task c ON b.hrm_kpi_task_id=c.id
        //                             JOIN
        //                         hrm_kpi_assesment_date d ON d.id = b.hrm_kpi_assesment_date_id
        //                             AND a.valid = 1
        //                             JOIN
        //                         hrm_kpi_assesment_year e ON d.hrm_kpi_assesment_year_id = e.id
        //                     GROUP BY a.id,a.description,e.description,d.start_date,d.end_date");




        $data   = DB::select("SELECT
                                a.id,
                                a.description,
                                GROUP_CONCAT(CONCAT(c.description) order by c.description ASC SEPARATOR '<br>') AS task_name
                            FROM

                                hrm_kpi_task_department a
                                     JOIN
                                hrm_kpi_task_details b ON a.id=b.hrm_kpi_task_department_id
                                AND a.valid=1 AND b.valid=1
                                    JOIN
                                hrm_kpi_task c ON b.hrm_kpi_task_id=c.id
                            GROUP BY a.id,a.description");

        return json_encode(array('data' => $data));


    }


   public function kpi_task_deptwise_list(Request $request){

        $data   = DB::select("SELECT
                                    a.id, a.description, b.id as dtlsVal
                                FROM
                                    hrm_kpi_task a
                                        LEFT JOIN
                                    hrm_kpi_task_details b ON a.id = b.hrm_kpi_task_id  AND a.valid=1 AND b.valid=1
                                        AND b.hrm_kpi_task_department_id = $request->task_department_id
                                WHERE
                                    a.valid = 1");
        return json_encode(array('data' => $data));

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_department_id'   => 'required',
            'id'                   => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('task_department/create')
                ->withErrors($validator)
                ->withInput();
        }
        $task_department_id= $request->task_department_id;

        // $activity   = DB::table('hrm_kpi_task_details')->where('hrm_kpi_task_department_id','=',$task_department_id)->first();

        // if (!empty($activity)){
        //     session()->flash('alert-danger', "You can't update this department info !! This department info use to employees ");
        //     return Redirect()->back();
        // }



        DB::UPDATE("UPDATE hrm_kpi_task_details SET Valid=0 WHERE hrm_kpi_task_department_id=$task_department_id");


        DB::beginTransaction();
        try {

            $count_row  = count($request->id);

            for($r = 0; $r <$count_row; $r++) {
                $insert_details = new HrmKPITaskDetails;
                $insert_details->hrm_kpi_task_department_id = $request->task_department_id;
                // $insert_details->hrm_kpi_assesment_date_id  = $request->hrm_kpi_assesment_date_id;
                $insert_details->hrm_kpi_task_id            = $request->id[$r];
                $insert_details->valid                      = 1;


                $insert_details->save();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'success'           => false,
                'error_messages'    => true,
                'messages'          => "insert problem !! " . $e->getMessage()
            ));
        }

        return redirect()->to('kpi_task_details')
            ->with('alert-success', 'data has been successfully added!');
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
                                id,
                                description
                            FROM
                                hrm_kpi_task_department WHERE id=$id");

          // $dtls_data=DB::SELECT("SELECT
          //                               a.id, a.depertment_name, b.hrm_depertment_id
          //                           FROM
          //                               hrm_depertment a
          //                                   LEFT JOIN
          //                               hrm_kpi_task_department_details b ON a.id = b.hrm_depertment_id AND a.valid=1
          //                                   AND b.hrm_kpi_task_department_id = $id");


          return view('kpi_task_details.edit_kpi_task_details')
                ->with('edit_data',$edit_data);
                // ->with('dtls_data',$dtls_data);
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
