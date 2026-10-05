<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HRMCVCompensation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MasterDataController extends Controller
{

    public function __construct()  {
        $this->middleware('auth');
    }

    public function depertmentlist(Request $request)
    {
        $depertment = [];
        if (!empty($request->term)){
            $depertment = DB::select("SELECT id,depertment_name as text FROM hrm_depertment
                                    WHERE valid = 1 AND depertment_name LIKE '%$request->term%';");
        }else{
            $depertment = DB::select("SELECT id,depertment_name as text FROM hrm_depertment
                                    WHERE valid = 1 LIMIT 10;");
        }
        return response()->json($depertment);
    }

    public function religionlistdata(Request $request){
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,religion as text FROM hrm_religion
                                WHERE religion LIKE '%$request->term%';");

        }else{
            $data = DB::select("SELECT id,religion as text FROM hrm_religion;");
        }

        return response()->json($data);
    }

    public function resignationListData(Request $request){
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,type_name as text FROM hrm_resignation_type
                                WHERE type_name LIKE '%$request->term%' AND valid = 1;");

        }else{
            $data = DB::select("SELECT id,type_name as text FROM hrm_resignation_type WHERE valid = 1;");
        }

        return response()->json($data);
    }

    public function maritalstatuslistdata(Request $request){
        $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,marital_status as text FROM hrm_marital_status
                                WHERE marital_status LIKE '%$request->term%';");

        }else{
        $data = DB::select("SELECT id,marital_status as text FROM hrm_marital_status;");
        }
            return response()->json($data);
    }


public function bloodgrouplistdata(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,blood_group as text FROM hrm_blood_group
                            WHERE blood_group LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,blood_group as text FROM hrm_blood_group;");
    }
        return response()->json($data);
}



public function designationlist(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,designation_name as text FROM hrm_designation
                            WHERE valid = 1 AND designation_name LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,designation_name as text FROM hrm_designation ;");
    }
        return response()->json($data);
}



public function categorylist(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,category_name as text FROM hrm_category
                            WHERE valid = 1 AND category_name LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,category_name as text FROM hrm_category
                            WHERE valid = 1 ;");
    }
        return response()->json($data);
}
public function shiftlist(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,shift_name as text FROM hrm_shift
                            WHERE valid = 1 AND shift_name LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,shift_name as text FROM hrm_shift
                            WHERE valid = 1 ;");
    }
        return response()->json($data);
}


public function loantypes_list(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,loan_type as text FROM hrm_loan_type
                            WHERE  loan_type LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,loan_type as text FROM hrm_loan_type
                            ;");
    }
        return response()->json($data);
}



