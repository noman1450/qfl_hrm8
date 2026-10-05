<?php

namespace App\Http\Controllers;

use Jaspersoft\Client\Client;
use Jaspersoft\Service\jobService;
use Jaspersoft\Service\ReportService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Input;
use Validator;
use Redirect;
use Session;
use Crypt;
use Config;
use Response;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CostToTheCompanyExport;

use App\User;
use App\Models\HrmLocation;
use App\Models\HrmShift;
use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmBonus;
use App\Models\HrmLeaveYear;
use App\Models\HrmCategory;
use App\Models\HrmDepertment;
use App\Models\HrmSection;
use App\Models\HrmBank;
use App\Models\HrmEmployeeBonusMaster;
use App\Models\HrmMonth;
use Carbon\Carbon;



class ReportsController extends Controller
{


    function __construct(){
        $this->middleware('auth');
        // ob_clean();

        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        // flush();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
     {


        $c_month =Carbon::now()->month;
        $userid  = Auth::user()->id;

        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $userid AND a.`default_location`=1");

        $location = DB::select("SELECT a.id,a.location_name FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $userid");



        $status      = DB::select("SELECT id,attendance_status FROM hrm_attendance_status");
        $month       = DB::Select("SELECT id,month_name,$c_month as c_month FROM hrm_month");
        $leave_years = DB::Select("SELECT id,leave_year FROM hrm_leave_years WHERE active_status<>0 ORDER BY Id DESC");
        $bankname    = DB::Select("SELECT id,bank_name  FROM hrm_bank");

         return view('reports.reports')
              ->with('location',$location)
              ->with('status',$status)
              ->with('leave_years',$leave_years)
              ->with('default_user_location',$default_user_location)
              ->with('bankname',$bankname)
              ->with('month',$month);
    }




    public function attendance_report (Request $request)
    {

        // For All Company
        if ($request->report_status==0){
            $status="All";
        }else{
            $query=DB::SELECT("SELECT attendance_status FROM hrm_attendance_status WHERE id=$request->report_status");
            $status=$query[0]->attendance_status;
        }

        $location_name = HrmLocation::find($request->location);

        if (isset($request->depertment)){
                $dept = HrmDepertment::find($request->depertment);
                $dept_name=$dept->depertment_name;

        }else{
                $dept_name = "All Department";
        }

        $date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $parameter = " AND (a.punche_date) = '". $date . "' ";

        $parameter = $parameter . " AND a.hrm_employee_id IN (SELECT
                                                            b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            '". $date . "' BETWEEN a.start_date AND a.end_date
                                                                AND a.end_date IS NOT NULL
                                                                AND a.hrm_employee_activity_status_id not in (7) UNION ALL SELECT
                                                           b.hrm_employee_id
                                                        FROM
                                                            hrm_employee_activity a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id= b.id
                                                        WHERE
                                                            a.end_date IS NULL
                                                                AND '". $date . "' >= a.start_date AND a.hrm_employee_activity_status_id not in (7))";




        $parameter = $parameter ." AND h.id IN (SELECT id FROM hrm_employee_shift
                     WHERE  '". $date . "'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                     UNION ALL
                     SELECT id FROM hrm_employee_shift WHERE end_date IS NULL
                     AND '". $date . "' >= start_date)";

        if (isset($request->location)){
            $parameter  =  $parameter . " AND b.hrm_location_id = ".$request->location ;
        }

        if (isset($request->section)){
            $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;
        }

         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
        }


         if (isset($request->category)){
            $parameter  =  $parameter . " AND b.hrm_category_id = ".$request->category ;
        }

         if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND b.hrm_employee_id = ".$request->employee_name ;
        }


        if (isset($request->working_shift)){
            $parameter  =  $parameter . " AND i.id = ".$request->working_shift ;
        }


        if ($request->report_status==0){
        }else{
            $parameter  =  $parameter . " AND a.attendance_status = ".$request->report_status ;
        }



        $parameter2 = " AND b.id IN (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                    WHERE  '". $date . "'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                    AND hrm_employee_activity_status_id not in (7) UNION ALL
                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                    AND '". $date . "' >= start_date AND hrm_employee_activity_status_id not in (7))";
//  dd($parameter2);

        // dd($parameter,$parameter2);


        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Daily Attendance(".$status .") || Dated: ".$request->date."($dept_name)"."[$location_name->location_name]",
            'condition_parameter'   => $parameter,
            'condition_parameter_1' => $parameter2,
            'attendance_date'       => "Attendance Date - ".$request->date,
        );
        $report_path    = Config::get('configaration.report_path').'hrm_attendance';
        $report         = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

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




    //Only FOR Al-Mostafa

        // if ($request->report_status==0){
        //     $status="All";
        // }else{
        //     $query=DB::SELECT("SELECT attendance_status FROM hrm_attendance_status WHERE id=$request->report_status");
        //     $status=$query[0]->attendance_status;
        // }
        // $location_name = HrmLocation::find($request->location);
        // if (isset($request->working_shift)){
        //         $shift = HrmShift::find($request->working_shift);
        //         $shift_name=$shift->shift_name;
        // }else{
        //         $shift_name = "All Shift";
        // }

        // $date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));

        // $jasper_server = new Client(
        //     Config::get('configaration.jasperjasper_url'),
        //     Config::get('configaration.jasper_user'),
        //     Config::get('configaration.jasper_password')
        // );

        // $parameter = " AND (a.punche_date) = '". $date . "' ";


        // if (isset($request->location)){
        //     $parameter  =  $parameter . " AND b.hrm_location_id = ".$request->location ;
        // }

        // if (isset($request->section)){
        //     $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;
        // }

        //  if (isset($request->depertment)){
        //     $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
        // }

        //  if (isset($request->designation)){
        //     $parameter  =  $parameter . " AND b.hrm_designation_id = ".$request->designation ;
        // }

        //  if (isset($request->employee_name)){
        //     $parameter  =  $parameter . " AND b.hrm_employee_id = ".$request->employee_name ;
        // }


        // if (isset($request->working_shift)){
        //     $parameter  =  $parameter . " AND i.id = ".$request->working_shift ;
        // }


        // if ($request->report_status==0){
        // }else{
        //     $parameter  =  $parameter . " AND a.attendance_status = ".$request->report_status ;
        // }
        // $controls = array(
        //     'company_name'          => Config::get('configaration.company_name'),
        //     'address'               => Config::get('configaration.company_address'),
        //     'title'                 => "Daily Attendance(".$status .") || Dated: ".$request->date."($shift_name)"."[$location_name->location_name]",
        //     'condition_parameter'   => $parameter,
        //     // 'condition_parameter_1' => $parameter2,
        //     'attendance_date'       => "Attendance Date - ".$request->date,
        // );
        // $report_path    = Config::get('configaration.report_path').'hrm_attendance_amg';
        // $report         = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        // $generate_type  = $request->generate_type;

        // if (strlen($report) > 988){

        //     if($generate_type=="pdf"){
        //             $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
        //             header("Content-type:application/pdf");
        //             echo $report;
        //             echo "data:application/pdf;base64, " . $report;
        //     }else{

        //             $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
        //             header('Cache-Control: must-revalidate');
        //             header('Pragma: public');
        //             header('Content-Description: File Transfer');
        //             header('Content-Disposition: attachment; filename=mr_statement.'.$generate_type);
        //             header('Content-Transfer-Encoding: binary');
        //             header('Content-Length: ' . strlen($report));
        //             header('Content-Type: application/'.$generate_type);
        //             echo $report;
        //     }
        // }else{
        //    dd("No data found");
        // }






    }





   public function categorywise_manpower_report (Request $request)
   {

       if (isset($request->depertment)) {
           $shift = HrmDepertment::find($request->depertment);
           $shift_name = $shift->shift_name;

       } else {
           $shift_name = "All Deptartment";
       }

       $date = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));

       $jasper_server = new Client(
           Config::get('configaration.jasperjasper_url'),
           Config::get('configaration.jasper_user'),
           Config::get('configaration.jasper_password')
       );


       $parameter = "";

       if ($request->location == 0) {
           $location_name = 'All Location';
       } else {
           $parameter = $parameter . " AND c.id = " . $request->location;
           $location_name = HrmLocation::find($request->location);
           $location_name = $location_name->location_name;
       }

       if (isset($request->depertment)) {
           $parameter = $parameter . " AND d.id = " . $request->depertment;
       }

       if (isset($request->designation)) {
           $parameter = $parameter . " AND e.id = " . $request->designation;
       }

       if (isset($request->employee_name)) {
           $parameter = $parameter . " AND a.id = " . $request->employee_name;
       }

       if (isset($request->category)) {
           $parameter = $parameter . " AND h.id = " . $request->category;
       }


       if (isset($request->working_shift)) {
           $parameter = $parameter . " AND l.id = " . $request->working_shift;
       }


       $user_id = Auth::user()->id;
       $controls = array(
           'company_name' => Config::get('configaration.company_name'),
           'address' => Config::get('configaration.company_address'),
           'title' => "Category Wise Employee List" . "($shift_name)" . "[$location_name]",
           'condition_parameter' => $parameter,
           'user_id' => $user_id,
       );


       $report_path = Config::get('configaration.report_path') . 'hrm_category_wise_man_power';

       $report = $jasper_server->reportService()->runReport($report_path, "pdf", null, null, $controls);

       $generate_type = $request->generate_type;

       if (strlen($report) > 988) {

           if ($generate_type == "pdf") {

               $report = $jasper_server->reportService()->runReport($report_path, $generate_type, null, null, $controls);
               header("Content-type:application/pdf");
               echo $report;
               echo "data:application/pdf;base64, " . $report;

           } else {

               $report = $jasper_server->reportService()->runReport($report_path, $generate_type, null, null, $controls);
               header('Cache-Control: must-revalidate');
               header('Pragma: public');
               header('Content-Description: File Transfer');
               header('Content-Disposition: attachment; filename=mr_statement.' . $generate_type);
               header('Content-Transfer-Encoding: binary');
               header('Content-Length: ' . strlen($report));
               header('Content-Type: application/' . $generate_type);
               echo $report;
           }
       } else {
           dd("No data found");
       }
   }


   public function manpower_report(Request $request)
    {


        if (isset($request->depertment)){
                $shift = HrmDepertment::find($request->depertment);
                $shift_name = $shift->depertment_name;

        }else{
                $shift_name = "All Department";
        }

        $date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $user_id = Auth::user()->id;
        $parameter = "";




        // if($request->location<>0){
        //     $location = HrmLocation::find($request->location);
        //     $location_name =  $location->location_name;

        //     $parameter  =  $parameter . " AND c.id = ".$request->location ;
        // }else{
        //     $location_name = 'All Location';
        // }

        if($request->location==0){
            $parameter = "";
            $location_name = 'All Location';
        }elseif($request->location=='999'){
            $parameter  =  $parameter . " AND c.id in (SELECT id FROM hrm_location WHERE location_type=3 AND valid = 1) ";
            $location_name = 'All Depot';
        }else{
            $parameter  =  $parameter . " AND c.id = ".$request->location ;
            $location_name = HrmLocation::find($request->location)->location_name;
        }



        if($request->manpower_daterange==1){

           $datefrom    = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
           $dateto      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));


            if($request->joining=='joining_date'){
                $parameter  =  $parameter . " AND f.joining_date between '$datefrom' AND '$dateto' ";
            }else{
                $parameter  =  $parameter . " AND f.confirmation_date between '$datefrom' AND '$dateto' ";

            }
        }


        // if (isset($request->counting_month)){

        //     if($request->joining=='joining_date'){
        //         $parameter  =  $parameter . " AND TIMESTAMPDIFF(MONTH,f.joining_date,CURDATE())< ".$request->counting_month ;
        //     }else{
        //         $parameter  =  $parameter . " AND TIMESTAMPDIFF(MONTH,f.confirmation_date,CURDATE())< ".$request->counting_month ;
        //     }
        // }

        // if (isset($request->befor_this_day)){
        //     $befor_this_day     = date('Y-m-d', strtotime(str_replace('/', '-', $request->befor_this_day)));

        //     if($request->joining=='joining_date'){
        //         $parameter  =  $parameter . " AND f.joining_date<'$befor_this_day'" ;
        //     }else{
        //         $parameter  =  $parameter . " AND f.confirmation_date<'$befor_this_day' " ;
        //     }
        // }

        // dd($parameter);


        if (isset($request->plant_name)){
            $parameter  =  $parameter . " AND b.hrm_plant_id = ".$request->plant_name ;
        }

        if (isset($request->section)){
            $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;
        }


       if (isset($request->religion)){
            $parameter  =  $parameter . " AND a.hrm_religion_id = ".$request->religion ;
        }


         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND d.id = ".$request->depertment ;
        }

         if (isset($request->designation)){
            $parameter  =  $parameter . " AND b.hrm_designation_id=".$request->designation ;
        }

         if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND a.id = ".$request->employee_name ;
        }




        if (isset($request->category)){
            $list = implode(',',$request->category);
            $parameter  =  $parameter . " AND h.id in (".$list.')' ;
        }


        if (isset($request->working_shift)){
            $parameter  =  $parameter . " AND l.id = ".$request->working_shift ;
        }

        if (isset($request->working_shift)){
            $parameter  =  $parameter . " AND l.id = ".$request->working_shift ;
        }

