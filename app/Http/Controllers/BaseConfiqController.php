<?php

namespace App\Http\Controllers;

use App\Models\HrmLocation;
use Illuminate\Http\Request;
// use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\HrmLocationInterval;
use App\Models\HrmDeviceInformation;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use DataTables;

class BaseConfiqController extends Controller
{
    function __construct() {
        $this->middleware('auth');
    }

// START interval_time  Calculation

    public function interval_time()
    {
        return view('base_confiq.interval_time_list');
    }

    public function interval_time_list()
    {
        $data = DB::SELECT("SELECT a.id,
                                   b.location_name,
                                   a.interval_time,
                                   a.grace_minute
                                FROM hrm_location_interval a
                                JOIN hrm_location  b ON a.hrm_location_id=b.id and b.valid=1");

        return json_encode(array('data' => $data));
    }


    public function interval_time_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location'      => 'required|unique:hrm_location_interval,hrm_location_id|max:4',
            'interval_time' => 'required',
            'grace_minute'  => 'required',
        ]);

        if ($validator->fails()) {

            $request->session()->flash('alert-danger', 'Invalid Input!');
            return redirect('interval_time');
                        // ->withErrors($validator)
                        // ->withInput();
        }

        $insert = new HrmLocationInterval;
        $insert->hrm_location_id = $request->location;
        $insert->interval_time   = $request->interval_time;
        $insert->grace_minute    = $request->grace_minute;

        $insert->save();

        $this->recordActivity(
             1,
             'Created Interval Time',
             null,
             $insert->id,
             'hrm_location_interval'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('interval_time');
    }

    public function interval_time_cancel(Request $request,$id)
    {
        $cancel = HrmLocationInterval::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        DB::table('hrm_location_interval')->where('id', '=', $id)->delete();

        $this->recordActivity(
             1,
             'Deleted Interval Time',
             null,
             $id,
             'hrm_location_interval'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('interval_time');
    }

// End interval_time  Calculation



// Start device_config  Calculation

    public function device_config()
    {
        return view('base_confiq.device_config_list')
             ->with('device', HrmDeviceInformation::all());
    }

    public function device_config_list()
    {
        $data = DB::SELECT("SELECT a.id,
                                   a.location_name,
                                   b.description
                                FROM hrm_location a
                                JOIN hrm_device_information  b ON a.hrm_device_information_id=b.id and a.valid=1");

        return json_encode(array('data' => $data));
    }

    public function device_config_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_name' => 'required',
            'location'    => 'required',
        ]);

        if ($validator->fails()) {

            $request->session()->flash('alert-danger', 'Invalid Input!');
            return redirect('interval_time');
                        // ->withErrors($validator)
                        // ->withInput();
        }

        $device_name = $request->device_name;
        $id = $request->location;

        DB::UPDATE("UPDATE hrm_location SET hrm_device_information_id=$device_name WHERE id=$id ");

        $this->recordActivity(
             1,
             'Created Attendance Device Config',
             null,
             $id,
             'hrm_location'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('device_config');
    }

    public function device_config_cancel(Request $request,$id)
    {
        $cancel = HrmLocation::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        DB::table('hrm_location_interval')->where('id', '=', $id)->delete();
        DB::UPDATE("UPDATE hrm_location SET hrm_device_information_id=0 WHERE id=$id ");

        $this->recordActivity(
             1,
             'Deleted Attendance Device Config',
             null,
             $id,
             'hrm_location'
        );

        $request->session()->flash('alert-success', 'Successfully deleted !');
        return Redirect::to('device_config');
    }


    // End device_config  Calculation




    // Start overtime_config  Calculation

    public function overtime_config()
    {
        return view('base_confiq.overtime_config_list');
    }

    public function overtime_config_list()
    {
        $data = DB::SELECT("SELECT
                                id,
                                location_name,
                                overtime_eligibility,
                                IF(ot_eligibility_include_exclude=1,'Include','Exclude') as ot_eligibility_include_exclude
                            FROM
                                hrm_location
                            WHERE
                                valid = 1 AND overtime_eligibility>0");

        return json_encode(array('data' => $data));
    }

    public function overtime_config_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'overtime_eligibility'        => 'required',
            'eligibility_include_exclude' => 'required',
            'location'                    => 'required',
        ]);

        if ($validator->fails()) {

            $request->session()->flash('alert-danger', 'Invalid Input!');
            return redirect('overtime_config');
                        // ->withErrors($validator)
                        // ->withInput();
        }

        $overtime_eligibility           = $request->overtime_eligibility;
        $ot_eligibility_include_exclude = $request->eligibility_include_exclude;
        $id                             = $request->location;

        DB::UPDATE("UPDATE hrm_location SET overtime_eligibility=$overtime_eligibility,ot_eligibility_include_exclude=$ot_eligibility_include_exclude WHERE id=$id ");

        $this->recordActivity(
             1,
             'Created OverTime Config',
             null,
             $id,
             'hrm_location'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated!');
        return Redirect::to('overtime_config');
    }

    public function overtime_config_cancel(Request $request,$id)
    {
        $cancel = HrmLocation::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        DB::table('hrm_location_interval')->where('id', '=', $id)->delete();
        DB::UPDATE("UPDATE hrm_location SET overtime_eligibility=0,ot_eligibility_include_exclude=1 WHERE id=$id ");

        $this->recordActivity(
             1,
             'Deleted OverTime Config',
             null,
             $id,
             'hrm_location'
        );

        $request->session()->flash('alert-success', 'Successfully deleted !');
        return Redirect::to('overtime_config');
    }



    // End overtime_config  Calculation

    public function weekly_holiday_config()
    {
        return view('base_confiq.weekly_holiday_config_list');
    }

    public function weekly_holiday_config_data_list()
    {
        // $configs = DB::table('hrm_holiday_configure')
        //     ->join('hrm_days_name', 'hrm_holiday_configure.hrm_days_name_id', '=', 'hrm_days_name.id')
        //     ->join('hrm_location', 'hrm_holiday_configure.hrm_location_id', '=', 'hrm_location.id')
        //     ->select('hrm_holiday_configure.*', 'hrm_days_name.days_name', 'hrm_location.location_name')
        //     ->get();

         $configs = DB::select("SELECT
                            a.start_date,
                            a.end_date,
                            c.location_name,
                            a.is_active,
                            GROUP_CONCAT(b.days_name) AS days_name
                        FROM
                            hrm_holiday_configure AS a
                                JOIN
                            hrm_days_name AS b ON a.hrm_days_name_id = b.id
                                AND a.is_active = 1 and a.end_date is null
                                JOIN
                            hrm_location AS c ON a.hrm_location_id = c.id
                        GROUP BY a.start_date , a.end_date , c.location_name , a.is_active");

        $configs = collect($configs);

        return datatables()->of($configs)
            ->addColumn('isActive', function ($configs) {

                if ($configs->is_active == 1) {
                    $status = "<span class='badge' style='background-color:#2f47f3'>Active</span>";
                } elseif ($configs->is_active == 0) {
                    $status = "<span class='badge' style='background-color:#de3d19'>Inactive</span>";
                }
                return $status;
            })
            ->rawColumns(['isActive'])
            ->make(true);
    }

    public function weekly_holiday_config_create()
    {
        return view('base_confiq.weekly_holiday_config_create');
    }

    public function weekly_holiday_config_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required',
            'hrm_location_id' => 'required',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // current date and same location then just update
        $reqDate = date('Y-m-d', strtotime($request->start_date));

        $currentDateCheck = DB::table('hrm_holiday_configure')
            ->where('hrm_location_id', $request->hrm_location_id)
            ->where('is_active', 1)
            ->where('end_date', null)
            ->first();

        if ($currentDateCheck) {
            if ($currentDateCheck->start_date > $reqDate) {
                return back()->with('warning', 'Can\'t add previous date');
            }

            if ($currentDateCheck->start_date == $reqDate) {
                DB::table('hrm_holiday_configure')
                    ->where('start_date', $currentDateCheck->start_date)
                    ->where('hrm_location_id', $currentDateCheck->hrm_location_id)
                    ->update([
                        'is_active' => 0,
                        'updated_at' => now()->toDateTimeString(),
                        'users_id' => auth()->id(),
                    ]);
            } else {
                $end_date = date('Y-m-d', strtotime("-1 days", strtotime($request->start_date)));

                DB::table('hrm_holiday_configure')
                    ->where('hrm_location_id', $request->hrm_location_id)
                    ->where('end_date', null)
                    ->where('is_active', 1)
                    ->update([
                        'end_date' => $end_date
                    ]);
            }
        }

        $array = [];

        foreach ($request->hrm_days_name_id as $key => $value) {
            array_push($array, [
                'hrm_days_name_id' => $request->hrm_days_name_id[$key],
                'hrm_location_id' => $request->hrm_location_id,
                'start_date' => date('Y-m-d', strtotime($request->start_date)),
                'users_id' => auth()->id(),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        DB::table('hrm_holiday_configure')
            ->insert($array);

        $this->recordActivity(
             1,
             'Created Weekly Holiday Config Of This Location',
             null,
             $request->hrm_location_id,
             'hrm_location'
        );

        return redirect('weekly_holiday_config');
    }

    public function weekly_holiday_config_change_location(Request $request)
    {
        $location = $request->location_id;
        $config = DB::select("
            select b.id, a.id as hrm_days_name_id, a.days_name from
            hrm_days_name as a
                left join
            hrm_holiday_configure as b on b.hrm_days_name_id = a.id
                and b.hrm_location_id = $location
                and b.end_date is null and b.is_active = 1
                order by a.id
        ");

        return response()->json($config);
    }

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

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // $bloodgroup      = HrmBloodGroup::find($id);

        // if (empty($bloodgroup)){
        //     session()->flash('alert-danger', 'Invalid blood group !!');
        //     return Redirect()->back();
        // }

        // return view('bloodgroup.edit_bloodgroup')->with('bloodgroup',$bloodgroup);
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
        // $validator = Validator::make($request->all(), [
        //     // 'group_name'    => 'required|unique:hrm_blood_group,blood_group|max:255',
        //     'group_name'    => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return Redirect::back()->withErrors($validator)->withInput();
        // }

        // $insert = HrmBloodGroup::find($id);
        // $insert->blood_group = $request->group_name;
        // $insert->save();

        // $request->session()->flash('alert-success', 'successfully updated!');
        // return Redirect::to('bloodgroup');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @par

     am  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cancel(Request $request,$id){

        // $cancel = HrmBloodGroup::find($id);

        // if (empty($cancel)){
        //     session()->flash('alert-danger', 'Invalid blood group !!');
        //     return Redirect()->back();
        // }

        // $activity   = DB::table('hrm_employee')->where('hrm_blood_group_id','=',$id)->first();

        // if (!empty($activity)){
        //     session()->flash('alert-danger', "You can't delete this blood group !! This blood group use to employees ");
        //     return Redirect()->back();
        // }

        // DB::table('hrm_blood_group')->where('id', '=', $id)->delete();

        // $request->session()->flash('alert-success', 'successfully deleted !');
        // return Redirect::to('bloodgroup');

    }
}
