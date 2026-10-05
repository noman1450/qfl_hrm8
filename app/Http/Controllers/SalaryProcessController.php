<?php

namespace App\Http\Controllers;

use Crypt;
use Session;
use App\User;
use DateTime;
use Datatables;
use Carbon\Carbon;
use App\Models\HrmMonth;
use App\Models\HrmEmployee;
use App\Models\HrmLocation;
use App\Models\HrmReligion;
use Illuminate\Http\Request;
use App\Models\HrmBloodGroup;
use App\Models\HrmLoanLedger;

use App\Models\HrmPayRegister;
use App\Models\HrmEmployeeShift;
use App\Models\HrmLastPromotion;
use App\Models\HrmMaritalStatus;
use App\Models\HrmEmployeeSalary;
use App\Models\HrmEmployeeJobInfo;

use App\Models\HrmEmployeeJoining;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeActivity;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmEmployeeTransfer;
use App\Models\HrmSalaryGradeMaster;
use Illuminate\Support\Facades\Auth;
use App\Models\HrmPayRegisterDetails;
use App\Models\HrmSalaryGradeDetails;
use Illuminate\Support\Facades\Config;
use App\Models\HrmSalaryGenerateMaster;
use App\Models\HrmEmployeeSalaryDetails;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Models\HrmLoanPayRegisterDetails;
use Illuminate\Support\Facades\Validator;