// dd($list);

        if (isset($request->employee_type)){
            $list = implode(',',$request->employee_type);
            $parameter  =  $parameter . " AND b.hrm_employment_status_id in ($list)" ;
        }



        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Man Power Report"."($shift_name)"."[$location_name]",
            'condition_parameter'   => $parameter,
            'user_id'               => $user_id,
        );


        $report_path = Config::get('configaration.report_path').'hrm_man_power';


        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);


        $generate_type  = $request->generate_type;

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




   public function bloodgroup_report (Request $request)
    {


        $location_name = HrmLocation::find($request->location);

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        // dd($request->all());
        $parameter = "";

        if ($request->location==0){
            $title = '-All-';
        }
        elseif($request->location == '777'){
            $title = '-Only Depot-';
            $parameter  =  $parameter." AND c.location_type = 3";
        }
        else{
            $title = $location_name->location_name;
            $parameter  =  $parameter . " AND c.id = ".$request->location ;
        }

        if (isset($request->depertment)){
            $parameter  =  $parameter . " AND d.id = ".$request->depertment ;
        }

        if (isset($request->designation)){
            $parameter  =  $parameter . " AND e.id = ".$request->designation ;
        }

        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND a.id = ".$request->employee_name ;
        }

        if (isset($request->bloodgroup_name)){
            $parameter  =  $parameter . " AND a.hrm_blood_group_id = ".$request->bloodgroup_name ;
        }

