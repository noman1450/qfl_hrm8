<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ResignApprovedExport implements FromCollection, WithHeadings, WithStyles
{
    public $locationName, $locationId, $date_from, $date_to , $category;

    public function __construct($locationName, $locationId, $date_from, $date_to, $category,$filter)
    {
        $this->locationName = $locationName;
        $this->locationId = $locationId;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->category = $category;
        $this->filter = $filter;
    }

    public function collection()
    {
        $locationId = $this->locationId;
        $category = $this->category;
        $filter = $this->filter;

        $location_con   = '';
        if(!empty($locationId)){
            $location_con = " and b.hrm_location_id in ($locationId) ";    
        }

        if(!empty($this->category)){
            $location_con .= " and b.hrm_category_id in ($category) ";    
        }



        $resignApproveData = DB::select("
            select
                c.id as unique_id,
                concat(c.employee_name, ' | ', ifnull(b.employee_code,'')) as employee_name,
                d.depertment_name,
                e.designation_name,
                cc.joining_date,
                cc.confirmation_date,
                a.effective_date as resign_date,
                loc.location_name
            from
                hrm_employee_resignation as a
                    join
                hrm_employee_job_info as b on b.id = a.hrm_employee_job_info_id
                        and a.resingnation_status in (2, 4)
                        and a.valid = 1
                    $location_con      
                    join
                hrm_employee c on c.id = b.hrm_employee_id
                    join
                hrm_depertment as d on b.hrm_depertment_id = d.id
                    join
                hrm_designation e on b.hrm_designation_id = e.id
                    JOIN
                hrm_employee_joining cc ON c.id = cc.hrm_employee_id
                    JOIN 
                hrm_location loc ON b.hrm_location_id = loc.id
                    $filter

        ");



        return collect($resignApproveData);
    }

    public function headings(): array
    {
        $dateFrom = date('d-M-Y', strtotime($this->date_from));
        $dateTo = date('d-M-Y', strtotime($this->date_to));

        $locationName = "Location: $this->locationName";
        $dateRange = "From $dateFrom To $dateTo";

        return [
            [$locationName],
            [$dateRange],
            [
                "Unique Code",
                "Employee",
                "Department",
                "Designation",
                "Joining Date",
                "Confirm Date",
                "Resign Date",
                "Location",
            ]
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14] ],
            2 => ['font' => ['bold' => true, 'size' => 11] ],
            3 => ['font' => ['bold' => true, 'size' => 10] ],
        ];
    }
}

