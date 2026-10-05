<?php

namespace App\Http\Controllers;

use Config;
use Session;
use App\User;

use Redirect;
use Response;
use DataTables;
use App\Models\HrmBank;
use App\Models\HrmMonth;
use Illuminate\Http\Request;
use Jaspersoft\Client\Client;
use App\Models\HrmPayRegister;
use App\Models\HrmDeletedDataLog;
use Illuminate\Support\Facades\DB;
use Jaspersoft\Service\jobService;

use Illuminate\Support\Facades\Auth;
use App\Models\HrmPayRegisterDetails;
use Illuminate\Support\Facades\Crypt;
use Jaspersoft\Service\ReportService;
use Illuminate\Support\Facades\Validator;



class OtherFacilityPayRegisterController extends Controller
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
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");




        $running_month_year = DB::SELECT("SELECT a.`year_id`,a.`hrm_month_id`,b.month_name FROM `pay_register` a JOIN hrm_month b ON a.hrm_month_id=b.id AND a.`salary_genarate_type`<>0 AND a.apply_for=2 ORDER BY a.id DESC LIMIT 0, 1");

        $year = DB::SELECT("SELECT year_id FROM pay_register WHERE salary_genarate_type<>0 AND apply_for=2  GROUP BY year_id");

    
         return view('otherfacility_pay_register.other_facility_payregister_list')
          ->with('month',HrmMonth::all())
          -> with('default_user_location',  $default_user_location)
            ->with('year',$year)
              ->with('running_month_year',$running_month_year);
    }


    public function payregisterlistdata(Request $request)
    {
        //dd($request->all());

        $condition ='';

        if($request->location==null) {
            $condition = '';
        } else {
            $condition = ' AND b.hrm_location_id= '.$request->location;
        }


        if(!empty($request->employeestatus)){
            $condition = $condition.' AND b.hrm_employment_status_id= '.$request->employeestatus;
        }
        
        $data   = DB::select("SELECT
                                    a.id,
                                    a.salary_genarate_type,
                                    concat(c.employee_name,' | ',b.employee_code,' | ',f.alis,' | ',d.depertment_name) as employee_name,
                                    c.id AS employee_id,
                                    d.depertment_name,
                                    e.location_name,
                                    a.amount as salary_amount,
                                    a.year_id,
                                    a.hrm_month_id,
                                    a.day_of_month,
                                    a.total_present,
                                    a.accounts_code,
                                    IF(a.payment_mode = 1, 'Cash', j.short_name) AS payment_mode,
                                    a.account_no,
                                    GROUP_CONCAT(CONCAT(g.amount)
                                        SEPARATOR '<br>') AS amount,
                                    GROUP_CONCAT(CONCAT(IF(g.amount_type = 1, '%', 'TK'))
                                        SEPARATOR '<br>') AS amount_type,
                                    GROUP_CONCAT(CONCAT(h.salary_head)
                                        SEPARATOR '<br>') AS salary_head,
                                    GROUP_CONCAT(CONCAT(IF(g.amount_type = 1,
                                                    (a.amount * g.amount / 100),
                                                    g.amount))
                                        SEPARATOR '<br>') AS salary_head_amount,
                                    GROUP_CONCAT(CONCAT(IF(i.generate_type = 1,
                                                    'Addition',
                                                    'Deduction'))
                                        SEPARATOR '<br>') AS Status,
                                    GROUP_CONCAT(CONCAT(i.group_name)
                                        SEPARATOR '<br>') AS group_name
                                FROM
                                    pay_register a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        $condition
                                        AND a.salary_genarate_type<>0
                                        AND a.year_id=$request->year AND a.hrm_month_id=$request->month
                                        AND a.apply_for = 2
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_location e ON b.hrm_location_id = e.id
                                        JOIN
                                    hrm_designation f ON b.hrm_designation_id = f.id
                                        JOIN
                                    pay_register_details g ON a.id = g.pay_register_id
                                        JOIN
                                    hrm_salary_head h ON g.hrm_salary_head_id = h.id
                                        JOIN
                                    hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id
                                        LEFT JOIN
                                    hrm_bank j ON a.hrm_bank_id = j.id

                                    GROUP BY a.id , c.employee_name ,b.employee_code, d.depertment_name , f.alis , e.location_name , a.amount , c.id, a.year_id,a.hrm_month_id,a.day_of_month,a.total_present,a.accounts_code,a.payment_mode,a.account_no,j.short_name");

        // return json_encode(array('data' => $data));
        return datatables()->of($data)
        ->addColumn('Link', function ($data) {
            if ($data->salary_genarate_type == 2) {
                $edit = '<a href="'.url('/ofpayregister').'/'. encrypt($data->id).'/edit'.'" class="btn btn-sm block btn-flat"><i class="glyphicon glyphicon-edit" id="customer-confrimed"></i>Edit </a>';
            } else {
                $edit = '<a href="'.url('/ofpayregister').'/'. encrypt($data->id).'/edit'.'" class="modalLink btn btn-sm block btn-flat" data-modal-size="xl" data-title="Edit Employee Fringe Benefits Pay Register" modal-center footer-none><i class="glyphicon glyphicon-edit" id="customer-confrimed"></i>Edit </a>';
            }

           return '
           '.$edit.'

            <a href="'.url('/ofpayregister').'/'. encrypt($data->id).'/print'.'" class="btn  btn-sm block btn-flat" target="_blank" ><i class="glyphicon glyphicon-print" id="customer-confrimed"></i>Print</a>';
         })
        ->rawColumns(['Link','amount','amount_type','salary_head','salary_head_amount','Status','group_name'])
        ->make(true);

    }

    public function show($id)
    {

        $id = Crypt::decrypt($id);

        // dd( $id);

        $jasper_server = new Client(
            Config::get('configaration.jasperjasper_url'),
            Config::get('configaration.jasper_user'),
            Config::get('configaration.jasper_password')
        );


        $controls = array(
            'company_name'          => Config::get('configaration.company_name'),
            'address'               => Config::get('configaration.company_address'),
            // 'title'                 => "Daily Attendance(".$status .") || Dated: ".$request->date."($shift_name)"."[$location_name->location_name]",
            'title'                 => "Fringe Benefits Pay Register",
            // 'condition_parameter'   => $parameter,
            'id'   => $id,
        );


        $report_path = Config::get('configaration.report_path').'hrm_payregister';
        $exporttype  = "pdf";
        $report      = $jasper_server->reportService()->runReport($report_path, $exporttype,null,null,$controls);



        if (strlen($report) > 940){
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . strlen($report));
            header('Content-Type: application/'.$exporttype);
            echo $report;
            echo $report;
            echo $report;
            echo $report;
            echo "data:application/pdf;base64, " . $report;
        }else{
            // Session::flash('flash_message', 'No More Data For View');
            dd("No data found");
        }
    }

    public function additional_deduction_add(Request $request)
    {

        $query = DB::SELECT("select a.* from pay_register a join hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND a.year_id=$request->years And a.hrm_month_id=$request->month_names and b.hrm_designation_id=$request->designation");

        // $count_row_addition  = count($query);

        // dd($query);
        // amount_type 1 for %
        // amount_type 2 for tk
        foreach ($query as $key ){
            $insert_salary_details  = new HrmPayRegisterDetails;
            $insert_salary_details->pay_register_id            = $key->id;
            $insert_salary_details->hrm_salary_head_id         = $request->deduction_head;
            $insert_salary_details->amount                     = $request->deduction_amount;
            $insert_salary_details->amount_type                = 2;
            $insert_salary_details->actual_amount              = $request->deduction_amount;
            $insert_salary_details->save();
        }


        // for($r = 0; $r <$count_row_addition; $r++) {


        // $check= $query[$r];

        //     if($check>0){


        //     $insert_salary_details  = new HrmPayRegisterDetails;
        //     $insert_salary_details->pay_register_id            = $insert ->id;
        //     $insert_salary_details->hrm_salary_head_id         = $request->addition_id[$r];
        //     $insert_salary_details->amount                     = $request->amountaddition[$r];
        //     $insert_salary_details->amount_type                = $request->typeaddition[$r];
        //     $insert_salary_details->actual_amount              = $request->addition_actual_amount[$r];
        //     $insert_salary_details->save();

        //   }
        // }






    }

    public function edit(Request $request,$id)
    {//dd('Hello');
        $id = Crypt::decrypt($id);

        $check_data = DB::select("SELECT id FROM pay_register WHERE id = $id AND salary_genarate_type =2");

        if (!empty($check_data)) {
            return back()->with('show-error', 'Sorry you can not edit,This Month Salary Already Generated');
        }

        $data['edit_data'] =DB::SELECT("SELECT
                                a.id,
                                c.employee_name,
                                a.amount as salary_amount,
                                h.id as salary_head_id,
                                h.salary_head,
                                    IF(i.generate_type = 1,
                                    'Addition',
                                    'Deduction') AS Status,
                                i.group_name AS group_name,
                                    g.amount,
                                    g.amount_type,
                                IF(g.amount_type = 1, '%', 'TK') As type ,
                                IF(g.amount_type = 1,
                                    (a.amount * g.amount / 100),
                                    g.amount) AS salary_head_amount,
                                g.actual_amount
                            FROM
                                pay_register a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    -- AND b.employee_activity = 1
                                    AND a.id = $id
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    JOIN
                                hrm_depertment d ON b.hrm_depertment_id = d.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                pay_register_details g ON a.id = g.pay_register_id
                                   Right JOIN
                                hrm_salary_head h ON g.hrm_salary_head_id = h.id
                                    JOIN
                                hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id WHERE i.generate_type=1 AND h.apply_for=2");


        $data['edit_data_deduction'] = DB::SELECT("SELECT
                                a.id,
                                c.employee_name,
                                a.amount as salary_amount,
                                h.id as salary_head_id,
                                h.salary_head,
                                    IF(i.generate_type = 1,
                                    'Addition',
                                    'Deduction') AS Status,
                                i.group_name AS group_name,
                                    g.amount,
                                    g.amount_type,
                                IF(g.amount_type = 1, '%', 'TK') As type ,
                                IF(g.amount_type = 1,
                                    (a.amount * g.amount / 100),
                                    g.amount) AS salary_head_amount,
                                g.actual_amount,
                                a.accounts_code
                            FROM
                                pay_register a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    -- AND b.employee_activity = 1
                                    AND a.id = $id
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    JOIN
                                hrm_depertment d ON b.hrm_depertment_id = d.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id
                                    JOIN
                                hrm_designation f ON b.hrm_designation_id = f.id
                                    JOIN
                                pay_register_details g ON a.id = g.pay_register_id
                                   Right JOIN
                                hrm_salary_head h ON g.hrm_salary_head_id = h.id
                                    JOIN
                                hrm_salary_head_group i ON i.id = h.hrm_salary_head_group_id WHERE i.generate_type=2  AND h.apply_for=2");

        //dd($id);
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
                                g.month_name,
                                b.id as hrm_job_info_id,
                                a.accounts_code,
                                a.payment_mode,
                                h.id as hrm_bank_id,
                                h.bank_name,
                                a.account_no

                            FROM
                                pay_register a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    -- AND b.employee_activity = 1
                                    AND a.id = $id
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
                                hrm_bank h ON h.id = a.hrm_bank_id");

        $data['bank_name'] = HrmBank::all();

        return view('otherfacility_pay_register.edit_other_facility_payregister', $data);
    }

    public function update(Request $request, $id)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'hrm_job_info_id'     => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            $count_row_addition  = count($request->amountaddition);
            $count_row_deduction = count($request->amountdeduction);
            $user_id             = Auth::user()->id;
            $user_name           = Auth::user()->name;

            DB::beginTransaction();
            try{
                $deleted_data_store = new HrmDeletedDataLog;
                $deleted_data_store->user_name       = $user_name;
                $deleted_data_store->delete_from     = 'Payregister FB Data Delete';
                $deleted_data_store->users_id        = $user_id;
                $deleted_data_store->master_table_id = $id;
                $deleted_data_store->save();

                // $find_data = HrmPayRegister::find($id);
                // $find_data->salary_genarate_type =0;
                // $find_data->save();


                $insert = HrmPayRegister::find($id);
                // $insert->year_id                  = $find_data->year_id;
                // $insert->hrm_month_id             = $find_data->hrm_month_id;
                // $insert->hrm_employee_job_info_id = $find_data->hrm_employee_job_info_id;
                $insert->day_of_month             = $request->day_of_month;
                $insert->total_present            = $request->total_present;
                $insert->salary_genarate_type     = 1;
                $insert->users_id                 = Auth::user()->id;
                // $insert->hrm_employee_salary_id   = $find_data->hrm_employee_salary_id;
                $insert->amount                   = $request->salary_amount;
                $insert->apply_for                = 2;
                $insert->payment_mode             = $request->payment_mode;
                $insert->account_no               = $request->account_no;
                // $insert->by_bank_percent          = $find_data->by_bank_percent;
                $insert->hrm_bank_id              = $request->hrm_bank_id;
                // $insert->hrm_salary_generate_master_id  = $find_data->hrm_salary_generate_master_id;
                // $insert->hrm_location_id          = $find_data->hrm_location_id;
                // $insert->note                     = $find_data->note;
                // $insert->accounts_code            = $find_data->accounts_code;
                $insert->save();



                DB::Delete("DELETE FROM pay_register_details WHERE pay_register_id=$id and hrm_salary_head_id in
                    (SELECT id from hrm_salary_head WHERE apply_for=2)");




                for($r = 0; $r <$count_row_addition; $r++) {
                    $check= $request->addition_actual_amount[$r];

                    if($check>0){

                        $insert_salary_details  = new HrmPayRegisterDetails;
                        $insert_salary_details->pay_register_id            = $insert ->id;
                        $insert_salary_details->hrm_salary_head_id         = $request->addition_id[$r];
                        $insert_salary_details->amount                     = $request->amountaddition[$r];
                        $insert_salary_details->amount_type                = $request->typeaddition[$r];
                        $insert_salary_details->actual_amount              = $request->addition_actual_amount[$r];
                        $insert_salary_details->save();

                    }
                }


                for($r = 0; $r <$count_row_deduction; $r++) {

                    $check= $request->deduction_actual_amount[$r];

                    if($check>0){
                        $insert_salary_details  = new HrmPayRegisterDetails;
                        $insert_salary_details->pay_register_id            = $insert->id;
                        $insert_salary_details->hrm_salary_head_id         = $request->deduction_id[$r];
                        $insert_salary_details->amount                     = $request->amountdeduction[$r];
                        $insert_salary_details->amount_type                = $request->typededuction[$r];
                        $insert_salary_details->actual_amount              = $request->deduction_actual_amount[$r];
                        $insert_salary_details->save();

                    }

                }

                DB::commit();

                $status = true;
                $message = 'Other Facility pay register has been updated..!';
            } catch (\Exception $e) {
                DB::rollback();
                $message = $e->getMessage();
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }
}
