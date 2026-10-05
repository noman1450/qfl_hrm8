<?php

namespace App\Http\Controllers\Filters;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomEmployeeExport;

class FilteringController extends Controller
{
    public function employeeFilter()
    {
        return view('custom_filter.employee.index');
    }

    public function getEmployeeFilter(Request $request)
    {
        // return $request->salary_grade;
        // die();
        $condition = '';

        if (isset($request->filter_name)) {
            $getTableName = DB::table('hrm_custom_filter as a')
                ->join('hrm_custom_filter_master as b', 'a.id', '=', 'b.hrm_custom_filter_id')
                ->select('a.table_name')
                ->where('b.id', $request->filter_name)->first();

            $refIds = " SELECT ref_id FROM hrm_custom_filter_details WHERE hrm_custom_filter_master_id = $request->filter_name";

            switch ($getTableName->table_name) {
                case 'hrm_religion':
                    $condition = " AND a.hrm_religion_id IN ($refIds)";
                    break;
                case 'hrm_blood_group':
                    $condition = " AND a.hrm_blood_group_id IN ($refIds)";
                    break;
                case 'hrm_marital_status':
                    $condition = " AND a.hrm_marital_status_id IN ($refIds)";
                    break;
                case 'hrm_depertment':
                    $condition = " AND b.hrm_depertment_id IN ($refIds)";
                    break;
                case 'hrm_designation':
                    $condition = " AND b.hrm_designation_id IN ($refIds)";
                    break;
                case 'hrm_category':
                    $condition = " AND b.hrm_category_id IN ($refIds)";
                    break;
                case 'hrm_plant':
                    $condition = " AND b.hrm_plant_id IN ($refIds)";
                    break;
                case 'hrm_section':
                    $condition = " AND b.hrm_section_id IN ($refIds)";
                    break;
                case 'hrm_location':
                    $condition = " AND b.hrm_location_id IN ($refIds)";
                    break;
                case 'hrm_shift':
                    $condition = " AND b.id IN (SELECT
                                        aa.hrm_employee_job_info_id
                                    FROM
                                        hrm_employee_shift aa
                                        WHERE aa.hrm_shift_id IN ($refIds) AND aa.hrm_employee_job_info_id = b.id
                                        AND id IN (
                                            SELECT MAX(id) AS id FROM hrm_employee_shift
                                            WHERE aa.hrm_employee_job_info_id = hrm_employee_job_info_id)) ";
                    break;
            }
        }

        if ($request->activity == 1) {
            $condition .= " AND b.employee_activity = 1";
        }

        if ($request->activity == 0) {
            $condition .= " AND b.id in (Select  hrm_employee_job_info_id FROM hrm_employee_resignation WHERE resingnation_status = 2) ";
        }


        if ($designation_id = $request->designation_id) {
            $condition .= " AND b.hrm_designation_id = {$designation_id}";
        }

