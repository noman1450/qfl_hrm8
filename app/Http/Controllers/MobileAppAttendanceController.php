<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use DB;
use Crypt;
use Validator;
use Config;
use Session;
use DataTables;

use App\Models\HrmBloodGroup;
use App\Models\HrmMonth;
use App\Models\MobileLog;

class MobileAppAttendanceController extends Controller
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


        $cmonth = date('m');
        $cmonth = HrmMonth::find($cmonth);
        $cyear  = date('Y');


        return view('mobile_app_attendance.current_month_user')
              -> with('cmonth',  $cmonth)
              -> with('cyear',   $cyear) ;

    }


    public function get_current_month_app_user(Request $request)
    {

            $year_id  =$request->year_id;
            $month_id =$request->month_id;



            $app_user_data = DB::SELECT("SELECT
                                                COUNT(aa.id) as count_id
                                            FROM
                                                (SELECT
                                                        MAX(b.id) as id
                                                    FROM
                                                        mobile_log a
                                                    JOIN
                                                        hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                        AND a.valid = 1 AND MONTH(a.created_at) = $month_id
                                                            AND YEAR(a.created_at) = $year_id
                                                    GROUP BY b.hrm_employee_id) aa");


            return json_encode(array('app_user_data' => $app_user_data));


    }




    public function app_user_log()
    {
        return view('mobile_app_attendance.app_user_log_list');
    }




   public function get_current_month_app_user_list (Request $request){



        $year_id  =$request->year_id;
        $month_id =$request->month_id;


        $currentemployee = DB::select("SELECT
                                            a.id,
                                            CONCAT(a.employee_name, ' | ', b.employee_code) AS employee_name,
                                            c.location_name,
                                            LPAD(a.id, 5, '0') AS Unique_Code,
                                            d.depertment_name,
                                            e.designation_name,
                                            a.contact_number,
                                            DATE_FORMAT(f.joining_date, '%d-%m-%Y') AS joining_date,
                                            e.priority,
                                            a.Images
                                        FROM
                                            hrm_employee a
                                                JOIN
                                            hrm_employee_job_info b ON a.id = b.hrm_employee_id
                                                -- AND a.active_status = 1
                                                -- AND b.employee_activity = 1
                                                AND b.id in (SELECT
                                                                    MAX(b.id) as id
                                                                FROM
                                                                    mobile_log a
                                                                JOIN
                                                                    hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                                                                    AND a.valid = 1 AND MONTH(a.created_at) = $month_id
                                                                        AND YEAR(a.created_at) = $year_id
                                                                GROUP BY b.hrm_employee_id)
                                                JOIN
                                            hrm_location c ON b.hrm_location_id = c.id
                                                JOIN
                                            hrm_depertment d ON b.hrm_depertment_id = d.id
                                                JOIN
                                            hrm_designation e ON b.hrm_designation_id = e.id
                                                JOIN
                                            hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id") ;

        return datatables()->of($currentemployee)
        ->addColumn('Link', function ($currentemployee) {
           return
           ' <a href="'. url('/employeejoin') . '/' .
           Crypt::encrypt($currentemployee->id) .
           '/edit' .'"' .
           'class="btn  btn-sm block btn-flat"><i class="glyphicon glyphicon-edit" id="customer-confrimed"></i> Edit</a>';
         })
        ->editColumn('id', '{{$id}}')
        ->setRowId('id')
        ->rawColumns(['Link'])
        ->make(true);

    }





    public function attendancelistdata_forappuser(Request $request){

        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));



        $data   = DB::select("SELECT
                                aa.hrm_employee_id,
                                concat(hh.employee_name ,' | ',cc.employee_code) as employee_name,
                                ee.depertment_name,
                                ff.designation_name,
                                gg.location_name,
                                ii.punche_date,
                                ii.in_time,
                                ii.out_time,
                                dd.shift_name,
                                CONCAT(ff.designation_name, ',' , ee.depertment_name , ',' , gg.location_name) as employee_summary,
                                GROUP_CONCAT(CONCAT(aa.in_datetime) order by aa.in_datetime ASC SEPARATOR '<br>') AS indatetime,
                                GROUP_CONCAT(CONCAT(aa.out_datetime) order by aa.in_datetime ASC SEPARATOR '<br>') AS outdatetime,
                                GROUP_CONCAT(CONCAT(aa.in_datetime) order by aa.in_datetime ASC SEPARATOR '<br>') AS intime,
                                GROUP_CONCAT(CONCAT(aa.out_datetime) order by aa.in_datetime ASC SEPARATOR '<br>') AS outtime,
                                GROUP_CONCAT(CONCAT(aa.working_hour) order by aa.in_datetime ASC SEPARATOR '<br>') AS working_hour
                            FROM

                                log aa
                                     JOIN
                                hrm_employee_job_info cc ON aa.hrm_employee_id=cc.hrm_employee_id AND
                                cc.id in (SELECT max(id) FROM hrm_employee_job_info Group By hrm_employee_id)
                                AND cc.hrm_employee_id in (SELECT
                                                                    b.hrm_employee_id
                                                                FROM
                                                                    mobile_log a
                                                                        JOIN
                                                                    users b ON a.user_id = b.id
                                                                WHERE
                                                                    a.valid = 1 AND date(a.created_at) = '$date'
                                                                GROUP BY b.hrm_employee_id)
                                    JOIN
                                hrm_attendance ii ON aa.hrm_employee_id = ii.hrm_employee_id
                                    AND ii.punche_date = aa.punch_date
                                    AND ii.punche_date = '$date'
                                    JOIN
                                hrm_shift dd ON ii.hrm_shift_id = dd.id
                                    JOIN
                                hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                                    JOIN
                                hrm_designation ff ON cc.hrm_designation_id = ff.id
                                    JOIN
                                hrm_location gg ON cc.hrm_location_id = gg.id
                                    JOIN
                                hrm_employee hh ON cc.hrm_employee_id = hh.id

                            GROUP BY aa.hrm_employee_id ,hh.employee_name, ee.depertment_name , ff.designation_name , gg.location_name , dd.shift_name,
                            ii.punche_date , ii.in_time , ii.out_time,cc.employee_code

                            ");



        return json_encode(array('data' => $data));

    }

    public function app_user_details_log()
    {
        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`=b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('mobile_app_attendance.app_user_details_log_list')-> with('default_user_location',  $default_user_location) ;
    }






    public function get_app_user_details_log_list(Request $request){

        $date         = date('Y-m-d', strtotime(str_replace('/', '-', $request->punch_date)));


        $data   = DB::select("SELECT
                                    aa.id,
                                    aa.hrm_employee_id,
                                    hh.Images,
                                    CONCAT(hh.employee_name,
                                            ' | ',IFNULL(cc.employee_code, 'N/A')
                                            ) AS employee_name,
                                    ee.depertment_name,
                                    ff.designation_name,
                                    gg.location_name,
                                    CONCAT(ff.designation_name,
                                            ',',
                                            ee.depertment_name,
                                            ',',
                                            gg.location_name) AS employee_summary,
                                    aa.attachment,
                                    aa.created_at as date_time,
                                    aa.status,
                                    if(aa.status=1,'In Time','Out Time') as status_result
                                FROM
                                    mobile_log aa
                                        JOIN
                                    hrm_employee_job_info cc ON aa.hrm_employee_id = cc.hrm_employee_id AND date(date) ='$date'
                                    AND cc.hrm_location_id = $request->location
                                    AND aa.valid=1
                                        AND cc.id IN (SELECT
                                            MAX(id)
                                        FROM
                                            hrm_employee_job_info
                                        GROUP BY hrm_employee_id)
                                        JOIN
                                    hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                                        JOIN
                                    hrm_designation ff ON cc.hrm_designation_id = ff.id
                                        JOIN
                                    hrm_location gg ON cc.hrm_location_id = gg.id
                                        JOIN
                                    hrm_employee hh ON cc.hrm_employee_id = hh.id ORDER BY aa.created_at desc");



            return datatables()->of($data)
                // ->addColumn('StatusInOut', function($data) {
                //     if ($data->status === 1) {
                //         return '<label class="label label-success">In</label>';
                //     } elseif ($data->status === 2) {
                //         return '<label class="label label-danger">Out</label>';
                //     }
                // })

                ->addColumn('Link', function($data) {
                    $status = $data->status === 1 ? '(In Time)' : ($data->status === 2 ? '(Out Time)' : '');

                    return '
                    <a href="'.route('current_month_app_user.show', encrypt($data->id)).'" class="btn btn-info modalLink btn-sm btn-flat" title="Details of '.$data->employee_name.' '.$status.'"   modal_size="modal-lg">
                        Show Details
                    </a>';

                    // <form action="'.route('current_month_app_user.destroy', encrypt($data->id)).'" method="post" style="display:inline;overflow:hidden">
                    //     '.csrf_field().'
                    //     '.method_field("delete").'
                    //     <button type="submit" class="btn btn-danger btn-sm btn-flat" onclick="return confirm(\'Do you really want to Delete?\');">
                    //         <span class="glyphicon glyphicon-trash"></span>
                    //         Delete
                    //     </button>
                    // </form>



                })
                ->rawColumns(['Link'])
                ->make(true);


        // return json_encode(array('data' => $data));

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'group_name'    => 'required|unique:hrm_blood_group,blood_group|max:255',
        // ]);

        // if ($validator->fails()) {
        //     return redirect('bloodgroup/create')
        //                 ->withErrors($validator)
        //                 ->withInput();
        // }

        // $insert = new HrmBloodGroup;
        // $insert->blood_group = $request->group_name;
        // $insert->save();

        // $request->session()->flash('alert-success', 'data has been successfully added!');
        // return Redirect::to('bloodgroup');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function getuserwisedata($id,$date)
    {
            $date         = date('Y-m-d', strtotime(str_replace('/', '-', $date)));

            $user_data = DB::SELECT("SELECT a.lon,
                                            a.lat,
                                            CONCAT(b.employee_name,'--',a.created_at) as employee_name
                                      FROM mobile_log a
                                      JOIN hrm_employee b
                                      ON a.hrm_employee_id=b.id
                                      AND a.hrm_employee_id=$id
                                      AND a.valid=1
                                      AND DATE(a.created_at)='$date' ");


            return json_encode(array('user_data' => $user_data));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

    }


    public function show($id)
    {

        // dd($id);
        // $employeeAttendance = AttendanceInfo::query()
        //     ->join('employees as b', 'employee_attendance.employees_id', '=', 'b.id')
        //     ->join('designation as c', 'b.designation_id', '=', 'c.id')
        //     ->selectRaw('getEmployeeAreaName(b.id) as marketing_areas, employee_attendance.*, b.employee_name, b.image as emp_img, c.designation_name')
        //     ->firstWhere('employee_attendance.id', decrypt($id));

        // return view('attendance.show', compact('employeeAttendance'));

          $id=decrypt($id);
          $employeeAttendance   = DB::select("SELECT
                                    aa.id,
                                    aa.hrm_employee_id,
                                    hh.Images,
                                    CONCAT(hh.employee_name,
                                            ' | ',IFNULL(cc.employee_code, 'N/A')
                                            ) AS employee_name,
                                    ee.depertment_name,
                                    ff.designation_name,
                                    gg.location_name,
                                    CONCAT(ff.designation_name,
                                            ',',
                                            ee.depertment_name,
                                            ',',
                                            gg.location_name) AS employee_summary,
                                    aa.attachment,
                                    aa.created_at as date_time,
                                    aa.status
                                FROM
                                    mobile_log aa
                                        JOIN
                                    hrm_employee_job_info cc ON aa.hrm_employee_id = cc.hrm_employee_id and aa.id=$id
                                        AND cc.id IN (SELECT
                                            MAX(id)
                                        FROM
                                            hrm_employee_job_info
                                        GROUP BY hrm_employee_id)
                                        JOIN
                                    hrm_depertment ee ON cc.hrm_depertment_id = ee.id
                                        JOIN
                                    hrm_designation ff ON cc.hrm_designation_id = ff.id
                                        JOIN
                                    hrm_location gg ON cc.hrm_location_id = gg.id
                                        JOIN
                                    hrm_employee hh ON cc.hrm_employee_id = hh.id");
        // dd($employeeAttendance);
        $employeeAttendance = $employeeAttendance[0];
        return view('mobile_app_attendance.show', compact('employeeAttendance'));


    }


    static function leaflet_map($id)
    {



        // $employeeAttendance = MobileLog::query()
        //                     ->join('hrm_employee as b', 'mobile_log.hrm_employee_id', '=', 'b.id')
        //                     ->selectRaw('b.employee_name, mobile_log.*')
        //                     ->Where('mobile_log.id', $id);


        $employeeAttendance = DB::SELECT("SELECT a.id , a.lat,a.lon,b.employee_name,a.created_at,if(a.status=1,'In Time','Out Time') as status
         FROM mobile_log a JOIN hrm_employee b
                                        ON a.hrm_employee_id=b.id and a.id= $id");

        $employeeAttendance = $employeeAttendance[0];

        $data['locations'] = "['$employeeAttendance->employee_name,$employeeAttendance->created_at,$employeeAttendance->status', $employeeAttendance->lat,$employeeAttendance->lon]";
        $data['setlocations'] = "$employeeAttendance->lat,$employeeAttendance->lon";


        return view('mobile_app_attendance.map',$data);
    }



    public function destroy($id)
    {


        $id= decrypt($id);
        $attendance = MobileLog::find($id);
        $path = config('path_config.var_path').'/'.$attendance->attachment;

        if (file_exists($path)) {
            @unlink($path);
        }

        DB::UPDATE("Update mobile_log SET valid=0 WHERE id=$id");
        // $attendance->delete();

        return back();




    }





    /**
     * Remove the specified resource from storage.
     *
     * @par

     am  int  $id
     * @return \Illuminate\Http\Response
     */


    public function cancel(Request $request,$id){


    }
}