// dd($parameter);

        $user_id  = Auth::user()->id;
        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Blood Group Wise Report"."   [Location:-$title]",
            'condition_parameter'   => $parameter,
            'user_id'               => $user_id,
        );


        $report_path = Config::get('configaration.report_path').'hrm_bloodgroup_report';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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


   public function hrm_cw_salary_report (Request $request)
    {

        // dd($request->all());

        $location_name = HrmLocation::find($request->location);
        $month      =$request->month;
        $year      = $request->year;


         $parameter_1="AND a.hrm_month_id =$month  And a.year_id=$year AND b.hrm_location_id=$request->location";

        // $date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $parameter="";


         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND d.id = ".$request->depertment ;
        }


        if (isset($request->section)){
            $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;
        }


         if (isset($request->designation)){
            $parameter  =  $parameter . " AND f.id = ".$request->designation ;
        }

         if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND c.id = ".$request->employee_name ;
        }


        if (isset($request->plant_name)){
            $parameter  =  $parameter . " AND h.id = ".$request->plant_name ;
        }


        if($request->payment_mode==0){
        }else{
            $parameter  =  $parameter . " AND a.payment_mode = ".$request->payment_mode ;
        }


                // if (isset($request->bank_name)){
                //     $parameter  =  $parameter . " AND aa.hrm_bank_id = ".$request->bank_name ;
                // }



       $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
       $month_name=$month_name[0]->month_name;





        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Salary and Wages for Casual Worker  [".$month_name."-".$year."]  [$location_name->location_name]",
            'condition_parameter'   => $parameter,
            'condition_parameter_1' => $parameter_1,
        );


        // $report_path = Config::get('configaration.report_path').'hrm_cw_salary_report';


        if($request->payment_mode==1){
           // $report_path = Config::get('configaration.report_path').'hrm_salary_cash';
           $report_path = Config::get('configaration.report_path').'hrm_cw_salary_sheet_cash';
        }else{
           $report_path = Config::get('configaration.report_path').'hrm_cw_salary_sheet_all';
           // $report_path = Config::get('configaration.report_path').'hrm_salary';
        }


        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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





   public function individual_leave_ledger_report (Request $request)
    {



        $validator = Validator::make($request->all(), [
            'employee_name'    => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $employee_id = $request->employee_name;
        $leave_year_id     = $request->hrm_leave_years_id;

        return redirect()->route('emp_leave_balance.pdf', [
            'id' => $employee_id,
            'leave_year_id' => $leave_year_id,
        ]);


        // $jasper_server = new Client(
        //     Config::get('configaration.jasperjasper_url'),
        //     Config::get('configaration.jasper_user'),
        //     Config::get('configaration.jasper_password')
        // );

        // $parameter="";


        // $parameter  = " WHERE b.id = $request->employee_name AND j.id=$request->hrm_leave_years_id ";


        // // dd($parameter);


        // $controls = array(
        //     'company_name'          => Config::get('configaration.company_name'),
        //     'address'               => Config::get('configaration.company_address'),
        //     'title'                 => "Individual Leave Ledger",
        //     'condition_parameter'   => $parameter,
        // );


        // $report_path = Config::get('configaration.report_path').'hrm_individual_leave_ledger';

        // $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        // $generate_type  = $request->generate_type;

        // if (strlen($report) > 988){

        //     if($generate_type=="pdf"){

        //             $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
        //             header("Content-type:application/pdf");
        //             echo $report;
        //             echo "data:application/pdf;base64, " . $report;

        //     }else{

        //             $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
        //             header('Cache-Control: must-revalidate');
        //             header('Pragma: public');
        //             header('Content-Description: File Transfer');
        //             header('Content-Disposition: attachment; filename=mr_statement.'.$generate_type);
        //             header('Content-Transfer-Encoding: binary');
        //             header('Content-Length: ' . strlen($report));
        //             header('Content-Type: application/'.$generate_type);
        //             echo $report;
        //     }
        // }else{
        //    dd("No data found");
        // }
    }

    public function employee_leave_summary_report (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hrm_leave_years_id'    => 'required',
            'location'    => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $parameter="";
        $parameter_title =" | ";
        if($request->location<>0){
            $location = HrmLocation::find($request->location);
            $location_name =  $location->location_name;
            $parameter  =  $parameter . " AND d.hrm_location_id = ".$request->location ;
            $parameter_title .= " Location: ".$location_name." | ";
        }else{
            $location_name = 'All Location';
            $parameter_title .= " Location: ".$location_name." | ";
        }
        if (isset($request->hrm_leave_years_id)){
            $parameter  =  $parameter . " AND a.hrm_leave_years_id = ".$request->hrm_leave_years_id ;
            $LeaveYear = HrmLeaveYear::find($request->hrm_leave_years_id);
            $parameter_title .= " Leave Year: ".$LeaveYear->leave_year." | ";
        }
        if (isset($request->depertment)){
            $parameter  =  $parameter . " AND d.hrm_depertment_id = ".$request->depertment ;
            $parameter_title .= " Depertment | ";
        }
        if (isset($request->section)){
            $parameter  =  $parameter . " AND d.hrm_section_id = ".$request->section ;
            $parameter_title .= " Sub-department | ";
        }
        if (isset($request->designation)){
            $parameter  =  $parameter . " AND d.hrm_designation_id = ".$request->designation ;
            $parameter_title .= " Designation | ";
        }
        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND d.hrm_employee_id = ".$request->employee_name ;
            $parameter_title .= " Employee Name | ";
        }
        if (isset($request->employee_type)){
            $list = implode(',',$request->employee_type);
            $parameter  =  $parameter . " AND d.hrm_employment_status_id in ($list)" ;
            $parameter_title .= " Employee Type | ";
        }
        if (isset($request->category)){
            $parameter  =  $parameter . " AND d.hrm_category_id = ".$request->category ;
            $parameter_title .= " Employee Category | ";
        }
        if (isset($request->plant_name)){
            $parameter  =  $parameter . " AND d.hrm_plant_id = ".$request->plant_name ;
            $parameter_title .= " Plant | ";
        }
        if (isset($request->leave_type)){
            $parameter  =  $parameter . " AND a.hrm_employee_leave_type_id = ".$request->leave_type ;
            $parameter_title .= " Leave Type | ";
        }
        if ($request->payment_mode!=0){
            $parameter  =  $parameter . " AND a.payment_mode = ".$request->payment_mode ;
            $parameter_title .= " Payment Mode | ";
        }


        //--

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Employee Leave Summary",
            'condition_parameter'   => $parameter,
            'parameter_title'   => $parameter_title,
        );



        $report_path = Config::get('configaration.report_path').'hrm_employee_leave_summary';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

        if (strlen($report) > 940){
            if($generate_type=="pdf"){
                    $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
                    header("Content-type:application/pdf");
                    echo $report;
                    echo "data:application/pdf;base64, " . $report;

            }else{
                    $report = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
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

   public function outofpocket_report (Request $request)
    {


        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $month       = $request->month;
        $year        = $request->year;

        $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
        $month_name=$month_name[0]->month_name;


        $parameter="";
        if($request->location<>0){
                $parameter  = " AND a.hrm_location_id = $request->location";
        }
        // dd($request->generate_type );

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Out of Pocket For The Month of " .$month_name."- $year",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year,
        );


        $report_path = Config::get('configaration.report_path').'hrm_out_of_pocket';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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


    public function hrm_fringbenefit_locationwise (Request $request)
    {


        $validator = Validator::make($request->all(), [
            'salary_head'    => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $month       = $request->month;
        $year        = $request->year;
        $salary_head = $request->salary_head;

        $salary_head_name   = DB::SELECT("SELECT salary_head FROM hrm_salary_head Where id=$salary_head");
        $salary_head_name   = $salary_head_name[0]->salary_head;

        $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
        $month_name=$month_name[0]->month_name;



        $parameter=" AND b.hrm_salary_head_id = ".$salary_head ;

        if($request->location<>0){
                $parameter  = $parameter." AND d.id = $request->location";
        }
// dd($request->generate_type );

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => $salary_head_name." For The Month of " .$month_name."- $year",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year,
        );


        $report_path = Config::get('configaration.report_path').'hrm_fringbenefit_locationwise';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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





    public function fringbenefit_designationwise (Request $request)
    {


        $validator = Validator::make($request->all(), [
            'salary_head'    => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $month       = $request->month;
        $year        = $request->year;
        $salary_head = $request->salary_head;

        $salary_head_name   = DB::SELECT("SELECT salary_head FROM hrm_salary_head Where id=$salary_head");
        $salary_head_name   = $salary_head_name[0]->salary_head;


        $month_name   = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
        $month_name   = $month_name[0]->month_name;


        $parameter="";

        if($request->location<>0){
            $parameter  = " AND a.hrm_location_id = $request->location";
        }

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Monthly ".$salary_head_name." For the Month of " .$month_name."- $year",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year,
            'salary_head'           => $salary_head,
        );


        $report_path    = Config::get('configaration.report_path').'hrm_fringbenefit_designationwise';
        $report         = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

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



  public function fringbenefit_employeewise (Request $request)
  {

    // dd($request->all());

        $validator = Validator::make($request->all(), [
            'salary_head'    => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $month       = $request->month;
        $year        = $request->year;
        $salary_head = $request->salary_head;

        $salary_head_name   = DB::SELECT("SELECT salary_head FROM hrm_salary_head Where id=$salary_head");
        $salary_head_name   = $salary_head_name[0]->salary_head;


        $month_name   = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
        $month_name   = $month_name[0]->month_name;


        $parameter="";

        if($request->location<>0){
            $parameter  = " AND a.hrm_location_id = $request->location";
        }

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Monthly ".$salary_head_name." For the Month of " .$month_name."- $year",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year,
            'salary_head'           => $salary_head,
        );


        $report_path    = Config::get('configaration.report_path').'hrm_fringbenefit_employeewise';
        $report         = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

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






  public function fring_benefit_topsheet (Request $request)
    {


        // $validator = Validator::make($request->all(), [
        //     'salary_head'    => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return Response::json(array(
        //         'success'   => false,
        //         'errors'    => $validator->getMessageBag()->toArray()
        //     ));
        // }

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $month       = $request->month;
        $year        = $request->year;
        // $salary_head = $request->salary_head;

        // $salary_head_name   = DB::SELECT("SELECT salary_head FROM hrm_salary_head Where id=$salary_head");
        // $salary_head_name   = $salary_head_name[0]->salary_head;


        $month_name   = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
        $month_name   = $month_name[0]->month_name;


        $parameter="";

        if($request->location<>0){
            $parameter  = " AND a.hrm_location_id = $request->location";
        }


        if($request->location=='777'){
            $parameter  = " AND d.location_type = 3";
        }


        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Fringe Benefits Top Sheet For the Month of " .$month_name."- $year",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year,
            // 'salary_head'           => $salary_head,
        );


        if($request->location=='999'){
            // $report_path    = Config::get('configaration.report_path').'hrm_topsheet_fringe_benefit_summary_test';
            $report_path    = Config::get('configaration.report_path').'hrm_topsheet_fringe_benefit_summary';
        }else{
            $report_path    = Config::get('configaration.report_path').'hrm_fring_benefit_topsheet';
        }




        $report         = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

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






      public function monthly_carallowance (Request $request)
    {


        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $month       = $request->month;
        $year        = $request->year;

       $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
       $month_name=$month_name[0]->month_name;


        $parameter="";
        if($request->location<>0){
                $parameter  = " AND d.id = $request->location";
        }
// dd($request->generate_type );

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Monthly Car Allowance For the Month of " .$month_name."- $year",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year,
        );


        $report_path = Config::get('configaration.report_path').'hrm_car_allowance';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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



      public function employeelog_report (Request $request)
    {


        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

       $datefrom    = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
       $dateto      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

       $parameter=" AND cc.hrm_employee_id = $request->employee_name AND ii.punche_date between '$datefrom' AND '$dateto'";


        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Employee Log Report",
            'condition_parameter'   => $parameter,
        );


        $report_path = Config::get('configaration.report_path').'hrm_employee_log';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;


// dd($parameter);

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

    public function salary_headwise_report (Request $request)
    {

        // dd("-");

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $datefrom    = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $dateto      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
        $param_between = " AND CONCAT(aa.year_id, '-', IF(LENGTH(aa.hrm_month_id) = 1, CONCAT('0', aa.hrm_month_id), aa.hrm_month_id), '-', '01') BETWEEN '$datefrom' AND '$dateto' " ;
        $title       = date('F-Y', strtotime($datefrom)).' To '.date('F-Y', strtotime($dateto)) ;


        $parameter="";
        if(!empty($request->location)){
                $parameter  = " AND aa.hrm_location_id = $request->location";
        }

        $location = HrmLocation::find($request->location);

        if(!empty($location)){
            $location = $location->location_name;
        }else{
            $location = "All";
        }

        if (isset($request->category)){
            $parameter  =  $parameter . " AND a.hrm_category_id = ".$request->category ;
        }



        if (isset($request->salary_head)){
            $list = implode(',',$request->salary_head);
            $parameter           =  $parameter . " AND c.id in ($list)" ;
        }

        $parameter           =  $parameter . " JOIN hrm_employee_job_info k ON a.hrm_employee_id = k.hrm_employee_id
        AND k.id IN (SELECT
            MAX(id)
        FROM
            hrm_employee_job_info
        WHERE
            hrm_employee_id = a.hrm_employee_id) " ;


// dd($param_between);


        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Salary Head Wise Report [" .$title. " & Location : ".$location. "]",
            'condition_parameter'   => $parameter,
            'parameter'             => $param_between,
        );
        // dd($parameter, $param_between);
        $report_path = Config::get('configaration.report_path').'hrm_salary_headwise_report';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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




public function bonus_topSheet (Request $request)
    {

        // dd($request->all());
        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        $parameter = '';

        if (isset($request->declaration_date)){

            $declaration_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->declaration_date)));
            $parameter        = $parameter . " Where b.declaration_date = '$declaration_date' " ;
            $getYear          = date('Y', strtotime($declaration_date));
            $find             = HrmEmployeeBonusMaster::where('declaration_date',$declaration_date)->where('is_valid',1)->first();
            $bonus_name       = HrmBonus::find($find->hrm_bonus_id);
            $bonus_name       = $bonus_name->bonus_name;

            if($request->apply_for=='1'){
                $location_name = 'ALL Location';

            }else{
                $location_name = 'ALL Depot';
                // $parameter     = $parameter . " AND e.location_type = 3 ";

            }


        }

        $user_id   = Auth::user()->id;
        $parameter = $parameter . " and b.hrm_location_id in (SELECT hrm_location_id FROM user_location where users_id=$user_id)" ;


        if ($find->status==1 ){
            $title="Pending Bonus Top Sheet || "."[$bonus_name - $getYear ]"."[$location_name]";
        }else{
            $title="Bonus Top Sheet || "."[$bonus_name - $getYear ]"."[$location_name]";
        }


        if (isset($request->category)){
            $parameter  =  $parameter . " AND a.hrm_category_id = ".$request->category ;
        }




        if($request->apply_for==1) {
            $report_path = Config::get('configaration.report_path').'bonus_top_sheet';
        }else{
            $report_path = Config::get('configaration.report_path').'bonus_top_sheet_depot';
        }

        // dd($parameter);


        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "$title",
            'condition_parameter'   => $parameter,
        );



        $report         = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
                $generate_type  = $request->generate_type;

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








    public function monthly_topSheet (Request $request)
    {

        // dd($request->all());
        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $month       = $request->month;
        $year        = $request->year;

        $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
        $month_name=$month_name[0]->month_name;


        $parameter=" AND aa.salary_genarate_type <> 0";
        if(!empty($request->location)){
                $parameter  = $parameter." AND aa.hrm_location_id = $request->location ";
        }


        if (isset($request->category)){
            $parameter  =  $parameter . " AND a.hrm_category_id = ".$request->category ;
        }



        $location = HrmLocation::find($request->location);

        if(!empty($location)){
            $location = $location->location_name;
        }else{
            $location = "All";
        }

        // dd( $parameter);



        $parameter2 = '';


        if($request->with_pf_status==1){

            $parameter2= "UNION ALL SELECT
                e.id,
                99 as priority,
                'PF' as category_name,
                'x Company Contribution' location_name,
                9  as location_type,
                0 AS noOfEmployee,
                d.group_name,
                2 AS group_status,
                SUM(b.actual_amount)
            FROM
                pay_register aa
                    JOIN
                hrm_employee_job_info a ON aa.hrm_employee_job_info_id = a.id AND aa.apply_for=1
                    AND aa.year_id = $year
                    AND aa.hrm_month_id = $month
                        $parameter
                    JOIN
                pay_register_details b ON aa.id = b.pay_register_id
                    JOIN
                hrm_salary_head c ON b.hrm_salary_head_id = c.id
                    JOIN
                hrm_salary_head_group d ON c.hrm_salary_head_group_id = d.id AND c.apply_for=1
                    AND d.id = 6
                    JOIN
                hrm_category e ON a.hrm_category_id = e.id
                    JOIN
                hrm_location f ON aa.hrm_location_id = f.id AND f.location_type in (1,2,3)
                    LEFT JOIN
                hrm_bank h ON aa.hrm_bank_id = h.id
            GROUP BY aa.id, h.short_name , d.group_name";

        }

        // dd($parameter2);

        if($request->apply_for==1) {
            // $report_path = Config::get('configaration.report_path').'salary_top_sheet';
            $report_path = Config::get('configaration.report_path').'salary_top_sheet';
            $title = 'Salary Top Sheet';
        }else{
            $report_path = Config::get('configaration.report_path').'salary_top_sheet_depot';
            $title = 'Salary Top Sheet Depot';
        }

// dd($report_path);
        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "$title [" .$month_name."- $year]"."[Location : $location ]",
            'condition_parameter'   => $parameter,
            'pf_condition_parameter'=> $parameter2,
            'hrm_month_id'          => $month,
            'year_id'               => $year,
        );

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;
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





    public function monthly_topSheet_amg (Request $request)
    {

        // dd("Developing.. Live will be 25th May 2021");

        // dd($request->all());

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $month         = $request->month;
        $year          = $request->year;
        $date_of_month = $request->year.'-'.$month.'-01';
        $year_month_current = HrmMonth::find($month)->month_name.'-'.$year;

        $checkDateYM   = date('Y-m-d', strtotime("-1 months", strtotime($date_of_month)));
        $pre_month     = date('m', strtotime($checkDateYM));
        $pre_year      = date('Y', strtotime($checkDateYM));

        $year_month_previous = HrmMonth::find($pre_month)->month_name.'-'.$pre_year;
        $users_id       = Auth::user()->id;

        $parameter=" AND aa.salary_genarate_type <> 0";
        if(!empty($request->location)){
            $parameter  = $parameter." AND aa.hrm_location_id = $request->location ";
        }
        if (isset($request->section)){
            $parameter  =  $parameter . " AND a.hrm_section_id = ".$request->section ;
        }

        $location = HrmLocation::find($request->location);

        if(!empty($location)){
            $location = $location->location_name;
        }else{
            $location = "All";
        }


        if(!empty($request->slap_name)){
            $parameter  = $parameter." JOIN hrm_salary_generate_master j ON aa.hrm_salary_generate_master_id = j.id
             AND hrm_salary_slap_id =".$request->slap_name;
        }else{

        }



        if($request->apply_for==1) {
            $report_path = Config::get('configaration.report_path').'topsheet_amg';
            $title = 'Salary Top Sheet';
        }else{
            $report_path = Config::get('configaration.report_path').'topsheet_amg';
            $title = 'Salary Top Sheet Depot';
        }

        $month_name = HrmMonth::find($month)->month_name;



        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "$title [" .$month_name."- $year]"."[Location : $location ]",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year,
            'pre_hrm_month_id'      => $pre_month,
            'pre_year_id'           => $pre_year,
            'year_month_current'    => $year_month_current,
            'year_month_previous'   => $year_month_previous,
        );

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;
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



  public function monthly_attendancedetails_report (Request $request)
    {

// dd( $request->all());

        $location_name = HrmLocation::find($request->location);


        if (isset($request->working_shift)){
                $shift = HrmShift::find($request->working_shift);
                $shift_name=$shift->shift_name;

        }else{
                 $shift_name = "All Shift";
        }

        if($request->date_range == 1){
            $date_of_month  = date("Y-m-d", strtotime($request->from_date));
            $lastdate       = date("Y-m-d", strtotime($request->to_date));
            $parameter = $date_range = " AND a.punche_date BETWEEN '" . $date_of_month . "' AND '" . $lastdate . "' ";

            $month_name = $date_of_month.' To '.$lastdate;

        }
        else{

            $month          = $request->month;
            $year           = $request->year;
            $date_of_month  = $request->year.'-'.$month.'-01';
            $year_month     = $request->year.'-'.$month;
            $lastdate       = date("Y-m-t", strtotime($date_of_month));
            $date_range = " AND Month(a.punche_date)='". $month . "' And Year(a.punche_date)='". $year . "'  ";

            $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
            $month_name=  $month_name[0]->month_name . ' ( '. $year ;
        // dd( $date_range);

            $parameter=" AND Month(a.punche_date)=$month  And Year(a.punche_date)=$year";
        }




        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );




        // $parameter=$parameter." AND b.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
        //                         WHERE end_date BETWEEN '$date_of_month' AND '$lastdate'  AND end_date IS NOT NULL
        //                         AND hrm_employee_activity_status_id NOT IN (7,5)
        //                         UNION ALL
        //                         SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
        //                         AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5))";



        $parameter = $parameter." AND b.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                        '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                        AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                        end_date BETWEEN '$date_of_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                                WHERE
                                                 end_date BETWEEN '$date_of_month' AND '$lastdate'
                                                AND end_date IS NOT NULL
                                                AND hrm_employee_activity_status_id NOT IN (7,5)
                                        )";

           // dd();

        $title_parameter="";

        if (isset($request->location)){
            $parameter  =  $parameter . " AND b.hrm_location_id = ".$request->location ;
        }

        if (isset($request->depertment)){

            $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;

            $data = HrmDepertment::find($request->depertment);
            $title_parameter='Dept:-'.$data->depertment_name;

        }

        if (isset($request->section)){
            $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;

            $data = HrmSection::find($request->section);
            $title_parameter=  $title_parameter.',Sub-Dept:-'.$data->section_name;

        }

        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND b.hrm_employee_id = ".$request->employee_name ;
        }

        if (isset($request->category)){
            $parameter  =  $parameter . " AND b.hrm_category_id = ".$request->category ;
        }


        // if (isset($request->location)){
        //     $parameter  =  $parameter . " AND g.id = ".$request->location ;
        // }

        //  if (isset($request->depertment)){
        //     $parameter  =  $parameter . " AND c.id = ".$request->depertment ;
        // }

        //  if (isset($request->designation)){
        //     $parameter  =  $parameter . " AND d.id = ".$request->designation ;
        // }

        //  if (isset($request->employee_name)){
        //     $parameter  =  $parameter . " AND a.hrm_employee_id = ".$request->employee_name ;
        // }

        // if (isset($request->category)){
        //     $parameter  =  $parameter . " AND b.hrm_category_id = ".$request->category ;
        // }


        // if (isset($request->working_shift)){
        //     $parameter  =  $parameter . " AND i.id = ".$request->working_shift ;
        // }






        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Monthly Attendance Details || ".$month_name." ($title_parameter)"."[$location_name->location_name]",
            'date_range'            => $date_range,
            'condition_parameter'   => $parameter,

        );
        // dd($parameter);

        $report_path = Config::get('configaration.report_path').'hrm_monthy_attendance_details';

        // $report_path = Config::get('configaration.report_path').'hrm_monthy_attendance_details_with_inOut';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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




 public function monthly_attendancedetails_with_inout (Request $request)
    {

        // dd("working..");
          $location_name = HrmLocation::find($request->location);

        if($request->date_range == 1){
            $date_of_month  = date("Y-m-d", strtotime($request->from_date));
            $lastdate       = date("Y-m-d", strtotime($request->to_date));
            $parameter = $date_range = " AND a.punche_date BETWEEN '" . $date_of_month . "' AND '" . $lastdate . "' ";

            $month_name = $date_of_month.' To '.$lastdate;

        }
        else{
            $month          = $request->month;
            $year           = $request->year;
            $date_of_month  = $request->year.'-'.$month.'-01';
            $year_month     = $request->year.'-'.$month;
            $lastdate       = date("Y-m-t", strtotime($date_of_month));
            $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
            $month_name=$month_name[0]->month_name . "-". $year;
             $date_range = " AND Month(a.punche_date)='". $month . "' And Year(a.punche_date)='". $year . "'  ";
        // dd( $date_range);

            $parameter=" AND Month(a.punche_date)=$month  And Year(a.punche_date)=$year";
        }








        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );



        // $parameter="";

        $title_parameter="";

        if (isset($request->location)){
            $parameter  =  $parameter . " AND b.hrm_location_id = ".$request->location ;
        }

        if (isset($request->depertment)){

            $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;

            $data = HrmDepertment::find($request->depertment);
            $title_parameter='Dept:-'.$data->depertment_name;

        }

        if (isset($request->section)){
            $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;

            $data = HrmSection::find($request->section);
            $title_parameter=  $title_parameter.',Sub-Dept:-'.$data->section_name;

        }

        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND b.hrm_employee_id = ".$request->employee_name ;
        }

        if (isset($request->category)){
            $parameter  =  $parameter . " AND b.hrm_category_id = ".$request->category ;
        }






        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Monthly Attendance Details Report ||  ".$month_name." ($title_parameter)"."[$location_name->location_name]",
            'date_range'            => $date_range,
            'condition_parameter'   => $parameter,

        );


        $report_path = Config::get('configaration.report_path').'hrm_monthy_attendance_details_with_inOut';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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

            $subquery_parameter=" AND punche_date between '$date_of_month' AND '$lastdate' AND hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info $subquery ) ";


            $month_name = $date_of_month.' To '.$lastdate;

        }else{
            $month      =$request->month;
            $year      = $request->year;
            $date_of_month = date('Y-m-01', strtotime("$year-$month-01"));

    // মাসের শেষ দিন
            $lastdate = date('Y-m-t', strtotime($date_of_month));
            $parameter = '';
            $comment_parameter = '';
            $subquery_parameter = '';

            $location = (int) ($request->location ?? 0);
            $parameter = "AND b.hrm_location_id = $location AND a.punche_date between '$date_of_month' AND '$lastdate'" ;

            $subquery_parameter=" AND punche_date between '$date_of_month' AND '$lastdate' AND hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info $subquery ) ";


            $monthData = DB::select(
                    "SELECT month_name FROM hrm_month WHERE id = ?",
                    [$month]
                );

                $month_name = (!empty($monthData))
                    ? $monthData[0]->month_name . "-" . $year
                    : $year;
        }





        // dd('ok');


         if (isset($request->depertment)){
            $parameter          =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
            $comment_parameter  =  $comment_parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
            $subquery            =  $subquery . " AND hrm_depertment_id = ".$request->depertment ;
        }


        if (isset($request->section)){
            $parameter          =  $parameter . " AND b.hrm_section_id = ".$request->section ;
            $comment_parameter  =  $comment_parameter . " AND b.hrm_section_id = ".$request->section ;
            $subquery           =  $subquery . " AND hrm_section_id = ".$request->section ;
        }


        if (isset($request->category)){
            $parameter          =  $parameter . " AND b.hrm_category_id = ".$request->category ;
            $comment_parameter  =  $comment_parameter . " AND b.hrm_category_id = ".$request->category ;
            $subquery           =  $subquery . " AND hrm_category_id = ".$request->category ;
        }



        if (isset($request->employee_name)){
            $list = implode(',',$request->employee_name);

            $parameter           =  $parameter . " AND b.hrm_employee_id in ($list)" ;
            $comment_parameter   =  $comment_parameter . " AND b.hrm_employee_id in ($list)" ;
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

        // dd($parameter);


        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Job Card Report $status || ".$month_name." || ".$location_name,
            'subquery_parameter'    => $subquery_parameter,
            'condition_parameter'   => $parameter,
            'condition_parameter_1' => $comment_parameter,



        );

        // dd($controls);


        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;
        // // dd($report);

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



   public function monthly_attendancesummary_report (Request $request)
   {


        $location_name   = HrmLocation::find($request->location);
        $title_parameter = '';
        $hrm_location_id= $request->location;

        if($request->date_range == 1){
            $date_of_month  = date("Y-m-d", strtotime($request->from_date));
            $lastdate       = date("Y-m-d", strtotime($request->to_date));
            $parameter = $date_range = " AND a.punche_date BETWEEN '" . $date_of_month . "' AND '" . $lastdate . "' ";

            $month_name = $date_of_month.' To '.$lastdate;

        }else{
            $month          = $request->month;
            $year           = $request->year;

            $date_of_month  = $request->year.'-'.$month.'-01';
            $year_month     = $request->year.'-'.$month;
            $lastdate       = date("Y-m-t", strtotime($date_of_month));
            $date_range = " AND Month(a.punche_date)='". $month . "' And Year(a.punche_date)='". $year . "' ";
            $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
            $month_name=$month_name[0]->month_name . "-". $year;

        }


         $CountDays      = date("t", strtotime($lastdate));


        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );




        // $parameter  =  " Where hrm_location_id = ".$request->location ;


        // if (isset($request->location)){
        // }

        $parameter = " AND b.id in (SELECT MAX(bbb.id) as id FROM (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                        '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                        AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                        end_date BETWEEN '$date_of_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                        UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$date_of_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)) aaa
                                        JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                        AND bbb.hrm_location_id = $hrm_location_id
                                        group by bbb.hrm_employee_id)";


