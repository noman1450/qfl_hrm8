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

use App\Models\HrmLoanApplication;
use App\Models\HrmLoanLedger;


class LoanApplicationController extends Controller
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
         return view('loan_application.loan_application_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('loan_application.create_loan_application');

    }


    public function loanapprovedlist()
    {
         return view('loan_application.loan_approved_list');

    }

   public function paidloanledger()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");
         return view('loan_application.paid_loanledger_list')
          -> with('default_user_location',  $default_user_location) ;


    }

    public function loanledger()
    {

        $user_id = Auth::user()->id;
        $default_user_location = DB::select("SELECT b.id,b.location_name
                                                FROM `user_location` a
                                                JOIN hrm_location b ON a.`hrm_location_id`= b.id
                                                and a.`users_id`= $user_id
                                                AND a.`default_location`=1");

         return view('loan_application.loanledger_list')
          -> with('default_user_location',  $default_user_location) ;
    }



    public function loanledgerdetails($id)
    {


        $list = DB::SELECT("SELECT
                                a.id,
                                a.entry_status,
                                CONCAT(c.employee_name, ' | ', e.employee_code,' | ',IFNULL(gg.accounts_code, '-')) AS employee_name,
                                f.designation_name,
                                d.loan_type,
                                a.narration,
                                a.`credit` AS loan_amount,
                                a.`debit` AS payment,
                                h.depertment_name,
                                concat(i.month_name,' - ',a.year_id) as month_year,
                                b.installment_size,
                                b.no_of_installment,
                                b.installment_start_date as approved_date,

                                (SELECT Sum(credit) FROM hrm_loan_ledger WHERE hrm_loan_application_id = $id AND valid = 1 Group By hrm_loan_application_id ) as opening_loan,
                                (SELECT Sum(debit) FROM hrm_loan_ledger WHERE hrm_loan_application_id = $id AND valid = 1 Group By hrm_loan_application_id) as paid_loan,
                                (SELECT Sum(credit-debit) FROM hrm_loan_ledger WHERE hrm_loan_application_id = $id AND valid = 1 Group By hrm_loan_application_id) as remaining_loan

                            FROM
                                hrm_loan_ledger a
                                    JOIN
                                hrm_loan_application b ON a.hrm_loan_application_id = b.id
                                    AND a.valid = 1 AND b.id = $id AND a.valid = 1 AND b.approved_status=2
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    JOIN
                                hrm_loan_type d ON b.hrm_loan_type_id = d.id
                                    JOIN
                                hrm_employee_job_info e ON e.hrm_employee_id = c.id
                                    AND e.id in (SELECT max(id) FROM hrm_employee_job_info WHERE hrm_employee_id = c.id)
                                    JOIN
                                hrm_designation f ON e.hrm_designation_id = f.id
                                    Join
                                hrm_location g ON e.hrm_location_id=g.id
                                    JOIN
                                hrm_employee_salary gg ON e.id = gg.hrm_employee_job_info_id
                                    JOIN
                                hrm_depertment h ON e.hrm_depertment_id = h.id
                                    JOIN
                                hrm_month i ON  a.hrm_month_id = i.id");


         return view('loan_application.loanledger_details_list')
               ->with('data',  $list);

    }





   public function loanapplicationlist(Request $request ){

    $status = $request->status;
    $user_id = Auth::user()->id;


    $list = DB::SELECT("SELECT
                            a.id,
                            a.opening_loan_amount,
                            a.paid_amount,

                            -- IFNULL((SELECT SUM(debit) FROM hrm_loan_ledger WHERE a.id = hrm_loan_ledger.hrm_loan_application_id
                            -- AND hrm_loan_ledger.valid=1) , 0) as paid_amount,

                            -- (a.opening_loan_amount-SUM(IFNULL((SELECT SUM(debit) FROM hrm_loan_ledger WHERE a.id = hrm_loan_ledger.hrm_loan_application_id
                            -- AND hrm_loan_ledger.valid=1) , 0))) remaining_amount,

                            (a.opening_loan_amount-a.paid_amount) remaining_amount,
                            a.no_of_installment,
                            a.installment_size,a.installment_start_date,
                            CONCAT(b.employee_name, ' | ', d.employee_code,' | ',IFNULL(gg.accounts_code, '-')) AS employee_name,
                            b.Images,
                            c.loan_type,
                            a.apply_date,
                            a.hrm_employee_id

                        FROM
                            hrm_loan_application a
                                JOIN
                            hrm_employee b ON a.hrm_employee_id = b.id AND a.approved_status=$status
                                JOIN
                            hrm_loan_type c ON a.hrm_loan_type_id= c.id
                                JOIN
                            hrm_employee_job_info d ON b.id=d.hrm_employee_id
                                AND d.id in (SELECT max(id) FROM hrm_employee_job_info WHERE  hrm_employee_job_info.hrm_employee_id = d.hrm_employee_id)
                                JOIN
                            hrm_location f ON d.hrm_location_id=f.id
                                JOIN
                            hrm_employee_salary gg ON d.id = gg.hrm_employee_job_info_id
                                JOIN
                            user_location h ON h.hrm_location_id = f.id AND h.users_id = $user_id
                            Group By a.id,a.opening_loan_amount,a.paid_amount,a.remaining_amount,a.no_of_installment,a.installment_size,a.installment_start_date,b.employee_name,d.employee_code,gg.accounts_code,b.Images,c.loan_type,a.apply_date,a.hrm_employee_id");

    return json_encode(array('data' => $list));

   }


   public function loanledgersummary_list(Request $request){



        if(empty($request->location)){
            $location = '';
        }else{
            $location = " AND e.hrm_location_id =".$request->location;
        }

        // dd($location);

        $user_id = Auth::user()->id;
        $list = DB::SELECT("SELECT
                                c.Images,
                                CONCAT(c.employee_name, ' | ', e.employee_code,' | ',IFNULL(gg.accounts_code, '-')) AS employee_name,
                                f.designation_name,
                                d.loan_type,
                                SUM(a.`credit`) AS loan_amount,
                                SUM(a.`debit`) AS payment,
                                SUM(a.credit - a.debit) AS remaining_amount,
                                b.id AS loan_application_id,
                                e.hrm_employee_id
                            FROM
                                hrm_loan_ledger a
                                    JOIN
                                hrm_loan_application b ON a.hrm_loan_application_id = b.id
                                    AND a.valid = 1 AND b.approved_status=2
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    JOIN
                                hrm_loan_type d ON b.hrm_loan_type_id = d.id
                                    JOIN
                                hrm_employee_job_info e ON e.hrm_employee_id = c.id
                                and e.id IN (SELECT MAX(id) FROM hrm_employee_job_info where hrm_employee_id=c.id group by c.id)
                                    -- AND e.employee_activity = 1
                                    $location
                                    JOIN
                                hrm_designation f ON e.hrm_designation_id = f.id
                                    Join
                                hrm_location g ON e.hrm_location_id=g.id
                                    JOIN
                                hrm_employee_salary gg ON e.id = gg.hrm_employee_job_info_id
                                    JOIN
                                user_location h ON e.hrm_location_id = h.hrm_location_id AND h.users_id = $user_id
                            GROUP BY  e.employee_code,c.Images,c.employee_name,f.designation_name,d.loan_type,b.id,e.hrm_employee_id,gg.accounts_code
                            HAVING SUM(a.credit - a.debit) > 0");

        return json_encode(array('data' => $list));

   }


   public function paidloanledgersummary_list(Request $request ){


        if($request->location==null){
            $location = '';
        }else{
            $location = ' AND e.hrm_location_id='.$request->location;
        }

        $user_id = Auth::user()->id;
        $list = DB::SELECT("SELECT
                                c.Images,
                                CONCAT(c.employee_name, ' | ', e.employee_code,' | ', IFNULL(gg.accounts_code, '-') ) AS employee_name,
                                f.designation_name,
                                d.loan_type,
                                SUM(a.`credit`) AS loan_amount,
                                SUM(a.`debit`) AS payment,
                                SUM(a.credit - a.debit) AS remaining_amount,
                                b.id AS loan_application_id,
                                e.hrm_employee_id

                            FROM
                                hrm_loan_ledger a
                                    JOIN
                                hrm_loan_application b ON a.hrm_loan_application_id = b.id
                                    AND a.valid = 1 AND b.approved_status=2
                                    JOIN
                                hrm_employee c ON b.hrm_employee_id = c.id
                                    JOIN
                                hrm_loan_type d ON b.hrm_loan_type_id = d.id
                                    JOIN
                                hrm_employee_job_info e ON e.hrm_employee_id = c.id
                                    -- AND e.employee_activity = 1
                                and e.id IN (SELECT MAX(id) FROM hrm_employee_job_info where hrm_employee_id=c.id group by c.id)
                                    $location
                                    JOIN
                                hrm_designation f ON e.hrm_designation_id = f.id
                                    Join
                                hrm_location g ON e.hrm_location_id=g.id
                                    JOIN
                                hrm_employee_salary gg ON e.id = gg.hrm_employee_job_info_id
                                    JOIN
                                user_location h ON e.hrm_location_id = h.hrm_location_id AND h.users_id = $user_id
                            GROUP BY e.employee_code , c.Images,c.employee_name,f.designation_name,d.loan_type,b.id,e.hrm_employee_id,gg.accounts_code
                            HAVING SUM(a.credit - a.debit) < 1");

        return json_encode(array('data' => $list));

   }



   public function loanpayment(Request $request)
    {


        // dd($request->all());

        if($request->remaining_amount >=$request->payment_amount ){

        }else{

            $request->session()->flash('alert-danger', 'Sorry,Payment Amount can not greter then remaining amount !');
            return Redirect::to('loanledger');
        }


         $year   = date('Y', strtotime(str_replace('/', '-', $request->payment_date)));
         $month   = date('m', strtotime(str_replace('/', '-', $request->payment_date)));


        if ($month<10){
            $month = substr($month,1);
        }else{
            $month = $month;
        }

        $users_id     = Auth::user()->id;

        $insert = new HrmLoanLedger;
        $insert->hrm_loan_application_id  = $request->loan_application_id;
        $insert->debit                    = $request->payment_amount;
        $insert->credit                   = 0;
        $insert->hrm_month_id             = $month;
        $insert->year_id                  = $year ;
        $insert->valid                    = 1;
        $insert->narration                = $request->comment ;
        $insert->users_id                 = $users_id ;
        $insert->entry_status             = 2 ;
        $insert->save();

        $this->recordActivity(
             1,
             'Payment Loan',
             $insert->getChanges(),
             $request->id,
             'hrm_loan_ledger'
        );

        $request->session()->flash('alert-success', 'Successfully Done !');
        return Redirect::to('loanledger');

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
            'applied'             => 'required',
            'employee_name'       => 'required',
            'loan_type'           => 'required',
            'opening_loan_amount' => 'required',
            'paid_amount'         => 'required',
            'remaining_amount'    => 'required',
            'instalment_type'     => 'required',
            'installment_size'    => 'required',
            'no_of_instalment'    => 'required',
            'start_from'          => 'required',

        ]);


        if ($validator->fails()) {
            return redirect('loanapplication/create')
                        ->withErrors($validator)
                        ->withInput();
        }
        $users_id     = Auth::user()->id;
        $applied      = date('Y-m-d', strtotime(str_replace('/', '-', $request->applied)));
        $start_from   = date('Y-m-d', strtotime(str_replace('/', '-', $request->start_from)));

        $check = DB::SELECT("SELECT id From hrm_loan_application WHERE  hrm_employee_id=$request->employee_name AND approved_status=1 AND  hrm_loan_type_id=$request->loan_type and installment_start_date='$start_from'");
// dd($check);
        if(!empty($check)){

            $request->session()->flash('alert-danger', 'Already added this loan amount!');
            return Redirect::to('loanapplication');
        }


        $insert = new HrmLoanApplication;
        $insert->apply_date             = $applied;
        $insert->hrm_employee_id        = $request->employee_name;
        $insert->hrm_loan_type_id       = $request->loan_type;
        $insert->opening_loan_amount    = $request->opening_loan_amount;
        $insert->paid_amount            = $request->paid_amount;
        $insert->remaining_amount       = $request->remaining_amount;
        $insert->instalment_type        = $request->instalment_type;

        if($request->instalment_type==1){
            $insert->installment_size       = $request->installment_size;
            $insert->no_of_installment      = $request->no_of_instalment;
        }else{
            $insert->installment_size       = $request->no_of_instalment;
            $insert->no_of_installment      = $request->installment_size;
        }

        $insert->approved_status        = 1;
        $insert->installment_start_date = $start_from ;
        $insert->users_id               = $users_id ;
        $insert->note                   = $request->note ;

        $insert->save();

        $this->recordActivity(
             1,
             'Created Loan Application',
             $insert,
             $insert->id,
             'hrm_loan_application'
        );


        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('loanapplication');
    }




    public function loanapprove(Request $request)
    {

    // dd($request->all());

        $check = HrmLoanApplication::find($request->id);


        if (empty($check)){
            session()->flash('alert-danger', 'Invalid Loan Approve !!');
            return Redirect()->back();
        }

        $check ->approved_status       = $request->get('action');
        $check->save();

         $year   = date('Y', strtotime(str_replace('/', '-', $request->installment_start_date)));
         $month   = date('m', strtotime(str_replace('/', '-', $request->installment_start_date)));


        if ($month<10){
            $month = substr($month,1);
        }else{
            $month = $month;
        }

        if ($check->paid_amount>0){
            $comment ="Opening Balance & Paid Amount" ;
        }else{
            $comment = "Opening Balance";
        }



        $users_id     = Auth::user()->id;

        $insert = new HrmLoanLedger;
        $insert->hrm_loan_application_id  = $check->id;
        $insert->debit                    = $check->paid_amount;
        $insert->credit                   = $check->opening_loan_amount;
        $insert->hrm_month_id             = $month;
        $insert->year_id                  = $year ;
        $insert->valid                    = 1;
        $insert->narration                = $comment ;
        $insert->users_id                 = $users_id ;
        $insert->entry_status             = 2 ;

        $insert->save();

        $this->recordActivity(
             1,
             'Loan Approved',
             null,
             $request->id,
             'hrm_loan_ledger'
        );

        $request->session()->flash('alert-success', 'Successfully Done !');
        return Redirect::to('loanapplication');
    }



   public function rescheduleloan(Request $request)
    {
        // dd($request->all());

        $users_id     = Auth::user()->id;

        DB::update("UPDATE hrm_loan_application SET installment_start_date = '$request->installment_start_date', users_id = $users_id WHERE id = $request->id");

        $this->recordActivity(
                 1,
                 'Reschedule loan From Loan Approved List',
                 null,
                 $request->id,
                 'hrm_loan_application'
            );

        $request->session()->flash('alert-success', 'Successfully Done !');
        return Redirect::to('loanapprovedlist');
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

    public function cancel(Request $request, $id)
    {
        $user_id = Auth::user()->id;
        $query   = DB::SELECT("SELECT id FROM hrm_loan_ledger WHERE hrm_loan_application_id = $id
            AND entry_status = 1 AND valid=1");

        // dd($query);

        if (empty($query)){

            DB::UPDATE("UPDATE hrm_loan_ledger SET valid=0 WHERE hrm_loan_application_id=$id");
            DB::UPDATE("UPDATE hrm_loan_application SET approved_status=0,note='Deleted By User_id=$user_id' WHERE id=$id");

            // DB::table('hrm_loan_ledger')->where('hrm_loan_application_id', '=', $id)->delete();
            // DB::table('hrm_loan_application')->where('id', '=', $id)->delete();

            $this->recordActivity(
                 1,
                 'Deleted Loan Application',
                 null,
                 $id,
                 'hrm_loan_application'
            );

            $request->session()->flash('alert-success', 'successfully deleted !');
            return Redirect::to('loanapplication');
        }

        session()->flash('alert-danger', 'Sorry Loan Adjusted With Salary !!');
        return Redirect()->back();


    }

    public function loanledgercancel(Request $request, $id)
    {
        DB::table('hrm_loan_ledger')->where('id', '=', $id)->delete();


        $this->recordActivity(
                 1,
                 'Deleted Payment From Loan Ledger',
                 null,
                 $id,
                 'hrm_loan_ledger'
            );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect()->back();
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

    public function employeeWiseLoan(){
        $employee_id = request('employee_id');

        $data = DB::SELECT("SELECT
            d.loan_type,
            SUM(a.`credit`) AS loan_amount,
            SUM(a.`debit`) AS payment,
            SUM(a.credit - a.debit) AS remaining_amount
        FROM
            hrm_loan_ledger a
                JOIN
            hrm_loan_application b ON a.hrm_loan_application_id = b.id
                AND a.valid = 1
                AND b.approved_status = 2
                AND b.hrm_employee_id = $employee_id
                JOIN
            hrm_employee c ON b.hrm_employee_id = c.id
                JOIN
            hrm_loan_type d ON b.hrm_loan_type_id = d.id
                JOIN
            hrm_employee_job_info e ON e.hrm_employee_id = c.id
                AND e.id IN (SELECT
                    MAX(id)
                FROM
                    hrm_employee_job_info
                WHERE
                    hrm_employee_id = c.id
                GROUP BY c.id)
                JOIN
            hrm_employee_salary gg ON e.id = gg.hrm_employee_job_info_id
        GROUP BY  d.loan_type , b.id , e.hrm_employee_id , gg.accounts_code
            ");
        return json_encode(array('data' => $data));
    }
}
