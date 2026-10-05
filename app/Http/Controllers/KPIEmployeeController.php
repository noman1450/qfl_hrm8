<?php

namespace App\Http\Controllers;

use Auth;
use Crypt;
use Config;


use Session;
use App\User;
use Redirect;
use Response;
use Validator;
use Datatables;
use App\Models\HrmKPIMarks;
use Illuminate\Http\Request;
use Jaspersoft\Client\Client;
use Illuminate\Support\Facades\DB;
use Jaspersoft\Service\jobService;

use App\Models\HrmKPIEmployeeMaster;
use App\Models\HrmKPIEmployeeDetails;
use Jaspersoft\Service\ReportService;
use App\Models\HrmKPIEmployeeFinalSummary;



class KPIEmployeeController extends Controller
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

         return view('kpi_employee.kpi_employee_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('kpi_employee.create_kpi_employee');
    }

    public function kpi_employee_final_list()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

         return view('kpi_employee.kpi_employee_final_list')
             -> with('default_user_location',  $default_user_location) ;

    }


    public function addnote($id)
    {

         $data = DB::select("SELECT
                                        k.id as  hrm_kpi_employee_final_summary_id,
                                        a.id,
                                        concat(c.employee_name,' | ',b.employee_code) as employee_name,
                                        d.depertment_name,
                                        e.designation_name,
                                        k.total_point,
                                        k.deduct_point,
                                        cc.location_name,
                                        (k.total_point-k.deduct_point) as net_point,
                                        k.promotion_status as promotion,
                                        k.note,
                                        Concat(k.increment_amount,k.increment_amount_type) as increment,
                                        concat(bb.description,' | ','From :',aa.start_date,'  To :',aa.end_date) as kpi_year,
                                        GROUP_CONCAT(CONCAT(i.description,' -> ','<b>',g.description,' - ',g.point,'</b>')
                                            ORDER BY i.description ASC
                                            SEPARATOR '<br>') AS task_name,
                                        GROUP_CONCAT(CONCAT(g.description)
                                            ORDER BY g.description ASC
                                            SEPARATOR '<br>') AS marks,
                                        GROUP_CONCAT(CONCAT(g.point)
                                            ORDER BY g.point ASC
                                            SEPARATOR '<br>') AS point

                                    FROM
                                        hrm_kpi_employee_master a
                                            JOIN
                                        hrm_kpi_assesment_date aa ON a.hrm_kpi_assesment_date_id=aa.id  AND a.status=2
                                            JOIN
                                        hrm_kpi_assesment_year bb ON aa.hrm_kpi_assesment_year_id=bb.id
                                            JOIN
                                        hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                            AND a.valid = 1
                                           JOIN
                                        hrm_location cc ON b.hrm_location_id = cc.id
                                            JOIN
                                        hrm_employee c ON b.hrm_employee_id = c.id
                                            Join
                                        hrm_depertment d ON b.hrm_depertment_id=d.id
                                            JOIN
                                        hrm_designation e ON b.hrm_designation_id=e.id
                                            JOIN
                                        hrm_kpi_employee_details f ON a.id=f.hrm_kpi_employee_master_id
                                            JOIN
                                        hrm_kpi_marks g ON f.hrm_kpi_marks_id=g.id
                                            JOIN
                                        hrm_kpi_task_details h ON f.hrm_kpi_task_details_id=h.id
                                            JOIN
                                        hrm_kpi_task i ON h.hrm_kpi_task_id=i.id
                                            JOIN
                                        hrm_kpi_employee_final_summary k ON a.id=k.hrm_kpi_employee_master_id AND k.id = $id
                                    GROUP BY k.id,a.id,c.employee_name,b.employee_code,d.depertment_name,
                                        e.designation_name,bb.description,aa.start_date,aa.end_date,k.increment_amount,k.increment_amount_type,k.total_point,k.promotion_status,k.total_point,k.deduct_point,cc.location_name,k.note");

         return view('kpi_employee.kpi_employee_final_addnote')->with('data', $data);

    }





    public function createnew($date_id,$id)
    {


         $data   = DB::select("SELECT
                                    b.id as employee_id,
                                    CONCAT(b.employee_name, ' | ', a.employee_code) AS employee_name,
                                    c.depertment_name,
                                    c.id as depertment_id,
                                    d.designation_name,
                                    CONCAT(h.description,
                                            ' | ',
                                            'From :',
                                            g.start_date,
                                            '  To :',
                                            g.end_date) AS kpi_year,
                                    g.id as hrm_kpi_assesment_date_id,
                                    i.joining_date,
                                    i.confirmation_date,
                                    a.id as hrm_employee_job_info_id,
                                    j.category_name
                                FROM
                                    hrm_employee_job_info a
                                        JOIN
                                    hrm_employee b ON a.hrm_employee_id = b.id
                                        AND a.employee_activity = 1 and a.id=$id
                                        JOIN
                                    hrm_depertment c ON a.hrm_depertment_id = c.id
                                        JOIN
                                    hrm_designation d ON a.hrm_designation_id = d.id
                                        JOIN
                                    hrm_location e ON a.hrm_location_id = e.id
                                        JOIN
                                    hrm_kpi_dept_head_assign_master f ON e.id = f.hrm_location_id
                                        JOIN
                                    hrm_kpi_assesment_date g ON f.hrm_kpi_assesment_date_id = g.id AND g.id=$date_id
                                        JOIN
                                    hrm_kpi_assesment_year h ON g.hrm_kpi_assesment_year_id = h.id
                                        join
                                    hrm_employee_joining i ON b.id=i.hrm_employee_id
                                       JOIN
                                    hrm_category  j ON a.hrm_category_id = j.id ");


         return view('kpi_employee.create_kpi_employee')
                ->with('data',$data);
    }



    public function kpi_employee_list_user_wise(Request $request){


        $category = '';

        if(!empty($request->hrm_category_id)){
            $category = ' And a.hrm_category_id='.$request->hrm_category_id;
        }

        if(!empty($request->hrm_employment_status_id)){
            $category = $category.' And a.hrm_employment_status_id='.$request->hrm_employment_status_id;
        }




        $user_id =Auth::user()->id;
        $data    = DB::select("SELECT
                                    a.id,
                                    CONCAT(b.employee_name, ' | ', a.employee_code) AS employee_name,
                                    c.depertment_name,
                                    d.designation_name,
                                    dd.category_name,
                                    CONCAT(h.description,
                                            ' | ',
                                            'From :',
                                            g.start_date,
                                            '  To :',
                                            g.end_date) AS kpi_year,
                                    g.id as hrm_kpi_assesment_date_id,
                                    b.images,
                                    d.priority,
                                    e.location_name
                                FROM
                                    hrm_employee_job_info a
                                        JOIN
                                    hrm_employee b ON a.hrm_employee_id = b.id
                                        AND a.employee_activity = 1 AND a.id NOT IN (SELECT
                                        hrm_employee_job_info_id FROM hrm_kpi_employee_master WHERE hrm_kpi_assesment_date_id=$request->hrm_kpi_assesment_date_id)
                                        $category
                                        JOIN
                                    hrm_depertment c ON a.hrm_depertment_id = c.id
                                        JOIN
                                    hrm_designation d ON a.hrm_designation_id = d.id
                                        JOIN
                                    hrm_category dd ON a.hrm_category_id = dd.id
                                        JOIN
                                    hrm_location e ON a.hrm_location_id = e.id
                                        JOIN
                                    hrm_kpi_dept_head_assign_master f ON e.id = f.hrm_location_id
                                        JOIN
                                    hrm_kpi_assesment_date g ON f.hrm_kpi_assesment_date_id = g.id  AND g.id=$request->hrm_kpi_assesment_date_id
                                        JOIN
                                    hrm_kpi_assesment_year h ON g.hrm_kpi_assesment_year_id = h.id
                                        JOIN
                                    hrm_employee_job_info i ON f.hrm_employee_job_info_id = i.id
                                        JOIN
                                    users j ON i.hrm_employee_id = j.hrm_employee_id and j.id=$user_id
                                        JOIN
                                    hrm_kpi_dept_head_assign_details k ON f.id=k.hrm_kpi_dept_head_assign_master_id AND a.hrm_depertment_id = k.hrm_depertment_id");


        return json_encode(array('data' => $data));

    }

    public function kpi_employee_list(Request $request){
       return view('kpi_employee.kpi_employee_list_data');
    }


    public function kpi_employee_list_data(Request $request){
        $user_id = Auth::user()->id;
        $data    = DB::select("SELECT
                                    a.id,
                                    concat(c.employee_name,' | ',b.employee_code) as employee_name,
                                    d.depertment_name,
                                    e.designation_name,
                                    sum(g.point) as total_points,
                                    concat(bb.description, ' | ', 'From: ', aa.start_date, '  To: ', aa.end_date) as kpi_year,
                                    GROUP_CONCAT(CONCAT(i.description, ' -> ', '<b>', g.description, '</b>')
                                        ORDER BY i.description ASC
                                        SEPARATOR '<br>') AS task_name,
                                    GROUP_CONCAT(CONCAT(g.description)
                                        ORDER BY g.description ASC
                                        SEPARATOR '<br>') AS marks,
                                    GROUP_CONCAT(CONCAT(g.point)
                                        ORDER BY g.point ASC
                                        SEPARATOR '<br>') AS point
                                FROM
                                    hrm_kpi_employee_master a
                                        JOIN
                                    hrm_kpi_assesment_date aa ON a.hrm_kpi_assesment_date_id=aa.id AND aa.id=$request->hrm_kpi_assesment_date_id AND a.status=1
                                        JOIN
                                    hrm_kpi_assesment_year bb ON aa.hrm_kpi_assesment_year_id=bb.id
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND a.valid = 1
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        Join
                                    hrm_depertment d ON b.hrm_depertment_id=d.id
                                        JOIN
                                    hrm_designation e ON b.hrm_designation_id=e.id
                                        JOIN
                                    hrm_kpi_employee_details f ON a.id=f.hrm_kpi_employee_master_id
                                        JOIN
                                    hrm_kpi_marks g ON f.hrm_kpi_marks_id=g.id
                                        JOIN
                                    hrm_kpi_task_details h ON f.hrm_kpi_task_details_id=h.id
                                        JOIN
                                    hrm_kpi_task i ON h.hrm_kpi_task_id=i.id
                                        JOIN
                                    users j ON a.users_id=j.id AND j.id=$user_id

                                GROUP BY a.id,c.employee_name,b.employee_code,d.depertment_name,
                                    e.designation_name,bb.description,aa.start_date,aa.end_date");



        return json_encode(array('data' => $data));

    }





    public function kpi_employee_final_list_data(Request $request){

        $condition = '';

        if (isset($request->location)){
            $condition  =  $condition . " AND b.hrm_location_id = ".$request->location ;
        }

        if (isset($request->department)){
            $condition  =  $condition . " AND b.hrm_depertment_id = ".$request->department ;
        }

        // dd($condition);

        $data      = DB::select("SELECT
                                    k.id as  hrm_kpi_employee_final_summary_id,
                                    a.id,
                                    concat(c.employee_name,' | ',b.employee_code) as employee_name,
                                    d.depertment_name,
                                    e.designation_name,
                                    k.total_point,
                                    k.deduct_point,
                                    cc.location_name,
                                    (k.total_point-k.deduct_point) as net_point,
                                    k.promotion_status as promotion,
                                    CONCAT(ifnull(k.note,''),' - ',ifnull(a.comments,'') ) as note,
                                    Concat(k.increment_amount,k.increment_amount_type) as increment,
                                    concat(bb.description,' | ','From :',aa.start_date,'  To :',aa.end_date) as kpi_year,
                                    GROUP_CONCAT(CONCAT(i.description,' -> ','<b>',g.description,' - ',g.point,'</b>')
                                        ORDER BY i.description ASC
                                        SEPARATOR '<br>') AS task_name,
                                    GROUP_CONCAT(CONCAT(g.description)
                                        ORDER BY g.description ASC
                                        SEPARATOR '<br>') AS marks,
                                    GROUP_CONCAT(CONCAT(g.point)
                                        ORDER BY g.point ASC
                                        SEPARATOR '<br>') AS point
                                FROM
                                    hrm_kpi_employee_master a
                                        JOIN
                                    hrm_kpi_assesment_date aa ON a.hrm_kpi_assesment_date_id=aa.id AND aa.id=$request->hrm_kpi_assesment_date_id AND a.status=2
                                        JOIN
                                    hrm_kpi_assesment_year bb ON aa.hrm_kpi_assesment_year_id=bb.id
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND a.valid = 1
                                        $condition
                                        JOIN
                                    hrm_location cc ON b.hrm_location_id = cc.id
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        Join
                                    hrm_depertment d ON b.hrm_depertment_id=d.id
                                        JOIN
                                    hrm_designation e ON b.hrm_designation_id=e.id
                                        JOIN
                                    hrm_kpi_employee_details f ON a.id=f.hrm_kpi_employee_master_id
                                        JOIN
                                    hrm_kpi_marks g ON f.hrm_kpi_marks_id=g.id
                                        JOIN
                                    hrm_kpi_task_details h ON f.hrm_kpi_task_details_id=h.id
                                        JOIN
                                    hrm_kpi_task i ON h.hrm_kpi_task_id=i.id
                                        JOIN
                                    hrm_kpi_employee_final_summary k ON a.id=k.hrm_kpi_employee_master_id
                                GROUP BY k.id,a.id,c.employee_name,b.employee_code,d.depertment_name,
                                    e.designation_name,bb.description,aa.start_date,aa.end_date,k.increment_amount,k.increment_amount_type,k.total_point,k.promotion_status,k.total_point,k.deduct_point,cc.location_name,k.note");


        return json_encode(array('data' => $data));

    }


   public function kpi_task_list_by_department(Request $request){

        // $data   = DB::select("SELECT
        //                             b.id,a.description
        //                         FROM
        //                             hrm_kpi_task a
        //                                 JOIN
        //                             hrm_kpi_task_details b ON a.id = b.hrm_kpi_task_id AND a.valid = 1 AND b.valid=1
        //                                 JOIN
        //                             hrm_kpi_task_department c ON b.hrm_kpi_task_department_id=c.id
        //                                 JOIN
        //                             hrm_kpi_task_department_details d ON c.id=d.hrm_kpi_task_department_id
        //                                 JOIN
        //                             hrm_depertment e ON d.hrm_depertment_id=e.id AND e.id=$request->department_id");

        $find = DB::select(" SELECT
                                        f.id hrm_kpi_dynamic_table_id,
                                        e.hrm_category_id
                                    FROM
                                        hrm_kpi_set_config a
                                            JOIN
                                        hrm_kpi_task_details b ON a.id = b.hrm_kpi_set_config_id
                                            AND a.hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id
                                            JOIN
                                        hrm_kpi_task c ON b.hrm_kpi_task_id = c.id
                                            JOIN
                                        hrm_kpi_set_config_department d ON a.id = d.hrm_kpi_set_config_id
                                            JOIN
                                        hrm_employee_job_info e ON d.hrm_depertment_id = e.hrm_depertment_id
                                            AND e.id = $request->hrm_employee_job_info_id
                                            AND d.hrm_depertment_id = $request->department_id
                                            JOIN
                                        hrm_kpi_dynamic_table f ON a.tag_with_department = f.id LIMIT 1
                                    ");



        $condition = '';

        if (! empty($find)) {
            $tableId = $find[0]->hrm_kpi_dynamic_table_id;

            switch ($tableId) {
                case 1:
                    $condition = '';
                    break;

                case 2:
                    // Note: Base condition hrm_category
                    $condition = ' JOIN hrm_kpi_set_config_dept_with g ON d.id = g.hrm_kpi_set_config_department_id AND  g.ref_id = e.hrm_category_id  ';
                    break;

                case 3:
                    // Note: Base condition hrm_designation
                    $condition = ' JOIN hrm_kpi_set_config_dept_with g ON d.id = g.hrm_kpi_set_config_department_id AND  g.ref_id = e.hrm_designation_id  ';
                    break;
            }
        }

        // dd($condition);

        $data = DB::select(" SELECT
                                        b.id,
                                        c.description,
                                        a.id hrm_kpi_set_config_id,
                                        f.id hrm_kpi_dynamic_table_id,
                                        e.id hrm_employee_job_info_id
                                    FROM
                                        hrm_kpi_set_config a
                                            JOIN
                                        hrm_kpi_task_details b ON a.id = b.hrm_kpi_set_config_id
                                            AND a.hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id
                                            JOIN
                                        hrm_kpi_task c ON b.hrm_kpi_task_id = c.id
                                            JOIN
                                        hrm_kpi_set_config_department d ON a.id = d.hrm_kpi_set_config_id
                                            JOIN
                                        hrm_employee_job_info e ON d.hrm_depertment_id = e.hrm_depertment_id
                                            AND e.id = $request->hrm_employee_job_info_id
                                            AND d.hrm_depertment_id = $request->department_id
                                            JOIN
                                        hrm_kpi_dynamic_table f ON a.tag_with_department = f.id
                                         $condition ");



        // Note: Ekhane dynamic table implement hobe, apatoto hrm_category table diye query generate kora hosse. 17-04-2023

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
            'hrm_kpi_task_details_id'   => 'required',
            'mark'                      => 'required',
            'hrm_kpi_assesment_date_id' => 'required',
            'hrm_employee_job_info_id'  => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('kpi_employee/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $check_data = DB::SELECT("SELECT id FROM hrm_kpi_employee_master WHERE hrm_employee_job_info_id=$request->hrm_employee_job_info_id AND hrm_kpi_assesment_date_id=$request->hrm_kpi_assesment_date_id");

        if(!empty($check_data)){
          $request->session()->flash('alert-danger', 'Sorry this employee already added !');
          return Redirect::to('kpi_employee');
        }

        DB::beginTransaction();
                try {

                        $insert_master = new HrmKPIEmployeeMaster;
                        $insert_master->hrm_employee_job_info_id  = $request->hrm_employee_job_info_id;
                        $insert_master->hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id;
                        $insert_master->comments                  = $request->comments;
                        $insert_master->users_id                  = Auth::user()->id;
                        $insert_master->valid                     = 1;
                        $insert_master->save();


                        $insert_master_final = new HrmKPIEmployeeFinalSummary;
                        $insert_master_final->hrm_kpi_employee_master_id  = $insert_master->id;
                        $insert_master_final->save();


                        $count_row  = count($request->mark);

                        for($r = 0; $r <$count_row; $r++) {

                            $marks_dtls = HrmKPIMarks::find($request->mark[$r]);

                            $insert_details = new HrmKPIEmployeeDetails;
                            $insert_details->hrm_kpi_employee_master_id = $insert_master->id;
                            $insert_details->hrm_kpi_task_details_id    = $request->hrm_kpi_task_details_id[$r];
                            $insert_details->hrm_kpi_marks_id           = $request->mark[$r];
                            // $insert_details->marks_name                 = $marks_dtls->description;
                            // $insert_details->points                     = $marks_dtls->point;
                            $insert_details->valid                      = 1;

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
        return Redirect::to('kpi_employee');

    }




    public function kpi_employee_final_addnote_submit(Request $request)
    {

        DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET note='$request->note' WHERE id=$request->hrm_kpi_employee_final_summary_id");

        $this->recordActivity(
            1,
           'Created New Employee KPI From Final List',
            null,
            $request->hrm_kpi_employee_final_summary_id,
            'hrm_kpi_employee_final_summary'
        );


        $request->session()->flash('alert-success', 'Note has been added!');
        return Redirect::to('kpi_employee_final_list');
    }

    public function kpi_employee_final_submit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id'                        => 'required',
            'hrm_kpi_assesment_date_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('kpi_employee_list')
                        ->withErrors($validator)
                        ->withInput();
        }


        $count_row  = count($request->id);
        $hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id;


        DB::beginTransaction();
                try{


                    for($r = 0; $r <$count_row; $r++) {

                        $id =$request->id[$r];
                        DB::UPDATE("UPDATE hrm_kpi_employee_master SET status=2 WHERE id=$id");

                        //Total Point Calculation
                        $total_points = DB::SELECT("SELECT SUM(b.point) as points FROM hrm_kpi_employee_details  a JOIN hrm_kpi_marks b ON a.hrm_kpi_marks_id=b.id AND a.valid=1 WHERE hrm_kpi_employee_master_id=$id");

                        if(!empty($total_points)){
                            $total_points = $total_points[0]->points;

                           DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET total_point=$total_points WHERE hrm_kpi_employee_master_id=$id");

                        }else{
                           DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET total_point=0 WHERE hrm_kpi_employee_master_id=$id");

                        }



                    $last_promotion=DB::SELECT("SELECT
                                                       max(d.promotion_date) as start_date
                                                    FROM
                                                        hrm_kpi_employee_master a
                                                            JOIN
                                                        hrm_kpi_assesment_date b ON a.hrm_kpi_assesment_date_id = b.id
                                                            AND a.valid = 1
                                                            AND a.id = $id
                                                            JOIN
                                                        hrm_employee_job_info c ON a.hrm_employee_job_info_id = c.id
                                                            JOIN
                                                        hrm_last_promotion d ON c.hrm_employee_id = d.hrm_employee_id
                                                            AND d.promotion_date<b.end_date
                                                            LIMIT 1
                                                    ");

                        if(!empty($last_promotion[0]->start_date)){
                           $last_promotion=$last_promotion[0]->start_date;
                           DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET last_promotion_date='$last_promotion' WHERE hrm_kpi_employee_master_id=$id");
                        }


                        DB::insert("INSERT INTO hrm_kpi_employee_mark_deduct (hrm_kpi_employee_master_id,
                        activity_date,hrm_file_type_id,point)
                        SELECT
                                a.id AS hrm_kpi_employee_master_id,
                                e.attached_date AS activity_date,
                                e.hrm_file_type_id,
                                d.point
                            FROM
                                hrm_kpi_employee_master a
                                    JOIN
                                hrm_kpi_assesment_date b ON a.hrm_kpi_assesment_date_id = b.id
                                    AND a.valid = 1 AND a.id = $id
                                    JOIN
                                hrm_employee_job_info c ON a.hrm_employee_job_info_id = c.id
                                    AND c.employee_activity = 1
                                    JOIN
                                hrm_kpi_deduction_mark d ON b.id = d.hrm_kpi_assesment_date_id AND d.valid=1
                                    JOIN
                                hrm_employee_file e ON e.hrm_employee_id = c.hrm_employee_id AND d.hrm_file_type_id=e.hrm_file_type_id
                                AND e.attached_date between b.start_date and b.end_date");

                        $deduct_point=DB::SELECT("SELECT SUM(point) as deduct_point FROM hrm_kpi_employee_mark_deduct WHERE hrm_kpi_employee_master_id= $id ");

                        if(!empty($deduct_point[0]->deduct_point)){
                           $deduct_point = $deduct_point[0]->deduct_point;
                           DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET deduct_point=$deduct_point  WHERE hrm_kpi_employee_master_id=$id");
                        }else{
                           DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET deduct_point=0  WHERE hrm_kpi_employee_master_id=$id");
                        }
                        //End employee_mark_deduct



                        //Start salary Increment Percentage
                        $fint_data= DB::SELECT("SELECT sum(total_point-deduct_point) point FROM hrm_kpi_employee_final_summary  WHERE hrm_kpi_employee_master_id=$id");

                        $net_point= $fint_data[0]->point;

                        $incr_data= DB::SELECT("SELECT id,increment_amount,(IF(increment_amount_type=1,'%','Tk.')) amount_type FROM hrm_kpi_increment_range   where  hrm_kpi_assesment_date_id=$hrm_kpi_assesment_date_id  AND   valid=1 AND $net_point BETWEEN number_from and number_to AND entry_status=1");

                        if(!empty($incr_data[0]->id)){
                           $incr_id               =  $incr_data[0]->id;
                           $increment_amount      =  $incr_data[0]->increment_amount;
                           $increment_amount_type =  $incr_data[0]->amount_type;
                           // dd($increment_amount_type);
                           DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET hrm_kpi_increment_range_id=$incr_id,increment_amount=$increment_amount,
                                        increment_amount_type='$increment_amount_type' WHERE hrm_kpi_employee_master_id=$id");
                        }
                        //END salary Increment Percentage

                        //Start Promotion Status
                        $prom_data= DB::SELECT("SELECT id FROM hrm_kpi_increment_range  where   hrm_kpi_assesment_date_id=$hrm_kpi_assesment_date_id AND
                            valid=1 and $net_point BETWEEN number_from and number_to AND entry_status=2");
                        // dd("WORKING");

                        if(!empty($prom_data[0]->id)){
                           $prom_id =  $prom_data[0]->id;
                           DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET hrm_kpi_promotion_range_id=$prom_id,promotion_status='Yes' WHERE hrm_kpi_employee_master_id=$id");
                        }else{
                           DB::UPDATE("UPDATE hrm_kpi_employee_final_summary SET promotion_status='No' WHERE hrm_kpi_employee_master_id=$id");
                        }
                        //Start Promotion Status



                    }

                       DB::commit();
                    }catch (\Exception $e) {
                        DB::rollback();
                        $validator->errors()->add('field', $e->getMessage());
                        return response()->json($validator->errors()->all());
                    }





        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('kpi_employee_list');

        // dd( $request->all());

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

       //  $month       = $request->month;
       //  $year        = $request->year;

       // $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
       // $month_name=$month_name[0]->month_name;


       //  $parameter="";
       //  if($request->location<>0){
       //          $parameter  = " AND d.id = $request->location";
       //  }

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Employee Annual Evaluation",
            // 'condition_parameter'   => $parameter,
            // 'hrm_month_id'          => $month,
            'id'               => $id,
        );


        $report_path = Config::get('configaration.report_path').'hrm_kpi_employee_details';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        // $generate_type  = $request->generate_type;
        $generate_type  = "pdf";

        if (strlen($report) > 940){

            // if($generate_type=="pdf"){

                    $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
                    header("Content-type:application/pdf");
                    echo $report;
                    echo "data:application/pdf;base64, " . $report;

            // }else{

            //         $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
            //         header('Cache-Control: must-revalidate');
            //         header('Pragma: public');
            //         header('Content-Description: File Transfer');
            //         header('Content-Disposition: attachment; filename=mr_statement.'.$generate_type);
            //         header('Content-Transfer-Encoding: binary');
            //         header('Content-Length: ' . strlen($report));
            //         header('Content-Type: application/'.$generate_type);
            //         echo $report;
            // }
        }else{
           dd("No data found");
        }
    }








   public function kpi_summary_report (Request $request)
    {


        // $location_name = HrmLocation::find($request->location);

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );



        $parameter = "AND aa.id = $request->hrm_kpi_assesment_date_id";

        // if ($request->location==0){
        //     $title = '-All-';
        // }else{
        //     $title = $location_name->location_name;
        //     $parameter  =  $parameter . " AND c.id = ".$request->location ;
        // }

        // if (isset($request->depertment)){
        //     $parameter  =  $parameter . " AND d.id = ".$request->depertment ;
        // }

        // if (isset($request->designation)){
        //     $parameter  =  $parameter . " AND e.id = ".$request->designation ;
        // }

        // if (isset($request->employee_name)){
        //     $parameter  =  $parameter . " AND a.id = ".$request->employee_name ;
        // }

        // if (isset($request->bloodgroup_name)){
        //     $parameter  =  $parameter . " AND a.hrm_blood_group_id = ".$request->bloodgroup_name ;
        // }

// dd($parameter);

        // $user_id  = Auth::user()->id;
        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Annual Evaluation Summary Report",
            'condition_parameter'   => $parameter,
            // 'user_id'               => $user_id,
        );


        $report_path = Config::get('configaration.report_path').'hrm_kpi_employee_summary';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        // $generate_type  = $request->generate_type;
        $generate_type  = 'pdf';

        if (strlen($report) > 940){

            if($generate_type=="pdf"){

                    $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
                    header("Content-type:application/pdf");
                    echo $report;
                    echo "data:application/pdf;base64, " . $report;

            }else{

                    $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Description: File Transfer');
                    header('Content-Disposition: attachment; filename=mr_statement.'.$generate_type);
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . strlen($report));
                    header('Content-Type: application/'.$generate_type);
                    echo $report;
            }
        }else{
           dd("No data found");
        }
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
           $data   = DB::select("SELECT
                                a.id as hrm_kpi_employee_master_id,
                                concat(c.employee_name,' | ',b.employee_code) as employee_name,
                                c.id as employee_id,
                                d.id as department_id,
                                d.depertment_name,
                                e.designation_name,
                                concat(bb.description,' | ','From :',aa.start_date,'  To :',aa.end_date) as kpi_date,
                                b.id as hrm_employee_job_info_id,
                                a.comments

                            FROM
                                hrm_kpi_employee_master a
                                    JOIN
                                hrm_kpi_assesment_date aa ON a.hrm_kpi_assesment_date_id=aa.id AND a.id=$id
                                    JOIN
                                hrm_kpi_assesment_year bb ON aa.hrm_kpi_assesment_year_id=bb.id
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    AND a.valid = 1
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    Join
                                hrm_depertment d ON b.hrm_depertment_id=d.id
                                    JOIN
                                hrm_designation e ON b.hrm_designation_id=e.id
");

         return view('kpi_employee.edit_kpi_employee')->with('data',$data);

    }


    public function kpi_task_list_by_emp_jobid(Request $request)
    {


           $data   = DB::select("SELECT
                                        h.id AS hrm_kpi_task_details_id,
                                        i.description,
                                        g.id AS hrm_kpi_marks_id,
                                        CONCAT(g.description) AS marks,
                                        h.hrm_kpi_set_config_id,
                                        j.id as hrm_kpi_set_config_id
                                    FROM
                                        hrm_kpi_employee_master a
                                            JOIN
                                        hrm_kpi_employee_details f ON a.id = f.hrm_kpi_employee_master_id
                                            AND a.id = $request->hrm_kpi_employee_master_id
                                            JOIN
                                        hrm_kpi_marks g ON f.hrm_kpi_marks_id = g.id
                                            JOIN
                                        hrm_kpi_task_details h ON f.hrm_kpi_task_details_id = h.id
                                            JOIN
                                        hrm_kpi_task i ON h.hrm_kpi_task_id = i.id
                                            JOIN
                                        hrm_kpi_set_config  j ON a.hrm_kpi_assesment_date_id = j.hrm_kpi_assesment_date_id
                                        AND g.hrm_kpi_set_config_id = j.id
                                            JOIN
                                        hrm_kpi_set_config_department k ON j.id = k.hrm_kpi_set_config_id
                                        GROUP BY h.id");




           
            return json_encode(array('data' => $data));
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

        $validator = Validator::make($request->all(), [
            'hrm_kpi_employee_master_id'=> 'required',
            'mark'                      => 'required',
            'hrm_kpi_task_details_id'   => 'required',
            'hrm_employee_job_info_id'  => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('kpi_employee/create')
                        ->withErrors($validator)
                        ->withInput();
        }



        DB::beginTransaction();
                try {

                       DB::DELETE("DELETE FROM hrm_kpi_employee_details WHERE hrm_kpi_employee_master_id=$request->hrm_kpi_employee_master_id");

                        $insert_master = HrmKPIEmployeeMaster::find($request->hrm_kpi_employee_master_id);
                        // $insert_master->hrm_employee_job_info_id  = $request->hrm_employee_job_info_id;
                        // $insert_master->hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id;
                        $insert_master->comments                  = $request->comments;
                        $insert_master->users_id                  = Auth::user()->id;
                        // $insert_master->valid                     = 1;
                        $insert_master->save();

                        $count_row  = count($request->mark);

                        for($r = 0; $r <$count_row; $r++) {
                            $insert_details = new HrmKPIEmployeeDetails;
                            $insert_details->hrm_kpi_employee_master_id = $request->hrm_kpi_employee_master_id;
                            $insert_details->hrm_kpi_task_details_id    = $request->hrm_kpi_task_details_id[$r];
                            $insert_details->hrm_kpi_marks_id           = $request->mark[$r];
                            $insert_details->valid                      = 1;
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
        return Redirect::to('kpi_employee');

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


        // dd("Developing...");

        $cancel = HrmKPIEmployeeMaster::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }


        DB::table('hrm_kpi_employee_details')->where('hrm_kpi_employee_master_id', '=', $id)->delete();
        DB::table('hrm_kpi_employee_final_summary')->where('hrm_kpi_employee_master_id', '=', $id)->delete();
        DB::table('hrm_kpi_employee_mark_deduct')->where('hrm_kpi_employee_master_id', '=', $id)->delete();
        DB::table('hrm_kpi_employee_master')->delete($id);


        $request->session()->flash('alert-success', 'successfully deleted !');

        return Redirect::to('kpi_employee_final_list');

    }


    public function final_cancel(Request $request,$id){

            DB::UPDATE("UPDATE hrm_kpi_employee_master SET status=1 WHERE id=$id");


            DB::UPDATE("UPDATE hrm_kpi_employee_final_summary
                                SET
                                    last_promotion_date = null,
                                    total_point = 0,
                                    hrm_kpi_increment_range_id = null,
                                    increment_amount = 0,
                                    increment_amount_type = '',
                                    deduct_point = 0,
                                    promotion_status = '',
                                    hrm_kpi_promotion_range_id = null
                                WHERE
                                    hrm_kpi_employee_master_id = $id");

            DB::table('hrm_kpi_employee_mark_deduct')->where('hrm_kpi_employee_master_id', '=', $id)->delete();

            $this->recordActivity(
                 1,
                 'Deleted Employee KPI From Final List',
                 null,
                 $id,
                 'hrm_kpi_employee_master'
            );

            return Redirect::to('kpi_employee_final_list');

    }



    public function final_data_cancel(Request $request){


        $count_row  = count($request->id);


        DB::beginTransaction();
                try{

                    for($r = 0; $r <$count_row; $r++) {

                        $id =$request->id[$r];

                        DB::UPDATE("UPDATE hrm_kpi_employee_master SET status=1 WHERE id=$id");


                        DB::UPDATE("UPDATE hrm_kpi_employee_final_summary
                                            SET
                                                last_promotion_date = null,
                                                total_point = 0,
                                                hrm_kpi_increment_range_id = null,
                                                increment_amount = 0,
                                                increment_amount_type = '',
                                                deduct_point = 0,
                                                promotion_status = '',
                                                hrm_kpi_promotion_range_id = null
                                            WHERE
                                                hrm_kpi_employee_master_id = $id");

                        DB::table('hrm_kpi_employee_mark_deduct')->where('hrm_kpi_employee_master_id', '=', $id)->delete();
                    }

                       DB::commit();
                       // return Redirect::to('kpi_employee_final_list');

                    }catch (\Exception $e) {
                        DB::rollback();
                        $validator->errors()->add('field', $e->getMessage());
                        return response()->json($validator->errors()->all());
                    }
                    $request->session()->flash('alert-success', 'data has been successfully deleted!');
                    return Redirect::to('kpi_employee_final_list');


        }


}
