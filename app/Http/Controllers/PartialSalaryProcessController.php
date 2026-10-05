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
use Response;
use DateTime;
use Carbon\Carbon;

use App\Models\HrmMonth;
use App\Models\HrmLocation;
use App\Models\HrmPayRegister;
use App\Models\HrmPayRegisterDetails;
use App\Models\HrmLoanPayRegisterDetails;

use App\Models\HrmEmployeeJobInfo;
use App\Models\HrmEmployeeJoining;
use App\Models\HrmEmployeeShift;
use App\Models\HrmReligion;
use App\Models\HrmMaritalStatus;
use App\Models\HrmBloodGroup;
use App\Models\HrmEmployee;
use App\Models\HrmEmployeeTransfer;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmEmployeeSalaryDetails;
use App\Models\HrmSalaryGradeMaster;
use App\Models\HrmSalaryGradeDetails;
use App\Models\HrmEmployeeActivity;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmSalarySlap;
use App\Models\HrmSalaryGenerateMaster;
use App\Models\HrmLoanLedger;



class PartialSalaryProcessController extends Controller
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
    $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");          

         return view('partialsalary_process.partialsalary_process')
               ->with('month',HrmMonth::all())
               ->with('location',HrmLocation::all())
               ->with('default_user_location',  $default_user_location) ;
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

       // dd($request->all());

        $validator = Validator::make($request->all(), [
            'location'              => 'required',
            'year'              => 'required',
            'month_name'        => 'required',
        ]);
        

        if( $validator->fails() ){
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }        


        $declation_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->declation_date)));



        $salary_slap = HrmSalarySlap::find($request->slap_name);
        $date_from   = Carbon::parse($salary_slap->date_from);
        $date_to     = Carbon::parse($salary_slap->date_to);
        $action_days = ($date_to->diffInDays($date_from) + 1);
        $lastDay     = date('t', strtotime($date_from));


        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_salary_generate_master bb ON a.hrm_salary_generate_master_id = bb.id
                                    AND bb.hrm_salary_slap_id = $request->slap_name AND bb.status=1 AND bb.valid=1
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id 
                                    AND bb.hrm_location_id=$request->location  
                                    AND a.hrm_month_id = $request->month_name 
                                    AND a.year_id = $request->year  
                                    AND a.apply_for=1
                                    AND a.salary_genarate_type<>0 LIMIT 1");

        if (!empty($check_data)){
            $validator->errors()->add('field',"This month salary already process");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        if ($request->month_name>9){
            $month = $request->month_name;
        }else{
            $month = '0'.$request->month_name;
        }

        $date_of_month  = $request->year.'-'.$month.'-01';
        $year_month     = $request->year.'-'.$month;
        $users_id       = Auth::user()->id;

        DB::beginTransaction();
        try {

            // $this->GetNewSalaryIncrement($request->month_name,$request->year,$request->location);
           

            $insert     = new HrmSalaryGenerateMaster;
            $insert->hrm_location_id        = $request->location;
            $insert->hrm_month_id           = $request->month_name;
            $insert->year_id                = $request->year; 
            $insert->declaration_date       = $declation_date;
            // $insert->payment_date                      = $date_to;
            $insert->valid                  = 1;
            $insert->hrm_salary_slap_id     = $request->slap_name;
            $insert->users_id               = $users_id;
            $insert->status                 = 1;
            $insert->save();
            $master_id = $insert->id;


            // DB::insert("INSERT INTO pay_register(year_id,hrm_month_id,hrm_employee_job_info_id,
            //             day_of_month,total_present,salary_genarate_type,users_id,hrm_employee_salary_id,amount,apply_for,payment_mode,account_no,by_bank_percent,hrm_bank_id,hrm_salary_generate_master_id)
            //             SELECT $request->year,$request->month_name,a.id,$action_days,
            //             0,1,$users_id,b.id,b.salary_amount,1,b.payment_mode,b.account_no,b.by_bank_percent,b.hrm_bank_id,$master_id
                        
            //             FROM
            //                 hrm_employee_job_info a
            //                     JOIN
            //                 hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location   
            //                 AND a.employee_activity = 1 
            //                     JOIN
            //                 hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.status <> 2");

            DB::insert("INSERT INTO pay_register(year_id,hrm_month_id,hrm_employee_job_info_id,
                        day_of_month,total_present,salary_genarate_type,users_id,hrm_employee_salary_id,amount,apply_for,payment_mode,account_no,by_bank_percent,hrm_bank_id,hrm_salary_generate_master_id,hrm_location_id,accounts_code)
                        SELECT $request->year,
                               $request->month_name,
                               a.id,
                               $action_days,
                               0,1,$users_id,
                               b.id,b.salary_amount,1,b.payment_mode,b.account_no,b.by_bank_percent,b.hrm_bank_id,$master_id,$request->location,b.accounts_code
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location   
                            AND a.id in 

                            (SELECT Max(aa.hrm_employee_job_info_id) as id
                            FROM
                            (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE 
                                '$date_to' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL 
                                AND start_date<='$date_to' AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE 
                                end_date BETWEEN '$date_from'  AND '$date_to'  AND  end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7,5)
                                UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE 
                                         end_date BETWEEN '$date_from' AND '$date_to'  
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7,5)
                                )  aa JOIN hrm_employee_job_info bb ON aa.hrm_employee_job_info_id=bb.id  AND bb.hrm_location_id=$request->location 
                                GROUP BY bb.hrm_employee_id)

                                JOIN
                            hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.status <> 2
                                JOIN
                            hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id AND d.joining_date<'$date_to' ");



            DB::update("UPDATE pay_register
                        JOIN(SELECT 
                            COUNT(*) as total_present,b.id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.employee_activity = 1 AND b.hrm_location_id=$request->location 
                                AND a.punche_date BETWEEN '$date_from' AND '$date_to'
                                AND a.attendance_status in(2,3,4,5,6,7,8,11,10,12,13)
                                GROUP BY b.id) totalpresent
                        ON pay_register.hrm_employee_job_info_id = totalpresent.id 
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.total_present  = totalpresent.total_present
                        WHERE pay_register.apply_for = 1 AND pay_register.hrm_salary_generate_master_id = $master_id");


 // Deduct for late 

        $check_status_value = DB::SELECT("SELECT effect_apply FROM hrm_attendance_status WHERE id = 6 ");
        $check_status=$check_status_value[0]->effect_apply;


        if(!empty($check_status)){

            DB::update("UPDATE pay_register
                        JOIN(SELECT 
                            left((COUNT(*)/$check_status),1) as deduct_late,b.id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.employee_activity = 1 AND b.hrm_location_id=$request->location 
                                AND a.punche_date BETWEEN '$date_from' AND '$date_to'
                                AND a.attendance_status=6
                                GROUP BY b.id HAVING  Round((COUNT(*)/$check_status)) >0) deduct_late
                        ON pay_register.hrm_employee_job_info_id = deduct_late.id 
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        AND pay_register.hrm_salary_generate_master_id = $master_id
                        SET pay_register.total_present  = (pay_register.total_present-deduct_late.deduct_late)
                        WHERE pay_register.apply_for=1");
             
        }


// Deduction For HalfDay Leave

           DB::update("UPDATE pay_register
                                JOIN(SELECT 
                                    (COUNT(*)/2) as deduct_late,b.id
                                FROM
                                    hrm_attendance a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.employee_activity = 1 AND b.hrm_location_id=$request->location 
                                        AND a.punche_date BETWEEN '$date_from' AND '$date_to'
                                        AND a.attendance_status = 10
                                        GROUP BY b.id ) deduct_late
                                ON pay_register.hrm_employee_job_info_id = deduct_late.id 
                                AND pay_register.hrm_month_id   = $request->month_name
                                AND pay_register.year_id        = $request->year
                                AND pay_register.hrm_salary_generate_master_id = $master_id
                                SET pay_register.total_present  = (pay_register.total_present-deduct_late.deduct_late)
                                WHERE pay_register.apply_for = 1");


// Deduction For QuaterDay Leave

           DB::update("UPDATE pay_register
                                JOIN(SELECT 
                                    (COUNT(*)/4) as deduct_late,b.id
                                FROM
                                    hrm_attendance a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.employee_activity = 1 AND b.hrm_location_id=$request->location 
                                        AND a.punche_date BETWEEN '$date_from' AND '$date_to'
                                        AND a.attendance_status = 13
                                        GROUP BY b.id ) deduct_late
                                ON pay_register.hrm_employee_job_info_id = deduct_late.id 
                                AND pay_register.hrm_month_id   = $request->month_name
                                AND pay_register.year_id        = $request->year
                                AND pay_register.hrm_salary_generate_master_id = $master_id
                                SET pay_register.total_present  = (pay_register.total_present- deduct_late.deduct_late)
                                WHERE pay_register.apply_for = 1");



            DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                        SELECT a.id,c.hrm_salary_head_id,c.amount,c.amount_type,Round((c.actual_amount*$action_days/$lastDay),0) as actual_amount
                        FROM pay_register a
                        JOIN 
                        hrm_employee_salary b ON a.hrm_employee_salary_id = b.id  AND a.apply_for = 1
                        AND a.hrm_month_id = $request->month_name
                        AND a.year_id = $request->year
                        AND a.apply_for = 1
                        AND a.hrm_salary_generate_master_id = $master_id
                        JOIN
                        hrm_employee_salary_details c ON b.id = c.hrm_employee_salary_id AND c.actual_amount>0
                        JOIN
                        hrm_salary_head d ON c.hrm_salary_head_id=d.id and d.apply_for=1
                        JOIN 
                        hrm_employee_job_info e ON e.id=a.hrm_employee_job_info_id AND e.hrm_location_id=$request->location");

            // attendance deduction
            DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                        SELECT a.id,3,ROUND((($action_days - a.total_present)*(a.amount/$lastDay))),2,Round((($action_days - a.total_present)*(a.amount/$lastDay)))
                        FROM pay_register a
                        JOIN
                        hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id 
                        AND b.hrm_location_id =  $request->location
                        WHERE a.hrm_month_id = $request->month_name 
                        AND a.year_id = $request->year  
                        AND a.apply_for=1
                        AND a.hrm_salary_generate_master_id = $master_id");


            // loan & advance deduction (Query 100% Right but multiple loan manage korar jonno loop chalano hoase)
            // DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
            // SELECT c.id,e.hrm_salary_head_id,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS amount,2,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS actual_amount
            //             FROM
            //                 hrm_loan_application a
            //                     JOIN
            //                 hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
            //                     AND b.employee_activity = 1
            //                     AND a.approved_status = 2
            //                     AND b.hrm_location_id=  $request->location
            //                     AND '$year_month' >= LEFT(a.installment_start_date,7)
            //                     JOIN
            //                 pay_register c ON b.id = c.hrm_employee_job_info_id
            //                     AND c.hrm_month_id = $request->month_name
            //                     AND c.year_id      = $request->year
            //                     AND c.apply_for = 1
            //                     JOIN
            //                 hrm_loan_ledger d ON a.id = d.hrm_loan_application_id
            //                     JOIN
            //                 hrm_loan_type e ON a.hrm_loan_type_id = e.id  
            //             GROUP BY a.id,c.id,e.hrm_salary_head_id,a.installment_size    
            //             HAVING SUM(d.credit - d.debit)>0");


            $query=DB::SELECT("SELECT a.id as loan_application_id,c.id,e.hrm_salary_head_id,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS amount,2,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS actual_amount
                        FROM
                            hrm_loan_application a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND b.employee_activity = 1
                                AND a.approved_status = 2
                                AND b.hrm_location_id=  $request->location
                                AND '$year_month' >= LEFT(a.installment_start_date,7)
                                JOIN
                            pay_register c ON b.id = c.hrm_employee_job_info_id
                                AND c.hrm_month_id = $request->month_name
                                AND c.year_id      = $request->year
                                AND c.hrm_salary_generate_master_id = $master_id
                                AND c.apply_for = 1
                                JOIN
                            hrm_loan_ledger d ON a.id = d.hrm_loan_application_id
                                JOIN
                            hrm_loan_type e ON a.hrm_loan_type_id = e.id  
                        GROUP BY a.id,c.id,e.hrm_salary_head_id,a.installment_size    
                        HAVING SUM(d.credit - d.debit)>0");



                foreach($query as $result) {

                    // $insert  = new HrmPayRegisterDetails;
                    // $insert->pay_register_id            = $query[$r]->id; 
                    // $insert->hrm_salary_head_id         = $query[$r]->hrm_salary_head_id;
                    // $insert->amount                     = $query[$r]->amount;
                    // $insert->amount_type                = 2;
                    // $insert->actual_amount              = $query[$r]->actual_amount;
                    // $insert->save();

                    $insert  = new HrmPayRegisterDetails;
                    $insert->pay_register_id            = $result->id; 
                    $insert->hrm_salary_head_id         = $result->hrm_salary_head_id;
                    $insert->amount                     = $result->amount;
                    $insert->amount_type                = 2;
                    $insert->actual_amount              = Round($result->actual_amount*($action_days/$lastDay));
                    $insert->save();

                    $loan_ledger  = new HrmLoanLedger;
                    $loan_ledger->hrm_loan_application_id   = $result->loan_application_id;
                    $loan_ledger->debit                     = Round($result->amount*($action_days/$lastDay));
                    $loan_ledger->credit                    = 0;
                    $loan_ledger->hrm_month_id              = $request->month_name;
                    $loan_ledger->year_id                   = $request->year;
                    $loan_ledger->valid                     = 1;
                    $loan_ledger->narration                 = 'Loan Adjustment';
                    $loan_ledger->interest_amount           = 0;
                    $loan_ledger->users_id                  = $users_id;
                    $loan_ledger->entry_status              = 1;
                    $loan_ledger->save();                    

                    $insert_tag  = new HrmLoanPayRegisterDetails;
                    $insert_tag->hrm_loan_application_id     = $result->loan_application_id; 
                    $insert_tag->pay_register_details_id     = $insert->id;
                    $insert_tag->hrm_loan_ledger_id          = $loan_ledger->id;
                    $insert_tag->save();

             }

             // $count_row  = count($query);

             // for($r = 0; $r <$count_row; $r++) {

             //        $insert  = new HrmPayRegisterDetails;
             //        $insert->pay_register_id            = $query[$r]->id; 
             //        $insert->hrm_salary_head_id         = $query[$r]->hrm_salary_head_id;
             //        $insert->amount                     = $query[$r]->amount;
             //        $insert->amount_type                = 2;
             //        $insert->actual_amount              = $query[$r]->actual_amount*($action_days/$lastDay);
             //        $insert->save();


             //        $insert_tag  = new HrmLoanPayRegisterDetails;
             //        $insert_tag->hrm_loan_application_id     = $query[$r] ->loan_application_id; 
             //        $insert_tag->pay_register_details_id     = $insert->id;
             //        $insert_tag->save();

             // }
         

            // loan & advance deduction
            // DB::insert("INSERT INTO hrm_loan_ledger(hrm_loan_application_id,debit,credit,hrm_month_id,year_id,valid,narration,users_id)
            //             SELECT a.id,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size*($action_days/$lastDay),SUM(d.credit - d.debit)*($action_days/$lastDay))) AS debit,0,$request->month_name,$request->year,1,'Loan Adjustment',$users_id
            //             FROM
            //                 hrm_loan_application a
            //                     JOIN
            //                 hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
            //                     AND b.employee_activity = 1
            //                     AND a.approved_status = 2
            //                     AND b.hrm_location_id=  $request->location
            //                     AND '$year_month' >= LEFT(a.installment_start_date,7)
            //                     JOIN
            //                 pay_register c ON b.id = c.hrm_employee_job_info_id
            //                     AND c.hrm_month_id = $request->month_name
            //                     AND c.year_id      = $request->year
            //                     AND c.apply_for = 1                            
            //                     JOIN
            //                 hrm_loan_ledger d ON a.id = d.hrm_loan_application_id 
            //                     JOIN
            //                 hrm_loan_type e ON a.hrm_loan_type_id = e.id  
            //             GROUP BY a.id,a.installment_size
            //             HAVING SUM(d.credit - d.debit)>0");


            // Extra Feature Addition Or Deduction
            DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                        SELECT
                            e.id, a.hrm_salary_head_id, b.amount*($action_days/$lastDay), 1, Round(b.amount*($action_days/$lastDay))
                        FROM
                            hrm_salary_extra_feature a
                                JOIN
                            hrm_salary_extra_feature_details b ON a.id = b.hrm_salary_extra_feature_id
                                AND '$request->month_name' BETWEEN MONTH(a.month_from) AND MONTH(a.month_to)
                                AND '$request->year' BETWEEN YEAR(a.month_from) AND YEAR(a.month_to)
                                JOIN
                            hrm_employee_job_info c ON b.hrm_employee_job_info_id = c.id
                                JOIN
                            hrm_location d ON c.hrm_location_id = d.id
                                JOIN
                            pay_register e ON e.hrm_employee_job_info_id = b.hrm_employee_job_info_id
                                AND e.hrm_month_id = $request->month_name
                                AND e.year_id = $request->year
                                AND e.apply_for = 1");


        DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));         
        }        


        return response::json(array(
            'success'    => true,
            'messages'   => 'successfully process'
        )); 

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



    public function createsalaryslap(Request $request)
    {
        // dd($request->all());
      
        $validator = Validator::make($request->all(), [
            'year'              => 'required',
            'month_name'        => 'required',
            'salp_name'         => 'required',
            'date_from'         => 'required',
            'date_to'           => 'required',
        ]);

        $date_from   = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to     = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

        $check_date = DB::SELECT("SELECT id FROM hrm_salary_slap WHERE '$date_from' BETWEEN date_from AND date_to  ");
        $check_dates = DB::SELECT("SELECT id FROM hrm_salary_slap WHERE '$date_to' BETWEEN date_from AND date_to  ");

        if(!empty($check_date)){
            $request->session()->flash('alert-danger', 'Sorry This Date Range Already Exist!');        
            return Redirect::to('partialsalaryprocess');  

        }
        if(!empty($check_dates)){
            $request->session()->flash('alert-danger', 'Sorry This Date Range Already Exist!');        
            return Redirect::to('partialsalaryprocess');  
            
        }

        $insert = new HrmSalarySlap;
        $insert->slap_name      = $request->salp_name;
        $insert->date_from      = $date_from;
        $insert->date_to        = $date_to;
        $insert->year_id        = $request->year;
        $insert->hrm_month_id   = $request->month_name;
        $insert->users_id       = Auth::user()->id;

        $insert->save();

        $request->session()->flash('alert-success', 'data has been successfully added!');        
        return Redirect::to('partialsalaryprocess');  



    }


  public function deletepartialsalaryprocess(Request $request)
    {

// dd($request->all());

        $find_data = HrmSalaryGenerateMaster::find($request->hrm_salary_generate_master_id);

        $validator = Validator::make($request->all(), [
            'location'    => 'required',
            'year'        => 'required',
            'month_name'  => 'required',
            'hrm_salary_generate_master_id'   => 'required',

        ]);

    
        
        if (empty($find_data)){
            session()->flash('alert-danger', 'Invalid Request !!');        
            return Redirect()->back();              
        }
   
    
        $year_id  = $find_data->year_id;
        $month_id = $find_data->hrm_month_id;    
        $location_id = $find_data->hrm_location_id; 
        $master_id = $find_data->id;
        $hrm_salary_slap_id = $find_data->hrm_salary_slap_id;



        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id 
                                    AND b.hrm_location_id=$location_id 
                                    AND a.hrm_month_id = $month_id 
                                    AND a.year_id = $year_id  
                                    AND a.apply_for=1
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id = $hrm_salary_slap_id
                                    AND c.id=$master_id 
                                    AND a.salary_genarate_type=2  LIMIT 1");
 

        if (!empty($check_data)){

            $request->session()->flash('alert-danger', 'Sorry This month salary already Generated !');        
            return Redirect::to('salaryprocess'); 
        }

 


        $loan_ledger_with_payregisterdetails_delete =  DB::delete("DELETE FROM hrm_loan_payregister_details WHERE pay_register_details_id IN (
                                                        SELECT implicitTemp.id FROM 
                                                        (SELECT id FROM  pay_register_details WHERE pay_register_id in(SELECT a.id FROM pay_register a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND a.apply_for=1 AND b.hrm_location_id=$location_id    WHERE a.year_id =$year_id AND a.hrm_month_id = $month_id AND a.hrm_salary_generate_master_id =$master_id  )) implicitTemp)");


        $loan_ledger_delete =  DB::delete("DELETE FROM hrm_loan_ledger WHERE id IN (
                                                SELECT implicitTemp.id FROM 
                                                (SELECT a.id FROM hrm_loan_ledger a
                                                    JOIN hrm_loan_application b ON a.hrm_loan_application_id = b.id  AND a.entry_status = 1
                                                    JOIN hrm_employee_job_info c ON b.hrm_employee_id= c.hrm_employee_id 
                                                    AND c.employee_activity=1 AND c.hrm_location_id=$location_id     
                                                    AND a.year_id = $year_id AND a.hrm_month_id = $month_id

                                                    ) implicitTemp)");
        //payregister master er shathe connection korte hobe




        $details_delete = DB::delete("DELETE FROM  pay_register_details WHERE pay_register_id in(SELECT a.id FROM pay_register a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND a.apply_for=1 AND b.hrm_location_id=$location_id    WHERE a.year_id =$year_id AND a.hrm_month_id = $month_id AND a.hrm_salary_generate_master_id =$master_id)");
   
 
        $master_delete =  DB::delete("DELETE FROM pay_register WHERE id IN (
                                            SELECT implicitTemp.id FROM 
                                            (SELECT a.id FROM pay_register a 
                                                JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id 
                                                AND a.apply_for=1 AND b.hrm_location_id=$location_id    
                                                AND a.year_id =$year_id AND a.hrm_month_id = $month_id
                                            AND a.hrm_salary_generate_master_id =$master_id) implicitTemp)");

       
       //DB::table('hrm_salary_generate_master')
       //->where('id', $master_id)
       //->update(['valid' => 0]);
       
       
        $findUpdate = HrmSalaryGenerateMaster::find($master_id);
        $findUpdate->valid     = 0;
        $findUpdate->users_id  = Auth::user()->id;;
        $findUpdate->save();



        if (!empty($master_delete)){
                  $request->session()->flash('alert-success', 'successfully deleted !');        
                  return Redirect::to('salaryprocess');   
        }

       if (empty($master_delete)){
                  $request->session()->flash('alert-danger', 'This Month Salary Yet Not Process!');        
                  return Redirect::to('salaryprocess');  
        }



    }


}
