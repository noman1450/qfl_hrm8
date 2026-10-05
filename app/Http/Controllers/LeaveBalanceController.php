<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use Illuminate\Support\Facades\DB;
use Datatables;
use Crypt;
use Validator;
use Config;
use Session;

use App\Models\HrmHolidayAgainstLeave;
use Barryvdh\DomPDF\Facade as PDF;

class LeaveBalanceController extends Controller
{


    function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {

        // $month = DB::select("SELECT id,month_name FROM hrm_month");
         $user_id = auth()->id();
        if(request()->ajax()){
            // dd(request()->all());
            $condition = "";
            $location = "";
             if(request()->location==null){
                $location = "";
            }else{
                $location = "AND d.hrm_location_id=".request()->location;
            }
            if(request()->employee_name){
                $condition = 'AND b.id = '.request()->employee_name;
            }
            if ($designation_id = request()->designation_id) {
            $condition .= " AND d.hrm_designation_id = {$designation_id}";
            }

            if ($department_id = request()->department_id) {
                $condition .= " AND d.hrm_depertment_id = {$department_id}";
            }

            if ($category_id = request()->category_id) {
                $condition .= " AND d.hrm_category_id = {$category_id}";
            }

            if ($section_id = request()->section_id) {
                $condition .= " AND d.hrm_section_id = {$section_id}";
            }

            if ($employee_type_id = request()->employee_type_id) {
                $condition .= " AND d.hrm_employment_status_id = {$employee_type_id}";
            }


            $leave_year = request()->leave_year;



            $hrm_leave_years =  DB::table('hrm_leave_years')->where('id',$leave_year)->first();
            $date_from       = $hrm_leave_years->date_from;
            $date_to         = $hrm_leave_years->date_to;
            // dd($hrm_leave_years);

                $dataset = DB::select("SELECT
                        b.id AS hrm_employee_id,
                        CONCAT(b.employee_name, ' | ', d.employee_code) AS employee_name,

                        SUM(
                            CASE
                                WHEN (a.date_from >= :date_from AND a.date_to <= :date_to) THEN
                                    CASE
                                        WHEN a.duration = 1 THEN 0.50
                                        WHEN a.duration = 3 THEN 0.25
                                        ELSE (DATEDIFF(a.date_to, a.date_from) + 1)
                                    END
                                ELSE 0
                            END
                        ) AS used,

                        (
                            SELECT SUM(leave_days)
                            FROM hrm_employee_leave_type AS aa
                            JOIN hrm_leave_type_year bb
                                ON aa.id = bb.hrm_employee_leave_type_id
                            WHERE hrm_leave_years_id = :leave_year
                        ) AS total_leave,

                        (
                            (
                                SELECT SUM(leave_days)
                                FROM hrm_employee_leave_type AS aa
                                JOIN hrm_leave_type_year bb
                                    ON aa.id = bb.hrm_employee_leave_type_id
                                WHERE hrm_leave_years_id = :leave_year
                            )
                            -
                            SUM(
                                CASE
                                    WHEN (a.date_from >= :date_from AND a.date_to <= :date_to) THEN
                                        CASE
                                            WHEN a.duration = 1 THEN 0.50
                                            WHEN a.duration = 3 THEN 0.25
                                            ELSE (DATEDIFF(a.date_to, a.date_from) + 1)
                                        END
                                    ELSE 0
                                END
                            )
                        ) AS balance,

                        h.depertment_name,
                        i.designation_name

                    FROM  hrm_employee b
                    LEFT JOIN hrm_leave_application a
                        ON b.id = a.hrm_employee_id
                        AND b.active_status = 1
                    LEFT JOIN hrm_employee_leave_type c
                        ON a.hrm_employee_leave_type_id = c.id
                    JOIN hrm_employee_job_info d
                        ON b.id = d.hrm_employee_id
                        AND d.employee_activity = 1
                    $location
                    $condition
                    JOIN user_location e
                        ON d.hrm_location_id = e.hrm_location_id
                        AND e.users_id = :user_id
                    LEFT JOIN hrm_leave_approve f
                        ON f.hrm_leave_application_id = a.id
                        AND f.action_type = 1
                        AND f.forward = 2
                    LEFT JOIN users g
                        ON f.users_id = g.id
                    JOIN hrm_depertment h
                        ON h.id = d.hrm_depertment_id
                    JOIN hrm_designation i
                        ON d.hrm_designation_id = i.id
                    GROUP BY b.id
                ", [
                    'date_from' => $date_from,
                    'date_to'   => $date_to,
                    'leave_year' => $leave_year,
                    'user_id'   => $user_id,
                ]);

            // dd($dataset);



            return json_encode(array('data' => $dataset));
        }
        $leave_years = DB::select("SELECT id,leave_year FROM hrm_leave_years WHERE active_status=1 order by leave_year desc");
        // dd($leave_years);

        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");
       return view('leave_balance.index', [
            'default_user_location' => $default_user_location,
            'leave_years' => $leave_years
        ]);

    }


    public function getLeaveBalance(Request $r){

            $date = Date('Y-m-d');
            $hrm_leave_years = DB::table('hrm_leave_years')
                ->where('date_from', '<=', $date)
                ->where('date_to', '>=', $date)
                ->first();

            $date_from       = $hrm_leave_years->date_from;
            $date_to         = $hrm_leave_years->date_to;



              $data = DB::select("SELECT
                        b.id AS hrm_employee_id,
                        b.employee_name,

                        -- Used leaves



                        (
                            COALESCE((
                                SELECT SUM(leave_days)
                                FROM hrm_employee_leave_type AS aa
                                JOIN hrm_leave_type_year bb
                                    ON aa.id = bb.hrm_employee_leave_type_id
                                WHERE hrm_leave_years_id = :leave_year
                                AND aa.id = :leave_type
                            ),0)
                            -
                            COALESCE(SUM(
                                CASE
                                    WHEN (a.date_from >= :date_from AND a.date_to <= :date_to) THEN
                                        CASE
                                            WHEN a.duration = 1 THEN 0.50
                                            WHEN a.duration = 3 THEN 0.25
                                            ELSE (DATEDIFF(a.date_to, a.date_from) + 1)
                                        END
                                    ELSE 0
                                END
                            ), 0)
                        ) AS balance



                    FROM hrm_employee b
                    JOIN hrm_employee_job_info d
                        ON b.id = d.hrm_employee_id
                        AND d.employee_activity = 1
                    JOIN user_location e
                        ON d.hrm_location_id = e.hrm_location_id
                        AND e.users_id = :user_id
                    LEFT JOIN hrm_leave_application a
                        ON b.id = a.hrm_employee_id
                        AND a.hrm_employee_leave_type_id = :leave_type

                    LEFT JOIN hrm_leave_approve f
                        ON f.hrm_leave_application_id = a.id
                        AND f.action_type = 1
                        AND f.forward = 2
                    JOIN hrm_depertment h
                        ON h.id = d.hrm_depertment_id
                    JOIN hrm_designation i
                        ON i.id = d.hrm_designation_id
                    WHERE b.active_status = 1
                    AND b.id = :employee_id
                    GROUP BY b.id, b.employee_name, d.employee_code, h.depertment_name, i.designation_name
                ", [
                    'employee_id' => $r->employee_id,
                    'leave_type'  => $r->leave_type,
                    'date_from'   => $date_from,
                    'date_to'     => $date_to,
                    'leave_year'  => $hrm_leave_years->id,
                    'user_id'     => auth()->id(),
                ])[0];

                return response()->json($data);
                dd($data);

    }

    public function show($id, $leave_year_id) {

        $data = $this->monthlyLeaveDetailsQuery($id, $leave_year_id);

        // dd($data);

        return view('leave_balance.show', $data);

    }
    public function downloadPdf($id, $leave_year_id)
    {
        $data = $this->monthlyLeaveDetailsQuery($id, $leave_year_id);

        $data['company_name']    = Config::get('configaration.company_name');
        $data['company_address'] = Config::get('configaration.company_address');

        // return view('leave_balance.pdf', $data);
        $pdf = PDF::loadView('leave_balance.pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('leave-balance-'.$id.'.pdf');
    }
    static function monthlyLeaveDetailsQuery($id,  $leave_year_id)
    {
        $data = [];
        $employee = DB::select("SELECT
                a.id,
                a.employee_name,
                concat('+880',a.contact_number) as contact_number,
                a.email,
                d.depertment_name,
                e.designation_name,
                f.joining_date,
                b.employee_code,
                k.location_name,
                f.confirmation_date
            FROM
                hrm_employee a
                    JOIN
                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                    AND a.active_status = 1
                    AND b.employee_activity = 1
                    AND a.id = $id
                    JOIN
                hrm_depertment d ON b.hrm_depertment_id = d.id
                    JOIN
                hrm_designation e ON b.hrm_designation_id = e.id
                    JOIN
                hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                    JOIN
                hrm_employment_status g ON b.hrm_employment_status_id = g.id
                    JOIN
	            hrm_location k ON b.hrm_location_id = k.id

        ");

        // dd($employee);

        $data['employee'] = count($employee) > 0 ? $employee[0] : null;

        $hrm_leave_years =  DB::table('hrm_leave_years')->where('id',$leave_year_id)->first();
        $date_from       = $hrm_leave_years->date_from;
        $date_to         = $hrm_leave_years->date_to;

        $data['hrm_leave_years'] = $hrm_leave_years;

        $employeeLeaveDetails = DB::select("SELECT
                        :employee_id AS hrm_employee_id,
                        c.id as leave_type_id,
                        c.leave_type,

                        CAST(COALESCE(SUM(
                            CASE
                                WHEN (a.date_from >= :date_from AND a.date_to <= :date_to) THEN
                                    CASE
                                        WHEN a.duration = 1 THEN 0.50
                                        WHEN a.duration = 3 THEN 0.25
                                        ELSE (DATEDIFF(a.date_to, a.date_from) + 1)
                                    END
                                ELSE 0
                            END
                        ),0) AS DECIMAL(10,2)) AS used,

                        CAST(lt.total_leave AS DECIMAL(10,2)) AS total_leave,

                        CAST(
                            (lt.total_leave - COALESCE(SUM(
                                CASE
                                    WHEN (a.date_from >= :date_from AND a.date_to <= :date_to) THEN
                                        CASE
                                            WHEN a.duration = 1 THEN 0.50
                                            WHEN a.duration = 3 THEN 0.25
                                            ELSE (DATEDIFF(a.date_to, a.date_from) + 1)
                                        END
                                    ELSE 0
                                END
                            ),0))
                        AS DECIMAL(10,2)) AS balance

                    FROM hrm_employee_leave_type c

                    JOIN (
                        SELECT
                            aa.id,
                            SUM(bb.leave_days) as total_leave
                        FROM hrm_employee_leave_type aa
                        JOIN hrm_leave_type_year bb
                            ON aa.id = bb.hrm_employee_leave_type_id
                        WHERE bb.hrm_leave_years_id = :leave_year
                        GROUP BY aa.id
                    ) lt ON lt.id = c.id


                    LEFT JOIN hrm_leave_application a
                        ON a.hrm_employee_leave_type_id = c.id
                        AND a.hrm_employee_id = :employee_id

                    LEFT JOIN hrm_leave_approve f
                        ON f.hrm_leave_application_id = a.id
                        AND f.action_type = 1
                        AND f.forward = 2

                    GROUP BY c.id, c.leave_type, lt.total_leave
                ", [
                    'date_from'   => $date_from,
                    'date_to'     => $date_to,
                    'leave_year'  => $leave_year_id,
                    'employee_id' => $id,
                ]);

        $data['employeeLeaveDetails'] = $employeeLeaveDetails ?? [];

        $details = DB::select("SELECT
                b.id,
                c.leave_type,
                (CASE
                    WHEN a.duration = 1 THEN '.50'
                    WHEN a.duration = 3 THEN '.25'
                    ELSE(DATEDIFF(a.date_to, a.date_from) + 1)
                END) AS days,
                a.comment,
                date_format(a.date_from, '%d %b, %Y') date_from,
                date_format(a.date_to, '%d %b, %Y') date_to,
                CASE WHEN a.payment_mode = 1 THEN 'With Pay' ELSE 'Without Pay' END AS payment_mode,
                f.comment AS boss_note
            FROM
                hrm_leave_application a
                    JOIN
                hrm_employee b ON b.id = a.hrm_employee_id
                    AND b.active_status = 1
                     AND a.hrm_leave_years_id = $leave_year_id
                    JOIN
                hrm_employee_leave_type c ON a.hrm_employee_leave_type_id = c.id
                    JOIN
                hrm_employee_job_info d ON a.hrm_employee_id = d.hrm_employee_id
                    AND d.employee_activity = 1
                    JOIN
                user_location e ON d.hrm_location_id = e.hrm_location_id
                    JOIN
                hrm_leave_approve f ON f.hrm_leave_application_id = a.id
                    AND a.hrm_employee_id = $id
                    AND f.action_type = 1
            GROUP BY a.id
            ORDER BY a.id DESC
        ");
        // dd($details);
        $data['details'] = $details ?? [];

        return $data;
    }



}
