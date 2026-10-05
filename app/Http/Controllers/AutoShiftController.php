<?php

namespace App\Http\Controllers;

use App\Models\HrmShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AutoShiftController extends Controller
{

    public function index()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT
                                                    b.id, b.location_name
                                                FROM
                                                    user_location a
                                                        JOIN
                                                    hrm_location b ON a.hrm_location_id = b.id
                                                        AND a.users_id = $user_id
                                                        AND b.shifting_rules = 2");

        // dd($default_user_location);

         return view('auto_shift.index')
          ->with('default_user_location',  $default_user_location) ;
    }

    public function getAutoShiftList(Request $request)
    {
        $condition = "";
        
        if (isset($request->hrm_location_id)) {
            $condition = " AND a.hrm_location_id = ".$request->hrm_location_id;
        }
      

        if (isset($request->hrm_employee_id)) {
            $condition = " AND a.hrm_employee_id = " . $request->hrm_employee_id;
        }
        if (isset($request->hrm_shift_id)) {
            $condition .= " AND c.hrm_shift_id = " . $request->hrm_shift_id;
        }

        if (isset($request->hrm_depertment_id)) {
            $condition .= " AND a.hrm_depertment_id = " . $request->hrm_depertment_id;
        }

        if (isset($request->hrm_designation_id)) {
            $condition .= " AND a.hrm_designation_id = " . $request->hrm_designation_id;
        }

        if (isset($request->hrm_section_id)) {
            $condition .= " AND a.hrm_section_id = " . $request->hrm_section_id;
        }

        $autoShifts = DB::select("SELECT
                b.id,
                CONCAT(b.employee_name,' | ',e.depertment_name,' | ',
                f.designation_name,' | ', ifnull(a.employee_code,'') ,' | ',ifnull(ff.location_name,'')) as  employee_name,
                e.depertment_name,
                f.designation_name,
                GROUP_CONCAT(d.shift_name) AS shift_name,
                c.start_date AS last_change
            FROM
                hrm_employee_job_info AS a
                    JOIN
                hrm_employee AS b ON a.hrm_employee_id = b.id AND a.employee_activity=1
                    JOIN
                hrm_depertment AS e ON a.hrm_depertment_id = e.id
                    JOIN
                hrm_designation AS f ON a.hrm_designation_id = f.id
                    JOIN
                hrm_location ff ON a.hrm_location_id = ff.id 
                    LEFT JOIN
                hrm_employee_shift_auto c ON c.hrm_employee_job_info_id = a.id
                    LEFT JOIN
                hrm_shift d ON c.hrm_shift_id = d.id
                WHERE c.end_date IS NULL
                $condition
            GROUP BY a.id
        ");

        return DataTables::of($autoShifts)
            ->addColumn('Link', function ($autoShifts) {
                return '
                <a href="' . route('autoShift.change', encrypt($autoShifts->id)) . '" class="btn-sm btn btn-flat btn-success modalLink" footer-none data-title="Change Shift">Action</a>
                ';
            })->rawColumns(['Link'])
            ->make(true);
    }

    public function changeShift($id)
    {

        $id = decrypt($id);

        $data['autoShifts'] = DB::select("
                                        SELECT
                                            b.id,
                                            a.id as job_info_id,
                                            b.employee_name,
                                            e.depertment_name,
                                            f.designation_name,
                                            GROUP_CONCAT(d.id) AS shift_id,
                                            c.start_date AS last_change
                                        FROM
                                            hrm_employee_job_info AS a
                                                JOIN
                                            hrm_employee AS b ON a.hrm_employee_id = b.id and a.employee_activity=1 and b.id = $id

                                                JOIN
                                            hrm_depertment AS e ON a.hrm_depertment_id = e.id
                                                JOIN
                                            hrm_designation AS f ON a.hrm_designation_id = f.id
                                            LEFT JOIN
                                            hrm_employee_shift_auto c ON c.hrm_employee_job_info_id = a.id
                                                AND c.end_date IS NULL
                                                LEFT JOIN
                                            hrm_shift d ON c.hrm_shift_id = d.id
                                        GROUP BY b.id
        ")[0];

        $data['hrm_shifts'] = HrmShift::where('valid',1)->orderby('start_time','asc')->get();

        // dd($data['autoShifts']);

        return view('auto_shift.modal', $data);
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


        if (empty($request->shifts)) {
            $success = false;
            return back()->with('message', 'Selected shift is empty');
        } else if (count($request->shifts) > 4) {
            $success = false;
            return back()->with('message', 'Selected shift can\'t be more than 4');
        } else {

           DB::table('hrm_employee_shift_auto')
                ->where('hrm_employee_job_info_id', $request->job_info_id)
                ->where('start_date', date('Y-m-d'))->delete();


            DB::table('hrm_employee_shift_auto')
                ->where('hrm_employee_job_info_id', $request->job_info_id)
                ->update([
                    'end_date' => date('Y-m-d', strtotime("-1 days", strtotime(now()))),
                    'valid' => 1,
                    'updated_at' => now()->toDateTimeString(),
                    'users_id' => Auth::user()->id,
                ]);


            // temporary add delete
            DB::DELETE("DELETE FROM hrm_employee_shift_auto Where hrm_employee_job_info_id = $request->job_info_id");
            // temporary add delete

            foreach ($request->shifts as $shift) {
                 DB::table('hrm_employee_shift_auto')
                        ->insert([
                            'hrm_shift_id' => $shift,
                            // 'start_date' => date('Y-m-d'),
                            'start_date' => '2025-07-01',
                            'valid' => 1,
                            'hrm_employee_job_info_id' => $request->job_info_id,
                            'users_id' => Auth::user()->id,
                            'created_at' => now()->toDateTimeString()
                        ]);

                        $message = 'Successfully added.!';
                        $success = true;

                // $findData  = DB::SELECT("SELECT
                //                                 a.id, b.start_time, b.end_time
                //                             FROM
                //                                 hrm_employee_shift_auto a
                //                                     JOIN
                //                                 hrm_shift b ON a.hrm_shift_id = b.id
                //                             WHERE
                //                                 a.valid = 1
                //                                     AND hrm_employee_job_info_id = $request->job_info_id");


                // if(empty($findData)){

                //         DB::table('hrm_employee_shift_auto')
                //         ->insert([
                //             'hrm_shift_id' => $shift,
                //             // 'start_date' => date('Y-m-d'),
                //             'start_date' => '2025-07-01',
                //             'valid' => 1,
                //             'hrm_employee_job_info_id' => $request->job_info_id,
                //             'users_id' => Auth::user()->id,
                //             'created_at' => now()->toDateTimeString()
                //         ]);

                //         $message = 'Successfully added.!';
                //         $success = true;

                // }else{

                //         $assignShiftInfo = HrmShift::find($shift);

                //         // dump($findData);
                //         foreach($findData  as $data){

                //             $isExist = DB::select("select * from hrm_employee_shift_auto where id = $data->id AND '$assignShiftInfo->start_time' > '$data->start_time' AND '$assignShiftInfo->start_time'<'$data->end_time' OR '$assignShiftInfo->end_time'>'$data->start_time' AND '$assignShiftInfo->end_time'<'$data->end_time'");


                //             // dump("select * from hrm_employee_shift_auto where id = $data->id AND '$assignShiftInfo->start_time' > '$data->start_time' AND '$assignShiftInfo->start_time'<'$data->end_time' OR '$assignShiftInfo->end_time'>'$data->start_time' AND '$assignShiftInfo->end_time'<'$data->end_time'");

                //             if(empty($isExist)){

                //                 DB::table('hrm_employee_shift_auto')
                //                 ->insert([
                //                     'hrm_shift_id' => $shift,
                //                     // 'start_date' => date('Y-m-d'),
                //                     'start_date' => '2023-07-25',
                //                     'valid' => 1,
                //                     'hrm_employee_job_info_id' => $request->job_info_id,
                //                     'users_id' => Auth::user()->id,
                //                     'created_at' => now()->toDateTimeString()
                //                 ]);

                //                 $message = 'Successfully added.!';
                //                 $success = true;
                //             }else{

                //                 // dd("sddsd");
                //                 $message = 'Shift is overlaped.!';
                //                 $success = false;
                //                 return back()->with(['message'=>$message,'success'=>$success]);
                //             }

                //         }

                // }




            }


            return back()->with(['message'=>$message,'success'=>$success]);
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