public function slap_name_list_data(Request $request){
    $data = [];
    $hrm_month_id  = $request->hrm_month_id;
    $year_id       = $request->year_id;



    if (!empty($request->term)){
    $data = DB::select("SELECT id,concat(slap_name,'  (',date_from,' to ',date_to,')') as text FROM hrm_salary_slap
                            WHERE hrm_month_id=$hrm_month_id AND year_id=$year_id AND slap_name LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,concat(slap_name,'  (',date_from,' to ',date_to,')') as text FROM hrm_salary_slap
                            WHERE hrm_month_id=$hrm_month_id AND year_id=$year_id ;");
    }
        return response()->json($data);
}




public function partial_salary_list_data(Request $request){
    $data = [];
    $user_id    = Auth::user()->id;
    $hrm_month_id  = $request->hrm_month_id;
    $year_id       = $request->year_id;
    $delete_location_name       = $request->delete_location_name;

    $data = DB::select("SELECT
                                    a.id,
                                    concat(slap_name,'  (',date_from,' to ',date_to,')') as text

                                FROM
                                    hrm_salary_generate_master a
                                        JOIN
                                    hrm_location b ON a.hrm_location_id = b.id AND a.valid = 1 AND a.status=1
                                    AND a.hrm_location_id = $delete_location_name
                                    AND a.year_id = $year_id
                                    AND a.hrm_month_id = $hrm_month_id

                                        JOIN
                                    hrm_month c ON a.hrm_month_id = c.id
                                        JOIN
                                    user_location d ON a.hrm_location_id = d.hrm_location_id
                                        AND d.`users_id` =$user_id
                                        JOIN
                                    users e ON a.users_id=e.id
                                        JOIN
                                    hrm_salary_slap f ON a.hrm_salary_slap_id=f.id");


        return response()->json($data);
}


public function kpi_daterange_listdata(Request $request){
    $data = [];

    if (!empty($request->term)){
       $data = DB::select("SELECT id,description as text FROM hrm_kpi_assesment_year WHERE description LIKE '%$request->term%';");

    }else{
       $data = DB::select("SELECT id,description as text FROM hrm_kpi_assesment_year WHERE description LIKE '%$request->term%';");
    }
    return response()->json($data);
}


public function get_kpi_mark_list(Request $request){
    $data = [];

    if (!empty($request->term)){
       $data = DB::select("SELECT id, description  as text FROM hrm_kpi_marks WHERE valid = 1 AND description LIKE '%$request->term%' AND hrm_kpi_set_config_id = $request->hrm_kpi_set_config_id");

    }else{
       $data = DB::select("SELECT id, description as text FROM hrm_kpi_marks WHERE valid=1 AND hrm_kpi_set_config_id = $request->hrm_kpi_set_config_id");
    }
    return response()->json($data);
}


public function get_bank_list(Request $request){
    $data = [];

    if (!empty($request->term)){
       $data = DB::select("SELECT id,bank_name as text FROM hrm_bank WHERE  bank_name LIKE '%$request->term%'");

    }else{
       $data = DB::select("SELECT id,bank_name as text FROM hrm_bank ;");
    }
    return response()->json($data);
}

public function kpi_assesment_date_list_data(Request $request){
    $data = [];

    if (!empty($request->term)){
       $data = DB::select("SELECT
                                a.id,concat(b.description,' | ','From: ',a.start_date,'  To: ',a.end_date) as text
                            FROM
                                hrm_kpi_assesment_date a
                                    JOIN
                                hrm_kpi_assesment_year b ON b.id = a.hrm_kpi_assesment_year_id WHERE b.description LIKE '%$request->term%';");

    }else{
       $data = DB::select("SELECT
                                a.id,concat(b.description,' | ','From: ', a.start_date,'  To: ',a.end_date) as text
                            FROM
                                hrm_kpi_assesment_date a
                                    JOIN
                                hrm_kpi_assesment_year b ON b.id = a.hrm_kpi_assesment_year_id ;");
    }
    return response()->json($data);
}




public function kpi_task_department_listdata(Request $request){
    $data = [];

    if (!empty($request->term)){
       $data = DB::select("SELECT id,description as text FROM hrm_kpi_task_department WHERE valid=1 AND description LIKE '%$request->term%';");

    }else{
       $data = DB::select("SELECT id,description as text FROM hrm_kpi_task_department WHERE valid=1 ;");
    }
    return response()->json($data);
}


public function salary_master_listdata(Request $request){
    $data = [];
    $hrm_month_id       = $request->hrm_month_id;
    $year_id            = $request->year_id;
    $hrm_location_id    = $request->hrm_location_id;
    $status             = $request->status;

    if (!empty($request->term)){
    $data = DB::select("SELECT id,declaration_date as text FROM hrm_salary_generate_master
                            WHERE status=$status AND hrm_month_id=$hrm_month_id AND year_id=$year_id AND hrm_location_id=$hrm_location_id AND valid=1 AND declaration_date LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,declaration_date as text FROM hrm_salary_generate_master
                            WHERE status=$status AND hrm_month_id=$hrm_month_id AND year_id=$year_id AND hrm_location_id=$hrm_location_id AND valid=1 ;");
    }
        return response()->json($data);
}



public function employeelist(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,concat(employee_name, ' | ' ,IFNull(contact_number,'N/A'))as text FROM hrm_employee
                            WHERE active_status=1 and  employee_name LIKE '%$request->term%' ;");

    }else{
    $data = DB::select("SELECT id,concat(employee_name, ' | ' ,IFNull(contact_number,'N/A')) as text FROM hrm_employee
                            WHERE active_status=1  ;");
    }
        return response()->json($data);
}

public function location_wise_employeelist(Request $request){
    $data = DB::table('hrm_employee as a')
            ->join('hrm_employee_job_info as b', 'a.id', '=', 'b.hrm_employee_id')
            ->where('b.employee_activity', 1)
            ->where('b.hrm_location_id', $request->location_id)
            ->when($request->term, function ($q, $term) {
                $q->where(function ($query) use ($term) {
                    $query->where('a.employee_name', 'like', "%{$term}%")
                        ->orWhere('a.id', $term);
                });
            })
            ->select(
                'a.id',
                DB::raw("CONCAT(a.employee_name, ' | ', a.id , ' | ', IFNULL(a.contact_number, 'N/A')) as text")
            )
            ->get();



        return response()->json($data);
}

public function holiday_list_data(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,concat(holiday_name, ' | ' ,holiday_description) text FROM hrm_holiday
                            WHERE   holiday_name LIKE '%$request->term%' ;");

    }else{
    $data = DB::select("SELECT id,concat(holiday_name, ' | ' ,holiday_description) text FROM hrm_holiday
                             ;");
    }
        return response()->json($data);
}

public function salaryheadgrouplist(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,concat(group_name, ' | ' ,IF((generate_type=1),'Addition','Deduction') ) text FROM hrm_salary_head_group
                            WHERE   group_name LIKE '%$request->term%' ;");

    }else{
    $data = DB::select("SELECT id,concat(group_name, ' | ' ,IF((generate_type=1),'Addition','Deduction')) text FROM hrm_salary_head_group
                            ;");
    }
        return response()->json($data);
}

public function salarygrade_list(Request $request){
    $data = [];
    if (request()->status == 1) {
        if (!empty($request->term)){
            $data = DB::select("SELECT
                                    a.id,a.grade_name text
                                FROM hrm_salary_grade as a
                                JOIN hrm_salary_grade_master as b ON a.id=b.hrm_salary_grade_id
                                WHERE  a.valid=1 and  a.grade_name LIKE '%$request->term%' ;");

        }else{
            $data = DB::select("SELECT
                                    a.id,a.grade_name text
                                FROM hrm_salary_grade as a
                                JOIN hrm_salary_grade_master as b ON a.id=b.hrm_salary_grade_id
                                WHERE  valid=1  ;");
        }
    } else {
        if (!empty($request->term)){
        $data = DB::select("SELECT id,grade_name text FROM hrm_salary_grade
                                WHERE  valid=1 and  grade_name LIKE '%$request->term%' ;");

        }else{
        $data = DB::select("SELECT id,grade_name text FROM hrm_salary_grade
                                WHERE  valid=1  ;");
        }
    }

    return response()->json($data);
}



public function salary_head_list(Request $request){
    $data = [];

    $condition = '';
    if (!empty($request->fb)){
         $condition = ' AND a.apply_for=2 ';
    }




    if (!empty($request->term)){
    $data = DB::select("SELECT a.id,concat(a.salary_head,' || ',if(b.generate_type = 1,'Addition','Deduction')) text FROM   hrm_salary_head a
                            JOIN hrm_salary_head_group b ON a.hrm_salary_head_group_id = b.id
                            AND  a.active_status=1 $condition and  a.salary_head LIKE '%$request->term%' ;");

    }else{
    $data = DB::select("SELECT a.id,concat(a.salary_head,' || ',if(b.generate_type = 1,'Addition','Deduction')) text FROM hrm_salary_head a
                            JOIN hrm_salary_head_group b ON a.hrm_salary_head_group_id = b.id
                            AND  a.active_status=1 $condition ;");

    }
        return response()->json($data);
}

public function shiftrole_list_data(Request $request){
    $data = [];
    $user_id    = Auth::user()->id;

    if (!empty($request->term)){
    $data = DB::select("SELECT a.id,concat(a.shift_role_name, ' | ' ,b.location_name) as  text,b.id as location_id FROM  hrm_shift_role a JOIN hrm_location b ON a.hrm_location_id=b.id JOIN user_location c ON b.id = c.hrm_location_id AND c.users_id = $user_id
        WHERE a.shift_role_name  LIKE '%$request->term%' ;");

    }else{
   $data = DB::select("SELECT a.id,concat(a.shift_role_name, ' | ' ,b.location_name) as  text,b.id as location_id FROM  hrm_shift_role a JOIN hrm_location b ON a.hrm_location_id=b.id JOIN user_location c ON b.id = c.hrm_location_id AND c.users_id = $user_id
       ;");
    }
        return response()->json($data);
}

public function file_type_list(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,file_type_name text FROM hrm_file_type
                            WHERE    file_type_name LIKE '%$request->term%' ;");

    }else{
    $data = DB::select("SELECT id,file_type_name text FROM hrm_file_type
                             ;");
    }
        return response()->json($data);
}



public function bonus_name_list(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,bonus_name text FROM hrm_bonus
                            WHERE    bonus_name LIKE '%$request->term%' ;");

    }else{
    $data = DB::select("SELECT id,bonus_name text FROM hrm_bonus
                             ;");
    }
        return response()->json($data);
}


public function process_bonus_list(Request $request){
    $data = [];
    $user_id    = Auth::user()->id;


    if (!empty($request->term)){


    $data = DB::select("SELECT
                                a.id,concat(b.location_name,' | ', c.bonus_name,' | ', DATE_FORMAT(a.declaration_date, '%d-%m-%Y'),' | ', IF(a.apply_for = 1, 'General', 'CW')  ) as text
                            FROM
                                hrm_employee_bonus_master a
                                    JOIN
                                hrm_location b ON a.hrm_location_id = b.id
                                    AND a.is_valid = 1
                                    JOIN
                                hrm_bonus c ON a.hrm_bonus_id=c.id
                                    JOIN
                                user_location d ON b.id = d.hrm_location_id AND d.users_id = $user_id
                                WHERE b.location_name LIKE '%$request->term%'
                                OR c.bonus_name  LIKE '%$request->term%'    Order By a.id desc;");





    }else{
    $data = DB::select("SELECT
                                a.id,concat(b.location_name,' | ', c.bonus_name,' | ', DATE_FORMAT(a.declaration_date, '%d-%m-%Y'),' | ',IF(a.apply_for = 1, 'General', 'CW') ) as text
                            FROM
                                hrm_employee_bonus_master a
                                    JOIN
                                hrm_location b ON a.hrm_location_id = b.id
                                    AND a.is_valid = 1
                                    JOIN
                                hrm_bonus c ON a.hrm_bonus_id=c.id
                                   JOIN
                                user_location d ON b.id = d.hrm_location_id AND d.users_id = $user_id Order By a.id desc;");

    }
        return response()->json($data);
}



public function process_bonus_date_list(Request $request){
    $data = [];
    $user_id    = Auth::user()->id;


    $data = DB::select("SELECT
                                a.declaration_date as id, DATE_FORMAT(a.declaration_date, '%d-%m-%Y') as text
                            FROM
                                hrm_employee_bonus_master a
                                    JOIN
                                hrm_location b ON a.hrm_location_id = b.id
                                    AND a.is_valid = 1
                                    JOIN
                                hrm_bonus c ON a.hrm_bonus_id=c.id
                                   JOIN
                                user_location d ON b.id = d.hrm_location_id AND d.users_id = $user_id
                                GROUP BY a.declaration_date ORDER BY a.declaration_date desc ;");

    return response()->json($data);
}




public function plantname_list_data(Request $request){

    $user_id    = Auth::user()->id;
    $data       = [];
    $condition  = "";

    // if (!empty($request->term)){
    //     $data = DB::select("SELECT a.id,a.plant_name as text FROM hrm_plant a LEFT JOIN hrm_location b ON a.hrm_location_id=b.id
    //         JOIN user_location c ON b.id = c.hrm_location_id AND c.users_id = $user_id AND a.valid=1
    //         WHERE a.plant_name LIKE '%$request->term%' ;");

    // }else{
    //     $data = DB::select("SELECT a.id,a.plant_name as text  FROM hrm_plant a LEFT JOIN hrm_location b ON a.hrm_location_id=b.id
    //         JOIN user_location c ON b.id = c.hrm_location_id AND c.users_id = $user_id AND a.valid=1
    //         WHERE a.plant_name LIKE '%$request->term%' ;");
    // }

    // if($request->location != 0){
    //     $condition = " AND a.hrm_location_id = ".$request->location;
    // }

    $plant_list = DB::SELECT("SELECT a.id,
                            a.plant_name AS text
                            FROM hrm_plant a WHERE a.hrm_location_id is null
                            UNION
                            SELECT a.id,
                            CONCAT(a.plant_name,' | ',b.location_name) AS text
                            FROM hrm_plant a
                            JOIN hrm_location b ON a.hrm_location_id=b.id AND a.valid=1
                            JOIN user_location c ON b.id = c.hrm_location_id AND c.users_id = $user_id WHERE a.plant_name LIKE '%$request->term%' ;");
    return response()->json($plant_list);
}



public function education_list_data(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,education_name as text FROM hrm_education
                            WHERE    education_name LIKE '%$request->term%' ;");

    }else{
    $data = DB::select("SELECT id,education_name as text FROM hrm_education
                             ;");
    }
        return response()->json($data);
}






public function salary_head_loan_list(Request $request){
    $data = [];

    if (!empty($request->term)){
    $data = DB::select("SELECT a.id,concat(a.salary_head,' || ',if(b.generate_type = 1,'Addition','Deduction')) as text FROM hrm_salary_head a JOIN hrm_salary_head_group b ON b.id=a.hrm_salary_head_group_id AND b.group_status=3 AND a.active_status=1  AND  a.salary_head LIKE '%$request->term%' ;");

    }else{
    $data = DB::select("SELECT a.id,concat(a.salary_head,' || ',if(b.generate_type = 1,'Addition','Deduction')) as text FROM hrm_salary_head a JOIN hrm_salary_head_group b ON b.id=a.hrm_salary_head_group_id AND b.group_status=3 AND a.active_status=1   ;");
    }
        return response()->json($data);
}




    public function joinemployeelist(Request $request)
    {
        $apply_old_info = (int) $request->apply_old_info;
        $data           = [];
        $user_id        = Auth::user()->id;

        if ($apply_old_info==1){
            $condition ="AND b.id in   (SELECT id FROM  hrm_employee_job_info
                                        WHERE id in (SELECT max(id) FROM hrm_employee_job_info
                                        Group By hrm_employee_id) )";
        } else {
            $condition =" AND b.employee_activity=1 AND a.active_status=1";
        }

        if (isset($request->location)) {
            $condition= $condition." AND b.hrm_location_id=".$request->location;
        }

        if (!empty($request->term)){
            $data = DB::select("SELECT a.id,concat(a.employee_name, ' | ' ,LPAD(a.id, 5, '0') ,' | ',c.alis,' | ',a.contact_number, ' | ' , b.employee_code) as text,d.depertment_name,c.designation_name,f.shift_name,b.employee_code
                FROM hrm_employee a
                JOIN hrm_employee_job_info b  On a.id=b.hrm_employee_id
                $condition
                JOIN hrm_designation c On b.hrm_designation_id=c.id
                JOIN hrm_depertment  d On b.hrm_depertment_id=d.id
                JOIN hrm_employee_shift e On e.hrm_employee_job_info_id=b.id
                AND e.id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=b.id)
                JOIN hrm_shift f On e.hrm_shift_id=f.id
                JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id
                WHERE  a.employee_name  LIKE '%$request->term%' OR LPAD(a.id, 5, '0') LIKE '%$request->term%' OR b.employee_code LIKE '%$request->term%'");
        } else {
            $data = DB::select("SELECT a.id,concat(a.employee_name, ' | ' ,LPAD(a.id, 5, '0') ,' | ',c.alis,' | ',a.contact_number, ' | ' , b.employee_code) as text,d.depertment_name,c.designation_name,f.shift_name,b.employee_code
                FROM hrm_employee a
                JOIN hrm_employee_job_info b  On a.id=b.hrm_employee_id
                $condition
                JOIN hrm_designation c On b.hrm_designation_id=c.id
                JOIN hrm_depertment  d On b.hrm_depertment_id=d.id
                JOIN hrm_employee_shift e On e.hrm_employee_job_info_id=b.id
                AND e.id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=b.id)
                JOIN hrm_shift f On e.hrm_shift_id=f.id
                JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id
                LIMIT 10");
    }

    return response()->json($data);
}



public function joinemployeelist_accounts(Request $request){
    $data       = [];
    $user_id    = Auth::user()->id;
    if (!empty($request->term)){
        $data = DB::select("SELECT a.id,concat(a.employee_name, ' | ' ,IFNull(bb.accounts_code,'N/A'),' | ',c.alis,' | ',a.contact_number, ' | ' ,b.employee_code) as text,d.depertment_name,c.designation_name,f.shift_name,b.employee_code
                            FROM hrm_employee a
                            JOIN hrm_employee_job_info b  On a.id=b.hrm_employee_id AND a.active_status=1 and b.employee_activity=1
                            JOIN hrm_employee_salary bb ON b.id=bb.hrm_employee_job_info_id
                            JOIN hrm_designation c On b.hrm_designation_id=c.id
                            JOIN hrm_depertment  d On b.hrm_depertment_id=d.id
                            JOIN hrm_employee_shift e On e.hrm_employee_job_info_id=b.id
                            JOIN hrm_shift f On e.hrm_shift_id=f.id
                            JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id
                            AND e.id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=b.id)
                            WHERE  a.employee_name  LIKE '%$request->term%' OR b.employee_code LIKE '%$request->term%' OR bb.accounts_code LIKE '%$request->term%' ;");

    }else{


        $data = DB::select("SELECT a.id,concat(a.employee_name, ' | ' ,IFNull(bb.accounts_code,'N/A'),' | ',c.alis,' | ',a.contact_number, ' | ' ,b.employee_code) as text,d.depertment_name,c.designation_name,f.shift_name,b.employee_code
                            FROM hrm_employee a
                            JOIN hrm_employee_job_info b  On a.id=b.hrm_employee_id AND a.active_status=1 and b.employee_activity=1
                            JOIN hrm_employee_salary bb ON b.id=bb.hrm_employee_job_info_id
                            JOIN hrm_designation c On b.hrm_designation_id=c.id
                            JOIN hrm_depertment  d On b.hrm_depertment_id=d.id
                            JOIN hrm_employee_shift e On e.hrm_employee_job_info_id=b.id
                            JOIN hrm_shift f On e.hrm_shift_id=f.id
                            JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id
                            AND e.id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=b.id)
                            LIMIT 10 ;");
    }
    return response()->json($data);
}





public function getemployeejobinfo_details(Request $request){
    $data       = [];
    $user_id    = Auth::user()->id;

    if (!empty($request->term)){

        $data = DB::select("SELECT a.id,concat(ifnull(a.employee_name,''), ' | ' ,ifnull(b.employee_code,'') ,' | ',ifnull(e.alis,''),' | ',ifnull(a.contact_number,'')) as text,
                                c.location_name,
                                b.id as hrm_employee_job_info_id,
                                d.depertment_name,
                                d.id as department_id,
                                e.designation_name,
                                b.basic_salary,
                                f.joining_date,
                                f.confirmation_date,
                                g.employment_status as employeestatus_name,
                                h.category_name,
                                i.section_name,
                                b.overtime_status,
                                j.employee_name as manage_by_name,
                                l.shift_name,
                                b.employee_code,
                                b.insurance
                                from hrm_employee a
                                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id and a.active_status=1 and b.employee_activity=1
                                Join hrm_location c On b.hrm_location_id=c.id
                                JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                                Join hrm_designation e On b.hrm_designation_id=e.id
                                Join hrm_employee_joining f on b.hrm_employee_id=f.hrm_employee_id
                                Join hrm_employment_status g On b.hrm_employment_status_id=g.id
                                Join hrm_category h on b.hrm_category_id=h.id
                                Join hrm_section i on b.hrm_section_id=i.id
                                Left join hrm_employee j on b.hrm_manage_by_id=j.id
                                Join hrm_employee_shift k on b.id=k.hrm_employee_job_info_id AND  k.id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=b.id)
                                Join hrm_shift l on k.hrm_shift_id=l.id
                                JOIN user_location m ON b.hrm_location_id = m.hrm_location_id AND m.users_id = $user_id
                                WHERE  a.employee_name  LIKE '%$request->term%' OR b.employee_code LIKE '%$request->term%' ;");

    }else{

        $data = DB::select("SELECT a.id,concat(a.employee_name, ' | ' ,b.employee_code,' | ',e.alis,' | ',a.contact_number)  as text,
                                c.location_name,
                                b.id as hrm_employee_job_info_id,
                                d.depertment_name,
                                d.id as department_id,
                                e.designation_name,
                                b.basic_salary,
                                f.joining_date,
                                f.confirmation_date,
                                g.employment_status as employeestatus_name,
                                h.category_name,
                                i.section_name,
                                b.overtime_status,
                                j.employee_name as manage_by_name,
                                l.shift_name,
                                b.employee_code,
                                b.insurance
                                from hrm_employee a
                                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id and a.active_status=1 and b.employee_activity=1
                                Join hrm_location c On b.hrm_location_id=c.id
                                JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                                Join hrm_designation e On b.hrm_designation_id=e.id
                                Join hrm_employee_joining f on b.hrm_employee_id=f.hrm_employee_id
                                Join hrm_employment_status g On b.hrm_employment_status_id=g.id
                                Join hrm_category h on b.hrm_category_id=h.id
                                Join hrm_section i on b.hrm_section_id=i.id
                                Left join hrm_employee j on b.hrm_manage_by_id=j.id
                                Join hrm_employee_shift k on b.id=k.hrm_employee_job_info_id AND k.id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=b.id)
                                Join hrm_shift l on k.hrm_shift_id=l.id
                                JOIN user_location m ON b.hrm_location_id = m.hrm_location_id AND m.users_id = $user_id
                                LIMIT 10 ;");
    }
    return response()->json($data);
}



public function employeestatuslist(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,employment_status as text FROM hrm_employment_status
                            WHERE employment_status LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,employment_status as text FROM hrm_employment_status
                           ;");
    }
        return response()->json($data);
}


public function locationlist(Request $request){
    $data = [];
    $user_id    = Auth::user()->id;

    if (!empty($request->term)){
    $data = DB::select("SELECT a.id,
                            a.location_name as text,
                            c.short_description,c.id as hrm_device_information_id
                        FROM hrm_location a
                        JOIN user_location b ON a.id = b.hrm_location_id AND b.users_id = $user_id
                        AND a.valid = 1
                        LEFT JOIN hrm_device_information c ON a.hrm_device_information_id=c.id
                        WHERE a.valid = 1 AND a.location_name LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT a.id,
                                a.location_name as text,
                                c.short_description,c.id as hrm_device_information_id
                            FROM hrm_location a
                        JOIN user_location b ON a.id = b.hrm_location_id AND b.users_id = $user_id
                        AND a.valid = 1
                        LEFT JOIN hrm_device_information c ON a.hrm_device_information_id=c.id
                        WHERE a.valid = 1 ;");
    }
        return response()->json($data);
}














public function locationlistdataall(Request $request){
    $data = [];

    if (!empty($request->term)){
    $data = DB::select("SELECT id,location_name as text FROM hrm_location
                        WHERE valid = 1 AND location_name LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,location_name as text FROM hrm_location
                        WHERE valid = 1 ;");
    }
        return response()->json($data);
}



public function sectionlist(Request $request){
    $data = [];
    if (!empty($request->term)){
    $data = DB::select("SELECT id,section_name as text FROM hrm_section
                            WHERE section_name LIKE '%$request->term%';");

    }else{
    $data = DB::select("SELECT id,section_name as text FROM hrm_section
                           ;");
    }
        return response()->json($data);
}

    public function daylist(Request $request){
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,days_name as text FROM hrm_days_name
                            WHERE days_name LIKE '%$request->term%';");

        }else{
            $data = DB::select("SELECT id,days_name as text FROM hrm_days_name
                           ;");
        }
        return response()->json($data);
    }



    public function leavetypelist(Request $request){
    $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT id,leave_type as text FROM hrm_employee_leave_type
            WHERE valid=1 And leave_type LIKE '%$request->term%';");

        }else{
             $data = DB::select("SELECT id,leave_type as text FROM hrm_employee_leave_type
            WHERE  valid=1 ;");
        }
    return response()->json($data);
    }


    // public function leavetypelist(Request $request){
    // $data = [];
    //     if (!empty($request->term)){
    //         $data = DB::select("SELECT id,concat(leave_type, ' || ' , IF(leave_status=1,'Company Policy','Holiday Against Leave')) as text FROM hrm_employee_leave_type
    //         WHERE valid=1 And leave_type LIKE '%$request->term%';");

    //     }else{
    //          $data = DB::select("SELECT id,concat(leave_type, ' || ' , IF(leave_status=1,'Company Policy','Holiday Against Leave')) as text FROM hrm_employee_leave_type
    //         WHERE  valid=1 And leave_type LIKE '%$request->term%';");
    //     }
    // return response()->json($data);
    // }


public function monthlist(Request $request){
    $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,month_name as text FROM hrm_month
        WHERE month_name LIKE '%$request->term%';");

        }else{
        $data = DB::select("SELECT id,month_name as text FROM hrm_month ;");
        }
        return response()->json($data);
    }



public function userlist(Request $request){
    $data = [];
        if (!empty($request->term)){
        $data = DB::select("SELECT id,concat(name, ' | ' ,email) as text FROM users
        WHERE name LIKE '%$request->term%';");

        }else{
        $data = DB::select("SELECT id,concat(name, ' | ' ,email) as text FROM users");
        }
        return response()->json($data);
    }




    public function getDesignationByEmployeeName(Request $request){

        // dd($request);
        $term                    = $request->term;
        $acc_reference_type_id   = $request->acc_reference_type_id;

        $data = DB::select("SELECT id,reference_name  text FROM acc_reference_info WHERE valid = 1 AND acc_reference_type_id = $acc_reference_type_id AND reference_name LIKE '%$term%' ");
        return response()->json($data);
    }




public function plantwithsection_list_data(Request $request){

        $data         = [];
        $user_id      = Auth::user()->id;
        $hrm_plant_id = $request->hrm_plant_id;

        if (!empty($request->term)){

            $data = DB::select("SELECT
                                        a.id,
                                        CONCAT(b.plant_name,' -- ',d.section_name) as  text

                                    FROM
                                        hrm_plant_with_section a
                                            JOIN
                                        hrm_plant b ON a.hrm_plant_id = b.id AND a.valid = 1
                                            AND b.valid = 1 AND b.id=$hrm_plant_id
                                            JOIN
                                        hrm_location c ON b.hrm_location_id = c.id AND c.valid = 1
                                            JOIN
                                        hrm_section d ON a.hrm_section_id=d.id
                                            JOIN
                                        user_location e ON c.id = e.hrm_location_id
                                            AND e.users_id = $user_id
                                         WHERE  b.plant_name  LIKE '%$request->term%' OR d.section_name LIKE '%$request->term%'");

        }else{

            $data = DB::select("SELECT
                                        a.id,
                                        CONCAT(b.plant_name,' -- ',d.section_name) as  text

                                    FROM
                                        hrm_plant_with_section a
                                            JOIN
                                        hrm_plant b ON a.hrm_plant_id = b.id AND a.valid = 1
                                            AND b.valid = 1 AND b.id=$hrm_plant_id
                                            JOIN
                                        hrm_location c ON b.hrm_location_id = c.id AND c.valid = 1
                                            JOIN
                                        hrm_section d ON a.hrm_section_id=d.id
                                            JOIN
                                        user_location e ON c.id = e.hrm_location_id
                                            AND e.users_id = $user_id");


        }

        return response()->json($data);
    }




public function increment_range_list_data(Request $request){

        $data  = [];
        $hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id;





        $data = DB::select("SELECT
                                    aa.id,
                                    CONCAT(aa.increment_amounts,
                                            ' ',
                                            aa.entry_statuss,
                                            ' | ',
                                            aa.number_range) AS text
                                FROM
                                    (SELECT
                                        a.id,
                                            CONCAT(a.number_from, '-', a.number_to) AS number_range,
                                            IF(a.entry_status = 1, CONCAT(a.increment_amount, IF(a.increment_amount_type = 1, '%', 'Tk.')), 'Promoted') AS increment_amounts,
                                            IF(a.entry_status = 1, 'Increment', 'Promotion') AS entry_statuss,
                                            CONCAT(c.description, ' | ', 'From :', b.start_date, '  To :', b.end_date) AS date_year,
                                            b.id AS hrm_kpi_assesment_date_id
                                    FROM
                                        hrm_kpi_increment_range a
                                    JOIN hrm_kpi_assesment_date b ON b.id = a.hrm_kpi_assesment_date_id
                                        AND a.id in (SELECT hrm_kpi_increment_range_id as id FROM hrm_kpi_employee_final_summary  WHERE  hrm_kpi_assesment_date_id=$hrm_kpi_assesment_date_id  GROUP BY hrm_kpi_increment_range_id
                                            UNION ALL
                                            SELECT hrm_kpi_promotion_range_id as id FROM hrm_kpi_employee_final_summary  WHERE  hrm_kpi_assesment_date_id=$hrm_kpi_assesment_date_id  GROUP BY hrm_kpi_promotion_range_id)
                                    JOIN hrm_kpi_assesment_year c ON b.hrm_kpi_assesment_year_id = c.id) aa WHERE aa.hrm_kpi_assesment_date_id=$hrm_kpi_assesment_date_id");

        return response()->json($data);
    }


    // public function job_desctiption_list_data(Request $request)
    // {
    //     $jobDescriptions = DB::table('hrm_cv_job_description')
    //             ->join('hrm_cv_job_description_group', 'hrm_cv_job_description.hrm_cv_job_description_group_id', '=', 'hrm_cv_job_description_group.id')
    //             ->selectRaw(
    //                 'concat_ws(" | ", job_description_group_name, job_description) as text, hrm_cv_job_description.id'
    //             )
    //             ->where('hrm_cv_job_description.is_active', 1)
    //             ->where('job_description', 'like', "%{$request->term}%")
    //             ->get();

    //     return response()->json($jobDescriptions);
    // }

    // public function cv_job_requsition_list_data(Request $request)
    // {
    //     // $jobRequsitions = HRMCVJobRequsition::query()->selectRaw('id, job_title as text')
    //     //     ->where('is_active', 1)
    //     //     ->where('job_title', 'like', "%{$request->term}%")
    //     //     ->get();

    //     // $jobRequsitions = DB::table('hrm_cv_job_requsition')
    //     //     ->join('hrm_depertment', 'hrm_cv_job_requsition.hrm_depertment_id', '=', 'hrm_depertment.id')
    //     //     ->join('hrm_designation', 'hrm_cv_job_requsition.hrm_designation_id', '=', 'hrm_designation.id')
    //     //     ->select('hrm_cv_job_requsition.id', 'CONCAT(hrm_designation.designation_name,' | ',hrm_depertment.depertment_name)   as text')
    //     //     ->where('hrm_cv_job_requsition.is_active',1 )
    //     //     ->where('hrm_cv_job_requsition.status',2 )
    //     //     ->get();

    //       $jobRequsitions =   DB::SELECT("SELECT
    //                                             a.id,
    //                                             CONCAT(c.designation_name,
    //                                                     '  (',
    //                                                     b.depertment_name,')',' Publish:' ,a.published_date,' Ending:',a.ending_date) AS text
    //                                         FROM
    //                                             hrm_cv_job_requsition a
    //                                                 JOIN
    //                                             hrm_depertment b ON a.hrm_depertment_id = b.id
    //                                                 AND a.is_active = 1
    //                                                 AND a.status = 2
    //                                                 JOIN
    //                                             hrm_designation c ON a.hrm_designation_id = c.id");

    //     return response()->json($jobRequsitions);
    // }



    public function job_desctiption_list_data(Request $request)
    {
        $jobDescriptions = DB::table('hrm_cv_job_description')
                ->join('hrm_cv_job_description_group', 'hrm_cv_job_description.hrm_cv_job_description_group_id', '=', 'hrm_cv_job_description_group.id')
                ->selectRaw(
                    'concat_ws(" | ", job_description_group_name, job_description) as text, hrm_cv_job_description.id,hrm_cv_job_description.allow_points_calculation'
                )
                ->where('hrm_cv_job_description.is_active', 1)
                ->where('job_description', 'like', "%{$request->term}%")
                ->get();

        return response()->json($jobDescriptions);
    }

    public function cv_job_requsition_list_data(Request $request)
    {
        // $jobRequsitions = HRMCVJobRequsition::query()->selectRaw('id, job_title as text')
        //     ->where('is_active', 1)
        //     ->where('job_title', 'like', "%{$request->term}%")
        //     ->get();

        // return response()->json($jobRequsitions);

         $jobRequsitions =   DB::SELECT("SELECT
                                                a.id,
                                                CONCAT(c.designation_name,
                                                        '  (',
                                                        b.depertment_name,')',' Publish:' ,a.published_date,' Ending:',a.ending_date) AS text
                                            FROM
                                                hrm_cv_job_requsition a
                                                    JOIN
                                                hrm_depertment b ON a.hrm_depertment_id = b.id
                                                    AND a.is_active = 1
                                                    AND a.status = 2
                                                    JOIN
                                                hrm_designation c ON a.hrm_designation_id = c.id");

        return response()->json($jobRequsitions);
    }

    public function compensationDropList(Request $request)
    {
        $compensations = HRMCVCompensation::query()->selectRaw('id, compensation_name as text')
            ->where('compensation_name', 'like', "%{$request->term}%")
            ->get();

        return response()->json($compensations);
    }

    public function getAllPermissions(Request $request)
    {
        $permissions = DB::table('permissions as a')
            ->leftJoin('permissions as b', 'a.permission_id', '=', 'b.id')
            ->leftJoin('permissions as c', 'b.permission_id', '=', 'c.id')
            ->where('a.isActive', 1)
            ->selectRaw('
                a.id,
                concat_ws(" | ", a.name, b.name, c.name) as text
            ')
            ->having('text', 'like', "{$request->term}%")
            ->get();

        return response()->json($permissions);
    }

    public function salary_holdup_types_list(Request $request)
    {
        $holdup_types = DB::select("SELECT
                                        id, holdup_types_name AS text
                                    FROM
                                        hrm_holdup_types AS a
                                    WHERE
                                        a.is_active = 1");

        return response()->json($holdup_types);
    }
}
