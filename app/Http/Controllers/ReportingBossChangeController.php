<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\HrmEmployeeReportingBossChange;
use Redirect;
use Response;

class ReportingBossChangeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('reporting_boss_change.index')
            ->with('default_user_location',  $default_user_location) ;
    }

    public function reporting_boss_change_list(){
        return view('reporting_boss_change.details');
    }

    public function employeeList(Request $request)
    {
        $date_from = date('Y-m-d',strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to));

        $condition = '';

        if ($request->date_from != '' && $request->date_to != '') {
            $condition = " AND DATE(a.created_at) between '$date_from' AND '$date_to' ";
        }

        if ($manage_by_id = $request->manage_by) {
            $condition .= " AND a.transfer_to_employee_id = {$manage_by_id}";
        }

        if ($location_id = $request->location) {
            $condition .= " AND c.hrm_location_id = {$location_id}";
        }

        $employeeList = DB::select("SELECT
                b.id,
                b.employee_name,
                DATE_FORMAT(a.created_at,'%d-%m-%Y') as change_date,
                b.contact_number,
                d.depertment_name,
                e.joining_date,
                f.location_name,
                g.employee_name as manage_from ,
                h.employee_name as manage_to,
                i.name as change_by
            FROM
                hrm_reporting_boss_change a
                JOIN hrm_employee_job_info c ON a.hrm_employee_job_id = c.id
                $condition
                JOIN hrm_employee b ON c.hrm_employee_id = b.id
                JOIN hrm_depertment d ON c.hrm_depertment_id = d.id AND d.valid =1
                JOIN hrm_employee_joining e ON  c.hrm_employee_id = e.hrm_employee_id
                JOIN hrm_location f ON c.hrm_location_id = f.id AND f.valid = 1
                JOIN hrm_employee g ON a.transfer_from_employee_id = g.id
                JOIN hrm_employee h ON a.transfer_to_employee_id = h.id
                JOIN users i ON a.transfer_by_user_id = i.id and i.valid =1") ;

        return datatables()->of($employeeList)
        ->make(true);
    }

    public function create()
    {
        $user_id = Auth::user()->id;
        $user_location = DB::select("SELECT a.id,a.location_name,b.default_location FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $user_id");

          return view('reporting_boss_change.create')
               -> with('user_location',  $user_location) ;

    }

    public function reportingEmployeeList(Request $request)
    {

        $user_id=Auth::user()->id;

        $hrm_location_id = $request->hrm_location_id;
        if(empty($request->hrm_location_id)){
            $hrm_location_id = 0;
        }

        $hrm_manage_by_id = $request->hrm_manage_by_id;

        if(empty($request->hrm_manage_by_id)){
            $hrm_manage_by_id = 0;
        }



        $data = DB::select("SELECT
                                    b.id,
                                    CONCAT(a.employee_name,
                                            ' | ',
                                            IFNULL(b.employee_code, ' ')) AS employee_name,
                                    c.location_name,
                                    j.employee_name AS manage_by_name,
                                    LPAD(a.id, 5, '0') AS Unique_Code,
                                    d.depertment_name,
                                    e.designation_name,
                                    a.contact_number,
                                    DATE_FORMAT(f.joining_date, '%d-%m-%Y') AS joining_date,
                                    e.priority,
                                    a.Images
                                FROM
                                    hrm_employee a
                                        JOIN
                                    hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                        AND a.active_status = 1
                                        AND b.employee_activity = 1
                                        AND b.hrm_location_id = $hrm_location_id
                                        AND b.hrm_manage_by_id = $hrm_manage_by_id
                                        JOIN
                                    hrm_location c ON b.hrm_location_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_designation e ON b.hrm_designation_id = e.id
                                        JOIN
                                    hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                        JOIN
                                    hrm_employee j ON b.hrm_manage_by_id = j.id
                                        JOIN
                                    user_location g ON b.hrm_location_id = g.hrm_location_id
                                    AND g.users_id =  $user_id
                                    GROUP BY b.id
        ");

        return json_encode(array('data' => $data));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'location'     => 'required',
            'manage_by'        => 'required',
            'new_manage_by'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }


        DB::beginTransaction();
        try {
            foreach ($request->employee_ids as $empId) {

                // dd($empId);
                $exists = DB::table('hrm_reporting_boss_change')
                    ->where('hrm_employee_job_id', $empId)
                    ->where('transfer_from_employee_id', $request->manage_by)
                    ->whereRaw('DATE(created_at) = ?', [date('Y-m-d')])
                    ->exists();

                if ($exists) {
                    return Response::json(array(
                        'success'           => false,
                        'error_messages'    => "Today Employee Already Transferred",
                        'errors'          => "insert problem !! "
                    ));
                }

                // Update the employee's manager
                $model = HrmEmployeeJobInfo::where(['id' => $empId, 'employee_activity' => 1])->first();
                $model->hrm_manage_by_id = $request->new_manage_by;
                $model->save();

                // Insert a record into hrm_reporting_boss_change table
                $insert = new HrmEmployeeReportingBossChange();
                $insert->hrm_employee_job_id = $empId;
                $insert->transfer_from_employee_id = $request->manage_by;
                $insert->transfer_to_employee_id = $request->new_manage_by;
                $insert->transfer_by_user_id = auth::id();
                $insert->save();
            }

        DB::commit();
        $request->session()->flash('alert-success', 'Successfully Insert!');
        } catch (\Exception $e) {
        DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
        }

    }

    public function _store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'location'     => 'required',
            'manage_by'        => 'required',
            'new_manage_by'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()->all()]);
        }

        DB::beginTransaction();
        try {
            foreach ($request->id as $empId) {
                // dd($empId);
                $exists = DB::table('hrm_reporting_boss_change')
                    ->where('transfer_to_employee_id', $request->new_manage_by)
                    ->whereRaw('DATE(created_at) = ?', [date('Y-m-d')])
                    ->exists();

                    // if ($exists) {
                    //     return response()->json([
                    //         'status' => false,
                    //         'message' => 'Today Employee Already Transferred',
                    //         'code' => 200
                    //     ]);
                    // }

                // Update the employee's manager
                $model = HrmEmployeeJobInfo::where(['id' => $empId, 'employee_activity' => 1])->first();
                $model->hrm_manage_by_id = $request->new_manage_by;
                $model->save();

                // Insert a record into hrm_reporting_boss_change table
                $insert = new HrmEmployeeReportingBossChange();
                $insert->hrm_employee_job_id = $empId;
                $insert->transfer_from_employee_id = $request->manage_by;
                $insert->transfer_to_employee_id = $request->new_manage_by;
                $insert->transfer_by_user_id = auth::id();
                $insert->save();
            }

            DB::commit();
            return response()->json(['success' => 'Record successfully inserted']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => $e->getMessage()]);
        }
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

     public function monthly_jobcard_report (Request $request)
    {

        // dd($request->all());
        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );
        $location_name   = HrmLocation::find($request->location)->location_name;

         $subquery = "WHERE hrm_location_id = " .$request->location;

        if($request->date_range == 1){
           
            $date_of_month  = date("Y-m-d", strtotime($request->from_date));
            $lastdate       = date("Y-m-d", strtotime($request->to_date));
            $parameter = '';
            $comment_parameter = '';
            $subquery_parameter = '';
          
            $location = (int) ($request->location ?? 0);
            $parameter = "AND b.hrm_location_id = $location AND a.punche_date between '$date_of_month' AND '$lastdate'" ;
            
          
            // $comment_parameter = "AND a.punche_date between '$date_of_month' AND '$lastdate' AND b.hrm_location_id = $location " ;
            // dd($comment_parameter);


            $subquery_parameter=" AND punche_date between '$date_of_month' AND '$lastdate' AND hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info $subquery ) ";

             
            $month_name = $date_of_month.' To '.$lastdate;
            
        }else{
            $month      =$request->month;
            $year      = $request->year;
        //    if (!empty($request->location)) {
                $parameter  =  "AND Month(a.punche_date)=$month  And Year(a.punche_date)=$year  AND b.hrm_location_id = ".$request->location ;
              

                $comment_parameter = "AND Month(a.punch_date)=$month  And Year(a.punch_date)=$year  AND b.hrm_location_id = ".$request->location ;
                $comment_parameter1 = "AND Month(a.punche_date)=$month  And Year(a.punche_date)=$year  AND b.hrm_location_id = ".$request->location ;
            // }
            $subquery_parameter=" AND Month(punche_date)=$month  And Year(punche_date)=$year AND hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info $subquery ) ";

            $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
            $month_name=  $month_name[0]->month_name ."-". $year;
        }
       
     

        

        // dd('ok');
        

         if (isset($request->depertment)){
            $parameter          =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
            $comment_parameter  =  $comment_parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
            $comment_parameter1  =  $comment_parameter1 . " AND b.hrm_depertment_id = ".$request->depertment ;
            $subquery            =  $subquery . " AND hrm_depertment_id = ".$request->depertment ;
        }


        if (isset($request->section)){
            $parameter          =  $parameter . " AND b.hrm_section_id = ".$request->section ;
            $comment_parameter  =  $comment_parameter . " AND b.hrm_section_id = ".$request->section ;
            $comment_parameter1  =  $comment_parameter1 . " AND b.hrm_section_id = ".$request->section ;
            $subquery           =  $subquery . " AND hrm_section_id = ".$request->section ;
        }


        if (isset($request->category)){
            $parameter          =  $parameter . " AND b.hrm_category_id = ".$request->category ;
            $comment_parameter  =  $comment_parameter . " AND b.hrm_category_id = ".$request->category ;
            $comment_parameter1  =  $comment_parameter1 . " AND b.hrm_category_id = ".$request->category ;
            $subquery           =  $subquery . " AND hrm_category_id = ".$request->category ;
        }



        if (isset($request->employee_name)){
            $list = implode(',',$request->employee_name);

            $parameter           =  $parameter . " AND b.hrm_employee_id in ($list)" ;
            $comment_parameter   =  $comment_parameter . " AND b.hrm_employee_id in ($list)" ;
            $comment_parameter1   =  $comment_parameter1 . " AND b.hrm_employee_id in ($list)" ;
            $subquery   =  $subquery . " AND hrm_employee_id in ($list)" ;
        }

       

     

        $status = '';
        if($request->report_for==1){
            $status = '';
            $report_path = Config::get('configaration.report_path').'hrm_job_card_report';
        }else{

            $status = '(CW Only)';
            $report_path = Config::get('configaration.report_path').'hrm_job_card_report_cw';
        }

       

        if($request->report_for==1){
         $controls = array(
                'company_name'          => Config::get('configaration.company_name'),
                'address'               => Config::get('configaration.company_address'),
                'title'                 => "Job Card Report $status || ".$month_name." || ".$location_name,
                'subquery_parameter'    => $subquery_parameter,
                'condition_parameter'   => $parameter,
                'condition_parameter_1' => $comment_parameter,



            );
        }else{

            // dd($comment_parameter);
             $controls = array(
                'company_name'          => Config::get('configaration.company_name'),
                'address'               => Config::get('configaration.company_address'),
                'title'                 => "Job Card Report $status || ".$month_name." || ".$location_name,
                'subquery_parameter'    => $subquery_parameter,
                'condition_parameter'   => $comment_parameter,
                'condition_parameter_1' => $comment_parameter1,



            );
        }

       

        // dd($controls);

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;
        // dd($report);
       
        if (strlen($report) > 988){

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

}
