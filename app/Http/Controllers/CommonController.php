<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmEmployeeSalaryDetails;
use App\Models\HrmFringeBenefitsConfig;
use App\Models\HrmSalaryGradeMaster;
use App\Models\HrmSetPFConfiq;
use App\Models\HrmSetDefaultHead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    function insert_salary_config ($hrm_employee_job_info_id,$hrm_salary_grade_master_id,$gross_salary,$hrm_designation_id,$old_hrm_employee_job_info_id){




        if($old_hrm_employee_job_info_id != 0){

            $oldsalary  = DB::SELECT("SELECT * FROM hrm_employee_salary WHERE hrm_employee_job_info_id = $old_hrm_employee_job_info_id ");


            $payment_mode       = $oldsalary[0]->payment_mode;
            $account_no         = $oldsalary[0]->account_no;
            $hrm_bank_id        = $oldsalary[0]->hrm_bank_id;
            $accounts_code      = $oldsalary[0]->accounts_code;
            $by_bank_percent    = $oldsalary[0]->by_bank_percent;
            $payment_mode_fb    = $oldsalary[0]->payment_mode_fb;
            $account_no_fb      = $oldsalary[0]->account_no_fb;
            $hrm_bank_id_fb     = $oldsalary[0]->hrm_bank_id_fb;

        }else{

            $payment_mode       = 1;
            $account_no         = '';
            $hrm_bank_id        = null;
            $accounts_code      = '';
            $by_bank_percent    = 0;
            $payment_mode_fb    = 1;
            $account_no_fb      = '';
            $hrm_bank_id_fb     = null;


        }


        $insert_salary_master  = new HrmEmployeeSalary;
        $insert_salary_master->hrm_employee_job_info_id   = $hrm_employee_job_info_id;
        $insert_salary_master->hrm_salary_grade_master_id = $hrm_salary_grade_master_id;
        $insert_salary_master->salary_amount              = $gross_salary;
        $insert_salary_master->users_id                   = Auth::user()->id;
        $insert_salary_master->payment_mode               = $payment_mode;
        $insert_salary_master->account_no                 = $account_no;
        $insert_salary_master->hrm_bank_id                = $hrm_bank_id;
        $insert_salary_master->accounts_code              = $accounts_code;
        $insert_salary_master->by_bank_percent            = $by_bank_percent;
        $insert_salary_master->payment_mode_fb            = $payment_mode_fb;
        $insert_salary_master->account_no_fb              = $account_no_fb;
        $insert_salary_master->hrm_bank_id_fb             = $hrm_bank_id_fb;
        $insert_salary_master->save();

        // dd($insert_salary_master);

        $salary_grade_details = DB::SELECT("SELECT * FROM hrm_salary_grade_details WHERE hrm_salary_head_id IN (SELECT id FROM hrm_salary_head WHERE apply_for=1) AND  hrm_salary_grade_master_id=$hrm_salary_grade_master_id");

        $count_row            = count($salary_grade_details);

        $basicSalaryHead = HrmSetDefaultHead::where('hrm_default_head_id', 1)->first();
        if (!$basicSalaryHead) {
            throw new \Exception('Basic Salary Head is not configured. Please configure the default Basic Salary Head from Salary Settings before creating an employee.');
        }
        $getBasicSalaryHead = $basicSalaryHead->hrm_salary_head_id;

        for($r = 0; $r <$count_row; $r++) {

           $amount             = $salary_grade_details[$r]->amount;
           $amount_type        = $salary_grade_details[$r]->amount_type;
           $hrm_salary_head_id = $salary_grade_details[$r]->hrm_salary_head_id;



           if($amount_type==2){
               $actual_amount = $amount;
           }else{

               $actual_amount = floatval($gross_salary) * floatval(($amount / 100));
           }

           if($getBasicSalaryHead==$hrm_salary_head_id){
                $basic_salary_amount = $actual_amount;
           }



                $group_status_data = DB::SELECT("SELECT b.group_status
                                             FROM hrm_salary_head a
                                             JOIN hrm_salary_head_group b
                                             ON a.hrm_salary_head_group_id = b.id
                                             WHERE a.id = $hrm_salary_head_id");

                $salary_group_status = $group_status_data[0]->group_status;


                if($salary_group_status==4){

                    $getConfig       = HrmSetPFConfiq::where('date_to')->first();
                    $ownContribution = $getConfig->amount_own;

                    if($salary_grade_details[$r]->amount>0){
                        $actual_amount       =($basic_salary_amount*$ownContribution/100);
                    }else{
                        $actual_amount = 0;
                    }

                }

            //END provident fund amount calculation basic er 10%

            $insert_salary_details  = new HrmEmployeeSalaryDetails;
            $insert_salary_details->hrm_employee_salary_id     = $insert_salary_master->id;
            $insert_salary_details->hrm_salary_head_id         = $salary_grade_details[$r]->hrm_salary_head_id;
            $insert_salary_details->amount                     = $salary_grade_details[$r]->amount;
            $insert_salary_details->amount_type                = $salary_grade_details[$r]->amount_type;
            $insert_salary_details->actual_amount              = $actual_amount;
            $insert_salary_details->save();
        }


        $is_apply_fb = HrmFringeBenefitsConfig::where('hrm_designation_id', $hrm_designation_id)->first();


        if (!empty($is_apply_fb)){


                    $fb_details = DB::SELECT("SELECT * FROM hrm_fringe_benefits_config_details
                                  WHERE hrm_fringe_benefits_config_id = $is_apply_fb->id");
                    $count_row            = count($fb_details);

                    for($r = 0; $r <$count_row; $r++) {

                        $insert_fb_details  = new HrmEmployeeSalaryDetails;
                        $insert_fb_details->hrm_employee_salary_id     = $insert_salary_master->id;
                        $insert_fb_details->hrm_salary_head_id         = $fb_details[$r]->hrm_salary_head_id;
                        $insert_fb_details->amount                     = $fb_details[$r]->amount;
                        $insert_fb_details->amount_type                = $fb_details[$r]->amount_type;
                        $insert_fb_details->actual_amount              = $fb_details[$r]->amount;
                        $insert_fb_details->save();

                    }


        }

    }

    function modify_insert_SalaryConfig ($request){

        $hrm_employee_job_info_id = $request->hrm_employee_job_info_id;
        $hrm_salary_grade_master_id = HrmSalaryGradeMaster::where('hrm_salary_grade_id', $request->salary_grade)->first()->id;
        $gross_salary = $request->new_salary;
        $hrm_designation_id = $request->hrm_designation_id;
        $old_hrm_employee_job_info_id = $request->old_hrm_employee_job_info_id ?? null;
        // dd($old_hrm_employee_job_info_id, $hrm_salary_grade_master_id, $gross_salary, $hrm_designation_id, $old_hrm_employee_job_info_id);
        $oldsalary = [];
        if($old_hrm_employee_job_info_id){

            $oldsalary = DB::SELECT("SELECT * FROM hrm_employee_salary WHERE hrm_employee_job_info_id = $old_hrm_employee_job_info_id")[0] ?? null;
        }

        if($old_hrm_employee_job_info_id != null && !empty($oldsalary)){


            $payment_mode       = $oldsalary[0]->payment_mode;
            $account_no         = $oldsalary[0]->account_no;
            $hrm_bank_id        = $oldsalary[0]->hrm_bank_id;
            $accounts_code      = $oldsalary[0]->accounts_code;
            $by_bank_percent    = $oldsalary[0]->by_bank_percent;
            $payment_mode_fb    = $oldsalary[0]->payment_mode_fb;
            $account_no_fb      = $oldsalary[0]->account_no_fb;
            $hrm_bank_id_fb     = $oldsalary[0]->hrm_bank_id_fb;

        }else{

            $payment_mode       = $request->payment_mode;
            $account_no         = $request->account_no ?? null;
            $hrm_bank_id        = $request->hrm_bank_id ?? null;
            $accounts_code      = '';
            $by_bank_percent    = $request->by_bank_percent ?? 0;
            $payment_mode_fb    = 1;
            $account_no_fb      = '';
            $hrm_bank_id_fb     = null;


        }


        $insert_salary_master  = new HrmEmployeeSalary;
        $insert_salary_master->hrm_employee_job_info_id   = $hrm_employee_job_info_id;
        $insert_salary_master->hrm_salary_grade_master_id = $hrm_salary_grade_master_id;
        $insert_salary_master->salary_amount              = $gross_salary;
        $insert_salary_master->users_id                   = Auth::user()->id;
        $insert_salary_master->payment_mode               = $payment_mode;
        $insert_salary_master->account_no                 = $account_no;
        $insert_salary_master->hrm_bank_id                = $hrm_bank_id;
        $insert_salary_master->accounts_code              = $accounts_code;
        $insert_salary_master->by_bank_percent            = $by_bank_percent;
        $insert_salary_master->payment_mode_fb            = $payment_mode_fb;
        $insert_salary_master->account_no_fb              = $account_no_fb;
        $insert_salary_master->hrm_bank_id_fb             = $hrm_bank_id_fb;
        $insert_salary_master->save();



        $basic_salary_amount = 0;
        // dd($request->salary_head_id);
        foreach ($request->salary_head_id as $salary_head_id) {

            if ($request->has("amount.$salary_head_id") && $request->amount[$salary_head_id] !== null) {
                $amount      = $request->amount[$salary_head_id];
                $amount_type = $request->amount_type[$salary_head_id];
                if ($amount_type == 2) {
                    $actual_amount = $amount;
                } else {
                    $actual_amount = floatval($gross_salary) * floatval(($amount / 100));
                }



                $group_status_data = DB::SELECT("SELECT b.group_status
                                             FROM hrm_salary_head a
                                             JOIN hrm_salary_head_group b
                                             ON a.hrm_salary_head_group_id = b.id
                                             WHERE a.id = $salary_head_id");

                $salary_group_status = $group_status_data[0]->group_status ?? null;


                if ($salary_group_status == 4) {
                    $getConfig = HrmSetPFConfiq::where('date_to', '=', null)->first();
                    $ownContribution = $getConfig->amount_own;
                    //  dd($getConfig);

                    if ($request->amount[$salary_head_id] > 0) {
                        $actual_amount = ($basic_salary_amount * $ownContribution / 100);
                    } else {
                        $actual_amount = 0;
                    }

                }

                $insert_salary_details  = new HrmEmployeeSalaryDetails;
                $insert_salary_details->hrm_employee_salary_id     = $insert_salary_master->id;
                $insert_salary_details->hrm_salary_head_id         = $salary_head_id;
                $insert_salary_details->amount                     = $request->amount[$salary_head_id] ?? null;
                $insert_salary_details->amount_type                = $request->amount_type[$salary_head_id];
                $insert_salary_details->actual_amount              = $actual_amount;
                $insert_salary_details->save();
            }
        }

        $is_apply_fb = HrmFringeBenefitsConfig::where('hrm_designation_id', $hrm_designation_id)->first();


        if (!empty($is_apply_fb)){

            $fb_details = DB::SELECT("SELECT * FROM hrm_fringe_benefits_config_details
                            WHERE hrm_fringe_benefits_config_id = $is_apply_fb->id");
            $count_row            = count($fb_details);

            for($r = 0; $r <$count_row; $r++) {
                $insert_fb_details  = new HrmEmployeeSalaryDetails;
                $insert_fb_details->hrm_employee_salary_id     = $insert_salary_master->id;
                $insert_fb_details->hrm_salary_head_id         = $fb_details[$r]->hrm_salary_head_id;
                $insert_fb_details->amount                     = $fb_details[$r]->amount;
                $insert_fb_details->amount_type                = $fb_details[$r]->amount_type;
                $insert_fb_details->actual_amount              = $fb_details[$r]->amount;
                $insert_fb_details->save();
            }
        }

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
}
