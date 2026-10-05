<?php

namespace App\Http\Controllers;

use Config;
use DateTime;
use Redirect;
use Validator;
use App\Models\HrmReligion;
use Illuminate\Http\Request;
use App\Models\HrmBloodGroup;

use App\Models\HrmEmployeeShift;
use App\Models\HrmLastPromotion;
use App\Models\HrmMaritalStatus;
use App\Models\HrmEmployeeJobInfo;
use Illuminate\Support\Facades\DB;
use App\Models\HrmEmployeeActivity;
use App\Models\HrmEmployeeCardCode;
use App\Models\HrmSalaryGradeMaster;
use Illuminate\Support\Facades\Auth;

class TransferAndPromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
    }

    public function employeetransfer()
    {

       return view('transfer_promotion.transfer_list');

        // return view('transfer_promotion.employee_transfer')
        //     ->with('religion',HrmReligion::all())
        //     ->with('marital_status',HrmMaritalStatus::all())
        //     ->with('blood_group',HrmBloodGroup::all());
    }

    public function last_promotion_create()
    {

       return view('transfer_promotion.last_promotion_list');

    }



    public function employeepromotion()
    {

       return view('transfer_promotion.promotion_list');

        // return view('transfer_promotion.employee_promotion')
        //     ->with('religion',HrmReligion::all())
        //     ->with('marital_status',HrmMaritalStatus::all())
        //     ->with('blood_group',HrmBloodGroup::all());
    }
    public function employeepromotioncreate()
    {

        // return view('transfer_promotion.employee_promotion')
        //     ->with('religion',HrmReligion::all())
        //     ->with('marital_status',HrmMaritalStatus::all())
        //     ->with('blood_group',HrmBloodGroup::all());
    }

    public function employeetransfercreate()
    {

        return view('transfer_promotion.employee_transfer')
            ->with('religion',HrmReligion::all())
            ->with('marital_status',HrmMaritalStatus::all())
            ->with('blood_group',HrmBloodGroup::all());
    }




    public function last_promotion_list_data(Request $request)
    {
        $parameter ="";
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

        if (isset($request->location)){
            $parameter  = $parameter." AND b.hrm_location_id = ".$request->location ;
        }

        if (isset($request->date_range)){
            $parameter  =  $parameter." AND a.promotion_date between '$date_from' AND '$date_to'" ;
        }

        $user_id=Auth::user()->id;
        $plant_list=DB::SELECT("SELECT
                            bb.id as employee_id,
                            CONCAT(bb.employee_name,'|',b.employee_code) as employee_name,
                            bb.Images,
                            LPAD(bb.id, 5, '0') as Unique_Code,
                            c.location_name,
                            d.depertment_name,
                            e.alis,
                            a.promotion_date
                        FROM
                            hrm_last_promotion a
                                JOIN
                            hrm_employee_job_info b ON a.hrm_employee_id = b.hrm_employee_id
                               AND b.employee_activity=1
                               $parameter
                               JOIN
                            hrm_employee bb ON b.hrm_employee_id=bb.id
                                JOIN
                            hrm_location c ON b.hrm_location_id = c.id
                                JOIN
                            hrm_depertment d ON b.hrm_depertment_id = d.id
                                JOIN
                            hrm_designation e ON b.hrm_designation_id = e.id
                                JOIN
                            hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                JOIN
                            user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id ");


        return json_encode(array('data' =>$plant_list));


    }





    public function promotionandtransfer_list(Request $request)
    {
        $parameter = "AND a.hrm_employee_activity_status_id = ".$request->activity_status_id;

        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to   = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

        if (isset($request->location)){
            $parameter  = $parameter." AND b.hrm_location_id = ".$request->location ;
        }

        if (isset($request->date_range)){
            $parameter  =  $parameter." AND a.start_date between '$date_from' AND '$date_to'" ;
        }

        $user_id = Auth::user()->id;

        $plant_list=DB::SELECT("SELECT
                            j.id as employee_id,
                            j.employee_name,
                            j.Images,
                            LPAD(j.id, 5, '0') as Unique_Code,
                            c.location_name,
                            d.depertment_name,
                            e.alis,
                            f.joining_date,
                            f.confirmation_date,
                            a.start_date as activity_date,
                            k.location_name as newlocation_name,
                            l.depertment_name newdepertment_name,
                            m.alis newdesignation_name,
                            g.category_name
                        FROM
                            hrm_employee_activity a
                                JOIN
                            hrm_employee_job_info b ON a.old_hrm_employee_job_info_id = b.id
                               $parameter
                                JOIN
                            hrm_location c ON b.hrm_location_id = c.id
                                JOIN
                            hrm_depertment d ON b.hrm_depertment_id = d.id
                                JOIN
                            hrm_designation e ON b.hrm_designation_id = e.id
                                JOIN
                            hrm_employee_joining f ON b.hrm_employee_id = f.hrm_employee_id
                                JOIN
                            hrm_category g ON b.hrm_category_id = g.id
                                JOIN
                            hrm_employee j ON b.hrm_employee_id = j.id
                                JOIN
                            hrm_employee_job_info h ON a.hrm_employee_job_info_id=h.id
                                JOIN
                            hrm_location k ON h.hrm_location_id = k.id
                                JOIN
                            hrm_depertment l ON h.hrm_depertment_id = l.id
                                JOIN
                            hrm_designation m ON h.hrm_designation_id = m.id
                                JOIN
                            user_location n ON b.hrm_location_id = n.hrm_location_id AND n.users_id = $user_id

                        ");


        return json_encode(array('data' =>$plant_list));


    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function employee_transfer(Request $request)
    {

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'employee_name'     => 'required',
            'depertment'        => 'required|exists:hrm_depertment,id',
            'designation'       => 'required|exists:hrm_designation,id',
            'category'          => 'required|exists:hrm_category,id',
            'job_location'      => 'required|exists:hrm_location,id',
            'section'           => 'required|exists:hrm_section,id',
            'working_shift'     => 'required|exists:hrm_shift,id',
            'overtime'          => 'required',
            'manage_by'         => 'required|exists:hrm_employee,id',
            'employeestatus'    => 'required|exists:hrm_employment_status,id',
            'basic_salary'      => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $joining_date       = date('Y-m-d', strtotime(str_replace('/', '-', $request->oldjoining_date)));
        $confirmation_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->oldconfirmation_date)));
        $transfer_date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->transfer_date)));

        $xmasDay              = new DateTime($transfer_date.'- 1 day');
        $end_date             = $xmasDay->format('Y-m-d');

        $user_id = Auth::user()->id;
        $ldate   = date('Y-m-d H:i:s');
        $check_datas = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info WHERE id = $request->hrm_employee_job_info_id)) AND start_date='$transfer_date' ");


        if(!empty($check_datas)){
             $request->session()->flash('alert-danger', 'Sorry This date has been already used, Please Check!');
                return redirect('employeetransfer');

        }

        $check_data = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id = $request->hrm_employee_job_info_id AND end_date is null");


        if($transfer_date < $check_data[0]->start_date){
             $request->session()->flash('alert-danger', 'Sorry you can not give back date employee activity!');
                return redirect('employeetransfer');

        }



            DB::beginTransaction();
            try{


            DB::update("UPDATE hrm_employee_job_info SET employee_activity = 0
                        WHERE id = $request->hrm_employee_job_info_id ");

            $oldshift = DB::SELECT("SELECT * FROM hrm_employee_shift Where hrm_employee_job_info_id = $request->hrm_employee_job_info_id and id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=$request->hrm_employee_job_info_id)");

            $end_shift_date   = $oldshift[0]->start_date;
            $start_shift_date = $transfer_date;



            // if($transfer_date>$end_shift_date){
            //     $end_shift_date = date('Y-m-d', strtotime($transfer_date." -1 days"));
            // }






                $insert     = new HrmEmployeeJobInfo;
                $insert->employee_code              = $request->employee_code;
                $insert->hrm_employee_id            = $request->employee_name;
                $insert->hrm_depertment_id          = $request->depertment;
                $insert->hrm_designation_id         = $request->designation;
                $insert->hrm_category_id            = $request->category;
                $insert->hrm_employment_status_id   = $request->employeestatus;
                $insert->hrm_manage_by_id           = $request->manage_by;
                $insert->employee_shift_status      = 1;
                $insert->overtime_status            = $request->overtime;
                $insert->employee_activity          = 1;
                $insert->basic_salary               = $request->basic_salary;
                $insert->hrm_location_id            = $request->job_location;
                $insert->hrm_plant_id               = $request->plant_name;
                $insert->hrm_section_id             = $request->section;
                $insert->insurance                  = $request->insurance;

                $insert->users_id                   = Auth::user()->id;
                $insert->save();

                $new_job_id = $insert->id;

                DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date'
                        WHERE end_date is null AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id ");





                 // Need to work in future


                    if($transfer_date < $end_shift_date){

                        DB::update("UPDATE hrm_employee_shift
                        SET hrm_employee_job_info_id = ?
                        WHERE id IN (
                            SELECT id
                            FROM (
                                SELECT id
                                FROM hrm_employee_shift
                                WHERE start_date >= ?
                                AND valid = 1
                                AND hrm_employee_job_info_id IN (
                                    SELECT id
                                    FROM hrm_employee_job_info
                                    WHERE hrm_employee_id = ?
                                )
                            ) AS temp
                        )
                        ",[$insert->id, $transfer_date, $request->employee_name]);



                        $max_id = DB::table('hrm_employee_shift')
                            ->where('valid', 1)
                            ->where('start_date', '<', $transfer_date)
                            ->whereIn('hrm_employee_job_info_id', function ($query) use ($request) {
                                $query->select('id')
                                    ->from('hrm_employee_job_info')
                                    ->where('hrm_employee_id', $request->employee_name);
                            })
                            ->max('id');

                        $pre_date = date('Y-m-d', strtotime($transfer_date . ' -1 day'));

                            // dd($pre_date,$max_id);
                        DB::table('hrm_employee_shift')
                        ->where('id', $max_id)
                        ->update(['end_date' => $pre_date,'comment' =>'Update From Transfer']);



                        // $ ->end_date  = $max_pre_date;
                        // $insert_shift->save();

                        $min_id = DB::table('hrm_employee_shift')
                            ->where('valid', 1)
                            ->where('start_date', '>', $transfer_date)
                            ->whereIn('hrm_employee_job_info_id', function ($query) use ($request) {
                                $query->select('id')
                                    ->from('hrm_employee_job_info')
                                    ->where('hrm_employee_id', $request->employee_name);
                            })
                            ->min('id');

                            DB::table('hrm_employee_shift')
                            ->where('id', $min_id)
                            ->update(['start_date' => $transfer_date,'comment' =>'Update From Transfer 2']);

                    }else{

                        $pre_date = date('Y-m-d', strtotime($transfer_date . ' -1 day'));
                        DB::update("UPDATE hrm_employee_shift
                                        SET end_date= '$pre_date',
                                        comment='from employee transfer',
                                        users_id= $user_id,
                                        updated_at='$ldate'
                                    WHERE  end_date is null
                                        AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id  ");

                            $insert_shift  = new HrmEmployeeShift;
                            $insert_shift->hrm_employee_job_info_id = $insert->id;
                            $insert_shift->hrm_shift_id             = $request->working_shift;
                            $insert_shift->start_date               = $transfer_date;
                            $insert_shift->valid                    = 1;
                            $insert_shift->comment                  = 'Transfer';
                            $insert_shift->users_id                 = Auth::user()->id;
                            $insert_shift->save();
                    }






            if(Config::get('module_config.payroll_module') == 1){

                $hrm_employee_job_info_id     = $insert->id;
                $hrm_salary_grade_master_id   = HrmSalaryGradeMaster::where('hrm_salary_grade_id', $request->salary_grade)->first()->id;
                $gross_salary                 = $insert->basic_salary;
                $hrm_designation_id           = $insert->hrm_designation_id;
                $old_hrm_employee_job_info_id = $request->hrm_employee_job_info_id;

                $getFunction = new CommonController();
                $getFunction->insert_salary_config($hrm_employee_job_info_id,$hrm_salary_grade_master_id,$gross_salary,$hrm_designation_id,$old_hrm_employee_job_info_id);
            }


            $insert_employee_activity  = new HrmEmployeeActivity;
            $insert_employee_activity->hrm_employee_job_info_id  = $insert->id;
            $insert_employee_activity->activity_date             = $transfer_date;
            $insert_employee_activity->comment                   = $request->comment ;
            $insert_employee_activity->users_id                  = Auth::user()->id;
            $insert_employee_activity->activity                  = 3;
            $insert_employee_activity->hrm_employee_activity_status_id  = 2;
            $insert_employee_activity->start_date                = $transfer_date;
            $insert_employee_activity->old_hrm_employee_job_info_id = $request->hrm_employee_job_info_id;
            $insert_employee_activity->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Employee Transfer',
                 $insert_employee_activity,
                 $insert_employee_activity->id,
                 'hrm_employee_activity'
            );

            }catch (\Exception $e) {
                DB::rollback();
                $validator->errors()->add('field', $e->getMessage());
                return response()->json($validator->errors()->all());
            }

            $request->session()->flash('alert-success', 'data has been successfully added!');
            return Redirect::to('employeetransfer');
    }




    public function employee_promotion(Request $request)
    {


        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'employee_name'     => 'required',
            'depertment'        => 'required|exists:hrm_depertment,id',
            'designation'       => 'required|exists:hrm_designation,id',
            'category'          => 'required|exists:hrm_category,id',
            'job_location'      => 'required|exists:hrm_location,id',
            'section'           => 'required|exists:hrm_section,id',
            'working_shift'     => 'required|exists:hrm_shift,id',
            'overtime'          => 'required',
            'manage_by'         => 'required|exists:hrm_employee,id',
            'employeestatus'    => 'required|exists:hrm_employment_status,id',
            'basic_salary'      => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $joining_date       = date('Y-m-d', strtotime(str_replace('/', '-', $request->oldjoining_date)));
        $confirmation_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->oldconfirmation_date)));
        $transfer_date      = date('Y-m-d', strtotime(str_replace('/', '-', $request->transfer_date)));

        $xmasDay            = new DateTime($transfer_date.'- 1 day');
        $end_date           = $xmasDay->format('Y-m-d');

        $user_id            = Auth::user()->id;
        $ldate              = date('Y-m-d H:i:s');



       $check_datas = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id in (SELECT id FROM hrm_employee_job_info WHERE hrm_employee_id in (SELECT hrm_employee_id FROM hrm_employee_job_info WHERE id = $request->hrm_employee_job_info_id)) AND start_date='$transfer_date' ");


        if(!empty($check_datas)){
             $request->session()->flash('alert-danger', 'Sorry This date has been already used, Please Check!');
                return redirect('employeepromotion');

        }


        $check_data = DB::select("SELECT start_date FROM hrm_employee_activity  WHERE hrm_employee_job_info_id = $request->hrm_employee_job_info_id AND end_date is null");


        if($transfer_date<$check_data[0]->start_date){
             $request->session()->flash('alert-danger', 'Sorry you can not give back date employee activity!');
                return redirect('employeepromotion');

        }




            DB::beginTransaction();
            try{


            // $insert_employee_activity  = new HrmEmployeeActivity;
            // $insert_employee_activity->hrm_employee_job_info_id  = $request->hrm_employee_job_info_id;
            // $insert_employee_activity->activity_date             = $transfer_date ;
            // $insert_employee_activity->comment                   = $request->comment;
            // $insert_employee_activity->users_id                  = Auth::user()->id;
            // $insert_employee_activity->status                    = 2;
            // $insert_employee_activity->apply_for                 = 2;
            // $insert_employee_activity->save();

            // DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date'
            //             WHERE end_date is null AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id ");

            $insert_employee_activity  = new HrmLastPromotion;
            $insert_employee_activity->hrm_employee_id  = $request->employee_name;
            $insert_employee_activity->promotion_date   = $transfer_date;
            $insert_employee_activity->users_id         = Auth::user()->id;
            $insert_employee_activity->save();



            DB::update("UPDATE hrm_employee_job_info SET employee_activity = 0
                        WHERE id = $request->hrm_employee_job_info_id ");

            $oldshift = DB::SELECT("SELECT * FROM hrm_employee_shift Where hrm_employee_job_info_id = $request->hrm_employee_job_info_id and id in (SELECT max(id) FROM hrm_employee_shift WHERE hrm_employee_job_info_id=$request->hrm_employee_job_info_id)");

            $end_shift_date   = $oldshift[0]->start_date;
            $start_shift_date =date('Y-m-d', strtotime($end_shift_date." +1 days"));


            // if($transfer_date>$end_shift_date){
            //     $end_shift_date = date('Y-m-d', strtotime($transfer_date." -1 days"));
            // }




            //Here
            DB::update("UPDATE hrm_employee_shift SET end_date= '$end_shift_date',comment='from employee promotion',users_id= $user_id,updated_at='$ldate'  WHERE  end_date is null AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id  ");


                $insert     = new HrmEmployeeJobInfo;
                $insert->employee_code              = $request->employee_code;
                $insert->hrm_employee_id            = $request->employee_name;
                $insert->hrm_depertment_id          = $request->depertment;
                $insert->hrm_designation_id         = $request->designation;
                $insert->hrm_category_id            = $request->category;
                $insert->hrm_employment_status_id   = $request->employeestatus;
                $insert->hrm_manage_by_id           = $request->manage_by;
                $insert->employee_shift_status      = 1;
                $insert->overtime_status            = $request->overtime;
                $insert->hrm_plant_id               = $request->plant_name;
                $insert->employee_activity          = 1;
                $insert->basic_salary               = $request->basic_salary;
                $insert->hrm_location_id            = $request->job_location;
                $insert->hrm_section_id             = $request->section;
                $insert->insurance                  = $request->insurance;
                $insert->users_id                   = Auth::user()->id;
                $insert->save();





                $new_job_id = $insert->id;
                DB::update("UPDATE hrm_employee_activity SET end_date = '$end_date'
                        WHERE end_date is null AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id ");


                $insert_shift  = new HrmEmployeeShift;
                $insert_shift->hrm_employee_job_info_id = $insert->id;
                $insert_shift->hrm_shift_id             = $request->working_shift;
                $insert_shift->start_date               = $transfer_date;
                $insert_shift->valid                    = 1;
                $insert_shift->comment                  = 'Promotion';
                $insert_shift->users_id                 = Auth::user()->id;
                $insert_shift->save();

                $max_pre_date = null;
                // Need to work in future
                if($transfer_date<$end_shift_date){

                        DB::update("UPDATE hrm_employee_shift
                        SET hrm_employee_job_info_id = ?
                        WHERE id IN (
                            SELECT id
                            FROM (
                                SELECT id
                                FROM hrm_employee_shift
                                WHERE start_date >= ?
                                  AND valid = 1
                                  AND hrm_employee_job_info_id IN (
                                      SELECT id
                                      FROM hrm_employee_job_info
                                      WHERE hrm_employee_id = ?
                                  )
                            ) AS temp
                        )
                    ", [$insert->id, $transfer_date, $request->employee_name]);



                        $max_id = DB::table('hrm_employee_shift')
                            ->where('valid', 1)
                            ->where('start_date', '<', $transfer_date)
                            ->whereIn('hrm_employee_job_info_id', function ($query) use ($request) {
                                $query->select('id')
                                    ->from('hrm_employee_job_info')
                                    ->where('hrm_employee_id', $request->employee_name);
                            })
                            ->max('id');

                        $pre_date = date('Y-m-d', strtotime($transfer_date . ' -1 day'));

                            // dd($pre_date,$max_id);
                        DB::table('hrm_employee_shift')
                        ->where('id', $max_id)
                        ->update(['end_date' => $pre_date,'comment' =>'Update From Promotion']);



                        // $ ->end_date  = $max_pre_date;
                        // $insert_shift->save();

                        $min_id = DB::table('hrm_employee_shift')
                            ->where('valid', 1)
                            ->where('start_date', '>', $transfer_date)
                            ->whereIn('hrm_employee_job_info_id', function ($query) use ($request) {
                                $query->select('id')
                                    ->from('hrm_employee_job_info')
                                    ->where('hrm_employee_id', $request->employee_name);
                            })
                            ->min('id');

                            DB::table('hrm_employee_shift')
                            ->where('id', $min_id)
                            ->update(['start_date' => $transfer_date,'comment' =>'Update From Promotion 2']);

                    }else{

                         $pre_date = date('Y-m-d', strtotime($transfer_date . ' -1 day'));
                          DB::update("UPDATE hrm_employee_shift
                                        SET end_date= '$pre_date',
                                        comment='from employee promotion',
                                        users_id= $user_id,
                                        updated_at='$ldate'
                                    WHERE  end_date is null
                                        AND hrm_employee_job_info_id = $request->hrm_employee_job_info_id  ");

                            $insert_shift  = new HrmEmployeeShift;
                            $insert_shift->hrm_employee_job_info_id = $insert->id;
                            $insert_shift->hrm_shift_id             = $request->working_shift;
                            $insert_shift->start_date               = $transfer_date;
                            $insert_shift->valid                    = 1;
                            $insert_shift->comment                  = 'Promotion';
                            $insert_shift->users_id                 = Auth::user()->id;
                            $insert_shift->save();



                }



                $oldcard = DB::SELECT("SELECT * FROM hrm_employee_card_code Where hrm_employee_job_info_id = $request->hrm_employee_job_info_id");

                if(empty($oldcard)){

                }else{

                $insert_card     = new HrmEmployeeCardCode;
                $insert_card->card_code                = $oldcard[0]->card_code;
                $insert_card->device_id                = $oldcard[0]->device_id;
                $insert_card->hrm_employee_job_info_id = $insert->id;
                $insert_card->save();


                }


            // dd($new_job_id);



            if(Config::get('module_config.payroll_module') == 1){

                    $hrm_employee_job_info_id     = $insert->id;
                    $hrm_salary_grade_master_id   = HrmSalaryGradeMaster::where('hrm_salary_grade_id', $request->salary_grade)->first()->id;
                    $gross_salary                 = $insert->basic_salary;
                    $hrm_designation_id           = $insert->hrm_designation_id;
                    $old_hrm_employee_job_info_id = $request->hrm_employee_job_info_id;

                    $getFunction = new CommonController();
                    $getFunction->insert_salary_config($hrm_employee_job_info_id,$hrm_salary_grade_master_id,$gross_salary,$hrm_designation_id,$old_hrm_employee_job_info_id);


            }


            $insert_employee_activity  = new HrmEmployeeActivity;
            $insert_employee_activity->hrm_employee_job_info_id  = $insert->id;
            $insert_employee_activity->activity_date             = $transfer_date;
            $insert_employee_activity->comment                   = $request->comment;
            $insert_employee_activity->users_id                  = Auth::user()->id;
            $insert_employee_activity->activity                  = 1;
            $insert_employee_activity->hrm_employee_activity_status_id  = 3;
            $insert_employee_activity->start_date                   = $transfer_date;
            $insert_employee_activity->old_hrm_employee_job_info_id = $request->hrm_employee_job_info_id;
            $insert_employee_activity->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Employee Promotion',
                 $insert_employee_activity,
                 $insert_employee_activity->id,
                 'hrm_employee_activity'
            );

            }catch (\Exception $e) {
                DB::rollback();
                $validator->errors()->add('field', $e->getMessage());
                return response()->json($validator->errors()->all());
            }

            $request->session()->flash('alert-success', 'data has been successfully added!');
            return Redirect::to('employeepromotion');
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }



    public function last_promotion_data_submit(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'promotion_date'    => 'required',
            'employee_name'     => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $promotion_date       = date('Y-m-d', strtotime(str_replace('/', '-', $request->promotion_date)));

        $check_data = DB::select("SELECT id FROM hrm_last_promotion WHERE hrm_employee_id=$request->employee_name AND   promotion_date='$promotion_date' ");


        if(!empty($check_data)){
            $request->session()->flash('alert-danger', 'Sorry This date has been already Insert, Please Check!');
            return redirect('last_promotion_create');

        }



        $insert_employee_activity  = new HrmLastPromotion;
        $insert_employee_activity->hrm_employee_id  = $request->employee_name;
        $insert_employee_activity->promotion_date   = $promotion_date;
        $insert_employee_activity->users_id         = Auth::user()->id;
        $insert_employee_activity->save();


        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('last_promotion_create');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