// dd($parameterA);

        if (isset($request->depertment)){

            $parameter  =  $parameter . " AND hrm_depertment_id = ".$request->depertment ;

            $data = HrmDepertment::find($request->depertment);
            $title_parameter='Dept:-'.$data->depertment_name;

        }

        if (isset($request->section)){
            $parameter  =  $parameter . " AND hrm_section_id = ".$request->section ;

            $data = HrmSection::find($request->section);
            $title_parameter=  $title_parameter.',Sub-Dept:-'.$data->section_name;

        }

        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND hrm_employee_id = ".$request->employee_name ;
        }

        if (isset($request->category)){
            $parameter  =  $parameter . " AND hrm_category_id = ".$request->category ;
        }

        //  if (isset($request->designation)){
        //     $parameter  =  $parameter . " AND b.hrm_designation_id = ".$request->designation ;
        // }


        // if (isset($request->working_shift)){
        //     $parameter  =  $parameter . " AND l.id = ".$request->working_shift ;
        // }



        // dd($date_range);
        // dd($parameter);


       if($request->report_for==1){
            $status = '';
            $report_path = Config::get('configaration.report_path').'hrm_attendancesummary';
        }else{

            $status = '(CW Only)';
            $report_path = Config::get('configaration.report_path').'hrm_attendancesummary_cw';
        }


        // dd($date_range);

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Monthly Attendance Summary $status || ".$month_name." ($title_parameter)"."[$location_name->location_name]",
            'date_range'            => $date_range,
            'condition_parameter'   => $parameter,
            'CountDays'             => $CountDays,

        );


        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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

   public function monthly_overtime_report (Request $request)
    {



        $location_name = HrmLocation::find($request->location);


        if (isset($request->depertment)){
                $shift = HrmDepertment::find($request->depertment);
                $shift_name=$shift->depertment_name;

        }else{
                 $shift_name = "All Department";
        }

        if($request->date_range == 2){
            $month      =$request->month;
            $year      = $request->year;
            $date_of_month  = $request->year.'-'.$month.'-01';
            $year_month     = $request->year.'-'.$month;
            $lastdate       = date("Y-m-t", strtotime($date_of_month));
            $parameter = " AND Month(a.punche_date)=". $month . " And Year(a.punche_date)=". $year . " ";
            $parameter = $parameter." AND c.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE end_date BETWEEN '$date_of_month' AND '$lastdate'  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5))";
        }else{

            $from_date       = date("Y-m-d", strtotime($request->from_date));
            $to_date         = date("Y-m-d", strtotime($request->to_date));
            $parameter = " AND a.punche_date BETWEEN '$from_date' AND '$to_date' ";
            $parameter = $parameter." AND c.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE end_date BETWEEN '$from_date' AND '$to_date'  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$to_date' AND hrm_employee_activity_status_id NOT IN (7,5))";
        }


        // dd($request->all());

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );



        // dd( $date_range);
          // $parameter="";

        if (isset($request->location)){
            $parameter  =  $parameter . " AND c.hrm_location_id = ".$request->location ;
        }


        // dd($parameter);
         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND c.hrm_depertment_id = ".$request->depertment ;
        }

         if (isset($request->section)){
            $parameter  =  $parameter . " AND c.hrm_section_id = ".$request->section ;
        }


         if (isset($request->designation)){
            $parameter  =  $parameter . " AND c.hrm_designation_id = ".$request->designation ;
        }

         if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND c.hrm_employee_id = ".$request->employee_name ;
        }

        if (isset($request->category)){
            $parameter  =  $parameter . " AND c.hrm_category_id = ".$request->category ;
        }


        // if (isset($request->working_shift)){
        //     $parameter  =  $parameter . " AND l.id = ".$request->working_shift ;
        // }

        // dd($parameter);
         if($request->date_range == 2){
            $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
            $month_name=$month_name[0]->month_name;


            // var_dump();
            // dd($parameter);

                $controls = array(
                    'company_name'          => Config::get('configaration.company_name'),
                    'address'               => Config::get('configaration.company_address'),
                    'title'                 => "Monthly Overtime Report || ".$month_name."- $year ($shift_name)"."[$location_name->location_name]",
                    'condition_parameter'   => $parameter,
                );
            }else{



            // var_dump();
            // dd($parameter);

                $controls = array(
                    'company_name'          => Config::get('configaration.company_name'),
                    'address'               => Config::get('configaration.company_address'),
                    'title'                 => "Monthly Overtime Report ||  ".$from_date. " to " . $to_date ." ($shift_name)"."[$location_name->location_name]",
                    'condition_parameter'   => $parameter,
                );
            }


        if($request->report_for==1){

            $report_path = Config::get('configaration.report_path').'hrm_overtime_report';
        }else{
            // dd("working");
            $report_path = Config::get('configaration.report_path').'hrm_overtime_summary';
            // $report_path = Config::get('configaration.report_path').'hrm_overtime_report';

        }


        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;



        if (strlen($report) > 988){

            if($generate_type=="pdf"){
                    $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
                    header("Content-type:application/pdf");
                    echo $report;
                    echo $report;
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


    public function monthly_ot_sheet (Request $request)
    {



        $location_name = HrmLocation::find($request->location);
        $month      =$request->month;
        $year      = $request->year;
        $date_of_month  = $request->year.'-'.$month.'-01';
        $lastdate       = date("Y-m-t", strtotime($date_of_month));

        $previousMonth = date('Y-m-d', strtotime("-1 months", strtotime($date_of_month)));
        $previousM = date("m", strtotime($previousMonth));
        $previousY = date("Y", strtotime($previousMonth));
        $previousMonthLast = date("Y-m-t", strtotime($previousMonth));


        $parameter  = '';
        $parameter1  = '';

        if($request->boolean('all_location')){


            $current_month =  "  AND a.hrm_month_id=". $month . " AND a.year_id=". $year . "";
            $previous_month = "  AND a.hrm_month_id=". $previousM . " AND a.year_id=". $previousY . "";


        }else{

            $current_month =  " AND a.hrm_location_id =".$request->location ." AND a.hrm_month_id=". $month . " AND a.year_id=". $year . "";
            $previous_month = " AND a.hrm_location_id =".$request->location ." AND a.hrm_month_id=". $previousM . " AND a.year_id=". $previousY . "";

            $current_month = $current_month." AND c.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                    WHERE
                                    '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7,5)
                                    UNION ALL
                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                    AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7,5)
                                    UNION ALL
                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                    WHERE
                                    end_date BETWEEN '$date_of_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7,5)
                                    UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE
                                             end_date BETWEEN '$date_of_month' AND '$lastdate'
                                            AND end_date IS NOT NULL
                                            AND hrm_employee_activity_status_id NOT IN (7,5)
                                    ) ";


            $previous_month = $previous_month." AND c.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                    WHERE
                                    '$previousMonthLast' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7,5)
                                    UNION ALL
                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                    AND start_date<='$previousMonthLast' AND hrm_employee_activity_status_id NOT IN (7,5)
                                    UNION ALL
                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                    WHERE
                                    end_date BETWEEN '$previousMonth'  AND '$previousMonthLast'  AND  end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7,5)
                                    UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE
                                             end_date BETWEEN '$previousMonth' AND '$previousMonthLast'
                                            AND end_date IS NOT NULL
                                            AND hrm_employee_activity_status_id NOT IN (7,5)
                                    ) ";


        }




         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND c.hrm_depertment_id = ".$request->depertment ;
        }

         if (isset($request->designation)){
            $parameter  =  $parameter . " AND c.hrm_designation_id = ".$request->designation ;
        }

         if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND c.hrm_employee_id = ".$request->employee_name ;
        }

        if (isset($request->category)){
            $parameter  =  $parameter . " AND c.hrm_category_id = ".$request->category ;
        }

        if (isset($request->section)){
            $parameter  =  $parameter . " AND c.hrm_section_id = ".$request->section ;
        }

        if (isset($request->category)){
            $parameter  =  $parameter . " AND c.hrm_category_id = ".$request->category ;
        }

        if ($request->payment_mode!=0){
            $parameter  =  $parameter . " AND b.payment_mode = ".$request->payment_mode ;

            if($request->payment_mode==2){
                if($request->bank_name){
                     $parameter  =  $parameter . " AND b.hrm_bank_id = ".$request->bank_name ;
                }
            }


        }





       $current_month = $current_month.$parameter;
       $previous_month = $previous_month.$parameter;

       $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
       $month_name = $month_name[0]->month_name;


        if($request->report_for==1){
            $report_path   = Config::get('configaration.report_path').'hrm_monthly_ot_sheet';
            $title = "O.T Sheet Month of ".$month_name."- $year "."[$location_name->location_name]";
        }elseif($request->report_for==2){

            // $current_month  = " AND a.hrm_month_id=". $month . " AND a.year_id=". $year . " " ;
            // $previous_month = " AND  a.hrm_month_id=". $previousM . " AND a.year_id=". $previousY . "";

            $report_path   = Config::get('configaration.report_path').'hrm_monthly_ot_top_sheet';
            $title =  "O.T Top Sheet Month of ".$month_name."- $year ";
        }elseif($request->report_for==3){

            $report_path   = Config::get('configaration.report_path').'hrm_OT_bank_salary_sheet_total';
            $title =  "O.T Top Sheet Month of ".$month_name."- $year "."[$location_name->location_name]";
        }



        // dd( $current_month,$previous_month);
        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => $title,
            'condition_parameter'   => $current_month,
            'condition_parameter1'  => $previous_month,
        );


        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        $report        = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type = $request->generate_type;

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





   public function salaryreport (Request $request)
    {

        // dd($request->all());
        // if($request->report_type==1){

           $check_data = DB::select("SELECT * FROM pay_register WHERE hrm_month_id = $request->month AND  apply_for = 1 AND year_id = $request->year AND salary_genarate_type = 1  AND hrm_location_id=$request->location LIMIT 1");

           $parameter = "AND aa.salary_genarate_type<>0 AND aa.apply_for = 1 AND aa.hrm_salary_generate_master_id =$request->salary_master ";

        // }else{

        //    $check_data = DB::select("SELECT * FROM pay_register WHERE hrm_month_id = $request->month AND apply_for = 2 AND year_id =  $request->year AND salary_genarate_type = 1 LIMIT 1");

        //    $parameter = "AND aa.salary_genarate_type<>0 AND aa.apply_for = 2 AND aa.hrm_salary_generate_master_id =$request->salary_master";

        // }

        if (!empty($check_data)){
            $title="Pending Salary Sheet";
        }else{
             $title="Salary Sheet";
        }


        $location_name = HrmLocation::find($request->location);

        if (isset($request->working_shift)){
                $shift = HrmShift::find($request->working_shift);
                $shift_name = $shift->shift_name;

        }else{
                $shift_name = "All Shift";
        }

        $month     = $request->month;
        $year      = $request->year;

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        if (isset($request->location)){
            $parameter  =  $parameter . " AND aa.hrm_location_id = ".$request->location ;
        }

        if($request->payment_mode==2){

                if (isset($request->bank_name)){
                    $parameter  =  $parameter . " AND aa.hrm_bank_id = ".$request->bank_name ;
                }
        }

        // dd($parameter);
         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND a.hrm_depertment_id = ".$request->depertment ;
        }

         if (isset($request->section)){
            $parameter  =  $parameter . " AND a.hrm_section_id = ".$request->section ;
        }


         if (isset($request->designation)){
            $parameter  =  $parameter . " AND a.hrm_designation_id = ".$request->designation ;
        }

        if (!empty($request->employee_status) && is_array($request->employee_status)) {
            $employee_status = implode(',', array_map('intval', $request->employee_status));
            $parameter .= " AND a.hrm_employment_status_id IN ($employee_status)";
        }


         if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND a.hrm_employee_id = ".$request->employee_name ;
        }

        $category="";
        if (isset($request->category)){
            $parameter  =  $parameter . " AND a.hrm_category_id = ".$request->category ;
            $category = HrmCategory::find($request->category);
            $category = $category->category_name." Only";
        }

        $payment_type="=Cash & Bank=";

        if($request->payment_mode<>0){

            $parameter  =  $parameter . " AND aa.payment_mode = ".$request->payment_mode ;

                if($request->payment_mode==1){
                    $payment_type="=Cash Only=";
                }elseif ($request->payment_mode==2) {
                    $payment_type="=Bank Only=";
                }
        }


       $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
       $month_name=$month_name[0]->month_name;
       $rptSign = DB::SELECT("select * from hrm_signatory_config Where id=1");


        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "$title || ".$month_name."- $year "."[$location_name->location_name]"."[$category]"."[$payment_type]",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year,

        );
        if (!empty($rptSign[0])) {
            $controls['sg1'] = $rptSign[0]->sg1;
            $controls['sg2'] = $rptSign[0]->sg2;
            $controls['sg3'] = $rptSign[0]->sg3;
            $controls['sg4'] = $rptSign[0]->sg4;
            $controls['sg5'] = $rptSign[0]->sg5;
            $controls['sg6'] = $rptSign[0]->sg6;
            $controls['sg7'] = $rptSign[0]->sg7;
            $controls['sg8'] = $rptSign[0]->sg8;
            $controls['sg9'] = $rptSign[0]->sg9;
            $controls['sg10'] = $rptSign[0]->sg10;
        }

        // dd($controls);


        if($request->payment_mode==1){
           $report_path = Config::get('configaration.report_path').'hrm_salary_cash';
        }else{
           $report_path = Config::get('configaration.report_path').'hrm_salary';
        }

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;



        // dd(strlen($report));

        if (strlen($report) > 988){
        // if (!empty($report)) {
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



   public function salary_review (Request $request)
    {

        $month     = $request->month;
        $year      = $request->year;
        $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
        $month_name =$month_name[0]->month_name;

        $datefrom    = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $dateto      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

        if($request->report_name==1){
         $title="Cost to The Company [ Month :".$month_name." Year: ".$year." ]";
        }

        if($request->report_name==2){
         $title="Salary Review [ Date From:".$request->date_from." Date To: ".$request->date_to." ]";
        }


        if($request->report_name==3){
         $title="Salary Review Top Sheet [ Date From:".$request->date_from." Date To: ".$request->date_to." ]";
        }

        if(empty($request->increment_amount)){
            $increment_amount = 0;
        }else{
            $increment_amount = $request->increment_amount/100;
        }


        if(empty($request->exgratia_bonus)){
            $exgratia_bonus = 0;
        }else{
            $exgratia_bonus = $request->exgratia_bonus/100;
        }


        if(empty($request->festival_bonus)){
            $festival_bonus = 0;
        }else{
            $festival_bonus = $request->festival_bonus/100;
        }



        if(empty($request->pf)){
            $pf = 0;
        }else{
            $pf = $request->pf/100;
        }

        $parameter = '';
        $titl = '';
        if (isset($request->employee_type)){
            $list = implode(',',$request->employee_type);
            $parameter  =  $parameter . " AND b.hrm_employment_status_id in ($list)" ;
            $titl = DB::SELECT("SELECT GROUP_CONCAT(employment_status) as employment_status FROM hrm_employment_status WHERE id in ($list)")[0]->employment_status;

        }


        if ($request->location==0) {
            $location_name='All Location';
            $cost_parameter = "" ;
        }
        else{
            if($request->location =="999"){
                $location_name = 'All Depot';
                $parameter  =  $parameter . " AND b.hrm_location_id in (SELECT id FROM hrm_location WHERE location_type=3 AND valid = 1) ";
            } else {
                $parameter = $parameter . " AND b.hrm_location_id = " . $request->location;
                $location_name = HrmLocation::find($request->location);
                $location_name = $location_name->location_name;
            }
            $cost_parameter = " AND aa.hrm_location_id = ".$request->location ;
        }





        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        // if (isset($request->location)){
        //     $parameter  =  $parameter . " AND b.hrm_location_id = ".$request->location ;
        // }

         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
        }

         if (isset($request->section)){
            $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;
        }


         if (isset($request->designation)){
            $parameter  =  $parameter . " AND b.hrm_designation_id = ".$request->designation ;
        }

         if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND b.hrm_employee_id = ".$request->employee_name ;
        }

        $category="-";

        if (isset($request->category)){
            $parameter  =  $parameter . " AND b.hrm_category_id = ".$request->category ;
            $category = HrmCategory::find($request->category);
            $category = $category->category_name." Only";
        }



        if($request->report_name==1){

            $controls = array(
                'company_name'          => Config::get('configaration.company_name'),
                'address'               => Config::get('configaration.company_address'),
                'title'                 => "$title "."[$location_name]"."[$category]"."[$titl]",
                'condition_parameter'   => $cost_parameter,
                'hrm_month_id'          => $month,
                'year_id'               => $year,
                'increment_amount'      => $increment_amount,
                'exgratia_bonus'        => $exgratia_bonus,
                'festival_bonus'        => $festival_bonus,
                'pf'                    => $pf,

            );

        };


        if($request->report_name==2 ){

            $controls = array(
                'company_name'          => Config::get('configaration.company_name'),
                'address'               => Config::get('configaration.company_address'),
                'title'                 => "$title "."[$location_name]"."[$category]"."[$titl]",
                'condition_parameter'   => $parameter,
                'datefrom'              => $datefrom,
                'dateto'                => $dateto,
                'increment_amount'      => $increment_amount,
                'exgratia_bonus'        => $exgratia_bonus,
                'festival_bonus'        => $festival_bonus,
                'pf'                    => $pf,
            );

        };



        if($request->report_name==3 ){

            $controls = array(
                'company_name'          => Config::get('configaration.company_name'),
                'address'               => Config::get('configaration.company_address'),
                'title'                 => "$title "."[$location_name]"."[$category]"."[$titl]",
                'condition_parameter'   => $parameter,
                'datefrom'              => $datefrom,
                'dateto'                => $dateto,
                'increment_amount'      => $increment_amount,
                'exgratia_bonus'        => $exgratia_bonus,
                'festival_bonus'        => $festival_bonus,
                'pf'                    => $pf,


            );

        };



        if($request->report_name==1){
            $report_path = Config::get('configaration.report_path').'hrm_costtothecompany';
        }


        if($request->report_name==2){
            $report_path = Config::get('configaration.report_path').'hrm_salary_review';
        }

        if($request->report_name==3){
            $report_path = Config::get('configaration.report_path').'hrm_salary_review_topsheet';
        }


        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        // dd($report);

        $generate_type  = $request->generate_type;



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






  public function fringebenefitreport (Request $request)
    {



       $check_data = DB::select("SELECT * FROM pay_register WHERE hrm_month_id = $request->month AND apply_for = 2 AND year_id =  $request->year AND salary_genarate_type = 1 LIMIT 1");

       $parameter = "AND aa.salary_genarate_type<>0 AND aa.apply_for = 2 ";

        // }

        if (!empty($check_data)){
            $title="Fringe Benefits Sheet";
        }else{
             $title="Fringe Benefits Sheet";
        }

        $month     = $request->month;
        $year      = $request->year;

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        $location_name = 'All Location';

        if($request->location<>0){

            if($request->location=='777'){
                $location_name = 'All Depot';
                $parameter  = $parameter." JOIN hrm_location j ON aa.hrm_location_id=j.id  AND j.location_type = 3";

            }else{
                $parameter  = $parameter." AND aa.hrm_location_id = $request->location";
                $location_name = HrmLocation::find($request->location);
                $location_name = $location_name->location_name;
            }

        }


        // dd($parameter);
         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND a.hrm_depertment_id = ".$request->depertment ;
        }

         if (isset($request->designation)){
            $parameter  =  $parameter . " AND a.hrm_designation_id = ".$request->designation ;
        }

         if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND a.hrm_employee_id = ".$request->employee_name ;
        }

        $category="";
        if (isset($request->category)){
            $parameter  =  $parameter . " AND a.hrm_category_id = ".$request->category ;
            $category = HrmCategory::find($request->category);
            $category = $category->category_name." Only";
        }

        $payment_type="=Cash & Bank=";

        if($request->payment_mode<>0){

            $parameter  =  $parameter . " AND aa.payment_mode = ".$request->payment_mode ;

                if($request->payment_mode==1){
                    $payment_type="=Cash Only=";
                }elseif ($request->payment_mode==2) {
                    $payment_type="=Bank Only=";
                }
        }


       $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
       $month_name=$month_name[0]->month_name;

       // dd($paramet  er);

       // dd($parameter);

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "$title || ".$month_name."- $year "."[$location_name]"."[$category]"."[$payment_type]",
            'condition_parameter'   => $parameter,
            'hrm_month_id'          => $month,
            'year_id'               => $year
        );

        $report_path = Config::get('configaration.report_path').'hrm_fringe_benefit';
        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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


   public function banksalaryreport (Request $request)
    {

        $title="Bank Salary Sheet";
        if(!empty($request->location)){

            $check_data = DB::select("SELECT * FROM pay_register WHERE hrm_month_id = $request->month AND year_id =  $request->year AND salary_genarate_type=1 and hrm_location_id=$request->location LIMIT 1");

            if (!empty($check_data)){
                $title="Pending Bank Salary Sheet";
            }else{
                $title="Bank Salary Sheet";
            }


        }



        $month       = $request->month;
        $year        = $request->year;
        // $bank_name   = $request->bank_name;
        // $location    = $request->location;

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $parameter="";


        if($request->report_for==1){
            $parameter = "AND aa.salary_genarate_type<>0 AND aa.apply_for = 1 AND aa.hrm_month_id=$month  AND aa.year_id= $year  ";
        }


        if($request->report_for==2){
            $parameter = "AND aa.salary_genarate_type<>0 AND aa.apply_for = 3 AND aa.hrm_month_id=$month  AND aa.year_id= $year  ";

        }


        if($request->report_for==3){
            $parameter = "AND aa.salary_genarate_type<>0 AND aa.apply_for IN (1) AND aa.hrm_month_id=$month  AND aa.year_id= $year  ";

        }


      if($request->report_for==3){
            $title      =  'All Location Working...' ;
            $parameter  =  $parameter . " AND aa.hrm_location_id IN (SELECT id FROM hrm_location WHERE location_type=3) " ;

      }else{

        if (isset($request->location)){

            if($request->location==0){
                $title  =  'All Location' ;
            }else{
                $parameter     =  $parameter . " AND aa.hrm_location_id = ".$request->location ;
                $location_name = HrmLocation::find($request->location);
                $title = $location_name->location_name;
            }

        }



      }



        if (isset($request->bank_name)){

            if($request->bank_name==0){
                $title  =  $title.'-All Bank' ;
            }else{
                $parameter  =  $parameter . " AND aa.hrm_bank_id = ".$request->bank_name ;
                $bank_name = HrmBank::find($request->bank_name);
                $title  =  $title.'-'.$bank_name->short_name ;
            }

        }

        if (isset($request->depertment)){
            $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
        }



        if (isset($request->category)){
            $parameter  =  $parameter . " AND b.hrm_category_id = ".$request->category ;
        }



        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND b.hrm_employee_id = ".$request->employee_name ;
        }



        if(!empty($request->slap_name)){
            $parameter  = $parameter." JOIN hrm_salary_generate_master g ON aa.hrm_salary_generate_master_id = g.id
             AND g.hrm_salary_slap_id =".$request->slap_name;
        }


       // dd($parameter);

       $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id = $month");
       $month_name=$month_name[0]->month_name;

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "$title || ".$month_name."- $year ",
            'condition_parameter'   => $parameter,
            // 'hrm_month_id'          => $month,
            // 'year_id'               => $year,
            // 'bank_id'               => $bank_name,
        );

        // $report_path = Config::get('configaration.report_path').'hrm_bank_salary_sheet';

        if($request->report_for==2){

            $report_path = Config::get('configaration.report_path').'hrm_bank_salary_sheet_cw';

        }else{

            if($request->report_formate==1){
                // $report_path = Config::get('configaration.report_path').'hrm_bank_salary_sheet';
                $report_path = Config::get('configaration.report_path').'hrm_bank_salary_sheet_total';
            }else{
                $report_path = Config::get('configaration.report_path').'hrm_bank_sheet';
            }

        }

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

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
                    header('Content-Disposition: attachment; filename=hrm_bank_sheet.'.$generate_type);
                    // header('Content-Disposition: attachment; filename=hrm_bank_sheet.xls');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . strlen($report));
                    header('Content-Type: application/'.$generate_type);
                    echo $report;
            }
        }else{
           dd("No data found");
        }


    }





   public function compare_salary_report (Request $request)
    {

        // dd($request->all());

        $location_name = HrmLocation::find($request->location);

        $previous_month      =$request->previous_month;
        $previous_year      = $request->previous_year;



        $month      =$request->month;
        $year      = $request->year;

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        $previous_parameter = "AND a.hrm_month_id=". $previous_month . " And a.year_id=". $previous_year . " ";
        $current_parameter = "AND a.hrm_month_id=". $month . " And a.year_id=". $year . " ";

        if (isset($request->location)){
            $parameter =  " AND b.hrm_location_id = ".$request->location ;
        }


        if (isset($request->depertment)){
            $parameter  =  $parameter. " AND b.hrm_depertment_id = ".$request->depertment ;
        }

       // $parameter="";

       $previous_month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$previous_month");
       $previous_month_name=$previous_month_name[0]->month_name;
       $previous_month_name=$previous_month_name."- $previous_year ";

       $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
       $current_month_name=$month_name[0]->month_name;
       $current_month_name=$current_month_name."- $year ";

       $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Compare Salary Report"."[$location_name->location_name]",
            'previous_parameter'    => $previous_parameter,
            'current_parameter'     => $current_parameter,
            'previous_month_name'   => $previous_month_name,
            'current_month_name'    => $current_month_name,
            'parameter'             => $parameter,

        );

