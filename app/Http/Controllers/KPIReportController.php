<?php

namespace App\Http\Controllers;

use Jaspersoft\Client\Client;
use Jaspersoft\Service\jobService;
use Jaspersoft\Service\ReportService;


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
use Response;

use App\Models\HrmLocation;
use App\Models\HrmKPIIncrementRange;

// use App\Models\HrmKPIEmployeeMaster;
// use App\Models\HrmKPIEmployeeDetails;

class KPIReportController extends Controller
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

        $userid = Auth::user()->id;

        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $userid AND a.`default_location`=1");

        $location = DB::select("SELECT a.id,a.location_name FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $userid");

         return view('kpi_report.kpi_report')
              ->with('location',$location)
              ->with('default_user_location',$default_user_location);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    public function kpi_employee_final_list()
    {

    }








    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


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

        // dd($request->all());

        $parameter = "";

        if ($request->location==0) {
            $location_name = 'All Location';
        }elseif($request->location=='999'){
            $location_name = 'All Depot';
            $parameter  =  $parameter . " AND b.hrm_location_id in (SELECT id FROM hrm_location WHERE location_type=3 AND valid = 1) ";
        }else{
            $location_name = HrmLocation::find($request->location);
            $location_name = $location_name->location_name;
            $parameter     =  $parameter . " AND b.hrm_location_id = ".$request->location ;
        }

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );

        $year_date = DB::select("SELECT
                                a.id,concat(b.description,' | ','From :',a.start_date,'  To :',a.end_date) as year_date
                            FROM
                                hrm_kpi_assesment_date a
                                    JOIN
                                hrm_kpi_assesment_year b ON b.id = a.hrm_kpi_assesment_year_id WHERE a.id=$request->hrm_kpi_assesment_date_id;");

        $year_date = $year_date[0]->year_date;

        if (isset($request->depertment)){
            $parameter  =  $parameter . " AND b.hrm_depertment_id = ".$request->depertment ;
        }

        if (isset($request->designation)){
            $parameter  =  $parameter . " AND b.hrm_designation_id = ".$request->designation ;
        }


        if (isset($request->employee_name)){
            $parameter  =  $parameter . " AND b.hrm_employee_id = ".$request->employee_name ;
        }

        if (isset($request->section)){
            $parameter  =  $parameter . " AND b.hrm_section_id = ".$request->section ;
        }

        if (isset($request->category)){
            $parameter  =  $parameter . " AND b.hrm_category_id = ".$request->category ;
        }

        $parameter_1 = $parameter;

        if($request->report_for==1){
          $title = "Annual Evaluation Summary Report  "."[$location_name]";

          if (isset($request->increment_range)){
              $find = HrmKPIIncrementRange::find($request->increment_range);

              if($find->entry_status==1){
                $parameter = $parameter. " And g.hrm_kpi_increment_range_id=".$request->increment_range;
              }else{
                $parameter = $parameter. " And g.hrm_kpi_promotion_range_id=".$request->increment_range;
              }
          }

          $parameter = $parameter." AND aa.id = $request->hrm_kpi_assesment_date_id ";
          $report_path = Config::get('configaration.report_path').'hrm_kpi_employee_summary';

        }elseif($request->report_for==2){

          $parameter  =  $parameter . " AND b.hrm_designation_id in (1,198,2,3,4,5,6,7,8,9,10,11,196,197,13,14,15,12,194) ";

          $title = "Annual Evaluation (Yet Not Done) Report  "."[$location_name]";
          $report_path = Config::get('configaration.report_path').'hrm_kpi_employee_summary_without_done';

        }elseif($request->report_for==3){
          $parameter_1  =  $parameter_1 . " AND b.hrm_designation_id in (1,198,2,3,4,5,6,7,8,9,10,11,196,197,13,14,15,12,194) ";
          $parameter    = $parameter." AND aa.id = $request->hrm_kpi_assesment_date_id ";
          $title        = "Annual Evaluation (Done & Yet Not Done) Report  "."[$location_name]";


          $report_path  = Config::get('configaration.report_path').'hrm_kpi_employee_summary_with_all';
        }else{
          $report_path  = Config::get('configaration.report_path').'hrm_kpi_employee_summary';
        }

        $date_id  = $request->hrm_kpi_assesment_date_id;

          // dd($date_id);
        // dd($parameter);

        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            'title'                 => $title,
            'date_range'            => "Year: ".$year_date,
            'condition_parameter'   => $parameter,
            'condition_parameter_1' => $parameter_1,
            'date_id'               => $date_id,
        );




        $report      = $jasper_server->reportService()->runReport($report_path, "pdf",null,null,$controls);

        $generate_type  = $request->generate_type;
        // $generate_type  = 'pdf';

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

    public function cancel(Request $request,$id){


    }




}
