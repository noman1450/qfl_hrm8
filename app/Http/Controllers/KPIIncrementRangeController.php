<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HrmKPIIncrementRange;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class KPIIncrementRangeController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        return view('kpi_increment_range.kpi_increment_range_list');
    }

    public function create()
    {
        return view('kpi_mark.create_kpi_mark');
    }

    public function kpi_increment_range_list(Request $request)
    {
        $where = '';

        if ($hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id) {
            $where = " WHERE b.id = {$hrm_kpi_assesment_date_id}";
        }

        $data = DB::SELECT("SELECT
                a.id,
                CONCAT(a.number_from, '-', a.number_to) AS number_range,
                IF(a.entry_status = 1,
                    CONCAT(a.increment_amount,
                            IF(a.increment_amount_type = 1,
                                '%',
                                'Tk.')),
                    'Promoted') AS increment_amounts,
                IF(a.entry_status = 1,
                    'Increment',
                    'Promotion') AS entry_statuss,
                a.number_from,
                a.number_to,
                a.increment_amount,
                a.increment_amount_type,
                a.entry_status,
                CONCAT(c.description,
                        ' | ',
                        'From :',
                        b.start_date,
                        '  To :',
                        b.end_date) AS date_year,
                b.id AS hrm_kpi_assesment_date_id
            FROM
                hrm_kpi_increment_range a
                    JOIN
                hrm_kpi_assesment_date b ON b.id = a.hrm_kpi_assesment_date_id
                    AND a.valid = 1
                    JOIN
                hrm_kpi_assesment_year c ON b.hrm_kpi_assesment_year_id = c.id
                $where
        ");



        return json_encode(array('data' => $data));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number_from'  => 'required',
            'number_to'    => 'required',
            'entry_status' => 'required',
            'hrm_kpi_assesment_date_id' => 'required',

        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Invalid Request!');
            return redirect('kpi_increment_range')
                        ->withErrors($validator)
                        ->withInput();
        }



        if(!empty($request->id)){
         $insert = HrmKPIIncrementRange::find($request->id);
        }else{
         $insert = new HrmKPIIncrementRange;
        }
        $insert->number_from               = $request->number_from;
        $insert->number_to                 = $request->number_to;
        $insert->increment_amount          = $request->increment_amount;
        $insert->increment_amount_type     = $request->increment_amount_type;
        $insert->entry_status              = $request->entry_status;
        $insert->hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id;
        $insert->valid                     = 1;
        $insert->users_id                  = Auth::user()->id;
        $insert->save();

        $request->session()->flash('alert-success', 'Successfully Insert!');
        return Redirect::to('kpi_increment_range');
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



    public function destroy($id)
    {
        //
    }

    public function cancel(Request $request,$id){



        $cancel = HrmKPIIncrementRange::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }

        // $activity   = DB::table('hrm_kpi_task_details')->where('hrm_kpi_task_id','=',$id)
        //   ->first();

        // if (!empty($activity)){
        //     session()->flash('alert-danger', "You can't delete this task name !! This task Name already used ");
        //     return Redirect()->back();
        // }

        $cancel->valid              = 0;
        $cancel->users_id           = Auth::user()->id;
        $cancel->save();

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('kpi_increment_range');

    }
}