// dd($controls);

        $report_path = Config::get('configaration.report_path').'hrm_compare_salary_report';
        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

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






   public function compare_OT_report (Request $request)
    {


        $location_name = HrmLocation::find($request->location);

        $previous_month      =$request->previous_month;
        $previous_year      = $request->previous_year;



        $month      =$request->month;
        $year      = $request->year;

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        $previous_parameter = "AND Month(e.punche_date)=". $previous_month . " And Year(e.punche_date)=". $previous_year . " ";
        $current_parameter = " AND Month(e.punche_date)=". $month . " And Year(e.punche_date)=". $year . " ";

        if (isset($request->location)){
            $parameter =  " AND b.hrm_location_id = ".$request->location ;
        }


        if (isset($request->depertment)){
            $parameter  =  $parameter. " AND b.hrm_depertment_id = ".$request->depertment ;
        }

       // $parameter="";

       $previous_month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$previous_month");
       $previous_month_name=$previous_month_name[0]->month_name;
       $previous_month_name=$previous_month_name."- $previous_year ";

       $month_name = DB::SELECT("SELECT month_name FROM hrm_month Where id=$month");
       $current_month_name=$month_name[0]->month_name;
       $current_month_name=$current_month_name."- $year ";

       $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Compare OT Report"."[$location_name->location_name]",
            'previous_parameter'    => $previous_parameter,
            'current_parameter'     => $current_parameter,
            'previous_month_name'   => $previous_month_name,
            'current_month_name'    => $current_month_name,
            'parameter'             => $parameter,

        );


        $report_path = Config::get('configaration.report_path').'hrm_compare_OT_report';
        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

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











   public function bonusreport (Request $request)
    {

// dd($request->all());

        $parameter = ' AND a.is_valid = 1 ' ;

        if (isset($request->declaration_date)){

            $declaration_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->declaration_date)));
            $parameter        = $parameter . " AND aa.declaration_date = '$declaration_date' " ;
            $getYear          = date('Y', strtotime($declaration_date));
            $find             = HrmEmployeeBonusMaster::where('declaration_date',$declaration_date)->where('is_valid',1)->first();
            $bonus_name       = HrmBonus::find($find->hrm_bonus_id);
            $bonus_name       = $bonus_name->bonus_name;

            if($request->all_location=='777'){
                $location_name = 'ALL Location';

            }else{
                $location_name = 'ALL Depot';
                $parameter     = $parameter . " AND e.location_type = 3 ";

            }


        }else{

            $find             = HrmEmployeeBonusMaster::find($request->hrm_employee_bonus_master_id);
            $bonus_name       = HrmBonus::find($find->hrm_bonus_id);
            $bonus_name       = $bonus_name->bonus_name;
            $location_name    = HrmLocation::find($find->hrm_location_id);
            $location_name    = $location_name->location_name;
            $declaration_date = $find->declaration_date;
            $getYear          = date('Y', strtotime($declaration_date));

            $parameter        = $parameter ." AND aa.id= $request->hrm_employee_bonus_master_id ";

        }

        $userid  = Auth::user()->id;


        if ($find->status==1 ){
            $title="Pending Bonus Sheet || "."[$bonus_name - $getYear ]"."[$location_name]";
        }else{
            $title="Bonus Sheet || "."[$bonus_name - $getYear ]"."[$location_name]";
        }




        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );



        if (isset($request->location)){
            $parameter  =  $parameter . " AND b.hrm_location_id = ".$request->location ;
        }else{
            $parameter  =  $parameter . " AND b.hrm_location_id in (SELECT hrm_location_id from user_location WHERE users_id = $userid  )" ;
        }

        // dd($parameter);
         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
        }

         if (isset($request->designation)){
            $parameter  =  $parameter . " AND b.hrm_designation_id = ".$request->designation ;
        }


        if (isset($request->category)){
            $parameter  =  $parameter . " AND b.hrm_category_id = ".$request->category ;
        }


        if (isset($request->section)){
            $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;
        }

        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND b.hrm_employee_id = ".$request->employee_name ;
        }


        if (isset($request->bank_name)){
            $parameter  =  $parameter . " AND a.hrm_bank_id = ".$request->bank_name ;
        }
        if (isset($request->employee_status)){

            if (count($request->employee_status) > 0){
                $employee_status = implode(',', $request->employee_status);
                $parameter  =  $parameter . " AND b.hrm_employment_status_id IN ($employee_status) " ;
            }
            // dd($parameter);
        }



