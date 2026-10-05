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

use App\Models\HrmLeaveYear;

class LeaveYearController extends Controller
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
        return view('leave_year.leaveyear_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $max_date = HrmLeaveYear::where('active_status', 1)
            ->max('date_to');

        if ($max_date) {
            $next_date = \Carbon\Carbon::parse($max_date)->addDay()->toDateString();
        } else {
            // না থাকলে আজকের তারিখ
            $next_date = \Carbon\Carbon::today()->toDateString();
        }


        return view('leave_year.create_leaveyear',compact('next_date'));
    }



    public function leaveyearlist(Request $request){

       $leaveyearlist=DB::SELECT("SELECT id,
                                     date_from,
                                     date_to,
                                     leave_year
                                     FROM  hrm_leave_years
                                     WHERE active_status=1");


        return json_encode(array('data' =>$leaveyearlist));

    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    //    dd($request->all());
        $date_from     = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to       = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

        $validator = Validator::make($request->all(), [
                'date_from'  => 'required|date|before_or_equal:date_to',
                'date_to'    => 'required|date|after_or_equal:date_from',
                'year' => 'required',
            ]);
        // dd($request->all(),$validator->errors());
         $validator->after(function ($validator) use ($date_from, $date_to) {
                $exists = DB::table('hrm_leave_years')

                    ->where(function($q) use ($date_from, $date_to) {
                        $q->whereBetween('date_from', [$date_from, $date_to])
                        ->orWhereBetween('date_to', [$date_from, $date_to])
                        ->orWhere(function($query) use ($date_from, $date_to) {
                            $query->where('date_from', '<=', $date_from)
                                    ->where('date_to', '>=', $date_to);
                        });
                    })
                    ->where('active_status',1)
                    ->exists();



                if ($exists) {
                    $validator->errors()->add('date_from', 'Leave already applied for this date range.');
                    $validator->errors()->add('date_to', 'Leave already applied for this date range.');
                }
            });

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }




        $insert = new HrmLeaveYear;
        $insert->date_from          = $date_from;
        $insert->date_to            = $date_to;
        $insert->leave_year         = $request->year;
        $insert->active_status      = 1;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Leave Year Setup Name',
             null,
             $insert->id,
             'hrm_leave_years'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('leaveyear');
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

        $date_from     = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to       = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
        $validator = Validator::make($request->all(), [
                'date_from'  => 'required|date|before_or_equal:date_to',
                'date_to'    => 'required|date|after_or_equal:date_from',
                'leave_year' => 'required',
            ]);

            $validator->after(function ($validator) use ($date_from, $date_to,$id) {
                $exists = DB::table('hrm_leave_years')
                    ->where('id','!=', $id)
                    ->where(function($q) use ($date_from, $date_to) {
                        $q->whereBetween('date_from', [$date_from, $date_to])
                        ->orWhereBetween('date_to', [$date_from, $date_to])
                        ->orWhere(function($query) use ($date_from, $date_to) {
                            $query->where('date_from', '<=', $date_from)
                                    ->where('date_to', '>=', $date_to);
                        });
                    })
                    ->where('active_status',1)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('date_from', 'Leave already applied for this date range.');
                    $validator->errors()->add('date_to', 'Leave already applied for this date range.');
                }
            });

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }



        $date_from     = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to       = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));




        $update = HrmLeaveYear::find($id);
        $update->date_from          = $date_from;
        $update->date_to            = $date_to;
        $update->leave_year         = $request->leave_year;
        $update->active_status      = 1;
        $update->save();

        $this->recordActivity(
             1,
             'Updated Leave Year Setup Name',
             $update->getChanges(),
             $update->id,
             'hrm_leave_years'
        );

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

        $cancel = HrmLeaveYear::find($id);

        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        // dd($id);


        $exists = DB::table('hrm_leave_application')->where('hrm_leave_years_id', $id)->where('valid',1)->exists()
                || DB::table('hrm_leave_ledger')->where('hrm_leave_years_id', $id)->where('valid',1)->exists()
                || DB::table('hrm_employee_holiday')->where('hrm_leave_years_id', $id)->where('valid',1)->exists();



        if (!empty($exists)){
            session()->flash('alert-danger', "You can't delete this leave year !! This leave year already use.  ");
            return Redirect()->back();
        }

        DB::table('hrm_leave_years')
            ->where('id', $id)
            ->update(['active_status' => 0]);

        $this->recordActivity(
             1,
             'Deleted Leave Year Setup Name',
             null,
             $id,
             'hrm_leave_years'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('leaveyear');

    }



}
