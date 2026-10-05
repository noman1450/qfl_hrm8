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

use App\Models\HrmLoanType;



class LoanTypeController extends Controller
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
          return view('loan_type.loantype_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('loan_type.create_loantype');
    }




    public function loantypelist(Request $request){


       $loantype=DB::SELECT("SELECT a.id,
                                     a.loan_type,
                                     concat(b.salary_head,' || ',If(c.generate_type=1,'Additional','Deduction')) as loan_status
                                     FROM hrm_loan_type a
                                     JOIN hrm_salary_head b ON a.hrm_salary_head_id = b.id
                                     JOIN hrm_salary_head_group c ON b.hrm_salary_head_group_id = c.id");


        return json_encode(array('data' =>$loantype));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {



         $validator = Validator::make($request->all(), [
            'loan_type'   => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('loantype/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user_id = Auth::user()->id;


        $insert = new HrmloanType;
        $insert->loan_type          = $request->loan_type;
        $insert->hrm_salary_head_id = $request->salaryhead;
        $insert->users_id           = $user_id;

        $insert->save();

        $this->recordActivity(
             1,
             'Created Loan Type',
             $insert,
             $insert->id,
             'hrm_loan_type'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('loantype');

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

        $edit      = HrmloanType::find($id);

        if (empty($edit)){
            session()->flash('alert-danger', 'Invalid loan Type !!');
            return Redirect()->back();
        }

        return view('loan_type.edit_loantype')->with('edit_data',$edit);
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


        $validator = Validator::make($request->all(), [
            'loan_type'    => 'required',
            'loan_status'  => 'required',

        ]);

        if ($validator->fails()) {
            return redirect('loantype')
                        ->withErrors($validator)
                        ->withInput();
        }


        $update = HrmloanType::find($id);
        $update->loan_type       = $request->loan_type;
        $update->loan_status     = $request->loan_status;


        $update->save();

        $this->recordActivity(
             1,
             'Updated Loan Type',
             $update->getChanges(),
             $update->id,
             'hrm_loan_type'
        );


        $request->session()->flash('alert-success', 'data has been successfully Updated!');
        return Redirect::to('loantype');
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

        $query = DB::SELECT("SELECT id FROM hrm_loan_application WHERE hrm_loan_type_id = $id");

        if (empty($query)){

            DB::table('hrm_loan_type')->where('id', '=', $id)->delete();

            $this->recordActivity(
                 1,
                 'Deleted Loan Type',
                 null,
                 $id,
                 'hrm_loan_type'
            );

            $request->session()->flash('alert-success', 'successfully deleted !');
            return Redirect::to('loantype');

        }

        session()->flash('alert-danger', 'Sorry loan Type already use in Loan Application!!');
        return Redirect()->back();


    }

}
