<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
      //   $query = DB::SELECT("SELECT id,title,link_address FROM hrm_favourite_link WHERE users_id = $user_id ");

        $probation_employee = DB::SELECT("SELECT count(a.id) as probation_employee
                                from  hrm_employee a
                                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id AND  a.active_status=1 AND b.employee_activity=1 AND hrm_employment_status_id=1
                                Join hrm_location c On b.hrm_location_id=c.id
                                JOIN user_location i ON b.hrm_location_id = i.hrm_location_id AND i.users_id = $user_id ");

        $dashboard_data = DB::SELECT("SELECT count(DISTINCT b.hrm_depertment_id) as department,count(DISTINCT b.hrm_category_id) as count_category,count(a.id) as total_employee, count(DISTINCT b.hrm_location_id) as location, count(DISTINCT b.hrm_designation_id) as designation
                                from  hrm_employee a
                                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id AND  a.active_status=1 AND b.employee_activity=1
                                Join hrm_location c On b.hrm_location_id=c.id
                                join hrm_designation d on b.hrm_designation_id = d.id AND d.valid=1
                                JOIN user_location i ON b.hrm_location_id = i.hrm_location_id AND i.users_id = $user_id
                                ");

      //   // dd($dashboard_data);
      //   $old_resign_data  = DB::SELECT("SELECT
      //                                       ifnull(COUNT(a.id),0) AS old_resign_data

      //                                   FROM
      //                                       hrm_employee_resignation aa
      //                                           JOIN
      //                                       hrm_employee_job_info a ON a.id = aa.hrm_employee_job_info_id
      //                                           AND aa.resingnation_status IN (2 , 4)
      //                                           AND aa.valid = 1
      //                                           AND aa.effective_date between '2023-01-01' AND '2023-01-31'
      //                                           AND aa.resingnation_status = 2
      //                                           JOIN
      //                                       hrm_employee c ON c.id = a.hrm_employee_id
      //                                           JOIN
      //                                       hrm_location d ON a.hrm_location_id = d.id
      //                                           JOIN
      //                                       hrm_depertment e ON a.hrm_depertment_id = e.id
      //                                           JOIN
      //                                       hrm_designation f ON a.hrm_designation_id = f.id
      //                                           JOIN
      //                                       user_location g ON a.hrm_location_id = g.hrm_location_id
      //                                           AND g.users_id = $user_id");

      //   $old_join  = DB::SELECT("SELECT
      //                                       ifnull(COUNT(a.id),0) AS old_join
      //                                   FROM
      //                                       hrm_employee_job_info a
      //                                           JOIN
      //                                       hrm_employee_joining b ON a.hrm_employee_id = b.hrm_employee_id
      //                                           AND b.joining_date BETWEEN '2023-01-01' AND '2023-01-31'
      //                                           JOIN
      //                                       hrm_location c ON a.hrm_location_id = c.id
      //                                           JOIN
      //                                       user_location g ON a.hrm_location_id = g.hrm_location_id
      //                                           AND g.users_id =$user_id");


      // $new_resign_data  = DB::SELECT("SELECT
      //                                       ifnull(COUNT(a.id),0) AS new_resign_data

      //                                   FROM
      //                                       hrm_employee_resignation aa
      //                                           JOIN
      //                                       hrm_employee_job_info a ON a.id = aa.hrm_employee_job_info_id
      //                                           AND aa.resingnation_status IN (2 , 4)
      //                                           AND aa.valid = 1
      //                                           AND aa.effective_date between '2023-02-01' AND '2023-02-28'
      //                                           AND aa.resingnation_status = 2
      //                                           JOIN
      //                                       hrm_employee c ON c.id = a.hrm_employee_id
      //                                           JOIN
      //                                       hrm_location d ON a.hrm_location_id = d.id
      //                                           JOIN
      //                                       hrm_depertment e ON a.hrm_depertment_id = e.id
      //                                           JOIN
      //                                       hrm_designation f ON a.hrm_designation_id = f.id
      //                                           JOIN
      //                                       user_location g ON a.hrm_location_id = g.hrm_location_id
      //                                           AND g.users_id = $user_id");

      //   $new_join  = DB::SELECT("SELECT
      //                                       ifnull(COUNT(a.id),0) AS new_join
      //                                   FROM
      //                                       hrm_employee_job_info a
      //                                           JOIN
      //                                       hrm_employee_joining b ON a.hrm_employee_id = b.hrm_employee_id
      //                                           AND b.joining_date BETWEEN '2023-02-01' AND '2023-02-28'
      //                                           JOIN
      //                                       hrm_location c ON a.hrm_location_id = c.id
      //                                           JOIN
      //                                       user_location g ON a.hrm_location_id = g.hrm_location_id
      //                                           AND g.users_id =$user_id");

        // $continued_absent = DB::SELECT("SELECT
        //                                 DATE_FORMAT(MAX(a.punche_date), '%d-%b-%Y') AS last_present,
        //                                 b.employee_code,
        //                                 c.employee_name,
        //                                 CONCAT(DATEDIFF(CURRENT_DATE(), MAX(a.punche_date)),
        //                                         ' days') AS days
        //                                 FROM
        //                                 hrm_attendance a
        //                                     JOIN
        //                                 hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
        //                                     AND b.employee_activity = 1
        //                                     JOIN
        //                                 hrm_employee c ON b.hrm_employee_id = c.id
        //                                     AND a.attendance_status IN (3 , 6)
        //                                 GROUP BY c.id
        //                                 HAVING (DATEDIFF(CURRENT_DATE(), MAX(a.punche_date))) > 3
        //                                 ORDER BY (DATEDIFF(CURRENT_DATE(), MAX(a.punche_date))) DESC");

        // $current_year = date('Y');
        // $current_month = date('m');
        // $continued_late = DB::SELECT("SELECT
        //                 b.employee_code,
        //                 c.employee_name,
        //                 COUNT(a.id) AS late_this_month
        //                 FROM
        //                 hrm_attendance a
        //                     JOIN
        //                 hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
        //                     AND b.employee_activity = 1
        //                     AND YEAR(a.punche_date) = $current_year
        //                     AND MONTH(a.punche_date) = $current_month
        //                     JOIN
        //                 hrm_employee c ON b.hrm_employee_id = c.id
        //                     AND a.attendance_status = 6
        //                 GROUP BY c.id
        //                 HAVING COUNT(a.id) > 3
        //                 ORDER BY COUNT(a.id) DESC");


        $forwardTo = " AND g.users_id = $user_id";

        if (auth()->user()->user_type == 2) {
            $forwardTo = "";
        }

        // $date = date('Y-m-d');

        // $leave_year = DB::table('hrm_leave_years')
        //     ->where('date_from', '<=', $date)
        //     ->where('date_to', '>=', $date)
        //     ->first()->id;


        $leave_pending = DB::select("SELECT
                                d.hrm_location_id ,
                                count(b.id) as pending_leave,
                                dd.location_name
                            FROM
                                hrm_employee a
                                JOIN hrm_leave_application b on a.id=b.hrm_employee_id
                                JOIN hrm_employee_job_info d On a.id=d.hrm_employee_id AND d.employee_activity=1
                                JOIN hrm_location dd ON d.hrm_location_id = dd.id
                                JOIN user_location f ON d.hrm_location_id = f.hrm_location_id AND f.users_id = $user_id
                                JOIN hrm_leave_approve g On b.id=g.hrm_leave_application_id
                                JOIN users j ON g.users_id = j.id
                                WHERE a.active_status=1

                                 AND g.id in
                                (SELECT id  FROM hrm_leave_approve WHERE action_type is null and forward is null
                                 $forwardTo
                                  )
                                GROUP By d.hrm_location_id");


                                $leave_pending = collect($leave_pending);

        return view('dashboard.dashboard')
               ->with('probation_employee',$probation_employee)
               ->with('dashboard_data',$dashboard_data)
               ->with('leave_pending',$leave_pending);
               // ->with('continued_absent',$continued_absent)
               // ->with('continued_late',$continued_late)
               // ->with('data',$query)
               // ->with('old_resign_data',$old_resign_data)
               // ->with('new_resign_data',$new_resign_data)
               // ->with('old_join',$old_join)
               // ->with('new_join',$new_join);
    }

    public function this_month_resign_employee()
    {
        $startDate = date('Y-m-01',strtotime(request()->month_date));
        $endDate = date('Y-m-t',strtotime(request()->month_date));


        // dd($startDate,$endDate);

        $user_id = auth()->id();
        $data = DB::select("SELECT
                b.hrm_location_id,
                c.location_name,
                count(b.hrm_employee_id) total_resign
            FROM
                hrm_employee_resignation a
                    JOIN
                hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id
                    AND a.resingnation_status IN (2 , 4)
                    AND a.valid = 1
                    AND a.effective_date between '$startDate' AND '$endDate'
                    JOIN
                hrm_location c ON b.hrm_location_id = c.id
                    AND c.valid = 1
                    JOIN
                user_location g ON b.hrm_location_id = g.hrm_location_id
                    AND g.users_id = $user_id
            GROUP BY b.hrm_location_id
        ");

        return datatables()->of($data)
            ->addColumn('Link', function($data) use ($startDate, $endDate) {
                $route = url('resignapproved'). '?location_id='.$data->hrm_location_id.'&location_name='.$data->location_name.'&from_date='.$startDate.'&to_date='.$endDate;

                return '<a href="'. $route .'">
                    '. $data->location_name .'
                </a>';
            })
            ->rawColumns(['Link'])
            ->make(true);
    }

    public function this_month_join_employee()
    {
        $startDate = date('Y-m-01',strtotime(request()->month_date));
        $endDate = date('Y-m-t',strtotime(request()->month_date));
        $user_id = auth()->id();

        $data = DB::select("SELECT
                b.hrm_location_id,
                d.location_name,
                count(b.hrm_employee_id) total_join
            FROM
                hrm_employee a
                    JOIN
                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                    JOIN
                hrm_employee_joining c ON b.hrm_employee_id = c.hrm_employee_id
                    AND c.joining_date BETWEEN '$startDate' AND '$endDate'
                    AND b.employee_activity = 1
                    JOIN
                hrm_location d ON b.hrm_location_id = d.id
                    AND d.valid = 1
                    JOIN
                user_location e ON d.id = e.hrm_location_id
                AND e.users_id = $user_id
            GROUP BY b.hrm_location_id
        ");

        return datatables()->of($data)
            ->addColumn('Link', function($data) use ($startDate, $endDate) {
                $route = url('custom_employee_list'). '?location_id='.$data->hrm_location_id.'&location_name='.$data->location_name.'&from_date='.$startDate.'&to_date='.$endDate.'&joining=true';

                return '<a href="'. $route .'">
                    '. $data->location_name .'
                </a>';
            })
            ->rawColumns(['Link'])
            ->make(true);
    }

    public function pending_join()
    {
       $userId = auth()->id();

        // Authorization check: User authorization na thakle faka collection return korbe
        if (!auth()->user()->can('EmployeeJoiningApproval')) {
            return datatables()->of(collect([]))->make(true);
        }

        $isUserTypeTwo = (auth()->user()->user_type == 2);

        $subqueryWhere = "WHERE action_type = 1 AND forward = 1";
        $bindings = [
            'user_id' => $userId,
        ];

        if (!$isUserTypeTwo) {
            $subqueryWhere .= " AND job_users_id = :job_user_id";
            $bindings['job_user_id'] = $userId;
        }

        $data = DB::select("
            SELECT
                c.location_name,
                COUNT(DISTINCT b.id) AS total_employee,
                b.hrm_location_id
            FROM hrm_employee a
            JOIN hrm_employee_job_info b
                ON a.id = b.hrm_employee_id
                AND a.active_status = 1
                AND b.employee_activity = 10
            JOIN hrm_location c
                ON b.hrm_location_id = c.id
            JOIN user_location g
                ON b.hrm_location_id = g.hrm_location_id
                AND g.users_id = :user_id
            WHERE b.id IN (
                SELECT hrm_employee_job_info_id
                FROM hrm_employee_job_approval
                {$subqueryWhere}
            )
            GROUP BY
                b.hrm_location_id,
                c.location_name
            ORDER BY c.location_name
        ", $bindings);

        return datatables()->of($data)
            ->addColumn('Link', function ($row) {
                $route = url('employee_joining_approvals') . '?' . http_build_query([
                    'location_id'   => $row->hrm_location_id,
                    'location_name' => $row->location_name,
                ]);

                return '<a href="' . e($route) . '">' . e($row->location_name) . '</a>';
            })
            ->rawColumns(['Link'])
            ->make(true);
           
    }

    public function this_month_resign_employee_for_graph()
    {
        $month_date = Carbon::parse(request('month_date')) ;
        $endDate = $month_date->endOfMonth()->format('Y-m-d');

        $month_date = Carbon::parse(request('month_date'));
        $startDate = $month_date->subMonths(3)->startOfMonth()->format('Y-m-d');

        $user_id = auth()->id();

        // dd($endDate, $startDate, $user_id);

        $getData = DB::select("SELECT
                DATE_FORMAT(a.effective_date, '%M-%y') AS month,
                COUNT(b.hrm_employee_id) AS total_resign
            FROM hrm_employee_resignation a
            JOIN hrm_employee_job_info b
                ON b.id = a.hrm_employee_job_info_id
                AND a.resingnation_status in  (2,4)
                AND a.valid = 1
                AND a.effective_date between '$startDate' AND '$endDate'
                JOIN user_location g ON b.hrm_location_id = g.hrm_location_id
                AND g.users_id = $user_id
            GROUP BY month
            ORDER BY a.effective_date
        ");

        // Prepare the data with headers
        $chartData = [['Month', 'Total Resignations']];
        foreach ($getData as $row) {
            $chartData[] = [(string) $row->month, (int) $row->total_resign];
        }
        // dd($getData);
        return response()->json($chartData);
    }

    public function this_month_join_employee_for_graph()
    {

        $month_date = Carbon::parse(request('month_date')) ;
        $endDate = $month_date->endOfMonth()->format('Y-m-d');

        $month_date = Carbon::parse(request('month_date'));
        $startDate = $month_date->subMonths(3)->startOfMonth()->format('Y-m-d');

        $user_id = auth()->id();

        $getData = DB::select("SELECT
                DATE_FORMAT(c.joining_date, '%M-%y') AS month,
                count(b.hrm_employee_id) total_join
            FROM
                hrm_employee a
                    JOIN
                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                    JOIN
                hrm_employee_joining c ON b.hrm_employee_id = c.hrm_employee_id
                    AND c.joining_date BETWEEN '$startDate' AND '$endDate'
                    AND b.employee_activity = 1
                    JOIN
                hrm_location d ON b.hrm_location_id = d.id
                    AND d.valid = 1
                    JOIN
                user_location e ON d.id = e.hrm_location_id
                AND e.users_id = $user_id
            GROUP BY month
            ORDER BY c.joining_date
        ");

        // Prepare the data with headers
        $chartData = [['Month', 'Total Joining']];
        foreach ($getData as $row) {
            $chartData[] = [(string) $row->month, (int) $row->total_join];
        }
        // dd($chartData);
        return response()->json($chartData);
    }

    public function this_month_resign_join_employee_for_graph()
    {
        $month_date = Carbon::parse(request('month_date')) ;
        $endDate = $month_date->endOfMonth()->format('Y-m-d');

        $month_date = Carbon::parse(request('month_date'));
        $startDate = $month_date->subMonths(5)->startOfMonth()->format('Y-m-d');

        $user_id = auth()->id();


        $dataset = DB::select("SELECT aa.ym,aa.month, SUM(aa.total_resign) as total_resign , SUM(aa.total_join) as  total_join
                FROM(

                SELECT
                    DATE_FORMAT(a.effective_date, '%M-%y') AS month,
                    DATE_FORMAT(a.effective_date, '%y%m') as ym,
                    COUNT(b.hrm_employee_id) AS total_resign,
                    0 AS total_join
                FROM
                    hrm_employee_resignation a
                        JOIN
                    hrm_employee_job_info b ON b.id = a.hrm_employee_job_info_id
                        AND a.resingnation_status IN (2 , 4)
                        AND a.valid = 1
                        AND a.effective_date BETWEEN '$startDate' AND '$endDate'
                        JOIN
                    user_location g ON b.hrm_location_id = g.hrm_location_id
                        AND g.users_id = $user_id
                        JOIN hrm_employee h ON h.id = b.hrm_employee_id

                GROUP BY month
                UNION ALL SELECT
                    DATE_FORMAT(c.joining_date, '%M-%y') AS month,
                    DATE_FORMAT(c.joining_date, '%y%m') as ym,
                    0 AS total_resign,
                    COUNT(b.hrm_employee_id) total_join
                FROM
                    hrm_employee a
                        JOIN
                    hrm_employee_job_info b ON a.id = b.hrm_employee_id
                        JOIN
                    hrm_employee_joining c ON b.hrm_employee_id = c.hrm_employee_id
                        AND b.employee_activity = 1
                        AND c.joining_date BETWEEN '$startDate' AND '$endDate'
                        JOIN
                    hrm_location d ON b.hrm_location_id = d.id AND d.valid = 1
                        JOIN
                    user_location e ON d.id = e.hrm_location_id
                        AND e.users_id = $user_id
                        JOIN hrm_employee h ON h.id = b.hrm_employee_id

                GROUP BY month) aa
                GROUP BY aa.ym
                ORDER BY aa.ym
        ");


        return response()->json($dataset);
    }


    public function last_fifteen_ontime_graph()
    {
        $user_id = auth()->user()->id;

        $dataset = DB::select("SELECT aa.ontime,aa.punche_date,aa.pd FROM (SELECT
                                COUNT(a.id) AS ontime,
                                DATE_FORMAT(a.punche_date, '%d-%b-%Y') as punche_date,
                                a.punche_date as pd
                            FROM
                                hrm_attendance a
                                    JOIN
                                hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                    AND b.employee_activity = 1
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    AND a.attendance_status in  (2,6)
                                    JOIN
                                user_location d ON b.hrm_location_id = d.hrm_location_id
                                    AND d.users_id = $user_id
                            GROUP BY a.punche_date
                            ORDER BY a.punche_date desc
                            LIMIT 15) aa ORDER BY aa.pd asc");

        return response()->json($dataset);

    }

    public function last_fifteen_absent_graph()
    {
        $user_id = auth()->user()->id;
        $dataset = DB::select("SELECT aa.absent,aa.punche_date,aa.pd FROM (SELECT
                                    COUNT(a.id) AS absent,
                                    DATE_FORMAT(a.punche_date, '%d-%b-%Y') as punche_date,
                                    a.punche_date as pd
                                FROM
                                    hrm_attendance a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.employee_activity = 1
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        AND a.attendance_status = 1
                                        JOIN
                                    user_location d ON b.hrm_location_id = d.hrm_location_id
                                        AND d.users_id = $user_id
                                GROUP BY a.punche_date
                                ORDER BY a.punche_date desc
                                LIMIT 15) aa ORDER BY aa.pd asc");

        return response()->json($dataset);

    }

    public function last_fifteen_leave_graph()
    {
        $user_id = auth()->user()->id;

        $current_year = date('Y');
        $current_month = date('m');
        $dataset = DB::select("SELECT
                                    Round(SUM(aa.leavedays)) AS leavedays, aa.punche_date,DATE_FORMAT(aa.punche_date, '%d-%b-%Y') as punche_datee
                                FROM
                                    (SELECT
                                        COUNT(a.id) AS leavedays, a.punche_date
                                    FROM
                                        hrm_attendance a
                                    JOIN hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.employee_activity = 1
                                        AND YEAR(a.punche_date) = $current_year
                                        AND MONTH(a.punche_date) = $current_month
                                    JOIN hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    user_location d ON b.hrm_location_id = d.hrm_location_id
                                        AND d.users_id = $user_id
                                    WHERE
                                        a.attendance_status IN (3 , 9)
                                    GROUP BY a.punche_date UNION ALL SELECT
                                        COUNT(a.id) / 4 AS leavedays, a.punche_date
                                    FROM
                                        hrm_attendance a
                                    JOIN hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.employee_activity = 1
                                        AND YEAR(a.punche_date) = $current_year
                                        AND MONTH(a.punche_date) = $current_month
                                    JOIN hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    user_location d ON b.hrm_location_id = d.hrm_location_id
                                        AND d.users_id = $user_id
                                    WHERE
                                        a.attendance_status IN (12 , 13)
                                    GROUP BY a.punche_date UNION ALL SELECT
                                        COUNT(a.id) / 2 AS leavedays, a.punche_date
                                    FROM
                                        hrm_attendance a
                                    JOIN hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.employee_activity = 1
                                    JOIN hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    user_location d ON b.hrm_location_id = d.hrm_location_id
                                        AND d.users_id = $user_id
                                    WHERE
                                        a.attendance_status IN (4 , 10)
                                            AND YEAR(a.punche_date) = $current_year
                                            AND MONTH(a.punche_date) = $current_month
                                    GROUP BY a.punche_date) aa
                                GROUP BY aa.punche_date
                                ORDER BY aa.punche_date asc
                                LIMIT 7");



        return response()->json($dataset);

    }


    public function pending_attendance_request()
    {
        if (!Auth::user()->can('PendingManualAttendance')) {
            return response()->json([]);
        }

        $data = DB::select("SELECT
                                aa.hrm_location_id,
                                gg.location_name,
                                COUNT(DISTINCT aa.id) AS pending_attendance
                            FROM
                                hrm_manual_attendance_data aa
                                    JOIN
                                hrm_employee_job_info cc ON cc.hrm_employee_id = aa.hrm_employee_id AND cc.employee_activity=1
                                    JOIN
                                hrm_location gg ON cc.hrm_location_id = gg.id
                            WHERE
                                aa.data_from = 2
                                AND aa.valid = 1
                                AND aa.approved_at IS NULL
                            GROUP BY aa.hrm_location_id, gg.location_name
                            ORDER BY gg.location_name");

        return response()->json($data);
    }

    public function attendence_pie_chart()
    {

        $date = Carbon::parse(request('month_date'))->format('Y-m-d');
        $user_id = auth()->user()->id;
        $date_condition1 = " AND a.punche_date = '$date'";
        $date_condition2 = " punche_date = '$date'";

        if (request('num_percentage') == 1) {
            $dataset = DB::select("SELECT
                                        ROUND((COUNT(a.id) / e.total_count) * 100,2) AS percentage,
                                        DATE_FORMAT(a.punche_date, '%d-%b-%Y') AS attendance_date,
                                        cc.alies,
                                        CONCAT('#', cc.color_code) AS color_code


                                    FROM
                                        hrm_attendance a
                                            JOIN
                                        hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                            AND b.employee_activity = 1
                                            JOIN
                                        hrm_employee c ON b.hrm_employee_id = c.id
                                            JOIN
                                        hrm_attendance_status cc ON a.attendance_status = cc.id
                                            JOIN
                                        user_location d ON b.hrm_location_id = d.hrm_location_id
                                            AND d.users_id = $user_id
                                            $date_condition1
                                            JOIN
                                        (SELECT
                                            COUNT(id) AS total_count
                                        FROM
                                            hrm_attendance
                                        WHERE
                                            $date_condition2) AS e
                                    GROUP BY cc.id");

        } elseif (request('num_percentage') == 2) {


            $dataset = DB::SELECT("SELECT
                                    COUNT(a.id) AS ontime,
                                    DATE_FORMAT(a.punche_date, '%d-%b-%Y') AS attendance_date,
                                    cc.alies,
                                    concat('#',cc.color_code) as color_code
                                FROM
                                    hrm_attendance a
                                        JOIN
                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                        AND b.employee_activity = 1
                                        JOIN
                                    hrm_employee c ON b.hrm_employee_id = c.id
                                        JOIN
                                    hrm_attendance_status cc ON a.attendance_status = cc.id
                                        JOIN
                                    user_location d ON b.hrm_location_id = d.hrm_location_id
                                        AND d.users_id = 1
                                        $date_condition1
                                    GROUP BY cc.id");
        }




        return response()->json($dataset);
    }

}
