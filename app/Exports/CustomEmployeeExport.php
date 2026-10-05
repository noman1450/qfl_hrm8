<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomEmployeeExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
{
    public
        $filter_name_id,
        $designation_id,
        $department_id,
        $activity,
        $category_id,
        $section_id,
        $employee_id,
        $employee_type_id,
        $location_id;

        // $date_range,
        // $date_from,
        // $date_to,
        // $joining;

    public function __construct(
        $filter_name_id,
        $designation_id,
        $department_id,
        $activity,
        $category_id,
        $section_id,
        $employee_id,
        $employee_type_id,
        $location_id
    ) {
        $this->filter_name_id = $filter_name_id;
        $this->designation_id = $designation_id;
        $this->department_id = $department_id;
        $this->activity = $activity;

        $this->category_id = $category_id;
        $this->section_id = $section_id;
        $this->employee_id = $employee_id;
        $this->employee_type_id = $employee_type_id;
        $this->location_id = $location_id;

        // $this->date_range = $date_range;
        // $this->date_from = $date_from;
        // $this->date_to = $date_to;
        // $this->joining = $joining;
    }

    public function collection()
    {
        $condition = '';

        if (isset($this->filter_name_id)) {
            $getTableName = DB::table('hrm_custom_filter as a')
                ->join('hrm_custom_filter_master as b', 'a.id', '=', 'b.hrm_custom_filter_id')
                ->select('a.table_name')
                ->where('b.id',$this->filter_name_id)->first();

            $refIds = "SELECT ref_id FROM hrm_custom_filter_details WHERE hrm_custom_filter_master_id = $this->filter_name_id";

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

        // if ($this->activity != 2 && ! is_null($this->activity)) {
        //     $condition .= " AND b.employee_activity = {$this->activity} ";
        // }


        if($this->activity == 1){
            $condition .= " AND b.employee_activity = 1";
        }

        if($this->activity == 0){
            $condition .= " AND b.id in (Select  hrm_employee_job_info_id FROM hrm_employee_resignation WHERE resingnation_status=2) ";
        }


        if ($designation_id = $this->designation_id) {
            $condition .= " AND b.hrm_designation_id = {$designation_id}";
        }

        if ($location_id = $this->location_id) {
            $condition .= " AND b.hrm_location_id = {$location_id}";
        }


        if ($department_id = $this->department_id) {
            $condition .= " AND b.hrm_depertment_id = {$department_id}";
        }

        if ($category_id = $this->category_id) {
            $condition .= " AND b.hrm_category_id = {$category_id}";
        }

        if ($section_id = $this->section_id) {
            $condition .= " AND b.hrm_section_id = {$section_id}";
        }

        if ($employee_id = $this->employee_id) {
            $condition .= " AND b.hrm_employee_id = {$employee_id}";
        }

        if ($employee_type_id = $this->employee_type_id) {
            $condition .= " AND b.hrm_employment_status_id = {$employee_type_id}";
        }

        $dateRangeCond = '';

        // if($this->date_range) {
        //     $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $this->date_from)));
        //     $date_to = date('Y-m-d', strtotime(str_replace('/', '-', $this->date_to)));

        //     if ($this->joining == 'joining_date') {
        //         $dateRangeCond .= " AND f.joining_date between '$date_from' AND '$date_to' ";
        //     } else {
        //         $dateRangeCond .= " AND f.confirmation_date between '$date_from' AND '$date_to' ";
        //     }
        // }

        $employees = DB::select("SELECT
                LPAD(a.id, 5, '0') as Unique_Code,
                -- a.id,
                a.employee_name employee_name,
                b.employee_code,
                date_format(f.joining_date, '%d-%m-%Y') joining_date,
                (SELECT depertment_name FROM hrm_depertment WHERE id = b.hrm_depertment_id) AS department_name,
                (SELECT designation_name FROM hrm_designation WHERE id = b.hrm_designation_id) AS designation_name,
                b.official_email,
                b.official_contact_no,
                a.contact_number,
                b.basic_salary,
                (SELECT location_name FROM hrm_location WHERE id = b.hrm_location_id) AS job_placement,
                (SELECT category_name FROM hrm_category WHERE id = b.hrm_category_id) AS category_name,
                (SELECT section_name FROM hrm_section WHERE id = b.hrm_section_id) AS sub_department,
                (SELECT employee_name FROM hrm_employee WHERE id = b.hrm_manage_by_id) AS manage_by_name,
                (SELECT blood_group FROM hrm_blood_group WHERE id = a.hrm_blood_group_id) AS blood_group,
                (SELECT religion FROM hrm_religion WHERE id = a.hrm_religion_id) AS religion,
                (SELECT plant_name FROM hrm_plant WHERE id = b.hrm_plant_id) AS plant_name,
                (SELECT employment_status FROM hrm_employment_status WHERE id = b.hrm_employment_status_id) AS employment_status,
                date_format(f.confirmation_date, '%d-%m-%Y') confirmation_date,
                CONCAT(TIMESTAMPDIFF(YEAR, f.confirmation_date, CURDATE()), ' Year(s) ', MOD(TIMESTAMPDIFF(MONTH, f.confirmation_date, CURDATE()), 12), ' Month(s) ') AS jobduration,
                a.email,
                b.insurance,
                b.overtime_status,
                CASE
                    WHEN a.gender = 1 THEN 'Male'
                    ELSE 'Female'
                END gender,
                date_format(a.dob, '%d-%m-%Y') date_of_birth,
                @hrm_shift_id:=(
                    SELECT
                        aa.hrm_shift_id
                    FROM
                        hrm_employee_shift aa
                        WHERE aa.hrm_employee_job_info_id = b.id
                        AND id IN (
                            SELECT MAX(id) AS id FROM hrm_employee_shift
                            WHERE aa.hrm_employee_job_info_id = hrm_employee_job_info_id)) hrm_shift_id,

                (SELECT shift_name FROM hrm_shift WHERE id = @hrm_shift_id) AS shift_name,
                a.permanent_address,
                a.present_address,
                a.nid,
                a.tin,
                a.father_name,
                a.mother_name,
                (SELECT marital_status FROM hrm_marital_status WHERE id = a.hrm_marital_status_id) AS marital_status,
                (SELECT education_name FROM hrm_education WHERE id = a.hrm_education_id) AS education_name,


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
                (Select  effective_date FROM hrm_employee_resignation WHERE hrm_employee_resignation.hrm_employee_job_info_id = b.id LIMIT 1)  as resign_date
            FROM
                hrm_employee a
                    JOIN
                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                    $condition
                    JOIN
                hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                    $dateRangeCond
                    JOIN
                (SELECT @hrm_shift_id:=0) AS hrm_shift_id
                    JOIN
                (SELECT @hrm_probation_period_id:=0) AS hrm_probation_period_id
        ");

        return collect($employees);
    }

    public function map($employee): array
    {
        return [
            $employee->Unique_Code,
            $employee->employee_name,
            $employee->employee_code,
            $employee->joining_date,
            $employee->department_name,
            $employee->designation_name,
            $employee->official_email,
            $employee->official_contact_no,
            $employee->contact_number,
            $employee->basic_salary,
            $employee->job_placement,
            $employee->category_name,
            $employee->sub_department,
            $employee->manage_by_name,
            $employee->blood_group,
            $employee->religion,
            $employee->plant_name,
            $employee->employment_status,
            $employee->confirmation_date,
            $employee->jobduration,
            $employee->email,
            $employee->insurance,
            $employee->overtime_status,
            $employee->gender,
            $employee->date_of_birth,
            $employee->shift_name,
            $employee->permanent_address,
            $employee->present_address,
            $employee->nid,
            $employee->tin,
            $employee->father_name,
            $employee->mother_name,
            $employee->marital_status,
            $employee->education_name,
            $employee->period,
            $employee->active_status,
            $employee->resign_date

        ];
    }

    public function headings(): array
    {
        return [
            'Unique Code',
            'Employee Name',
            'Employee Code',
            'Join Date',
            'Department Name',
            'Designation Name',
            'Official Email',
            'Official Contact No',
            'Contact Number',
            'Gross Salary',
            'Job Placement',
            'Category Name',
            'Sub Department',
            'Manage By',
            'Blood Group',
            'Religion',
            'Plant Name',
            'Employment Status',
            'Confirm Date',
            'Job Duration',
            'Email',
            'Insurance',
            'Overtime Status',
            'Gender',
            'Date of Birth',
            'Shift Name',
            'Permanent Address',
            'Present Address',
            'NID',
            'TIN',
            'Father Name',
            'Mother Name',
            'Marital Status',
            'Education Name',
            'Period',
            'Activity',
            'Resign Date'
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 13] ]
        ];
    }
}