class SalaryProcessController extends Controller
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
                                                    AND a.apply_for = 1
                                                    JOIN
                                                hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                                    JOIN
                                                user_location d ON a.hrm_location_id = d.hrm_location_id
                                                    AND d.`users_id`= $user_id AND c.status=1
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

        return view('salary_process.salary_process_list')
              ->with('running_month_year',$running_month_year);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

         return view('salary_process.salary_process')
               ->with('month',HrmMonth::all())
               ->with('default_user_location',  $default_user_location) ;
    }







    public function salaryprocess_list(Request $request)
    {

        $condition = " AND a.year_id=".$request->year;

        if($request->location==null){
        }else{
            $condition = $condition." AND a.hrm_location_id=".$request->location;
        }

        if($request->month==null){
        }else{
            $condition = $condition." AND a.hrm_month_id=".$request->month;
        }


           $user_id = Auth::user()->id;
           $data = DB::select("SELECT
                                    a.id,
                                    a.hrm_location_id,
                                    a.hrm_month_id,
                                    a.year_id,
                                    a.declaration_date,
                                    a.payment_date,
                                    b.location_name,
                                    c.month_name,
                                    If((a.payment_date is null),If((a.hrm_salary_slap_id is null),'Salary Process(Full)',
                                      'Salary Process(Partial)'),If((a.hrm_salary_slap_id is null),
                                      'Salary Generated(Full)','Salary Generate(Partial)')) as status,
                                    e.name as user_name,
                                    a.created_at
                                FROM
                                    hrm_salary_generate_master a
                                        JOIN
                                    hrm_location b ON a.hrm_location_id = b.id AND a.valid = 1 AND a.status=1
                                      $condition
                                        JOIN
                                    hrm_month c ON a.hrm_month_id = c.id
                                        JOIN
                                    user_location d ON a.hrm_location_id = d.hrm_location_id
                                        AND d.`users_id` =$user_id
                                        JOIN
                                    users e ON a.users_id=e.id
                                        LEFT JOIN
                                    hrm_salary_slap f ON a.hrm_salary_slap_id=f.id");


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


        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND a.hrm_location_id=$request->location
                                    AND a.hrm_month_id = $request->month_name
                                    AND a.year_id = $request->year
                                    AND a.apply_for=1
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id is Null
                                    AND c.valid =1
                                    AND a.salary_genarate_type<>0  LIMIT 1");

        if (!empty($check_data)){
            $validator->errors()->add('field',"Sorry ! This Month Salary Already Process");
            return Response::json(array(
                'danger'   => true,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        $check_datas = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND a.hrm_location_id=$request->location
                                    AND a.hrm_month_id = $request->month_name
                                    AND a.year_id = $request->year
                                    AND a.apply_for=1
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id is not Null
                                    AND c.valid =1
                                    AND a.salary_genarate_type=1  LIMIT 1");


        if (!empty($check_datas)){
            $validator->errors()->add('field',"Sorry! Partial Salary already Process");
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }


        if ($request->month_name > 9){
            $month = $request->month_name;
        }else{
            $month = '0'.$request->month_name;
        }

        $date_of_month  = $request->year.'-'.$month.'-01';
        $year_month     = $request->year.'-'.$month;
        $lastdate       = date("Y-m-t", strtotime($date_of_month));
        $users_id       = Auth::user()->id;

        $addDays =0;

        if($request->fullAttendance==2){

            $datetime1 = date_create($declation_date);
            $datetime2 = date_create($lastdate);
            $addDays   = date_diff($datetime1, $datetime2)->days;

            $lastdate = $declation_date;

        }

        if($request->fullAttendance==3){
            $addDays  = date("t", strtotime($date_of_month));
        }




        DB::beginTransaction();
        try {

            // $this->GetNewSalaryIncrement($request->month_name,$request->year,$request->location);

            $insert     = new HrmSalaryGenerateMaster;
            $insert->hrm_location_id        = $request->location;
            $insert->hrm_month_id           = $request->month_name;
            $insert->year_id                = $request->year;
            $insert->declaration_date       = $declation_date;
            // $insert->payment_date           = $lastdate;
            $insert->valid                  = 1;
            $insert->users_id               = $users_id;
            $insert->status                 = 1;
            $insert->save();


            $master_id = $insert->id;


            DB::insert("INSERT INTO pay_register(year_id,hrm_month_id,hrm_employee_job_info_id,
                        day_of_month,total_present,salary_genarate_type,users_id,hrm_employee_salary_id,amount,apply_for,payment_mode,account_no,by_bank_percent,hrm_bank_id,hrm_salary_generate_master_id,hrm_location_id,accounts_code)

                        SELECT $request->year,$request->month_name,a.id,(SELECT RIGHT(LAST_DAY('$date_of_month'),2)),
                        0,1,$users_id,b.id,b.salary_amount,1,b.payment_mode,b.account_no,b.by_bank_percent,b.hrm_bank_id,$master_id,$request->location,b.accounts_code
                        FROM
                            hrm_employee_job_info a
                                JOIN
                            hrm_employee_salary b ON a.id = b.hrm_employee_job_info_id AND a.hrm_location_id=$request->location

                            AND a.id in
                            (SELECT Max(aa.hrm_employee_job_info_id) as id
                            FROM
                            (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
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
                                )  aa JOIN hrm_employee_job_info bb ON aa.hrm_employee_job_info_id=bb.id  AND bb.hrm_location_id=$request->location
                                GROUP BY bb.hrm_employee_id)
                                JOIN
                            hrm_employment_status c ON a.hrm_employment_status_id = c.id AND c.status <> 2
                                JOIN
                            hrm_employee_joining d ON a.hrm_employee_id=d.hrm_employee_id AND d.joining_date<'$lastdate'
                            WHERE a.employee_activity != 10 ");

// dd('sdf');

            DB::update("UPDATE pay_register
                        JOIN(SELECT
                            COUNT(*) as total_present,b.id
                        FROM
                            hrm_attendance a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                            AND b.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
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
                                )
                                AND b.hrm_location_id=$request->location
                                AND MONTH(a.punche_date) = $request->month_name
                                AND YEAR(a.punche_date)  = $request->year  AND a.attendance_status in (2,3,4,5,6,7,8,11,10,12,13,14)
                                AND a.punche_date <='$lastdate'
                                GROUP BY b.id) totalpresent
                        ON pay_register.hrm_employee_job_info_id = totalpresent.id
                        AND pay_register.hrm_month_id   = $request->month_name
                        AND pay_register.year_id        = $request->year
                        SET pay_register.total_present  = totalpresent.total_present
                        WHERE pay_register.apply_for = 1 AND pay_register.hrm_salary_generate_master_id = $master_id");


        // dd("working...");

        // Deduct for late
        $location_info = HrmLocation::where('id', $request->location)->first();



        if ($location_info->is_eo_punishment ==0) {

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
                                   AND b.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
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
                                    )
                                    AND b.hrm_location_id=$request->location
                                    AND MONTH(a.punche_date) = $request->month_name
                                    AND YEAR(a.punche_date)  = $request->year
                                    AND a.attendance_status=6
                                     AND a.punche_date <='$lastdate'
                                    GROUP BY b.id HAVING  Round((COUNT(*)/$check_status)) >0) deduct_late
                            ON pay_register.hrm_employee_job_info_id = deduct_late.id
                            AND pay_register.hrm_month_id   = $request->month_name
                            AND pay_register.year_id        = $request->year
                            AND pay_register.hrm_salary_generate_master_id = $master_id
                            SET pay_register.total_present  = (pay_register.total_present-deduct_late.deduct_late)
                            WHERE pay_register.apply_for=1");

                     }
       

        // dd("working...");

              // Early Out Deduct Implement 18-05-2023

              $check_status_value_eo = DB::SELECT("SELECT effect_apply FROM hrm_attendance_status WHERE id = 14 ");
              $check_status_eo       = $check_status_value_eo[0]->effect_apply;


                if(!empty($check_status_eo)){

                    DB::update("UPDATE pay_register
                                JOIN(SELECT
                                    left((COUNT(*)/$check_status),1) as deduct_late,b.id
                                FROM
                                    hrm_attendance a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                       AND b.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
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
                                        )
                                        AND b.hrm_location_id=$request->location
                                        AND MONTH(a.punche_date) = $request->month_name
                                        AND YEAR(a.punche_date)  = $request->year
                                        AND a.attendance_status=14
                                         AND a.punche_date <='$lastdate'
                                        GROUP BY b.id HAVING  Round((COUNT(*)/$check_status)) >0) deduct_late
                                ON pay_register.hrm_employee_job_info_id = deduct_late.id
                                AND pay_register.hrm_month_id   = $request->month_name
                                AND pay_register.year_id        = $request->year
                                AND pay_register.hrm_salary_generate_master_id = $master_id
                                SET pay_register.total_present  = (pay_register.total_present-deduct_late.deduct_late)
                                WHERE pay_register.apply_for=1");




                }
        }



            // Deduction For HalfDay Leave

           DB::update("UPDATE pay_register
                                JOIN(SELECT
                                    (COUNT(*)/2) as deduct_late,b.id
                                FROM
                                    hrm_attendance a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                       AND b.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
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
                                        )
                                        AND b.hrm_location_id=$request->location
                                        AND MONTH(a.punche_date) = $request->month_name
                                        AND YEAR(a.punche_date)  = $request->year
                                        AND a.attendance_status = 10
                                         AND a.punche_date <='$lastdate'
                                        GROUP BY b.id ) deduct_late
                                ON pay_register.hrm_employee_job_info_id = deduct_late.id
                                AND pay_register.hrm_month_id   = $request->month_name
                                AND pay_register.year_id        = $request->year
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
                               AND b.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
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
                                        )
                                    AND b.hrm_location_id=$request->location
                                    AND MONTH(a.punche_date) = $request->month_name
                                    AND YEAR(a.punche_date)  = $request->year
                                    AND a.attendance_status = 13
                                     AND a.punche_date <='$lastdate'
                                    GROUP BY b.id ) deduct_late
                                ON pay_register.hrm_employee_job_info_id = deduct_late.id
                                AND pay_register.hrm_month_id   = $request->month_name
                                AND pay_register.year_id        = $request->year
                                AND pay_register.hrm_salary_generate_master_id = $master_id
                                SET pay_register.total_present  = (pay_register.total_present- deduct_late.deduct_late)
                                WHERE pay_register.apply_for = 1");


        if($request->fullAttendance==2){

           DB::UPDATE("UPDATE  pay_register  SET total_present=total_present+$addDays WHERE  hrm_location_id=$request->location AND hrm_month_id=$request->month_name
           AND  year_id=$request->year AND hrm_salary_generate_master_id=$master_id");
        }

        if($request->fullAttendance==3){

           DB::UPDATE("UPDATE  pay_register  SET total_present=$addDays WHERE  hrm_location_id=$request->location AND hrm_month_id=$request->month_name
           AND  year_id=$request->year AND hrm_salary_generate_master_id=$master_id");



           // JOINING DATE BETWEEN month

                DB::UPDATE("UPDATE pay_register
                                JOIN(SELECT
                                        a.id,
                                        (DATEDIFF('$lastdate',b.joining_date)+1)  as payble_days
                                    FROM
                                        hrm_employee_job_info a
                                            JOIN
                                        hrm_employee_joining b ON a.id = b.hrm_employee_id
                                            AND b.joining_date BETWEEN '$date_of_month' AND '$lastdate'
                                            AND a.hrm_location_id=$request->location
                                            AND a.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
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
                                                    )) aa ON aa.id=pay_register.hrm_employee_job_info_id
                                        AND pay_register.hrm_month_id   = $request->month_name
                                        AND pay_register.year_id        = $request->year
                                        AND pay_register.hrm_salary_generate_master_id = $master_id
                                        SET pay_register.total_present= aa.payble_days
                   WHERE pay_register.apply_for = 1
                ");

            //END JOINING DATE BETWEEN month

        }



            DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                        SELECT a.id,c.hrm_salary_head_id,c.amount,c.amount_type,c.actual_amount
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


            $basic_salary_salary_head = DB::table('hrm_set_default_head')
                ->select('hrm_salary_head_id as basic_salary_salary_head')
                ->where('hrm_default_head_id', '=', 1)
                ->first()->basic_salary_salary_head;

            $attendance_deduction_salary_head = DB::table('hrm_set_default_head')
                ->select('hrm_salary_head_id as attendance_deduction_salary_head')
                ->where('hrm_default_head_id', '=', 2)
                ->first()->attendance_deduction_salary_head;


            $location_info = HrmLocation::where('id',$request->location)->first();

            if($location_info->is_attendance_deduction_on_basic==0){
                // attendance deduction on gross salary
                DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                            SELECT a.id,$attendance_deduction_salary_head,ROUND(((a.day_of_month - a.total_present)*(a.amount/a.day_of_month))),2,Round(((a.day_of_month - a.total_present)*(a.amount/a.day_of_month)))
                            FROM pay_register a
                            JOIN
                            hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND b.hrm_location_id=  $request->location
                            WHERE a.hrm_month_id = $request->month_name AND a.year_id = $request->year  AND a.apply_for=1
                            AND a.hrm_salary_generate_master_id = $master_id");

            }

            if($location_info->is_attendance_deduction_on_basic==1){
            // attendance deduction on basic
                DB::INSERT("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                                SELECT a.id,$attendance_deduction_salary_head,ROUND(((a.day_of_month - a.total_present)*(c.actual_amount/a.day_of_month))),2,Round(((a.day_of_month - a.total_present)*(c.actual_amount/a.day_of_month))) as actual_amount
                                FROM pay_register a
                                JOIN
                                hrm_employee_salary b ON a.hrm_employee_salary_id = b.id  AND a.apply_for = 1
                                AND a.hrm_month_id = $request->month_name
                                AND a.year_id = $request->year
                                AND a.apply_for = 1
                                AND a.hrm_salary_generate_master_id = $master_id
                                JOIN
                                hrm_employee_salary_details c ON b.id = c.hrm_employee_salary_id AND c.actual_amount>0 AND c.hrm_salary_head_id = $basic_salary_salary_head
                                JOIN
                                hrm_salary_head d ON c.hrm_salary_head_id=d.id and d.apply_for=1
                                JOIN
                                hrm_employee_job_info e ON e.id=a.hrm_employee_job_info_id AND e.hrm_location_id=$request->location
                                Where d.active_status=1");

            }





            // loan & advance deduction (Query 100% Right but multiple loan manage korar jonno loop chalano hoase)
            // DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
            //             SELECT c.id,e.hrm_salary_head_id,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS amount,2,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS actual_amount
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


            $query = DB::SELECT("SELECT a.id as loan_application_id,c.id,e.hrm_salary_head_id,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS amount,2,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS actual_amount
                        FROM
                            hrm_loan_application a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                               AND b.id in (SELECT hrm_employee_job_info_id FROM hrm_employee_activity
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
                                        )
                                AND a.approved_status = 2
                                AND b.hrm_location_id=  $request->location
                                AND '$year_month' >= LEFT(a.installment_start_date,7)
                                JOIN
                            pay_register c ON b.id = c.hrm_employee_job_info_id
                                AND c.hrm_month_id = $request->month_name
                                AND c.year_id      = $request->year
                                AND c.apply_for = 1
                                AND c.hrm_salary_generate_master_id = $master_id
                                JOIN
                            hrm_loan_ledger d ON a.id = d.hrm_loan_application_id AND d.valid=1
                                JOIN
                            hrm_loan_type e ON a.hrm_loan_type_id = e.id
                        GROUP BY a.id,c.id,e.hrm_salary_head_id,a.installment_size
                        HAVING SUM(d.credit - d.debit)>0");

             // $query2 = DB::SELECT("SELECT a.id,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS debit,
             //            0,$request->month_name,$request->year,1,'Loan Adjustment',$users_id
             //            FROM
             //                hrm_loan_application a
             //                    JOIN
             //                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
             //                    AND b.employee_activity = 1
             //                    AND a.approved_status = 2
             //                    AND b.hrm_location_id=  $request->location
             //                    AND '$year_month' >= LEFT(a.installment_start_date,7)
             //                    JOIN
             //                pay_register c ON b.id = c.hrm_employee_job_info_id
             //                    AND c.hrm_month_id = $request->month_name
             //                    AND c.year_id      = $request->year
             //                    AND c.apply_for = 1
             //                    AND c.hrm_salary_generate_master_id = $master_id
             //                    JOIN
             //                hrm_loan_ledger d ON a.id = d.hrm_loan_application_id
             //                    JOIN
             //                hrm_loan_type e ON a.hrm_loan_type_id = e.id
             //            GROUP BY a.id,a.installment_size
             //            HAVING SUM(d.credit - d.debit)>0");


             // $count_row  = count($query);

             // for($r = 0; $r <$count_row; $r++) {
                foreach($query as $result) {

                    // $insert  = new HrmPayRegisterDetails;
                    // $insert->pay_register_id            = $query[$r]->id;
                    // $insert->hrm_salary_head_id         = $query[$r]->hrm_salary_head_id;
                    // $insert->amount                     = $query[$r]->amount;
                    // $insert->amount_type                = 2;
                    // $insert->actual_amount              = $query[$r]->actual_amount;
                    // $insert->save();

                    $payregisterDtl  = new HrmPayRegisterDetails;
                    $payregisterDtl->pay_register_id            = $result->id;
                    $payregisterDtl->hrm_salary_head_id         = $result->hrm_salary_head_id;
                    $payregisterDtl->amount                     = $result->amount;
                    $payregisterDtl->amount_type                = 2;
                    $payregisterDtl->actual_amount              = $result->actual_amount;
                    $payregisterDtl->save();

                    $loan_ledger  = new HrmLoanLedger;
                    $loan_ledger->hrm_loan_application_id   = $result->loan_application_id;
                    $loan_ledger->debit                     = $result->amount;
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
                    $insert_tag->pay_register_details_id     = $payregisterDtl->id;
                    $insert_tag->hrm_loan_ledger_id          = $loan_ledger->id;
                    $insert_tag->save();

             }


            // loan & advance deduction
            // DB::insert("INSERT INTO hrm_loan_ledger(hrm_loan_application_id,debit,credit,hrm_month_id,year_id,valid,narration,users_id)
            //             SELECT a.id,(IF(SUM(d.credit - d.debit)>=(a.installment_size),a.installment_size,SUM(d.credit - d.debit))) AS debit,0,$request->month_name,$request->year,1,'Loan Adjustment',$users_id
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
            //                     AND c.hrm_salary_generate_master_id = $master_id
            //                     JOIN
            //                 hrm_loan_ledger d ON a.id = d.hrm_loan_application_id
            //                     JOIN
            //                 hrm_loan_type e ON a.hrm_loan_type_id = e.id
            //             GROUP BY a.id,a.installment_size
            //             HAVING SUM(d.credit - d.debit)>0");


            // Extra Feature Addition Or Deduction
            DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                        SELECT
                            e.id, a.hrm_salary_head_id, b.amount, 1, b.amount
                        FROM
                            hrm_salary_extra_feature a
                                JOIN
                            hrm_salary_extra_feature_details b ON a.id = b.hrm_salary_extra_feature_id
                                AND '$request->month_name' BETWEEN MONTH(a.month_from) AND MONTH(a.month_to)
                                AND '$request->year' BETWEEN YEAR(a.month_from) AND YEAR(a.month_to)
                                JOIN
                            hrm_employee_job_info c ON b.hrm_employee_job_info_id = c.id
                                AND c.hrm_location_id=  $request->location
                                JOIN
                            hrm_location d ON c.hrm_location_id = d.id
                                JOIN
                            pay_register e ON e.hrm_employee_job_info_id = b.hrm_employee_job_info_id
                                AND e.hrm_month_id = $request->month_name
                                AND e.year_id = $request->year
                                AND e.apply_for = 1
                                AND e.hrm_salary_generate_master_id = $master_id");


           // Salary Deduction
            DB::insert("INSERT INTO pay_register_details(pay_register_id,hrm_salary_head_id,amount,amount_type,actual_amount)
                        SELECT
                            d.id, a.hrm_salary_head_id, a.amount, 1, a.amount
                        FROM
                            hrm_salary_deduction a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND YEAR(a.month_from)='$request->year' AND MONTH(a.month_from) = '$request->month_name'
                                AND b.hrm_location_id = $request->location
                                AND a.is_active = 1
                                JOIN
                            hrm_location c ON b.hrm_location_id = c.id
                                JOIN
                            pay_register d ON d.hrm_employee_job_info_id = b.id
                                AND d.hrm_month_id = $request->month_name
                                AND d.year_id = $request->year
                                AND d.apply_for = 1
                                AND d.hrm_salary_generate_master_id = $master_id");



        DB::update("UPDATE hrm_salary_deduction SET is_active = 2
                            WHERE id in(SELECT id FROM (SELECT
                            a.id
                        FROM
                            hrm_salary_deduction a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND a.is_active = 1
                                AND YEAR(a.month_from)=$request->year
                                AND MONTH(a.month_from) = $request->month_name
                                AND b.hrm_location_id = $request->location) a )");




        DB::commit();

        $this->recordActivity(
             1,
             'Created Salary Process',
             null,
             $insert->id,
             'hrm_salary_generate_master'
        );

        } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => 'Insert problem || '.$e->getMessage()
            ));
        }


        return response::json(array(
            'success'    => true,
            'messages'   => 'successfully process'
        ));

    }


    public function GetNewSalaryIncrement($month_id,$year_id,$hrm_location_id){

        $increment_date = $year_id.'-'.$month_id.'-'.'01';

        $find_data = DB::SELECT("SELECT
                                        a.id,a.effective_date,b.hrm_employee_job_info_id, b.new_salary_amount,a.apply_for,b.hrm_designation_id
                                    FROM
                                        hrm_salary_increment a
                                            JOIN
                                        hrm_salary_increment_details b ON a.id = b.hrm_salary_increment_id
                                            AND a.status = 1 and a.hrm_location_id = $hrm_location_id
                                            AND a.effective_date <= '$increment_date'
                                            -- AND a.id =1303
                                            JOIN
                                        hrm_employee_job_info c ON b.hrm_employee_job_info_id = c.id
                                            AND c.hrm_location_id = a.hrm_location_id and c.employee_activity=1");



        if (!empty($find_data)){

            foreach($find_data as $keys) {

                    // dd($keys->effective_date);
                    $xmasDay    = new DateTime($keys->effective_date.'- 1 day');
                    $end_date   = $xmasDay->format('Y-m-d');

                    $ldate      = date('Y-m-d');
                    $cxmasDay   = new DateTime($ldate.'- 1 day');
                    $cend_date   = $cxmasDay->format('Y-m-d');

                    $jobid      = $keys->hrm_employee_job_info_id;
                    $newsalary  = $keys->new_salary_amount;
                    $new_hrm_designation_id  = $keys->hrm_designation_id;

                    $check_data = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id = $jobid AND end_date is null");
                    $date_from  = $keys->effective_date;




                    $final_check = DB::select("SELECT start_date FROM hrm_employee_activity Where hrm_employee_job_info_id in(
                                                SELECT id from hrm_employee_job_info where hrm_employee_id
                                                in (select hrm_employee_id from hrm_employee_job_info where id=$jobid ))
                                                and start_date='$date_from'");



                    if (!empty($final_check)) {
                       continue;
                    }


                    if($date_from<$check_data[0]->start_date){

                    }else{
                       $check_two = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info WHERE id = $jobid)) AND start_date='$date_from' ");


                        if(!empty($check_two)){
                        }else{

                            $oldinfo    = HrmEmployeeJobInfo::find($jobid);
                            $insert     = new HrmEmployeeJobInfo;
                            $insert->hrm_employee_id            = $oldinfo->hrm_employee_id;
                            $insert->employee_code              = $oldinfo->employee_code;
                            $insert->hrm_depertment_id          = $oldinfo->hrm_depertment_id;
                            $insert->hrm_designation_id         = $new_hrm_designation_id;

                            // if($keys->apply_for==2){
                            // }else{
                            //    $insert->hrm_designation_id         = $oldinfo->hrm_designation_id;
                            // }

                            $insert->hrm_category_id            = $oldinfo->hrm_category_id;
                            $insert->hrm_employment_status_id   = $oldinfo->hrm_employment_status_id;
                            $insert->hrm_manage_by_id           = $oldinfo->hrm_manage_by_id;
                            $insert->employee_shift_status      = 1;
                            $insert->overtime_status            = $oldinfo->overtime_status;
                            $insert->hrm_plant_id               = $oldinfo->hrm_plant_id;
                            $insert->employee_activity          = 1;
                            $insert->basic_salary               = $newsalary;
                            $insert->hrm_location_id            = $oldinfo->hrm_location_id;
                            $insert->hrm_section_id             = $oldinfo->hrm_section_id;
                            $insert->insurance                  = $oldinfo->insurance;
                            $insert->users_id                   = Auth::user()->id;
                            $insert->save();


                            $oldcard = DB::SELECT("SELECT * FROM hrm_employee_card_code Where hrm_employee_job_info_id = $jobid");

                            if(!empty($oldcard)){
                                $insert_card     = new HrmEmployeeCardCode;
                                $insert_card->card_code                = $oldcard[0]->card_code;
                                $insert_card->device_id                = $oldcard[0]->device_id;
                                $insert_card->hrm_employee_job_info_id = $insert->id;
                                $insert_card->save();
                            }

                            $oldshift = DB::SELECT("SELECT * FROM hrm_employee_shift Where hrm_employee_job_info_id = $jobid AND id in (
                                            SELECT MAX(id) AS id FROM hrm_employee_shift
                                            WHERE hrm_employee_job_info_id = $jobid) ");

                            $end_shift_date   = $oldshift[0]->start_date;
                            $start_shift_date = date('Y-m-d', strtotime($end_shift_date." +1 days"));


                            if($date_from<$end_shift_date){
                                continue;
                            }

                            if($date_from>$end_shift_date){
                                $end_shift_date = date('Y-m-d', strtotime($date_from." -1 days"));
                            }




                            $insert_shift  = new HrmEmployeeShift;
                            $insert_shift->hrm_employee_job_info_id = $insert->id;
                            $insert_shift->hrm_shift_id             = $oldshift[0]->hrm_shift_id;
                            $insert_shift->start_date               = $start_shift_date;
                            $insert_shift->comment                  = 'SalaryProcess';
                            $insert_shift->users_id                 = Auth::user()->id;
                            $insert_shift->valid                    = 1;
                            $insert_shift->save();

                            $oldsalary  = DB::SELECT("SELECT * FROM hrm_employee_salary WHERE hrm_employee_job_info_id = $jobid ");



                            if(Config::get('module_config.payroll_module') == 1){

                                    $hrm_employee_job_info_id     = $insert->id;
                                    $hrm_salary_grade_master_id   = $oldsalary[0]->hrm_salary_grade_master_id;
                                    $gross_salary                 = $insert->basic_salary;
                                    $hrm_designation_id           = $insert->hrm_designation_id;
                                    $old_hrm_employee_job_info_id = $jobid;

                                    $getFunction = new CommonController();
                                    $getFunction->insert_salary_config($hrm_employee_job_info_id,$hrm_salary_grade_master_id,$gross_salary,$hrm_designation_id,$old_hrm_employee_job_info_id);


                            }

                            //END OLD SALARY THAKA FRINGE BENEFIT INSERT KORA HOASE


                            $insert_employee_activity  = new HrmEmployeeActivity;
                            $insert_employee_activity->hrm_employee_job_info_id        = $insert->id;
                            $insert_employee_activity->activity_date                   = $ldate;
                            $insert_employee_activity->comment                         = 'Salary Increment';
                            $insert_employee_activity->users_id                        = Auth::user()->id;
                            $insert_employee_activity->activity                        = 1;

                            if($keys->apply_for==2){
                              $insert_employee_activity->hrm_employee_activity_status_id = 3;
                            }else{
                              $insert_employee_activity->hrm_employee_activity_status_id = 4;

                            }

                            $insert_employee_activity->start_date                      = $keys->effective_date;
                            $insert_employee_activity->old_hrm_employee_job_info_id    = $jobid;
                            $insert_employee_activity->save();



                            if($keys->apply_for==2){

                                $old_emp_id  = $oldinfo->hrm_employee_id;
                                $effect_date = $keys->effective_date;

                                $find_prom_data = DB::SELECT("SELECT id FROM hrm_last_promotion WHERE hrm_employee_id=$old_emp_id AND promotion_date='$keys->effective_date'");

                                if(empty($find_prom_data)){

                                    $insert_employee_activity  = new HrmLastPromotion;
                                    $insert_employee_activity->hrm_employee_id  = $oldinfo->hrm_employee_id;
                                    $insert_employee_activity->promotion_date   = $keys->effective_date;
                                    $insert_employee_activity->users_id         = Auth::user()->id;
                                    $insert_employee_activity->save();

                                }


                            }




                            DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date'
                                        WHERE end_date is null AND hrm_employee_job_info_id = $jobid");

                            DB::update("UPDATE hrm_employee_job_info SET employee_activity = 0
                                        WHERE id = $jobid ");

                            DB::update("UPDATE hrm_employee_shift
                                        SET end_date = '$end_shift_date',
                                        comment      = 'Salary Process'
                                        WHERE end_date is null
                                        AND hrm_employee_job_info_id = $jobid ");


                           // var_dump($jobid);

                        }
                }



            }
                            // dd("ANY @@");

            DB::update("UPDATE hrm_salary_increment SET status = 2
                            WHERE id in(SELECT id FROM (SELECT
                                        a.id
                                    FROM
                                        hrm_salary_increment a
                                            JOIN
                                        hrm_salary_increment_details b ON a.id = b.hrm_salary_increment_id
                                            AND a.status = 1 and a.hrm_location_id = $hrm_location_id
                                            AND a.effective_date <= '$increment_date'
                                            GROUP BY a.id) a )");

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

    public function deletesalaryprocess(Request $request,$id)
    {

        $find_data = HrmSalaryGenerateMaster::find($id);
        $validator = Validator::make($request->all(), [

        ]);


        if (empty($find_data)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        $year_id  = $find_data->year_id;
        $month_id = $find_data->hrm_month_id;
        $location_id = $find_data->hrm_location_id;
        $master_id = $find_data->id;

        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND a.hrm_location_id=$location_id
                                    AND a.hrm_month_id = $month_id
                                    AND a.year_id = $year_id
                                    AND a.apply_for=1
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id is Null
                                    AND c.id=$master_id
                                    AND a.salary_genarate_type=2  LIMIT 1");


        if (!empty($check_data)){

            $request->session()->flash('alert-danger', 'Sorry This month salary already Generated !');
            return Redirect::to('salaryprocess');
        }

        $check_data = DB::select("SELECT * FROM pay_register a
                                    JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                    AND a.hrm_location_id=$location_id
                                    AND a.hrm_month_id = $month_id
                                    AND a.year_id = $year_id
                                    AND a.apply_for=1
                                    JOIN
                                    hrm_salary_generate_master c ON a.hrm_salary_generate_master_id = c.id
                                    AND c.hrm_salary_slap_id is NOT Null
                                    AND a.salary_genarate_type=1  LIMIT 1");


        if (!empty($check_data)){
            $request->session()->flash('alert-danger', 'Sorry ! Please delete from Partial Salary Option');
            return Redirect::to('salaryprocess');

        }


        $loan_ledger_with_payregisterdetails_delete =  DB::delete("DELETE FROM hrm_loan_payregister_details WHERE pay_register_details_id IN (
                                                        SELECT implicitTemp.id FROM
                                                        (SELECT id FROM  pay_register_details WHERE pay_register_id in(SELECT a.id FROM pay_register a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND a.apply_for=1 AND a.hrm_location_id=$location_id    WHERE a.year_id =$year_id AND a.hrm_month_id = $month_id AND a.hrm_salary_generate_master_id =$master_id  )) implicitTemp)");


        $loan_ledger_delete =  DB::delete("DELETE FROM hrm_loan_ledger WHERE id IN (
                                                SELECT implicitTemp.id FROM
                                                (SELECT a.id FROM hrm_loan_ledger a
                                                    JOIN hrm_loan_application b ON a.hrm_loan_application_id = b.id  AND a.entry_status = 1
                                                    JOIN hrm_employee_job_info c ON b.hrm_employee_id= c.hrm_employee_id
                                                    AND c.employee_activity=1 AND c.hrm_location_id=$location_id
                                                    AND a.year_id = $year_id AND a.hrm_month_id = $month_id

                                                    ) implicitTemp)");
        //payregister master er shathe connection korte hobe


        $details_delete = DB::delete("DELETE FROM  pay_register_details WHERE pay_register_id in(SELECT a.id FROM pay_register a JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id AND a.apply_for=1 AND a.hrm_location_id=$location_id    WHERE a.year_id =$year_id AND a.hrm_month_id = $month_id AND a.hrm_salary_generate_master_id =$master_id)");


        $master_delete =  DB::delete("DELETE FROM pay_register WHERE id IN (
                                            SELECT implicitTemp.id FROM
                                            (SELECT a.id FROM pay_register a
                                                JOIN hrm_employee_job_info b ON a.hrm_employee_job_info_id=b.id
                                                AND a.apply_for=1 AND a.hrm_location_id=$location_id
                                                AND a.year_id =$year_id AND a.hrm_month_id = $month_id
                                            AND a.hrm_salary_generate_master_id =$master_id) implicitTemp)");




        DB::update("UPDATE hrm_salary_deduction SET is_active = 1
                            WHERE id in(SELECT id FROM (SELECT
                            a.id
                        FROM
                            hrm_salary_deduction a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                AND a.is_active = 2
                                AND YEAR(a.month_from)=$year_id
                                AND MONTH(a.month_from) = $month_id
                                AND b.hrm_location_id = $location_id) a )");



       //DB::table('hrm_salary_generate_master')
       //->where('id', $master_id)
       //->update(['valid' => 0]);


        $findUpdate = HrmSalaryGenerateMaster::find($master_id);
        $findUpdate->valid     = 0;
        $findUpdate->users_id  = Auth::user()->id;;
        $findUpdate->save();

        $this->recordActivity(
             1,
             'Deleted Salary Process',
             $findUpdate,
             $master_id,
             'hrm_salary_generate_master'
        );



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