        if ($location_id = $request->location_id) {

            if($request->location_id=='999'){
                $condition .= " AND b.hrm_location_id in (SELECT id FROM hrm_location WHERE location_type=3 AND valid = 1) ";

            }else{
                $condition .= " AND b.hrm_location_id = {$location_id}";
            }

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

        if ($employee_id = $request->employee_id) {
            $condition .= " AND b.hrm_employee_id = {$employee_id}";
        }

        if ($employee_type_id = $request->employee_type_id) {
            $condition .= " AND b.hrm_employment_status_id = {$employee_type_id}";
        }

        if ($payment_mode_id = $request->payment_mode_id) {
            $condition .= " AND g.payment_mode = {$payment_mode_id}";
        }

        if ($bank_id = $request->bank_id) {
            $condition .= " AND g.hrm_bank_id = {$bank_id}";
        }

        if ($salary_grade = $request->salary_grade) {
            $condition .= " AND ggg.id = {$salary_grade}";
        }

        $dateRangeCond = '';

        if($request->boolean('date_range')) {
            $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
            $date_to = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

            if ($request->joining == 'joining_date') {
                $dateRangeCond .= " AND f.joining_date between '$date_from' AND '$date_to' ";
            } else {
                $dateRangeCond .= " AND f.confirmation_date between '$date_from' AND '$date_to' ";
            }
        }

        $employees = DB::select("SELECT
                -- a.id,
                LPAD(a.id, 5, '0') as Unique_Code,
                a.employee_name employee_name,
                b.employee_code,
                a.email,
                b.official_email,
                b.official_contact_no,
                a.contact_number,
                a.permanent_address,
                a.present_address,
                CASE
                    WHEN a.gender = 1 THEN 'Male'
                    ELSE 'Female'
                END gender,
                a.dob AS date_of_birth,
                a.nid,
                a.tin,
                a.father_name,
                a.mother_name,
                b.basic_salary,
                b.insurance,
                b.overtime_status,
                ggg.grade_name,
                CASE
                    WHEN g.payment_mode = 1 THEN 'Cash'
                    ELSE 'Bank'
                END payment_mode,
                CONCAT(h.bank_name, ' (', h.short_name, ')') AS bank_name,
                g.account_no,
                (SELECT location_name FROM hrm_location WHERE id = b.hrm_location_id) AS job_placement,
                (SELECT depertment_name FROM hrm_depertment WHERE id = b.hrm_depertment_id) AS department_name,
                (SELECT designation_name FROM hrm_designation WHERE id = b.hrm_designation_id) AS designation_name,

                    0 as hrm_shift_id,
                    '' as shift_name,


                date_format(f.joining_date, '%d-%m-%Y') joining_date,
                date_format(f.confirmation_date, '%d-%m-%Y') confirmation_date,
                CONCAT(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Year(s) ', MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12), ' Month(s) ') AS jobduration,

                (SELECT category_name FROM hrm_category WHERE id = b.hrm_category_id) AS category_name,
                (SELECT section_name FROM hrm_section WHERE id = b.hrm_section_id) AS sub_department,
                (SELECT employee_name FROM hrm_employee WHERE id = b.hrm_manage_by_id) AS manage_by_name,
                (SELECT religion FROM hrm_religion WHERE id = a.hrm_religion_id) AS religion,
                (SELECT marital_status FROM hrm_marital_status WHERE id = a.hrm_marital_status_id) AS marital_status,
                (SELECT blood_group FROM hrm_blood_group WHERE id = a.hrm_blood_group_id) AS blood_group,
                (SELECT education_name FROM hrm_education WHERE id = a.hrm_education_id) AS education_name,
                (SELECT plant_name FROM hrm_plant WHERE id = b.hrm_plant_id) AS plant_name,
                (SELECT employment_status FROM hrm_employment_status WHERE id = b.hrm_employment_status_id) AS employment_status,
                (SELECT max(start_date)  FROM hrm_employee_activity as pro JOIN hrm_employee_job_info as _b ON _b.id = pro.hrm_employee_job_info_id  WHERE _b.hrm_employee_id = b.hrm_employee_id AND pro.hrm_employee_activity_status_id = 3 ) as last_promotion_date,
                (SELECT max(start_date)  FROM hrm_employee_activity as pro JOIN hrm_employee_job_info as _b ON _b.id = pro.hrm_employee_job_info_id  WHERE _b.hrm_employee_id = b.hrm_employee_id AND pro.hrm_employee_activity_status_id = 4 ) as last_increment_date,


                @hrm_probation_period_id:=(
                    SELECT
                        hrm_probation_period_id
                    FROM
                        hrm_employee_probation
                    WHERE
                        hrm_employee_id = a.id) hrm_probation_period_id,

                (SELECT period FROM hrm_probation_period WHERE id = @hrm_probation_period_id) period,

                CASE
                    WHEN b.employee_activity = 1 THEN 'Active'
                    ELSE 'Inactive'
                END active_status,
                (Select  effective_date FROM hrm_employee_resignation WHERE hrm_employee_resignation.hrm_employee_job_info_id = b.id LIMIT 1) as resign_date
            FROM
                hrm_employee a
                    JOIN
                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                    JOIN
                hrm_employee_salary g ON g.hrm_employee_job_info_id = b.id
                    JOIN
                hrm_salary_grade_master gg ON  g.hrm_salary_grade_master_id = gg.id
                    join
                hrm_salary_grade ggg ON gg.hrm_salary_grade_id = ggg.id
                    $condition
                    JOIN
                hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                    $dateRangeCond
                    JOIN
                (SELECT @hrm_shift_id := 0) AS hrm_shift_id
                    JOIN
                (SELECT @hrm_probation_period_id := 0) AS hrm_probation_period_id

                LEFT JOIN hrm_bank h ON g.hrm_bank_id = h.id
                AND h.status = 1
        ");
            // dd($employees);

        return json_encode(array('data' => $employees));




                // -- @hrm_shift_id:=(
                // --     SELECT
                // --         aa.hrm_shift_id
                // --     FROM
                // --         hrm_employee_shift aa
                // --         WHERE aa.hrm_employee_job_info_id = b.id
                // --         AND id IN (
                // --             SELECT MAX(id) AS id FROM hrm_employee_shift
                // --             WHERE aa.hrm_employee_job_info_id = hrm_employee_job_info_id)) hrm_shift_id,

                // -- (SELECT shift_name FROM hrm_shift WHERE id = @hrm_shift_id) AS shift_name,
                // 0 as hrm_shift_id,
                // '' as shift_name,




    }

    public function employeeExport()
    {
        ob_end_clean(); ob_start();

        $today = date('d_m_Y');

        return Excel::download(
            new CustomEmployeeExport(
                request()->filter_name_id,
                request()->designation_id,
                request()->department_id,
                request()->activity,
                request()->category_id,
                request()->section_id,
                request()->employee_id,
                request()->employee_type_id,
                request()->location_id,

                // request()->boolean('date_range'),
                // request()->date_from,
                // request()->date_to,
                // request()->joining,
            ),

            "employee_$today.xlsx"
        );
    }
}