// dd($parameter);


        $payment_type="=Cash & Bank=";

        if($request->payment_mode<>0){
            $parameter  =  $parameter . " AND a.payment_mode = ".$request->payment_mode ;


             if($request->payment_mode==1){
                    $payment_type="=Cash Only=";
                }elseif ($request->payment_mode==2) {
                    $payment_type="=Bank Only=";
             }

        }



        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "$title "."[$payment_type]",
            'condition_parameter'   => $parameter,
        );



        if($request->payment_mode==1){

                $report_path = Config::get('configaration.report_path').'hrm_bonus_sheet_cash_only';

        }else{


                if($request->view==1){
                    $report_path = Config::get('configaration.report_path').'hrm_bonus_subreport';
                }else{

                    if($request->report_number==1){
                        $report_path = Config::get('configaration.report_path').'hrm_bonus_sheet';
                        // dd($report_path);
                    }elseif ($request->report_number==2) {
                        $report_path = Config::get('configaration.report_path').'hrm_bank_bonus_sheet';
                    }else{
                        $report_path = Config::get('configaration.report_path').'hrm_bank_bonus_sheet_total';
                    }

                }

        }



        // dd($report_path);


        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

        if (strlen($report) > 988){

            if($generate_type=="pdf"){

                    $report      = $jasper_server->reportService()->runReport($report_path, $generate_type,null,null,$controls);
                    header("Content-type:application/pdf");
                    echo $report;
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


   public function pfreport (Request $request)
    {
        if($request->report_number==1){
            $validator = Validator::make($request->all(), [
                'employee_name'    => 'required',
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
        };


        $parameter ='';


        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        if($request->location<>0){

            if (isset($request->location)){
                $parameter  =  $parameter . " AND aa.hrm_location_id = ".$request->location ;
            }

        }

        if (isset($request->depertment)){
            $parameter  =  $parameter . " AND a.hrm_depertment_id = ".$request->depertment ;
        }

        if (isset($request->designation)){
            $parameter  =  $parameter . " AND a.hrm_designation_id = ".$request->designation ;
        }


        if (isset($request->category)){
            $parameter  =  $parameter . " AND a.hrm_category_id = ".$request->category ;
        }


        if (isset($request->section)){
            $parameter  =  $parameter . " AND a.hrm_section_id = ".$request->section ;
        }

        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND a.hrm_employee_id = ".$request->employee_name ;
        }
// dd($parameter);

        $datefrom    = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $dateto      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
        $lastdate       = date("Y-m-t", strtotime($dateto));


        $parameter_1 = " WHERE aaa.activity_date between '$datefrom' and '$lastdate' " ;
        $title       = date('F-Y', strtotime($datefrom)).' To '.date('F-Y', strtotime($dateto)) ;

        if($request->report_number==1){
            $title           = "Employee Wise Provident Fund Details Report( $title )";
            $report_path     = Config::get('configaration.report_path').'hrm_pf_details';
        }else{
            $title           = "Employee Wise Provident Fund Summary Report( $title )" ;
            $report_path     = Config::get('configaration.report_path').'hrm_pf_summary';
        }

        // dd($parameter);
        // dd($parameter_1);

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => $title,
            'condition_parameter'   => $parameter,
            'condition_parameter_1' => $parameter_1,
        );

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);
        $generate_type  = $request->generate_type;

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

   public function departmentwisesummary (Request $request)
    {


        $location_name = HrmLocation::find($request->location);

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));


        $parameter = " AND (a.punche_date) = '". $date . "'  ";

        if (isset($request->location)){
            $parameter  =  $parameter . " AND b.hrm_location_id = ".$request->location ;
        }

         if (isset($request->depertment)){
            $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
        }

        // $parameter = $parameter." AND (a.punche_date) = '". $date . "'  AND b.id in  (SELECT Max(id) as id FROM hrm_employee_job_info
        // GROUP BY hrm_employee_id) ";




        $parameter = $parameter." AND b.id IN (SELECT
                                                        hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE
                                                        '". $date . "' BETWEEN start_date AND end_date
                                                            AND end_date IS NOT NULL
                                                            AND hrm_employee_activity_status_id not in (5,7)
                                                            UNION ALL SELECT
                                                       hrm_employee_job_info_id
                                                    FROM
                                                        hrm_employee_activity
                                                    WHERE
                                                        end_date IS NULL
                                                            AND '". $date . "' >= start_date AND
                                                             hrm_employee_activity_status_id not in (5,7))  ";
        // dd($parameter);
        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Department Wise Attendance Summary || Dated: ".$request->date."[$location_name->location_name]",
            'date_range'            => $date,
            'condition_parameter'   => $parameter,

        );


        $report_path = Config::get('configaration.report_path').'hrm_department_wise_summary';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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



   public function missingoutreport (Request $request)
    {


        $location_name = HrmLocation::find($request->location);

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));
        $parameter = " AND a.punch_date = '". $date . "'";

        // if (isset($request->employee_name)){
        //     $parameter  =  $parameter . " AND b.id = ".$request->employee_name ;
        // }

        if (isset($request->location)){
            $parameter  =  $parameter . " AND c.hrm_location_id = ".$request->location ;
        }

        if (isset($request->depertment)){
            $parameter  =  $parameter . " AND c.hrm_depertment_id = ".$request->depertment ;
        }



        $parameter = $parameter ." AND f.id IN (SELECT id FROM hrm_employee_shift
                      WHERE  '". $date . "'  BETWEEN start_date AND end_date AND end_date IS NOT NULL
                      UNION ALL
                      SELECT id FROM hrm_employee_shift WHERE end_date IS NULL
                      AND '". $date . "' >= start_date)";

        // dd($parameter);
        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => "Attendance Missing Out Report || Dated: ".$request->date."[$location_name->location_name]",
            'condition_parameter'   => $parameter,
        );


        $report_path = Config::get('configaration.report_path').'hrmattendancemissingout';

        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;

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







    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
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

    public function cost_to_the_company(){
        return view('reports.cost-to-the-company');
    }

    public function get_cost_to_the_company(Request $request){
        [$monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo] = $this->buildCostParams($request);
        $dataset = $this->getCostToTheCompanyGroupedData($monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo);

        $columns = [
            ['data' => 'sl_no',                 'title' => 'Sl.'],
            ['data' => 'details',               'title' => 'Details'],
            ['data' => 'no_of_employees',       'title' => 'No. of Employees'],
            ['data' => 'gross_salary',          'title' => 'Gross Salary'],
            ['data' => 'two_festival_bonus',    'title' => 'Two Festival Bonus'],
            ['data' => 'pf_contribution',       'title' => 'PF Com. Contribution'],
            ['data' => 'transport_outOfPocket', 'title' => 'Out of Pocket & Transport'],
            ['data' => 'houseRent_allowance',   'title' => 'House Rent'],
            ['data' => 'fixed_allowance',       'title' => 'Fixed Allowance'],
            ['data' => 'total_cost',            'title' => 'Total Cost'],
        ];

        return json_encode(array('data' => $dataset, 'columns' => $columns));
    }

    public function exportExcelCostToTheCompany(Request $request){
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        [$monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo] = $this->buildCostParams($request);
        $dataset     = $this->getCostToTheCompanyData($monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo);
        $month_from  = $request->month_from;
        $month_to    = $request->month_to;

        return Excel::download(new CostToTheCompanyExport($dataset, $month_from, $month_to), 'cost_to_the_company.xlsx');
    }

    public function exportPdfCostToTheCompany(Request $request){
        [$monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo] = $this->buildCostParams($request);
        $dataset     = $this->getCostToTheCompanyData($monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo);
        $data        = $dataset;
        $month_from  = $request->month_from;
        $month_to    = $request->month_to;
        $company_name = Config::get('configaration.company_name');
        $company_address = Config::get('configaration.company_address');

        $pdf = PDF::loadView('reports.cost-to-the-company-pdf', compact('data', 'month_from', 'month_to', 'company_name', 'company_address'))
                    ->setPaper('a4', 'P');

        return $pdf->download('cost_to_the_company.pdf');
    }

    private function buildCostParams(Request $request){
        $monthFrom       = date('Ym', strtotime(str_replace('/', '-', $request->month_from)));
        $monthTo         = date('Ym', strtotime(str_replace('/', '-', $request->month_to)));
        $durationFrom    = date('Y-m-d', strtotime(str_replace('/', '-', $request->month_from)));
        $durationTo      = date('Y-m-d', strtotime(str_replace('/', '-', $request->month_to)));

        $locationCondition = '';
        $locationId = $request->hrm_location_id;
        if (is_numeric($locationId) && $locationId != '999') {
            $locationCondition = " AND a.hrm_location_id = {$locationId}";
        }

        return [$monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo];
    }

    private function getCostToTheCompanyData($monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo){
        return DB::select("SELECT
                                yyy.level_name,
                                yyy.hrm_location_id,
                                yyy.location_name,
                                SUM(yyy.pf_contribution) AS pf_contribution,
                                SUM(yyy.gross_salary) AS gross_salary,
                                SUM(yyy.two_festival_bonus) AS two_festival_bonus,
                                SUM(yyy.houseRent_allowance) AS houseRent_allowance,
                                SUM(yyy.fixed_allowance) AS fixed_allowance,
                                SUM(yyy.transport_outOfPocket) AS transport_outOfPocket,
                                COUNT(DISTINCT yyy.hrm_employee_id) AS NoOfEmployee,
                                SUM(
                                    IFNULL(yyy.gross_salary, 0) +
                                    IFNULL(yyy.two_festival_bonus, 0) +
                                    IFNULL(yyy.pf_contribution, 0) +
                                    IFNULL(yyy.transport_outOfPocket, 0) +
                                    IFNULL(yyy.houseRent_allowance, 0) +
                                    IFNULL(yyy.fixed_allowance, 0)
                                ) AS existing_cost
                            FROM (
                                SELECT
                                    aa.hrm_location_id,
                                    aa.location_name,
                                    aa.hrm_employee_id,
                                    aa.level_name,
                                    SUM(aa.PF) AS pf_contribution,
                                    SUM(IFNULL(aa.basic_salary, 0) + IFNULL(aa.medical_allowance, 0) + IFNULL(aa.house_rent, 0)) AS gross_salary,
                                    (SUM(aa.bonusCalculate) * (TIMESTAMPDIFF(MONTH, '$durationFrom', '$durationTo') + 1)) AS two_festival_bonus,
                                    SUM(aa.houseRent_allowance) AS houseRent_allowance,
                                    SUM(aa.fixed_allowance) AS fixed_allowance,
                                    SUM(aa.transport_outOfPocket) AS transport_outOfPocket
                                FROM (
                                    /* ========================================== */
                                    /* 1. Contractual Worker (Status ID = 4)       */
                                    /* ========================================== */
                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                        IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                        IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                        IF(c.id = 6, b.actual_amount, 0) AS PF,
                                        IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                        IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                        IF(c.id IN (11, 8), b.actual_amount, 0) AS transport_outOfPocket,
                                        ROUND(IF(c.id = 1, b.actual_amount, 0) * 2 / 12, 0) AS bonusCalculate,
                                        IF(l.location_type = 3, 'All Depot', l.location_name) AS location_name,
                                        'Contructual Worker' AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id IN (4)
                                    JOIN pay_register_details b ON ap.id = b.pay_register_id
                                    JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for IN (1, 2)
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        0 AS basic_salary,
                                        0 AS medical_allowance,
                                        (((IFNULL(ap.amount, 0) * IFNULL(ap.total_present, 0)) + IFNULL(b.due_adjust, 0)) - IFNULL(b.adv_adjust, 0)) AS house_rent,
                                        0 AS PF,
                                        0 AS houseRent_allowance,
                                        0 AS fixed_allowance,
                                        0 AS transport_outOfPocket,
                                        ROUND((a.basic_salary * 2 * 0.60 / 12)) AS bonusCalculate,
                                        IF(l.location_type = 3, 'All Depot', l.location_name) AS location_name,
                                        'Contructual Worker' AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id IN (4)
                                    JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for = 3
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    /* ========================================== */
                                    /* 2. Regular Employees (Status NOT IN 3, 4)   */
                                    /* ========================================== */
                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                        IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                        IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                        IF(c.id = 6, b.actual_amount, 0) AS PF,
                                        IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                        IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                        IF(c.id IN (11, 8), b.actual_amount, 0) AS transport_outOfPocket,
                                        ROUND(IF(c.id = 1, b.actual_amount, 0) * 2 / 12, 0) AS bonusCalculate,
                                        IF(l.location_type = 3, 'All Depot', l.location_name) AS location_name,
                                        q.Level_name AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                     AND a.hrm_employment_status_id NOT IN (3, 4)
                                    JOIN pay_register_details b ON ap.id = b.pay_register_id
                                    JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    JOIN hrm_employee_salary m ON m.hrm_employee_job_info_id = a.id
                                    JOIN hrm_salary_grade_master o ON o.id = m.hrm_salary_grade_master_id
                                    JOIN hrm_employee_leveling_details p ON p.hrm_salary_grade_id = o.hrm_salary_grade_id AND p.valid = 1
                                    JOIN hrm_employee_leveling_master q ON q.id = p.hrm_employee_leveling_master_id AND q.valid = 1
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for IN (1, 2)
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        0 AS basic_salary,
                                        0 AS medical_allowance,
                                        (((IFNULL(ap.amount, 0) * IFNULL(ap.total_present, 0)) + IFNULL(b.due_adjust, 0)) - IFNULL(b.adv_adjust, 0)) AS house_rent,
                                        0 AS PF,
                                        0 AS houseRent_allowance,
                                        0 AS fixed_allowance,
                                        0 AS transport_outOfPocket,
                                        ROUND((a.basic_salary * 2 * 0.60 / 12)) AS bonusCalculate,
                                        IF(l.location_type = 3, 'All Depot', l.location_name) AS location_name,
                                        q.Level_name AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id NOT IN (3, 4)
                                    JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    JOIN hrm_employee_salary m ON m.hrm_employee_job_info_id = a.id
                                    JOIN hrm_salary_grade_master o ON o.id = m.hrm_salary_grade_master_id
                                    JOIN hrm_employee_leveling_details p ON p.hrm_salary_grade_id = o.hrm_salary_grade_id AND p.valid = 1
                                    JOIN hrm_employee_leveling_master q ON q.id = p.hrm_employee_leveling_master_id AND q.valid = 1
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for = 3
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    /* ========================================== */
                                    /* 3. Casual Worker (Status ID = 3)            */
                                    /* ========================================== */
                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                        IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                        IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                        IF(c.id = 6, b.actual_amount, 0) AS PF,
                                        IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                        IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                        IF(c.id IN (11, 8), b.actual_amount, 0) AS transport_outOfPocket,
                                        ROUND(IF(c.id = 1, b.actual_amount, 0) * 2 / 12, 0) AS bonusCalculate,
                                        IF(l.location_type = 3, 'All Depot', l.location_name) AS location_name,
                                        'Casual Worker' AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id IN (3)
                                    JOIN pay_register_details b ON ap.id = b.pay_register_id
                                    JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for IN (1, 2)
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        0 AS basic_salary,
                                        0 AS medical_allowance,
                                        (((IFNULL(ap.amount, 0) * IFNULL(ap.total_present, 0)) + IFNULL(b.due_adjust, 0)) - IFNULL(b.adv_adjust, 0)) AS house_rent,
                                        0 AS PF,
                                        0 AS houseRent_allowance,
                                        0 AS fixed_allowance,
                                        0 AS transport_outOfPocket,
                                        ROUND((a.basic_salary * 2 * 0.60 / 12)) AS bonusCalculate,
                                        IF(l.location_type = 3, 'All Depot', l.location_name) AS location_name,
                                        'Casual Worker' AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id IN (3)
                                    JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for = 3
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition
                                ) aa
                                GROUP BY aa.hrm_employee_id, aa.hrm_location_id, aa.location_name, aa.level_name
                            ) yyy
                            GROUP BY yyy.level_name, yyy.location_name
                            ORDER BY CASE yyy.level_name
                                        WHEN 'Casual Worker' THEN 2
                                        WHEN 'Contructual Worker' THEN 3
                                        ELSE 1
                                    END,
                                    yyy.level_name, yyy.location_name;
                                    ");
    }

    private function getCostToTheCompanyDataOld($monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo){
        return DB::select("SELECT
                                yyy.level_name,
                                yyy.hrm_location_id,
                                yyy.location_name,
                                SUM(yyy.pf_contribution) AS pf_contribution,
                                SUM(yyy.gross_salary) AS gross_salary,
                                SUM(yyy.two_festival_bonus) AS two_festival_bonus,
                                SUM(yyy.houseRent_allowance) AS houseRent_allowance,
                                SUM(yyy.fixed_allowance) AS fixed_allowance,
                                SUM(yyy.transport_outOfPocket) AS transport_outOfPocket,
                                COUNT(DISTINCT yyy.hrm_employee_id) AS NoOfEmployee,
                                SUM(
                                    IFNULL(yyy.gross_salary, 0) +
                                    IFNULL(yyy.two_festival_bonus, 0) +
                                    IFNULL(yyy.pf_contribution, 0) +
                                    IFNULL(yyy.transport_outOfPocket, 0) +
                                    IFNULL(yyy.houseRent_allowance, 0) +
                                    IFNULL(yyy.fixed_allowance, 0)
                                ) AS existing_cost
                            FROM (
                                SELECT
                                    aa.hrm_location_id,
                                    aa.location_name,
                                    aa.hrm_employee_id,
                                    aa.level_name,
                                    SUM(aa.PF) AS pf_contribution,
                                    SUM(IFNULL(aa.basic_salary, 0) + IFNULL(aa.medical_allowance, 0) + IFNULL(aa.house_rent, 0)) AS gross_salary,
                                    (SUM(aa.bonusCalculate) * (TIMESTAMPDIFF(MONTH, '$durationFrom', '$durationTo') + 1)) AS two_festival_bonus,
                                    SUM(aa.houseRent_allowance) AS houseRent_allowance,
                                    SUM(aa.fixed_allowance) AS fixed_allowance,
                                    SUM(aa.transport_outOfPocket) AS transport_outOfPocket
                                FROM (
                                    /* ========================================== */
                                    /* 1. Contractual Worker (Status ID = 4)       */
                                    /* ========================================== */
                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                        IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                        IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                        IF(c.id = 6, b.actual_amount, 0) AS PF,
                                        IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                        IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                        IF(c.id IN (11, 8), b.actual_amount, 0) AS transport_outOfPocket,
                                        ROUND(IF(c.id = 1, b.actual_amount, 0) * 2 / 12, 0) AS bonusCalculate,
                                        l.location_name,
                                        'Contructual Worker' AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id IN (4)
                                    JOIN pay_register_details b ON ap.id = b.pay_register_id
                                    JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for IN (1, 2)
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        0 AS basic_salary,
                                        0 AS medical_allowance,
                                        (((IFNULL(ap.amount, 0) * IFNULL(ap.total_present, 0)) + IFNULL(b.due_adjust, 0)) - IFNULL(b.adv_adjust, 0)) AS house_rent,
                                        0 AS PF,
                                        0 AS houseRent_allowance,
                                        0 AS fixed_allowance,
                                        0 AS transport_outOfPocket,
                                        ROUND((a.basic_salary * 2 * 0.60 / 12)) AS bonusCalculate,
                                        l.location_name,
                                        'Contructual Worker' AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id IN (4)
                                    JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for = 3
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    /* ========================================== */
                                    /* 2. Regular Employees (Status NOT IN 3, 4)   */
                                    /* ========================================== */
                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                        IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                        IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                        IF(c.id = 6, b.actual_amount, 0) AS PF,
                                        IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                        IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                        IF(c.id IN (11, 8), b.actual_amount, 0) AS transport_outOfPocket,
                                        ROUND(IF(c.id = 1, b.actual_amount, 0) * 2 / 12, 0) AS bonusCalculate,
                                        l.location_name,
                                        q.Level_name AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id
                                     AND a.hrm_employment_status_id NOT IN (3, 4)
                                    JOIN pay_register_details b ON ap.id = b.pay_register_id
                                    JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    JOIN hrm_employee_salary m ON m.hrm_employee_job_info_id = a.id
                                    JOIN hrm_salary_grade_master o ON o.id = m.hrm_salary_grade_master_id
                                    JOIN hrm_employee_leveling_details p ON p.hrm_salary_grade_id = o.hrm_salary_grade_id AND p.valid = 1
                                    JOIN hrm_employee_leveling_master q ON q.id = p.hrm_employee_leveling_master_id AND q.valid = 1
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for IN (1, 2)
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        0 AS basic_salary,
                                        0 AS medical_allowance,
                                        (((IFNULL(ap.amount, 0) * IFNULL(ap.total_present, 0)) + IFNULL(b.due_adjust, 0)) - IFNULL(b.adv_adjust, 0)) AS house_rent,
                                        0 AS PF,
                                        0 AS houseRent_allowance,
                                        0 AS fixed_allowance,
                                        0 AS transport_outOfPocket,
                                        ROUND((a.basic_salary * 2 * 0.60 / 12)) AS bonusCalculate,
                                        l.location_name,
                                        q.Level_name AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id NOT IN (3, 4)
                                    JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    JOIN hrm_employee_salary m ON m.hrm_employee_job_info_id = a.id
                                    JOIN hrm_salary_grade_master o ON o.id = m.hrm_salary_grade_master_id
                                    JOIN hrm_employee_leveling_details p ON p.hrm_salary_grade_id = o.hrm_salary_grade_id AND p.valid = 1
                                    JOIN hrm_employee_leveling_master q ON q.id = p.hrm_employee_leveling_master_id AND q.valid = 1
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for = 3
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    /* ========================================== */
                                    /* 3. Casual Worker (Status ID = 3)            */
                                    /* ========================================== */
                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        IF(c.id = 1, b.actual_amount, 0) AS basic_salary,
                                        IF(c.id = 2, b.actual_amount, 0) AS medical_allowance,
                                        IF(c.id = 5, b.actual_amount, 0) AS house_rent,
                                        IF(c.id = 6, b.actual_amount, 0) AS PF,
                                        IF(c.id = 9, b.actual_amount, 0) AS houseRent_allowance,
                                        IF(c.id = 12, b.actual_amount, 0) AS fixed_allowance,
                                        IF(c.id IN (11, 8), b.actual_amount, 0) AS transport_outOfPocket,
                                        ROUND(IF(c.id = 1, b.actual_amount, 0) * 2 / 12, 0) AS bonusCalculate,
                                        l.location_name,
                                        'Casual Worker' AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id IN (3)
                                    JOIN pay_register_details b ON ap.id = b.pay_register_id
                                    JOIN hrm_salary_head c ON b.hrm_salary_head_id = c.id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for IN (1, 2)
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition

                                    UNION ALL

                                    SELECT
                                        l.id AS hrm_location_id,
                                        a.hrm_employee_id,
                                        0 AS basic_salary,
                                        0 AS medical_allowance,
                                        (((IFNULL(ap.amount, 0) * IFNULL(ap.total_present, 0)) + IFNULL(b.due_adjust, 0)) - IFNULL(b.adv_adjust, 0)) AS house_rent,
                                        0 AS PF,
                                        0 AS houseRent_allowance,
                                        0 AS fixed_allowance,
                                        0 AS transport_outOfPocket,
                                        ROUND((a.basic_salary * 2 * 0.60 / 12)) AS bonusCalculate,
                                        l.location_name,
                                        'Casual Worker' AS level_name
                                    FROM pay_register ap
                                    JOIN hrm_employee_job_info a ON ap.hrm_employee_job_info_id = a.id AND a.hrm_employment_status_id IN (3)
                                    JOIN pay_register_cw b ON ap.id = b.pay_register_id
                                    JOIN hrm_employee e ON a.hrm_employee_id = e.id
                                    JOIN hrm_location l ON a.hrm_location_id = l.id
                                    WHERE ap.salary_genarate_type <> 0
                                    AND ap.apply_for = 3
                                    AND CONCAT(ap.year_id, LPAD(ap.hrm_month_id, 2, '0')) BETWEEN '$monthFrom' AND '$monthTo' $locationCondition
                                ) aa
                                GROUP BY aa.hrm_employee_id, aa.hrm_location_id, aa.location_name, aa.level_name
                            ) yyy
                            GROUP BY yyy.level_name, yyy.hrm_location_id, yyy.location_name
                            ORDER BY CASE yyy.level_name
                                        WHEN 'Casual Worker' THEN 2
                                        WHEN 'Contructual Worker' THEN 3
                                        ELSE 1
                                    END,
                                    yyy.level_name, yyy.location_name;
                                    ");
    }

    private function getCostToTheCompanyGroupedData($monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo){
        $dataset = $this->getCostToTheCompanyData($monthFrom, $monthTo, $locationCondition, $durationFrom, $durationTo);

        $groups = [];
        $order = [];
        $totalEmployees = 0;
        $totalGross     = 0;
        $totalBonus     = 0;
        $totalPF        = 0;
        $totalTransport = 0;
        $totalHouseRent = 0;
        $totalFixed     = 0;
        $totalCost      = 0;

        foreach ($dataset as $row) {
            if (!isset($groups[$row->level_name])) {
                $groups[$row->level_name] = [
                    'rows' => [],
                    'employees' => 0, 'gross' => 0, 'bonus' => 0, 'pf' => 0,
                    'transport' => 0, 'house_rent' => 0, 'fixed' => 0, 'cost' => 0,
                ];
                $order[] = $row->level_name;
            }

            $groups[$row->level_name]['rows'][] = $row;
            $groups[$row->level_name]['employees']  += $row->NoOfEmployee;
            $groups[$row->level_name]['gross']      += $row->gross_salary;
            $groups[$row->level_name]['bonus']      += $row->two_festival_bonus;
            $groups[$row->level_name]['pf']         += $row->pf_contribution;
            $groups[$row->level_name]['transport']  += $row->transport_outOfPocket;
            $groups[$row->level_name]['house_rent'] += $row->houseRent_allowance;
            $groups[$row->level_name]['fixed']      += $row->fixed_allowance;
            $groups[$row->level_name]['cost']       += $row->existing_cost;

            $totalEmployees += $row->NoOfEmployee;
            $totalGross     += $row->gross_salary;
            $totalBonus     += $row->two_festival_bonus;
            $totalPF        += $row->pf_contribution;
            $totalTransport += $row->transport_outOfPocket;
            $totalHouseRent += $row->houseRent_allowance;
            $totalFixed     += $row->fixed_allowance;
            $totalCost      += $row->existing_cost;
        }

        $result = [];
        $sl = 0;

        foreach ($order as $levelName) {
            $sl++;
            $g = $groups[$levelName];

            $result[] = (object)[
                'sl_no'                 => $sl,
                'details'               => $levelName,
                'row_type'              => 'subtotal',
                'no_of_employees'       => $g['employees'],
                'gross_salary'          => $g['gross'],
                'two_festival_bonus'    => $g['bonus'],
                'pf_contribution'       => $g['pf'],
                'transport_outOfPocket' => $g['transport'],
                'houseRent_allowance'   => $g['house_rent'],
                'fixed_allowance'       => $g['fixed'],
                'total_cost'            => $g['cost'],
                'hrm_location_id'       => null,
                'location_name'         => null,
            ];

            foreach ($g['rows'] as $r) {
                $result[] = (object)[
                    'sl_no'                 => '',
                    'details'               => $r->location_name,
                    'row_type'              => 'location',
                    'no_of_employees'       => $r->NoOfEmployee,
                    'gross_salary'          => $r->gross_salary,
                    'two_festival_bonus'    => $r->two_festival_bonus,
                    'pf_contribution'       => $r->pf_contribution,
                    'transport_outOfPocket' => $r->transport_outOfPocket,
                    'houseRent_allowance'   => $r->houseRent_allowance,
                    'fixed_allowance'       => $r->fixed_allowance,
                    'total_cost'            => $r->existing_cost,
                    'hrm_location_id'       => $r->hrm_location_id,
                    'location_name'         => $r->location_name,
                ];
            }
        }

        $result[] = (object)[
            'sl_no'                 => '',
            'details'               => 'TOTAL',
            'row_type'              => 'total',
            'no_of_employees'       => $totalEmployees,
            'gross_salary'          => $totalGross,
            'two_festival_bonus'    => $totalBonus,
            'pf_contribution'       => $totalPF,
            'transport_outOfPocket' => $totalTransport,
            'houseRent_allowance'   => $totalHouseRent,
            'fixed_allowance'       => $totalFixed,
            'total_cost'            => $totalCost,
            'hrm_location_id'       => null,
            'location_name'         => null,
        ];

        return $result;
    }
}
