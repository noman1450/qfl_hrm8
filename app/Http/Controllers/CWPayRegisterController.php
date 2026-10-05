<?php

namespace App\Http\Controllers;

use Response;
use App\Models\HrmBank;
use Illuminate\Http\Request;
use Jaspersoft\Client\Client;
use App\Models\HrmPayRegister;

use App\Models\HrmPayRegisterCW;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CWPayRegisterController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $user_id = Auth::user()->id;
        $running_month_year = DB::SELECT("SELECT
                                                a.year_id,
                                                a.hrm_month_id,
                                                b.month_name,
                                                c.id AS master_id,
                                                c.declaration_date,
                                                e.id as hrm_location_id,
                                                e.location_name
                                            FROM
                                                `pay_register` a
                                                    JOIN
                                                hrm_month b ON a.hrm_month_id = b.id
                                                    AND a.`salary_genarate_type` <> 0
                                                    AND a.apply_for = 3
                                                    JOIN
                                                hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                                    JOIN
                                                user_location d ON a.hrm_location_id = d.hrm_location_id
                                                    AND d.`users_id`= $user_id
                                                    JOIN
                                                hrm_location e ON d.hrm_location_id = e.id
                                            ORDER BY a.id DESC
                                            LIMIT 0 , 1");


        if(empty($running_month_year)){
            $running_month_year = DB::SELECT("SELECT
                                                    b.id AS hrm_location_id,
                                                    b.location_name,
                                                    0 AS year_id,
                                                    0 AS hrm_month_id,
                                                    0 AS month_name,
                                                    0 AS master_id,
                                                    0 As declaration_date
                                                FROM
                                                    `user_location` a
                                                        JOIN
                                                    hrm_location b ON a.`hrm_location_id` = b.id
                                                        AND a.`users_id` = $user_id
                                                        AND a.`default_location` = 1");
        }


          return view('cw_salary.cwpayregister_list')
              ->with('running_month_year',$running_month_year);
    }


    public function cwpayregisterlistdata(Request $request)
    {
        $condition='';

        if (isset($request->plant_name)){
           $condition = " AND g.id=$request->plant_name";
        }

        if (isset($request->hrm_plant_with_section_id)){
           $condition = $condition." AND c.hrm_plant_with_section_id=$request->hrm_plant_with_section_id";
        }


        $data = DB::select("SELECT
                                    a.id,
                                    a.salary_genarate_type,
                                    concat(e.employee_name,' | ',b.employee_code) as employee_name,
                                    e.id as employee_id,
                                    h.depertment_name,
                                    j.designation_name,
                                    i.location_name,
                                    a.amount as salary_amount,
                                    a.year_id,
                                    a.hrm_month_id,
                                    a.day_of_month,
                                    a.total_present,
                                    (a.amount*a.total_present) as total_salary,
                                    g.plant_name ,
                                    f.section_name,
                                    c.adv_adjust,
                                    c.due_adjust,
                                    IF(a.payment_mode = 1, 'Cash', a.account_no) AS payment_mode
                                FROM
                                    pay_register a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND a.salary_genarate_type<>0
                                        AND a.apply_for=3
                                        AND a.year_id=$request->year AND a.hrm_month_id=$request->month
                                        AND b.hrm_location_id=$request->location
                                        JOIN
                                    pay_register_cw c ON a.id=c.pay_register_id
                                        JOIN
                                    hrm_plant_with_section d ON c.hrm_plant_with_section_id=d.id
                                        JOIN
                                    hrm_employee e ON b.hrm_employee_id = e.id
                                        JOIN
                                    hrm_section f ON b.hrm_section_id=f.id
                                        JOIN
                                    hrm_plant g ON d.hrm_plant_id=g.id
                                        JOIN
                                    hrm_depertment h ON b.hrm_depertment_id = h.id
                                        JOIN
                                    hrm_location i ON a.hrm_location_id = i.id
                                        JOIN
                                    hrm_designation j ON b.hrm_designation_id = j.id
                                         $condition
                                    GROUP BY a.id,e.employee_name,b.employee_code,e.id,
                                    h.depertment_name,j.designation_name,i.location_name,a.amount,a.year_id,a.hrm_month_id,
                                    a.day_of_month,a.total_present,g.plant_name ,f.section_name,c.adv_adjust,c.due_adjust,a.payment_mode,a.account_no");

        $data = collect($data);

        return datatables()->of($data)
            ->make(true);
    }

    public function show($id)
    {


         // if ($request->report_status==0){
         //            $status="All";
         //        }else{

         //            $query=DB::SELECT("SELECT attendance_status FROM hrm_attendance_status WHERE id=$request->report_status");
         //            $status=$query[0]->attendance_status;
         //        }



         //        $location_name = HrmLocation::find($request->location);


                // if (isset($request->working_shift)){
                //         $shift = HrmShift::find($request->working_shift);
                //         $shift_name=$shift->shift_name;

                // }else{
                //          $shift_name = "All Shift";
                // }

                // $date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));

                $jasper_server = new Client(
                    config('configaration.jasperjasper_url'),
                    config('configaration.jasper_user'),
                    config('configaration.jasper_password')
                );

                // $parameter="";
                // $parameter = " AND (a.punche_date) = '". $date . "'";

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
                //     $parameter  =  $parameter . " AND e.id = ".$request->employee_name ;
                // }


                // if (isset($request->working_shift)){
                //     $parameter  =  $parameter . " AND i.id = ".$request->working_shift ;
                // }


                //  if (!($request->report_status==0)){
                //     $parameter  =  $parameter . " AND a.attendance_status = ".$request->report_status ;
                // }

        // dd($parameter);

                $controls = array(
                    'company_name'          => config('configaration.company_name'),
                    'address'               => config('configaration.company_address'),
                    // 'title'                 => "Daily Attendance(".$status .") || Dated: ".$request->date."($shift_name)"."[$location_name->location_name]",
                    'title'                 => "Pay Register",
                    // 'condition_parameter'   => $parameter,
                    'id'   => $id,
                );


        $report_path = config('configaration.report_path').'hrm_payregister';
        $exporttype  = "pdf";
        $report = $jasper_server->reportService()->runReport($report_path, $exporttype, null, null, $controls);


        if (strlen($report) > 940){
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . strlen($report));
            header('Content-Type: application/'.$exporttype);
            echo $report;
            echo $report;
            echo $report;
            echo $report;
            echo "data:application/pdf;base64, " . $report;
        } else {
            // Session::flash('flash_message', 'No More Data For View');
            dd("No data found");
        }
    }

    public function edit($id)
    {
        $check_data = DB::select("SELECT id FROM pay_register WHERE id = $id AND salary_genarate_type = 2");

        if (!empty($check_data)){
            return back()->with('show-error', 'Sorry you can not edit,This Month Salary Already Generated');
        }

        $data['master_data'] = DB::SELECT("SELECT
                                    a.id,
                                    c.employee_name,
                                    c.id AS employee_id,
                                    d.depertment_name,
                                    f.designation_name,
                                    e.location_name,
                                    a.amount as salary_amount,
                                    a.year_id,
                                    a.hrm_month_id,
                                    a.total_present,
                                    a.day_of_month,
                                    a.payment_mode,
                                    a.account_no,
                                    a.by_bank_percent,
                                    h.id as hrm_bank_id,
                                    h.bank_name,
                                    g.month_name,
                                    b.id as hrm_job_info_id,
                                    (a.total_present*a.amount) as total_salary,
                                    aa.adv_adjust,
                                    aa.due_adjust,
                                    ((a.total_present*a.amount)-aa.adv_adjust+aa.due_adjust) as net_salary

                                FROM
                                    pay_register a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND a.id = $id
                                        JOIN
                                    pay_register_cw aa ON a.id = aa.pay_register_id
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_location e ON b.hrm_location_id = e.id
                                        JOIN
                                    hrm_designation f ON b.hrm_designation_id = f.id
                                        JOIN
                                    hrm_month g ON a.hrm_month_id=g.id
                                        LEFT JOIN
                                    hrm_bank h ON a.hrm_bank_id=h.id");

        $data['bank_name'] = HrmBank::all();

        return view('cw_salary.edit_cwpayregister', $data);
    }

    public function update(Request $request, $id)
    {
        $status = false;

        $find_data = HrmPayRegister::find($id);

        if($find_data->salary_genarate_type == 0) {
            return back()->with('alert-danger', 'This data already updated , please check!');
        } else {
            $find_data->salary_genarate_type =0;
            $find_data->save();
        }

        if (empty($find_data)){
            session()->flash('alert-danger', 'Invalid Input !!');
            return Redirect()->back();
        }

        $find_datacw  = HrmPayRegisterCW::where('pay_register_id',$id)->first();

        DB::beginTransaction();
        try {

            $insert = new HrmPayRegister;
            $insert->year_id                  = $find_data->year_id;
            $insert->hrm_month_id             = $find_data->hrm_month_id;
            $insert->hrm_employee_job_info_id = $find_data->hrm_employee_job_info_id;
            $insert->day_of_month             = $find_data->day_of_month;
            $insert->total_present            = $request->total_present;
            $insert->salary_genarate_type     = 1;
            $insert->users_id                 = Auth::user()->id;
            $insert->hrm_employee_salary_id   = 0;
            $insert->amount                   = $request->salary_amount;
            $insert->apply_for                = 3;
            $insert->payment_mode             = $request->payment_mode;
            $insert->account_no               = $request->account_no;
            $insert->by_bank_percent          = $find_data->by_bank_percent;
            $insert->hrm_bank_id              = $request->hrm_bank_id;
            $insert->hrm_salary_generate_master_id  = $find_data->hrm_salary_generate_master_id;
            $insert->hrm_location_id          = $find_data->hrm_location_id;
            // $insert->note                     = $request->note;
            $insert->save();


            $insertCW = new HrmPayRegisterCW;
            $insertCW->pay_register_id           = $insert->id;
            $insertCW->adv_adjust                = $request->adv_adjust;
            $insertCW->due_adjust                = $request->due_adjust;
            $insertCW->hrm_plant_with_section_id = $find_datacw->hrm_plant_with_section_id;
            $insertCW->save();

            DB::commit();

            $status = true;
            $message = 'Data has been updated..!';
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }

    public function delete(Request $request,$id)
    {
        $check_data = DB::select("SELECT id FROM pay_register WHERE id = $id AND salary_genarate_type =2");

        if (!empty($check_data)){
            return response()->json(array(
                dd("Sorry you can not edit, This Month Salary Already Generated ")
            ));
        }

        DB::UPDATE("UPDATE pay_register SET salary_genarate_type=0 WHERE id=$id");

        return back()->with('alert-success', 'successfully deleted !');
    }
}
