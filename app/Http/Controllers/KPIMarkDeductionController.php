<?php

namespace App\Http\Controllers;

use Auth;
use Redirect;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HrmKPIMarkDeduction;

class KPIMarkDeductionController extends Controller
{
    public function index()
    {
        return view('kpi_markdeduct.kpi_markdeduct');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }




    public function kpi_markdeduct_list(Request $request)
    {
        $where = '';

        if ($hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id) {
            $where = " AND b.id = {$hrm_kpi_assesment_date_id}";
        }

       $data = DB::SELECT("SELECT
                              a.id,
                              CONCAT(c.description,
                                      ' | ',
                                      'From :',
                                      b.start_date,
                                      '  To :',
                                      b.end_date) as date_year,
                                      d.file_type_name,
                                      a.point,
                                      b.id as hrm_kpi_assesment_date_id,
                                      d.id as hrm_file_type_id
                          FROM
                              hrm_kpi_deduction_mark a
                                  JOIN
                              hrm_kpi_assesment_date b ON a.hrm_kpi_assesment_date_id = b.id
                                  AND a.valid = 1
                                  JOIN
                              hrm_kpi_assesment_year c ON b.hrm_kpi_assesment_year_id = c.id
                              $where
                              JOIN
                            hrm_file_type d ON a.hrm_file_type_id=d.id");

        return json_encode(array('data' =>$data));
    }






    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */






    public function store(Request $request)
    {

        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'hrm_kpi_assesment_date_id' => 'required',
            'file_type'                 => 'required',
            'point'                     => 'required',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('alert-danger', 'Already Exist!');
            return redirect('kpi_markdeduct')
                        ->withErrors($validator)
                        ->withInput();
        }


        $check_data=DB::SELECT("SELECT id FROM hrm_kpi_deduction_mark WHERE hrm_kpi_assesment_date_id=$request->hrm_kpi_assesment_date_id AND
          hrm_file_type_id=$request->file_type");

        if(!empty($check_data)){

           $request->session()->flash('alert-danger', 'Already Exist');
           return Redirect::to('kpi_markdeduct');
        }


        if(!empty($request->id)){
          $msg    ='Successfully Update!';
          $insert = HrmKPIMarkDeduction::find($request->id);

        }else{
          $msg    ='Successfully Insert!';
          $insert = new HrmKPIMarkDeduction;
        }

        $insert->hrm_kpi_assesment_date_id = $request->hrm_kpi_assesment_date_id;
        $insert->hrm_file_type_id          = $request->file_type;
        $insert->point                     = $request->point;
        $insert->users_id                  = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
            1,
           'Created KPI Mark Deduction',
            $insert,
            $insert->id,
            'hrm_kpi_deduction_mark'
        );

        $request->session()->flash('alert-success', $msg);
        return Redirect::to('kpi_markdeduct');
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

        $cancel = HrmKPIMarkDeduction::find($id);
        $cancel->delete();

        /*if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid Request !!');
            return Redirect()->back();
        }*/

        // $activity   = DB::table('hrm_kpi_assesment_date')->where('hrm_kpi_assesment_year_id','=',$id)->first();

        // if (!empty($activity)){
        //     session()->flash('alert-danger', "You can't delete this leave year !! This leave year already use.  ");
        //     return Redirect()->back();
        // }

        DB::table('hrm_kpi_deduction_mark')->delete($id);

        $this->recordActivity(
             1,
             'Deleted KPI Mark Deduction',
             $cancel,
             $id,
             'hrm_kpi_deduction_mark'
        );
        
        $request->session()->flash('alert-success', 'successfully deleted !');

        return Redirect::to('kpi_markdeduct');

    }






}
