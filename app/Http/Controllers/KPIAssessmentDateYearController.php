<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use DB;
use Datatables;
use Crypt;
use Validator;
use Config;
use Session;

use App\Models\HrmKPIAssesmentYear;
use App\Models\HrmKPIAssesmentDate;



class KPIAssessmentDateYearController extends Controller
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
        return view('kpi_assessment_date_year.kpi_year_list');
    }

    public function kpi_date_setup()
    {
        return view('kpi_assessment_date_year.kpi_date_setup');
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('leave_year.create_leaveyear');
    }



    public function kpi_year_list(Request $request){
        return json_encode(array('data' => HrmKPIAssesmentYear::all()));
    }


    public function kpi_date_list(Request $request){
       $data=DB::SELECT("SELECT
                                a.id,a.start_date,a.end_date,b.description,b.id as hrm_kpi_assesment_year_id
                            FROM
                                hrm_kpi_assesment_date a
                                    JOIN
                                hrm_kpi_assesment_year b ON b.id = a.hrm_kpi_assesment_year_id");

        return json_encode(array('data' =>$data));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function kpi_assessment_date_store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'hrm_kpi_assesment_year_id'=> 'required',
            'start_date'               => 'required',
            'end_date'                 => 'required',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Already Exist!');
            dd($request->all());
            return redirect('kpi_date_setup')
                        ->withErrors($validator)
                        ->withInput();
        }

        $start_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_date)));
        $end_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->end_date)));


        if(!empty($request->id)){

            $check_date = DB::SELECT ("SELECT id FROM hrm_kpi_assesment_date WHERE id NOT IN ($request->id) AND  '$start_date' BETWEEN  start_date AND end_date OR  id NOT IN ($request->id) AND '$end_date' BETWEEN  start_date AND end_date");

            if(!empty($check_date)){
              $request->session()->flash('alert-danger', 'Already Exist!');
              return redirect('kpi_date_setup')
              ->withErrors($validator)
              ->withInput();
            }

            $msg= 'Successfully Updated!';

          $insert = HrmKPIAssesmentDate::find($request->id);

        }else{

            $check_date = DB::SELECT ("SELECT id FROM hrm_kpi_assesment_date WHERE '$start_date' BETWEEN  start_date AND end_date   OR  '$end_date' BETWEEN  start_date AND end_date");
            // dd($check_date);

            if(!empty($check_date)){
              $request->session()->flash('alert-danger', 'Already Exist!');
              return redirect('kpi_date_setup')
              ->withErrors($validator)
              ->withInput();
            }

            $msg= 'Successfully Insert!';

          $insert = new HrmKPIAssesmentDate;
        }

        $insert->hrm_kpi_assesment_year_id = $request->hrm_kpi_assesment_year_id;
        $insert->start_date                = $start_date;
        $insert->end_date                  = $end_date ;
        $insert->users_id                  = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
            1,
           'Created KPI Date Range Setup',
            null,
            $insert->id,
            'hrm_kpi_assesment_date'
        );

        $request->session()->flash('alert-success', $msg);
        return Redirect::to('kpi_date_setup');

    }





    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'assessment_year'   => 'required|unique:hrm_kpi_assesment_year,description|max:255',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Already Exist!');
            return redirect('kpi_assessment_date_year')
                        ->withErrors($validator)
                        ->withInput();
        }

        if(!empty($request->id)){
          $insert = HrmKPIAssesmentYear::find($request->id);
        }else{
          $insert = new HrmKPIAssesmentYear;
        }

        $insert->description    = $request->assessment_year;
        $insert->users_id       = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             $request->id ? 'Updated KPI Year Setup' : 'Created KPI Year Setup',
             $request->id ? $insert->getChanges() : $insert,
             $insert->id,
             'hrm_kpi_assesment_year'
        );

        $request->session()->flash('alert-success', 'Successfully Insert!');
        return Redirect::to('kpi_assessment_date_year');
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

       $edit      = HrmLeaveYear::find($id);

        if (empty($edit)){
            session()->flash('alert-danger', 'Invalid Leave Year !!');
            return Redirect()->back();
        }

        return view('leave_year.edit_leaveyear')->with('edit_data',$edit);

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
    //dd("okkk");
        $validator = Validator::make($request->all(), [
            'date_from'   => 'required',
            'date_to'     => 'required',
            'leave_year'  => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('leaveyear')
                        ->withErrors($validator)
                        ->withInput();
        }

        $date_from     = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to       = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));




        $update = HrmLeaveYear::find($id);
        $update->date_from          = $date_from;
        $update->date_to            = $date_to;
        $update->leave_year         = $request->leave_year;
        $update->active_status      = 1;
        $update->save();


        $request->session()->flash('alert-success', 'data has been successfully Updated!');
        return Redirect::to('leaveyear');

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


    public function cancel(Request $request,$id){

        $cancel = HrmKPIAssesmentYear::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_kpi_assesment_date')->where('hrm_kpi_assesment_year_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this leave year !! This leave year already use.  ");
            return Redirect()->back();
        }

        DB::table('hrm_kpi_assesment_year')->delete($id);
        $request->session()->flash('alert-success', 'successfully deleted !');

        return Redirect::to('kpi_assessment_date_year');

    }


    public function kpi_date_setupcancel(Request $request,$id){

        $cancel = HrmKPIAssesmentDate::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        $activity   = DB::table('hrm_kpi_employee_master')->where('hrm_kpi_assesment_date_id','=',$id)->first();

        if (!empty($activity)){
            session()->flash('alert-danger', "You can't delete this date !! This date already use.  ");
            return Redirect()->back();
        }

        DB::table('hrm_kpi_assesment_date')->delete($id);
        $request->session()->flash('alert-success', 'successfully deleted !');

        return Redirect::to('kpi_date_setup');

    }








}
