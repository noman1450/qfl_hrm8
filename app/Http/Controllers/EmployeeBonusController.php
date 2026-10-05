<?php

namespace App\Http\Controllers;

use Auth;
use Crypt;
use Config;
use Session;
use App\User;
use Redirect;
use Response;
use Datatables;
use App\Models\HrmBank;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeBonus;



use App\Models\HrmEmployeeBonusCW;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeBonusMaster;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class EmployeeBonusController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        return view('employee_bonus.bonus_process_list');
    }

    public function employeebonus()
    {
        return view('employee_bonus.employee_bonus_list');
    }

    public function add_employee_to_bonus()
    {
        return view('employee_bonus.add_employee_to_bonus');
    }

    public function add_employee_to_bonus_submit(Request $request)
    {
        $success = false;

        $validator = Validator::make($request->all(), [
            'bonus_name' => 'required|integer',
            'hrm_employee_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = collect($validator->errors())->flatten()->implode(', ');
            $error_code = 422;
        } else {
            DB::beginTransaction();

            try {
                $info = HrmEmployeeBonusMaster::query()->find($request->bonus_name);

            $users_id = auth()->id();

            DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id, users_id, hrm_employee_bonus_master_id, is_valid, salary_amount, amount, amount_type, payment_mode, account_no, hrm_bank_id, bonus_amount, basic_salary)
            SELECT a.id, $users_id,$request->bonus_name, 1, b.salary_amount, $info->amount, '$info->amount_type', b.payment_mode, b.account_no, b.hrm_bank_id, IF('$info->amount_type'='%',b.salary_amount*($info->amount/100),$info->amount), b.salary_amount*($info->basic_salary_of_gross/100)
            FROM
                hrm_employee_job_info a
                    JOIN
                hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id
                AND a.employee_activity = 1 AND a.hrm_employee_id = $request->hrm_employee_id
                    JOIN
                hrm_employment_status c ON a.hrm_employment_status_id = c.id
                    JOIN
                hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id");

                DB::commit();

                $this->recordActivity(
                     1,
                     'Created Employee Bonus',
                     $info,
                     $info->id,
                     'hrm_employee_bonus_master'
                );

                $success = true;
                $message = 'Data has been saved.!';
                $error_code = 200;
            } catch (\Exception $e) {
                DB::rollback();
                $error = $e->getMessage();
                $message = collect($e->getMessage())->flatten()->implode(', ');
                $error_code = 500;
            }
        }

        return response()->json([
            'status' => $success,
            'message' => $message ?? '',
            'error' => $error ?? '',
            'error_code' => $error_code ?? ''
        ]);
    }

    public function bonusgenerate()
    {
        return view('employee_bonus.bonus_generate');
    }


    public function bonusprocess_list(Request $request)
    {
        // dd($request->all());

        $condition = '';

        if($request->apply_daterange=="true"){


            $datefrom    = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
            $dateto      = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

            $condition = " and a.declaration_date between  '$datefrom' AND '$dateto' ";
        }

        if($request->pending_status!=3){
            $condition = $condition.' and a.status ='.$request->pending_status;
        }


        if(!empty($request->declaration_date)){
            $condition = $condition." AND a.declaration_date= '$request->declaration_date'";
        }

      


        $data= DB::SELECT("SELECT
                                a.declaration_date,
                                CONCAT(a.amount) as amount_type,
                                a.payment_date,
                                DATE_FORMAT(a.based_on_month,'%M-%Y') as based_on_month ,
                                b.bonus_name,
                                a.payment_term,
                                a.note,
                                c.location_name,
                                IF(a.status = 1, 'Pending', 'Generated') AS status,
                                a.eligible_employee_type,
                                IF(a.apply_for = 1, 'General', 'Casual Worker') AS apply_for,
                                SUM(bb.bonus_amount) as bonus_amount
                            FROM
                                hrm_employee_bonus_master a
                                    JOIN
                                hrm_bonus b ON a.hrm_bonus_id = b.id AND a.is_valid = 1
                                    JOIN
                                hrm_employee_bonus bb ON a.id =  bb.hrm_employee_bonus_master_id
                                AND bb.is_valid=1
                                    JOIN
                                hrm_location c ON a.hrm_location_id = c.id
                                    $condition
                                Group By a.id");



        return json_encode(array('data' => $data));

    }








  public function bonusgeneratesubmit(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'hrm_employee_bonus_master_id'    => 'required',
        ]);


        if( $validator->fails() ){
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        $check_data = DB::select("SELECT id FROM hrm_employee_bonus_master  WHERE id=$request->hrm_employee_bonus_master_id and is_valid=1 AND status=2 ");

        if (!empty($check_data)){

            return response::json(array(
                'success'    => false,
                'messages'   => 'Sorry , Already Generated !'
            ));
        }


        $payment_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->payment_date)));


       DB::beginTransaction();
        try {



            $insert     = HrmEmployeeBonusMaster::find($request->hrm_employee_bonus_master_id);
            $insert->payment_date = $payment_date;
            $insert->status           = 2;
            $insert->users_id         = Auth::user()->id;
            $insert->note             = $request->note;
            $insert->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 'Generate Employee Bonus ',
                 null,
                 $request->hrm_employee_bonus_master_id,
                 'hrm_employee_bonus_master'
            );

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
            'messages'   => 'Successfully Generated'
        ));
    }



    public function bonuslistdata(Request $request)
    {

        $condition = '';

        if(!empty($request->bonus_name)){$condition = ' AND aa.id='.$request->bonus_name;}else{ $condition = ' AND aa.id=0';}

        if(!empty($request->department)){
            $condition = $condition.' AND b.hrm_depertment_id='.$request->department;
        }

        if(!empty($request->section)){
             $condition = $condition.' AND b.hrm_section_id='.$request->section;
        }

        if(!empty($request->section)){
             $condition = $condition.' AND b.hrm_category_id ='.$request->category;
        }

        // if (count($request->employee_status) > 0){
        if (is_array($request->employee_status) && count($request->employee_status) > 0) {
           
            $employee_status = implode(',', $request->employee_status);
            $condition  =  $condition . " AND b.hrm_employment_status_id IN ($employee_status) " ;
        }


         $data   = DB::select("SELECT
                                    a.id,
                                    concat(c.employee_name,' | ',b.employee_code) as employee_name,
                                    c.id AS employee_id,
                                    d.depertment_name,
                                    f.designation_name,
                                    e.location_name,
                                    a.bonus_amount,
                                    concat(a.amount,' ',a.amount_type) as amount_type,
                                    a.salary_amount,
                                    IF(a.payment_mode = 1, 'Cash', g.short_name) AS payment_mode,
                                    a.account_no,
                                    f.priority
                                FROM
                                    hrm_employee_bonus_master aa
                                        JOIN
                                    hrm_employee_bonus a ON aa.id=a.hrm_employee_bonus_master_id AND aa.is_valid=1
                                     and a.is_valid=1
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                    $condition
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_location e ON b.hrm_location_id = e.id
                                        JOIN
                                    hrm_designation f ON b.hrm_designation_id = f.id
                                        LEFT JOIN
                                    hrm_bank g ON a.hrm_bank_id = g.id

                                ");


        return json_encode(array('data' => $data));
    }







    public function create()
    {
        return view('employee_bonus.create_employee_bonus');
    }


    public function bonusprocesscw()
    {
        return view('employee_bonus.create_employee_bonus_cw');
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'amount'        => 'required',
            'bonus_name'        => 'required',
            'payment_term'        => 'required',
            'location_name'        => 'required',
            'basic_salary_of_gross'        => 'required',
        ]);


        if( $validator->fails() ){
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        // $check_data = DB::select("SELECT a.id FROM hrm_employee_bonus a JOIN  hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id WHERE a.hrm_bonus_id = $request->bonus_name AND b.hrm_location_id=$request->location_name LIMIT 1");


        $declaration_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->declaration_date)));
        $based_on_month    = date('Y-m-d', strtotime(str_replace('/', '-', $request->basedMonth)));
        $eligible_employee_type = implode(',',  $request->employeestatus);
        $lastdate          = date("Y-m-t", strtotime($based_on_month));
        $users_id          = Auth::user()->id;
        $hrm_location_id   = $request->location_name;


        $check_data = DB::select("SELECT id FROM hrm_employee_bonus_master  WHERE declaration_date='$declaration_date' AND  hrm_bonus_id = $request->bonus_name AND hrm_location_id=$request->location_name And is_valid=1 AND status in (1,2) and apply_for=1 ");



        if (!empty($check_data)){
            // $validator->errors()->add('field',"This Bonus Already Processed");
            // return Response::json(array(
            //     'success'   => false,
            //     'errors'    => $validator->getMessageBag()->toArray()
            // ));
            return response::json(array(
                'success'    => false,
                'error_messages'    => true,
                'messages'   => 'This Bonus Already Processed'
            ));

        }





     DB::beginTransaction();
        try {


            $insert     = new HrmEmployeeBonusMaster;
            $insert->declaration_date = $declaration_date;
            $insert->based_on_month   = $based_on_month;
            $insert->amount           = $request->amount;
            $insert->amount_type      = $request->amount_type;
            $insert->is_valid         = 1;
            $insert->status           = 1;
            $insert->eligible_employee_type = $eligible_employee_type;
            $insert->users_id         = Auth::user()->id;
            $insert->hrm_bonus_id     = $request->bonus_name;
            $insert->payment_term     = $request->payment_term;
            $insert->hrm_location_id  = $request->location_name;
            $insert->basic_salary_of_gross = $request->basic_salary_of_gross;
            $insert->apply_for  = 1;
            $insert->save();



         // IF('$request->amount_type'='%',a.basic_salary*($request->amount/100),$request->amount)

            if ($request->payment_term=='Payment Method As Per Salary'){

                DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id,users_id,hrm_employee_bonus_master_id,is_valid,salary_amount,amount,amount_type,payment_mode,account_no,hrm_bank_id,bonus_amount,basic_salary)
                        SELECT a.id,$users_id,$insert->id,1,b.salary_amount,$request->amount,'$request->amount_type',b.payment_mode,b.account_no,b.hrm_bank_id,IF('$request->amount_type'='%',b.salary_amount*($request->amount/100),$request->amount),b.salary_amount*($request->basic_salary_of_gross/100)
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location_name
                            AND a.id in (

                            SELECT MAX(bbb.id) as id FROM(
                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                end_date BETWEEN '$based_on_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$based_on_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7) ) aaa
                                        JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                         AND bbb.hrm_location_id = $hrm_location_id
                                         group by bbb.hrm_employee_id
                                )
                                JOIN
                            hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id in ($eligible_employee_type)
                                JOIN
                            hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id AND d.joining_date<'$lastdate' ");



            }else{

                DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id,users_id,hrm_employee_bonus_master_id,is_valid,salary_amount,amount,amount_type,payment_mode,account_no,hrm_bank_id,bonus_amount,basic_salary)
                        SELECT a.id,$users_id,$insert->id,1,b.salary_amount,$request->amount,'$request->amount_type',1,'',0,IF('$request->amount_type'='%',b.salary_amount*($request->amount/100),$request->amount),b.salary_amount*($request->basic_salary_of_gross/100)
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location_name
                            AND a.id in (

                            SELECT MAX(bbb.id) as id FROM(
                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                end_date BETWEEN '$based_on_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$based_on_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7) ) aaa
                                        JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                         AND bbb.hrm_location_id = $hrm_location_id
                                         group by bbb.hrm_employee_id
                                )
                                JOIN
                            hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id in ($eligible_employee_type)
                                JOIN
                            hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id AND d.joining_date<'$lastdate' ");


            }


         DB::commit();

         $this->recordActivity(
             1,
             'Created Employee Bonus Process',
             $insert,
             $insert->id,
             'hrm_employee_bonus_master'
        );

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












    public function bonusprocessforcw(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'declaration_date'=> 'required',
            'location_name'   => 'required',
            'bonus_name'      => 'required',
            'basedMonth'      => 'required',
            'rate'            => 'required',
            'days'            => 'required',
            'total_amount'    => 'required',
            'zero_to_six_months'  => 'required',
            'six_to_twelve_months'  => 'required',
            'more_than_one_year'  => 'required',
            'payment_term'    => 'required',
            'basic_salary_of_gross'    => 'required',


        ]);


        if( $validator->fails() ){
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        // $check_data = DB::select("SELECT a.id FROM hrm_employee_bonus a JOIN  hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id WHERE a.hrm_bonus_id = $request->bonus_name AND b.hrm_location_id=$request->location_name LIMIT 1");


        $declaration_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->declaration_date)));
        $based_on_month    = date('Y-m-d', strtotime(str_replace('/', '-', $request->basedMonth)));
        $eligible_employee_type = implode(',',  $request->employeestatus);
        $lastdate          = date("Y-m-t", strtotime($based_on_month));
        $users_id          = Auth::user()->id;
        $hrm_location_id   = $request->location_name;



        $check_data = DB::select("SELECT id FROM hrm_employee_bonus_master  WHERE declaration_date='$declaration_date' AND  hrm_bonus_id = $request->bonus_name AND hrm_location_id=$request->location_name And is_valid=1 AND status in (1,2) and apply_for=2 ");



        if (!empty($check_data)){
            // $validator->errors()->add('field',"This Bonus Already Processed");
            // return Response::json(array(
            //     'success'   => false,
            //     'errors'    => $validator->getMessageBag()->toArray()
            // ));
            return response::json(array(
                'success'    => false,
                'error_messages'    => true,
                'messages'   => 'This Bonus Already Processed'
            ));

        }





     DB::beginTransaction();
        try {


            $insert     = new HrmEmployeeBonusMaster;
            $insert->declaration_date = $declaration_date;
            $insert->based_on_month   = $based_on_month;
            $insert->amount           = $request->total_amount;
            $insert->amount_type      = '%';
            $insert->is_valid         = 1;
            $insert->status           = 1;
            $insert->eligible_employee_type = $eligible_employee_type;
            $insert->users_id         = Auth::user()->id;
            $insert->hrm_bonus_id     = $request->bonus_name;
            $insert->payment_term     = $request->payment_term;
            $insert->hrm_location_id  = $request->location_name;
            $insert->basic_salary_of_gross  = $request->basic_salary_of_gross;
            $insert->apply_for  = 2;
            $insert->save();



            $insert_cw     = new HrmEmployeeBonusCW;
            $insert_cw->hrm_employee_bonus_master_id = $insert->id;
            $insert_cw->rate           = $request->rate;
            $insert_cw->days           = $request->days;
            $insert_cw->is_valid       = 1;
            $insert_cw->gross_salary   = $request->total_amount;
            $insert_cw->zero_to_six_months = $request->zero_to_six_months;
            $insert_cw->six_to_twelve_months = $request->six_to_twelve_months;
            $insert_cw->more_than_one_year = $request->more_than_one_year;
            $insert_cw->save();



// IF('$request->amount_type'='%',a.basic_salary*($request->amount/100),$request->amount)

            if ($request->payment_term=='Payment Method As Per Salary'){


                if($request->zero_to_six_months>0){

                    DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id,users_id,hrm_employee_bonus_master_id,is_valid,salary_amount,amount,amount_type,payment_mode,account_no,hrm_bank_id,bonus_amount,basic_salary)
                            SELECT a.id,$users_id,$insert->id,1,($request->total_amount),$request->zero_to_six_months,'%',b.payment_mode,b.account_no,b.hrm_bank_id,($request->total_amount*$request->zero_to_six_months/100),($request->total_amount*$request->basic_salary_of_gross/100)
                            FROM
                                hrm_employee_job_info a
                                    JOIN
                                hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location_name
                                AND a.id in (

                            SELECT MAX(bbb.id) as id FROM(
                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                end_date BETWEEN '$based_on_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$based_on_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7) ) aaa
                                        JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                         AND bbb.hrm_location_id = $hrm_location_id
                                         group by bbb.hrm_employee_id
                                    )
                                    JOIN
                                hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id in ($eligible_employee_type)
                                    JOIN
                                hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id where TIMESTAMPDIFF(MONTH, d.joining_date, '$declaration_date')<6
                                        ");
                }


                if($request->six_to_twelve_months>0){

                    DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id,users_id,hrm_employee_bonus_master_id,is_valid,salary_amount,amount,amount_type,payment_mode,account_no,hrm_bank_id,bonus_amount,basic_salary)
                            SELECT a.id,$users_id,$insert->id,1,($request->total_amount),$request->six_to_twelve_months,'%',b.payment_mode,b.account_no,b.hrm_bank_id,($request->total_amount*$request->six_to_twelve_months/100),($request->total_amount*$request->basic_salary_of_gross/100)
                            FROM
                                hrm_employee_job_info a
                                    JOIN
                                hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location_name
                                AND a.id in (

                            SELECT MAX(bbb.id) as id FROM(
                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                end_date BETWEEN '$based_on_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$based_on_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7) ) aaa
                                        JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                         AND bbb.hrm_location_id = $hrm_location_id
                                         group by bbb.hrm_employee_id

                                    )
                                    JOIN
                                hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id in ($eligible_employee_type)
                                    JOIN
                                hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id where TIMESTAMPDIFF(MONTH, d.joining_date, '$declaration_date')>=6
                                    and TIMESTAMPDIFF(MONTH, d.joining_date, '$declaration_date')<12 ");

                }



                if($request->more_than_one_year>0){

                    DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id,users_id,hrm_employee_bonus_master_id,is_valid,salary_amount,amount,amount_type,payment_mode,account_no,hrm_bank_id,bonus_amount,basic_salary)
                                SELECT a.id,$users_id,$insert->id,1,($request->total_amount),$request->more_than_one_year,'%',b.payment_mode,b.account_no,b.hrm_bank_id,($request->total_amount*$request->more_than_one_year/100),($request->total_amount*$request->basic_salary_of_gross/100)
                                FROM
                                    hrm_employee_job_info a
                                        JOIN
                                    hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location_name
                                    AND a.id in (

                            SELECT MAX(bbb.id) as id FROM(
                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                end_date BETWEEN '$based_on_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$based_on_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7) ) aaa
                                        JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                         AND bbb.hrm_location_id = $hrm_location_id
                                         group by bbb.hrm_employee_id
                                    )
                                        JOIN
                                    hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id in ($eligible_employee_type)
                                        JOIN
                                    hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id where TIMESTAMPDIFF(YEAR, d.joining_date, '$declaration_date')>=1");

                }






            }else{




                if($request->zero_to_six_months>0){


                    DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id,users_id,hrm_employee_bonus_master_id,is_valid,salary_amount,amount,amount_type,payment_mode,account_no,hrm_bank_id,bonus_amount,basic_salary)
                            SELECT a.id,$users_id,$insert->id,1,($request->total_amount),$request->zero_to_six_months,'%',1,'',0,($request->total_amount*$request->zero_to_six_months/100),($request->total_amount*$request->basic_salary_of_gross/100)
                            FROM
                                hrm_employee_job_info a
                                    JOIN
                                hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location_name
                                AND a.id in (

                                SELECT MAX(bbb.id) as id FROM(
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                    WHERE
                                    '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7)
                                    UNION ALL
                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                    AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7)
                                    UNION ALL
                                    SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                    WHERE
                                    end_date BETWEEN '$based_on_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                    AND hrm_employee_activity_status_id NOT IN (7)
                                    UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE
                                             end_date BETWEEN '$based_on_month' AND '$lastdate'
                                            AND end_date IS NOT NULL
                                            AND hrm_employee_activity_status_id NOT IN (7) ) aaa
                                            JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                             AND bbb.hrm_location_id = $hrm_location_id
                                             group by bbb.hrm_employee_id
                                    )
                                    JOIN
                                hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id in ($eligible_employee_type)
                                    JOIN
                                hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id where TIMESTAMPDIFF(MONTH, d.joining_date, '$declaration_date')<6
                                    ");
                }






                if($request->six_to_twelve_months>0){


                    DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id,users_id,hrm_employee_bonus_master_id,is_valid,salary_amount,amount,amount_type,payment_mode,account_no,hrm_bank_id,bonus_amount,basic_salary)
                            SELECT a.id,$users_id,$insert->id,1,($request->total_amount),$request->six_to_twelve_months,'%',1,'',0,($request->total_amount*$request->six_to_twelve_months/100),($request->total_amount*$request->basic_salary_of_gross/100)
                            FROM
                                hrm_employee_job_info a
                                    JOIN
                                hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location_name
                                AND a.id in (

                            SELECT MAX(bbb.id) as id FROM(
                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                WHERE
                                end_date BETWEEN '$based_on_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                AND hrm_employee_activity_status_id NOT IN (7)
                                UNION ALL
                                        SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                        WHERE
                                         end_date BETWEEN '$based_on_month' AND '$lastdate'
                                        AND end_date IS NOT NULL
                                        AND hrm_employee_activity_status_id NOT IN (7) ) aaa
                                        JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                         AND bbb.hrm_location_id = $hrm_location_id
                                         group by bbb.hrm_employee_id
                                    )
                                    JOIN
                                hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id in ($eligible_employee_type)
                                    JOIN
                                hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id where TIMESTAMPDIFF(MONTH, d.joining_date, '$declaration_date')>=6
                                    and TIMESTAMPDIFF(MONTH, d.joining_date, '$declaration_date')<12 ");

                }


                if($request->more_than_one_year>0){


                    DB::insert("INSERT INTO hrm_employee_bonus(hrm_employee_job_info_id,users_id,hrm_employee_bonus_master_id,is_valid,salary_amount,amount,amount_type,payment_mode,account_no,hrm_bank_id,bonus_amount,basic_salary)
                                SELECT a.id,$users_id,$insert->id,1,($request->total_amount),$request->more_than_one_year,'%',1,'',0,($request->total_amount*$request->more_than_one_year/100),($request->total_amount*$request->basic_salary_of_gross/100)
                                FROM
                                    hrm_employee_job_info a
                                        JOIN
                                    hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location_name
                                    AND a.id in (

                                            SELECT MAX(bbb.id) as id FROM(
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE
                                            '$lastdate' BETWEEN start_date AND end_date  AND end_date IS NOT NULL
                                            AND hrm_employee_activity_status_id NOT IN (7)
                                            UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity WHERE end_date IS NULL
                                            AND start_date<='$lastdate' AND hrm_employee_activity_status_id NOT IN (7)
                                            UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE
                                            end_date BETWEEN '$based_on_month'  AND '$lastdate'  AND  end_date IS NOT NULL
                                            AND hrm_employee_activity_status_id NOT IN (7)
                                            UNION ALL
                                            SELECT hrm_employee_job_info_id FROM hrm_employee_activity
                                            WHERE
                                            end_date BETWEEN '$based_on_month' AND '$lastdate'
                                            AND end_date IS NOT NULL
                                            AND hrm_employee_activity_status_id NOT IN (7) ) aaa
                                            JOIN hrm_employee_job_info bbb ON aaa.hrm_employee_job_info_id= bbb.id
                                            AND bbb.hrm_location_id = $hrm_location_id
                                            group by bbb.hrm_employee_id
                                    )
                                        JOIN
                                    hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.id in ($eligible_employee_type)
                                        JOIN
                                    hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id where TIMESTAMPDIFF(YEAR, d.joining_date, '$declaration_date')>=1");


                }


            }


         DB::commit();

         $this->recordActivity(
             1,
             'CW Employee Bonus Process Created',
             $insert_cw,
             $insert_cw->id,
             'hrm_employee_bonus_cw'
        );

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
        $bonus = HrmEmployeeBonus::find($id);

        if (empty($bonus)){
            session()->flash('alert-danger', 'Invalid Employee!!');
            return Redirect()->back();
        }


        $find = HrmEmployeeBonusMaster::where('id','=',$bonus->hrm_employee_bonus_master_id)->first();

        if ($find->status==2){
            session()->flash('alert-danger', 'Sorry,Bonus Already Generated!!');
            return Redirect()->back();
        }


         $data   = DB::select("SELECT
                                    a.id,
                                    c.employee_name,
                                    c.id AS employee_id,
                                    d.depertment_name,
                                    f.designation_name,
                                    e.location_name,
                                    a.bonus_amount,
                                    a.amount,
                                    a.amount_type,
                                    a.salary_amount,
                                    a.account_no,
                                    a.payment_mode,
                                    g.id as hrm_bank_id,
                                    g.short_name
                                FROM
                                    hrm_employee_bonus a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id = b.id
                                        AND a.id=$id
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_depertment d ON b.hrm_depertment_id = d.id
                                        JOIN
                                    hrm_location e ON b.hrm_location_id = e.id
                                        JOIN
                                    hrm_designation f ON b.hrm_designation_id = f.id
                                        LEFT JOIN
                                    hrm_bank g ON a.hrm_bank_id = g.id");

        return view('employee_bonus.edit_employee_bonus')
             ->with('employee',$data)
             ->with('bank_name',HrmBank::all());

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
            'salary_amount'  => 'required',
            'amount'         => 'required',
            'amount_type'    => 'required',
            'bonus_amount'   => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $find = HrmEmployeeBonus::find($id);

        // dd($find);

        if($find->is_valid==0){

            $request->session()->flash('alert-danger', 'Sorry, Already updated !!');
            return Redirect::to('employeebonus');

        }



     DB::beginTransaction();
        try {



            $insert = HrmEmployeeBonus::find($id);
            $insert->is_valid=0;
            $insert->save();

            $insert_new = new HrmEmployeeBonus;
            $insert_new->salary_amount = $request->salary_amount;
            $insert_new->amount        = $request->amount;
            $insert_new->amount_type   = $request->amount_type;
            $insert_new->bonus_amount  = $request->bonus_amount;
            $insert_new->hrm_employee_job_info_id     = $insert->hrm_employee_job_info_id;
            $insert_new->hrm_employee_bonus_master_id = $insert->hrm_employee_bonus_master_id;
            $insert_new->payment_mode  = $request->payment_mode;
            $insert_new->hrm_bank_id   = $request->hrm_bank_id;
            $insert_new->account_no    = $request->account_no;
            $insert_new->is_valid      = 1;
            $insert_new->users_id      = Auth::user()->id;
            $insert_new->save();



             DB::commit();

             $this->recordActivity(
                 1,
                 'Updated Employee Bonus',
                 $insert_new,
                 $insert_new->id,
                 'hrm_employee_bonus'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
        }


        $request->session()->flash('alert-success', 'successfully updated');
        return Redirect::to('employeebonus');
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


    public function cancel(Request $request, $id)
    {


        $bonus = HrmEmployeeBonus::find($id);

        if (empty($bonus)){
            session()->flash('alert-danger', 'Invalid Employee!!');
            return Redirect()->back();
        }


        $find = HrmEmployeeBonusMaster::where('id','=',$bonus->hrm_employee_bonus_master_id)->first();

        if ($find->status==2){
            session()->flash('alert-danger', 'Sorry,Bonus Already Generated!!');
            return Redirect()->back();
        }


        DB::beginTransaction();
        try {

            $insert = HrmEmployeeBonus::find($id);
            $insert->is_valid=0;
            $insert->users_id      = Auth::user()->id;
            $insert->save();


         DB::commit();

         $this->recordActivity(
             1,
             'Deleted Employee Bonus',
             null,
             $id,
             'hrm_employee_bonus'
        );


        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
        }


        $request->session()->flash('alert-success', 'successfully updated');
        return Redirect::to('employeebonus');

    }



    public function deletebonusprocess(Request $request)
    {

            $validator = Validator::make($request->all(), [
                'hrm_employee_bonus_master_id'      => 'required',
                'note'   => 'required',
            ]);


            if( $validator->fails() ){
                return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
                ));
            }

            $check_data = DB::select("SELECT id FROM hrm_employee_bonus_master  WHERE id=$request->hrm_employee_bonus_master_id and is_valid=1 AND status=2 ");

            if (!empty($check_data)){

                $request->session()->flash('alert-danger', 'Sorry,This Bonus Finally Generated !!');
                return Redirect::to('bonusprocess');

            }


            DB::beginTransaction();
            try {

                    $insert     = HrmEmployeeBonusMaster::find($request->hrm_employee_bonus_master_id);
                    $insert->is_valid         = 0;
                    $insert->status           = 0;
                    $insert->users_id         = Auth::user()->id;
                    $insert->note             = $request->note;
                    $insert->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 'Deleted Processed Bonus',
                 $insert,
                 $request->hrm_employee_bonus_master_id,
                 'hrm_employee_bonus_master'
            );

            } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
            'success'           => false,
            'error_messages'    => true,
            'errors'          => "insert problem !! " . $e->getMessage()
            ));
            }

            $request->session()->flash('alert-success', 'successfully Deleted');
            return Redirect::to('bonusprocess');



    }


}



