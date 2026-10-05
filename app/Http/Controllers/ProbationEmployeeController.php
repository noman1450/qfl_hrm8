<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProbationEmployeeController extends Controller
{
    public function probationEmployee()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('probation_employee.probation_employee_index');
        // ->with('default_user_location',  $default_user_location) ;
    }

    public function probationEmployeeList(Request $request)
    {

        $condition = '';

        if($request->location==null){
            $location = "";
        }else{
            $location = "AND b.hrm_location_id=$request->location";
        }

        if ($designation_id = $request->designation_id) {
            $condition .= " AND b.hrm_designation_id = {$designation_id}";
        }

        if ($department_id = $request->department_id) {
            $condition .= " AND b.hrm_depertment_id = {$department_id}";
        }

        if ($category_id = $request->category_id) {
            $condition .= " AND b.hrm_category_id = {$category_id}";
        }

        if ($section_id = $request->section_id) {
            $condition .= " AND b.hrm_section_id = {$section_id}";
        }


        $user_id= Auth::user()->id;


        $currentemployee = DB::select("SELECT
                                            a.id,
                                            LPAD(a.id, 5, '0') as Unique_Code,
                                            CONCAT(a.employee_name, ' | ', b.employee_code) AS employee_name,
                                            c.location_name,
                                            d.depertment_name,
                                            e.alis AS designation_name,
                                            e.priority,
                                            a.contact_number,
                                            DATE_FORMAT(f.joining_date, '%d-%m-%Y') as joining_date,
                                            a.Images,
                                            b.id AS hrm_employee_job_info_id,
                                            DATE_FORMAT(DATE_ADD(f.joining_date, INTERVAL h.period MONTH), '%d-%m-%Y') as probable_date,
                                            DATEDIFF(DATE_ADD(f.joining_date, INTERVAL h.period MONTH), CURRENT_DATE) AS remaining_days
                                        FROM
                                            hrm_employee a
                                                JOIN
                                            hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                                AND a.active_status = 1
                                                AND b.employee_activity = 1
                                                AND b.hrm_employment_status_id = 1
                                                $condition
                                                $location
                                                JOIN
                                            hrm_location c ON b.hrm_location_id = c.id
                                                JOIN
                                            hrm_depertment d ON b.hrm_depertment_id = d.id
                                                JOIN
                                            hrm_designation e ON b.hrm_designation_id = e.id
                                                JOIN
                                            hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                                JOIN
                                            hrm_employee_probation g ON g.hrm_employee_id = a.id
                                                JOIN
                                            hrm_probation_period h ON h.id = g.hrm_probation_period_id
                                                JOIN
                                            user_location i ON b.hrm_location_id = i.hrm_location_id
                                                AND i.users_id = $user_id
                                                AND DATE_ADD(DATE_ADD(f.joining_date,
                                                    INTERVAL h.period MONTH),
                                                INTERVAL - 30 DAY) < CURRENT_DATE");

        return datatables()->of($currentemployee)
        ->addColumn('Link', function ($currentemployee) {
            return '<a href="' . url('/probation/' . $currentemployee->id) . '" class="btn btn-info btn-sm btn-flat">
                        <span class="glyphicon glyphicon-ok"></span> Make Confirm
                    </a>';
         })
         ->addColumn('RemainingDays', function ($currentemployee) {
            $days = $currentemployee->remaining_days;
            $color = $days < 0 ? 'red' : 'green';

            return '<span style="color:' . $color . ';">' . $days . ' days</span>';

         })
        ->setRowId('id')
        ->rawColumns(['Link', 'RemainingDays'])
        ->make(true);
    }
}
