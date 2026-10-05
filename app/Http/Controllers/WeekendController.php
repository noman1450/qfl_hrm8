<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use function GuzzleHttp\Promise\all;

class WeekendController extends Controller
{

    public function index()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id ");

        return view('weekend.index', compact('default_user_location'));
    }


    public function getEmployeeWeekend(Request $request)
    {

        $condition = '';

        if (isset($request->hrm_employee_id)) {
            $condition = 'AND a.hrm_employee_id = ' . $request->hrm_employee_id;
        }
        if (isset($request->hrm_location_id)) {
            $condition .= ' AND a.hrm_location_id = ' . $request->hrm_location_id;
        }

        if (isset($request->hrm_depertment_id)) {
            $condition .= ' AND a.hrm_depertment_id = ' . $request->hrm_depertment_id;
        }

        if (isset($request->hrm_section_id)) {
            $condition .= ' AND a.hrm_section_id = ' . $request->hrm_section_id;
        }

        if (isset($request->hrm_days_name_id)) {
            $condition .= ' AND g.hrm_days_name_id = ' . $request->hrm_days_name_id;
        }

        if (isset($request->hrm_shift_id)) {
            $condition .= ' AND d.id = ' . $request->hrm_shift_id;
        }

        if ($request->holiday != null) {
            if ($request->holiday === "0") {
                $condition .= ' AND g.hrm_employee_id IS NULL';
            } else {
                $condition .= ' AND g.hrm_employee_id != 0';
            }
        }


        $weekend = DB::select("
                                SELECT
                                    b.id,
                                    b.employee_name,
                                    e.depertment_name,
                                    f.designation_name,
                                    GROUP_CONCAT(h.days_name) AS days_name,
                                    g.start_date AS last_change
                                FROM
                                    hrm_employee_job_info AS a
                                        JOIN
                                    hrm_employee AS b ON a.hrm_employee_id = b.id
                                        LEFT JOIN
                                    hrm_employee_shift c ON c.hrm_employee_job_info_id = a.id
                                    AND c.end_date IS NULL
                                        LEFT JOIN
                                    hrm_shift d ON c.hrm_shift_id = d.id
                                        LEFT JOIN
                                    hrm_depertment AS e ON a.hrm_depertment_id = e.id
                                        LEFT JOIN
                                    hrm_designation AS f ON a.hrm_designation_id = f.id
                                        LEFT JOIN
                                    hrm_holiday_configure AS g ON a.hrm_employee_id = g.hrm_employee_id
                                        LEFT JOIN
                                    hrm_days_name AS h ON g.hrm_days_name_id = h.id
                                WHERE
                                    g.end_date IS NULL
                                $condition
                                GROUP BY b.id
        ");

        return DataTables::of($weekend)
            ->addColumn('Link', function ($weekend) {
                return '
                <a href="' . route('weekend_change', encrypt($weekend->id)) . '" class="btn-sm btn btn-flat btn-success modalLink" footer-none data-title="Change Weekend">Action </a>
                ';
            })
            ->rawColumns(['Link'])
            ->make(true);
    }

    public function changeWeekend($id)
    {

        $id = decrypt($id);

        $data['employee'] = DB::select("
                SELECT
                    b.id,
                    b.employee_name,
                    a.hrm_location_id,
                    c.depertment_name,
                    d.designation_name,
                    GROUP_CONCAT(e.hrm_days_name_id) hrm_days_name_id,
                    e.start_date AS last_change
                FROM
                    hrm_employee_job_info AS a
                        JOIN
                    hrm_employee AS b ON a.hrm_employee_id = b.id AND b.id = $id
                        JOIN
                    hrm_depertment AS c ON a.hrm_depertment_id = c.id
                        JOIN
                    hrm_designation AS d ON a.hrm_designation_id = d.id
                     LEFT JOIN
                    hrm_holiday_configure AS e ON a.hrm_employee_id = e.hrm_employee_id AND e.is_active = 1
                group by b.id
        ")[0];


        $data['days'] = DB::select("SELECT id,days_name FROM hrm_days_name");

//        dd($data);

        return view('weekend.modal', $data);
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


    public function store(Request $request)
    {


        if(empty($request->weekends)){
            return back()->with('warning', 'Selected weekend day is empty');
        } else {

            $reqDate = date('Y-m-d', strtotime($request->start_date));


            $isExist = DB::table('hrm_holiday_configure')
                ->where('hrm_location_id', $request->hrm_location_id)
                ->where('hrm_employee_id', $request->employee_id)
                ->where('is_active', 1)
                ->where('end_date', null)->first();


            if ($isExist) {


                $currentDateCheck = DB::table('hrm_holiday_configure')
                    ->where('hrm_location_id', $request->hrm_location_id)
                    ->where('hrm_employee_id', $request->employee_id)
                    ->where('start_date', '<=', $reqDate)
                    ->where('is_active', 1)
                    ->where('end_date', null)->first();

                if ($currentDateCheck) {
                    $end_date = date('Y-m-d', strtotime("-1 days", strtotime($request->start_date)));

                    DB::table('hrm_holiday_configure')
                        ->where('hrm_location_id', $currentDateCheck->hrm_location_id)
                        ->where('hrm_employee_id', $request->employee_id)
                        ->update([
                            'is_active' => 0,
                            'end_date' => $end_date,
                            'updated_at' => now()->toDateTimeString(),
                            'users_id' => auth()->id(),
                        ]);

                    $array = [];

                    foreach ($request->weekends as $key => $value) {
                        array_push($array, [
                            'hrm_days_name_id' => $request->weekends[$key],
                            'hrm_location_id' => $request->hrm_location_id,
                            'hrm_employee_id' => $request->employee_id,
                            'start_date' => $reqDate,
                            'users_id' => auth()->id(),
                            'created_at' => now()->toDateTimeString(),
                            'updated_at' => now()->toDateTimeString(),
                        ]);
                    }

                    DB::table('hrm_holiday_configure')
                        ->insert($array);


                    return redirect()->back();


                } else {
                    return back()->with('warning', 'Can\'t add previous date');
                }


            } else {


                $array = [];

                foreach ($request->weekends as $key => $value) {
                    array_push($array, [
                        'hrm_days_name_id' => $request->weekends[$key],
                        'hrm_location_id' => $request->hrm_location_id,
                        'hrm_employee_id' => $request->employee_id,
                        'start_date' => $reqDate,
                        'users_id' => auth()->id(),
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ]);
                }

                DB::table('hrm_holiday_configure')
                    ->insert($array);


                    $this->recordActivity(
                         1,
                         'Created Weekend Of This Employee',
                         null,
                         $request->employee_id,
                         'hrm_employee'
                    );


                return redirect()->back();

            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
