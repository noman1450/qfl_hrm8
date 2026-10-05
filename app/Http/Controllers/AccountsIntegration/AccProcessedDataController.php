<?php

namespace App\Http\Controllers\AccountsIntegration;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AccountsIntegration\HrmAccHeadTitle;
use App\Models\AccountsIntegration\HrmAccJournalType;
use App\Models\AccountsIntegration\AccAccountMaster;
use App\Models\AccountsIntegration\AccAccountDetail;
use App\Models\AccountsIntegration\AccCostCenterEntry;
use Illuminate\Support\Facades\Validator;
use App\Models\HrmMonth;
use Response;
use Auth;
use Carbon\Carbon;


class AccProcessedDataController extends Controller
{
    public function index()
    {

        // dd($request->all());
        $userid  = Auth::user()->id;
        $cmonth = date('m');
        $cmonth = HrmMonth::find($cmonth);
        $cyear = date('Y');

       $location = DB::select("SELECT a.id,a.location_name FROM hrm_location a JOIN user_location b ON a.id=b.hrm_location_id AND b.users_id = $userid");




        if (request()->ajax()) {

           $condition = '';

           $location_con=null;
           if(request()->hrm_location_id=='999'){
                $location_con = ' And a.hrm_location_id in ('.(DB::SELECT("SELECT GROUP_CONCAT(id) id from hrm_location Where location_type=3 AND valid=1")[0]->id).')' ;
           }else{

                if(request()->hrm_location_id=='0'){
                    $location_con = ' And a.hrm_location_id in ('.(DB::SELECT("SELECT GROUP_CONCAT(id) id from hrm_location Where valid=1")[0]->id).')' ;
                }else{
                    $location_con = ' And a.hrm_location_id='.request()->hrm_location_id;
                }
           }


            if(request()->hrm_acc_journal_type_id){
                $condition .= ' And a.hrm_acc_journal_type_id='.request()->hrm_acc_journal_type_id;
            }


            if(request()->hrm_month_id){
                $condition .= ' And a.hrm_month_id='.request()->hrm_month_id;
            }

            $year_id = request()->year_id;


            $data    = DB::select("
                        SELECT
                            a.hrm_acc_journal_type_id as id, 
                            a.journal_type_name,
                            a.head_name,
                            a.status,
                            concat(a.year_id,'-',b.month_name) as 'year_month',
                            SUM(a.debit_amount) AS debit_total,
                            SUM(a.credit_amount) AS credit_total,
                            GROUP_CONCAT(CONCAT(a.location_name)
                            SEPARATOR '<br>') AS location_name,    
                            GROUP_CONCAT(CONCAT(a.category_name)
                                SEPARATOR '<br>') AS category_name,
                            GROUP_CONCAT(CONCAT(a.debit_amount)
                                SEPARATOR '<br>') AS debit_amount,
                            GROUP_CONCAT(CONCAT(a.credit_amount)
                                SEPARATOR '<br>') AS credit_amount,
                            GROUP_CONCAT(CONCAT(c.salary_head)
                                SEPARATOR '<br>') AS salary_head
                        FROM
                            hrm_acc_processing_data_details a JOIN hrm_month b 
                            ON a.hrm_month_id = b.id AND  a.year_id = $year_id 
                            LEFT JOIN hrm_salary_head c ON a.hrm_salary_head_id=c.id 
                            WHERE a.hrm_acc_journal_type_id IS NOT NULL
                            $condition
                            $location_con
                        GROUP BY a.journal_type_name ,a.status,a.head_name,a.year_id,a.hrm_acc_journal_type_id,b.month_name
                        HAVING SUM(a.debit_amount + a.credit_amount) > 0

            ");

            return datatables()->of($data)
                ->addColumn('Link', function ($data) {
                    return '
                    <a href="'.route('account_head_title.edit', encrypt($data->id)).'" data-title="Edit Head Name" footer-none class="modalLink btn btn-sm btn-flat">
                        <i class="glyphicon glyphicon-edit"></i> Edit
                    </a>

                    <form action="'.route('account_head_title.destroy', encrypt($data->id)).'" method="post" class="deleteHeadTitle" style="display:inline-block">
                        '.csrf_field().'
                        '.method_field("delete").'
                        <button type="submit" class="edit btn btn-sm btn-flat" title="Delete Record"><i class="fa fa-trash"></i></button>
                    </form>';
                })
                ->rawColumns(['Link','location_name','category_name','debit_amount','credit_amount','salary_head'])
                ->make(true);
        }

        return view('AccountsIntegration.acc_processed_data.index')            
            -> with('cmonth',  $cmonth)
            -> with('cyear',  $cyear) 
            -> with('location',  $location) ;
    }

    public function create()
    {

    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'location'                => 'required',
            'year'                    => 'required',
            'month_name'              => 'required',
            'hrm_acc_journal_type_id' => 'required',
        ]);


        if( $validator->fails() ){
            return Response::json(array(
                'success'   => false,
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

       $user_id      = Auth::user()->id;
       $location_con = null;
       if($request->location=='999'){
            $location_con = DB::SELECT("SELECT GROUP_CONCAT(id) id from hrm_location Where location_type=3 AND valid=1")[0]->id;
       }else{

            if($request->location=='0'){
             $location_con = DB::SELECT("SELECT GROUP_CONCAT(id) id from hrm_location Where valid=1")[0]->id;
            }else{
             $location_con = $request->location;
            }


       }





    $find = DB::SELECT("SELECT * FROM  hrm_acc_processing_data_details_costcenter WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
            hrm_location_id in ($location_con)  AND hrm_acc_journal_type_id= $request->hrm_acc_journal_type_id AND isCasualWorker = 1 
            AND transaction_number is NOT null LIMIT 1");

    $find2 = DB::SELECT("SELECT * FROM hrm_acc_processing_data_details  WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
            hrm_location_id in ($location_con)  AND hrm_acc_journal_type_id= $request->hrm_acc_journal_type_id AND transaction_number is not null LIMIT 1");
        


    if(!empty($find)){

        return response::json(array(
            'success'    => false,
            'messages'   => 'SORRY Already transfer to A/C'
        ));

    }

    if(!empty($find2)){

        return response::json(array(
            'success'    => false,
            'messages'   => 'SORRY Already transfer to A/C'
        ));

    }




    
    if($request->submit_action=='reprocess'){



       DB::beginTransaction();
        try {



            //======== START Accounts Integration of Salary & Allowance ======================================

            DB::DELETE("DELETE FROM hrm_acc_processing_data_details WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
                hrm_location_id in ($location_con)  AND hrm_acc_journal_type_id= $request->hrm_acc_journal_type_id");
            
            DB::insert("INSERT INTO hrm_acc_processing_data_details (hrm_acc_journal_type_id,journal_type_name,hrm_acc_headwise_emp_tag_id,hrm_acc_head_title_id,head_name,hrm_salary_head_id,hrm_location_id,location_name,status,location_type,year_id,hrm_month_id,debit_amount,credit_amount,users_id,salary_apply_type,hrm_category_id,category_name,isCompanyContribute,isSalaryAllow,isCasualWorker)
                            SELECT 
                                i.id AS hrm_acc_journal_type_id,
                                i.journal_type_name,
                                a.id AS hrm_acc_headwise_emp_tag_id,
                                a.hrm_acc_head_title_id,
                                h.head_name,
                                d.hrm_salary_head_id,
                                e.id AS hrm_location_id,
                                e.location_name,
                                h.status,
                                e.location_type,
                                '$request->year',
                                '$request->month_name',
                                0 AS debit_amount,
                                0 AS credit_amount,
                                '$user_id',
                                j.generate_type,
                                c.hrm_category_id,
                                f.category_name,
                                h.isCompanyContribute,
                                h.isSalaryAllow,
                                h.isCasualWorker
                            FROM
                                hrm_acc_headwise_emp_tag AS a
                                    JOIN
                                hrm_acc_headwise_emp_tag_location AS b ON b.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_acc_headwise_emp_tag_category c ON c.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_acc_headwise_emp_tag_salaryhead d ON d.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id AND e.id in ($location_con)
                                    JOIN
                                hrm_category f ON c.hrm_category_id = f.id
                                    JOIN
                                hrm_salary_head g ON d.hrm_salary_head_id = g.id
                                    JOIN
                                hrm_acc_head_title h ON a.hrm_acc_head_title_id = h.id AND h.hrm_acc_head_title_id is null AND h.isCasualWorker is null
                                    JOIN
                                hrm_acc_journal_type i ON h.hrm_acc_journal_type_id = i.id AND i.id=$request->hrm_acc_journal_type_id 
                                    JOIN
                                hrm_salary_head_group j ON g.hrm_salary_head_group_id = j.id ");



            DB::UPDATE("UPDATE hrm_acc_processing_data_details 
                JOIN (SELECT
                            d.id,
                            if(d.status='Dr',SUM(b.actual_amount),0) as debit_amount,
                            if(d.status='Cr',SUM(b.actual_amount),0) as credit_amount
                        FROM
                            pay_register a
                                JOIN
                            pay_register_details b ON a.id = b.pay_register_id
                                AND a.salary_genarate_type <> 0
                                AND a.year_id= $request->year AND a.hrm_month_id= $request->month_name
                                AND a.hrm_location_id in ($location_con)
                                JOIN
                            hrm_employee_job_info c ON a.hrm_employee_job_info_id = c.id
                                JOIN
                            hrm_acc_processing_data_details d ON d.hrm_location_id = c.hrm_location_id
                               AND d.hrm_acc_journal_type_id = $request->hrm_acc_journal_type_id
                               AND d.hrm_salary_head_id = b.hrm_salary_head_id Where d.hrm_category_id = c.hrm_category_id
                        GROUP BY d.id,d.hrm_location_id,d.hrm_category_id,d.status) aaa ON hrm_acc_processing_data_details.id=aaa.id
                        SET  hrm_acc_processing_data_details.debit_amount = aaa.debit_amount , hrm_acc_processing_data_details.credit_amount = aaa.credit_amount  WHERE hrm_acc_processing_data_details.year_id=$request->year AND hrm_acc_processing_data_details.hrm_month_id=$request->month_name  AND hrm_acc_processing_data_details.hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_acc_processing_data_details.hrm_location_id in ($location_con) and hrm_acc_processing_data_details.isCasualWorker is null");





            DB::DELETE("DELETE FROM hrm_acc_processing_data_details WHERE debit_amount=0 AND credit_amount=0 AND year_id=$request->year AND hrm_month_id=$request->month_name  AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_location_id in ($location_con)");
            
            DB::UPDATE("UPDATE hrm_acc_processing_data_details SET debit_amount=(-debit_amount),credit_amount=(-credit_amount) WHERE salary_apply_type=2 AND isSalaryAllow=1 AND year_id=$request->year AND hrm_month_id=$request->month_name AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_location_id in ($location_con)");


            DB::UPDATE("UPDATE hrm_acc_processing_data_details SET debit_amount=(debit_amount*2) WHERE  isCompanyContribute=1 AND year_id=$request->year AND hrm_month_id=$request->month_name AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id AND hrm_location_id in ($location_con) ");




        // =====================  END Accounts Integration of Salary & Allowance ==============================



        // ===========================  START FOR COST CENTER ==============================================

            DB::DELETE("DELETE FROM hrm_acc_processing_data_details_costcenter WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
                hrm_location_id in ($location_con)  AND hrm_acc_journal_type_id= $request->hrm_acc_journal_type_id");
            
            DB::insert("INSERT INTO hrm_acc_processing_data_details_costcenter (hrm_acc_journal_type_id,journal_type_name,hrm_acc_headwise_emp_tag_id,hrm_acc_head_title_id,head_name,hrm_salary_head_id,hrm_location_id,location_name,status,location_type,year_id,hrm_month_id,debit_amount,credit_amount,users_id,salary_apply_type,hrm_category_id,category_name,isCompanyContribute,isSalaryAllow,isCasualWorker)
                            SELECT 
                                i.id AS hrm_acc_journal_type_id,
                                i.journal_type_name,
                                a.id AS hrm_acc_headwise_emp_tag_id,
                                a.hrm_acc_head_title_id,
                                h.head_name,
                                d.hrm_salary_head_id,
                                e.id AS hrm_location_id,
                                e.location_name,
                                h.status,
                                e.location_type,
                                '$request->year',
                                '$request->month_name',
                                0 AS debit_amount,
                                0 AS credit_amount,
                                '$user_id',
                                j.generate_type,
                                c.hrm_category_id,
                                f.category_name,
                                h.isCompanyContribute,
                                h.isSalaryAllow,
                                h.isCasualWorker
                            FROM
                                hrm_acc_headwise_emp_tag AS a
                                    JOIN
                                hrm_acc_headwise_emp_tag_location AS b ON b.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_acc_headwise_emp_tag_category c ON c.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_acc_headwise_emp_tag_salaryhead d ON d.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id AND e.id in ($location_con)
                                    JOIN
                                hrm_category f ON c.hrm_category_id = f.id
                                    JOIN
                                hrm_salary_head g ON d.hrm_salary_head_id = g.id
                                    JOIN
                                hrm_acc_head_title h ON a.hrm_acc_head_title_id = h.id AND h.hrm_acc_head_title_id is not null AND h.isCasualWorker is null
                                    JOIN
                                hrm_acc_journal_type i ON h.hrm_acc_journal_type_id = i.id AND i.id=$request->hrm_acc_journal_type_id 
                                    JOIN
                                hrm_salary_head_group j ON g.hrm_salary_head_group_id = j.id ");






            DB::UPDATE("UPDATE hrm_acc_processing_data_details_costcenter 
                JOIN (SELECT
                            d.id,
                            if(d.status='Dr',SUM(b.actual_amount),0) as debit_amount,
                            if(d.status='Cr',SUM(b.actual_amount),0) as credit_amount
                        FROM
                            pay_register a
                                JOIN
                            pay_register_details b ON a.id = b.pay_register_id
                                AND a.salary_genarate_type <> 0
                                AND a.year_id= $request->year AND a.hrm_month_id= $request->month_name
                                AND a.hrm_location_id in ($location_con)
                                JOIN
                            hrm_employee_job_info c ON a.hrm_employee_job_info_id = c.id
                                JOIN
                            hrm_acc_processing_data_details_costcenter d ON d.hrm_location_id = c.hrm_location_id
                               AND d.hrm_acc_journal_type_id = $request->hrm_acc_journal_type_id
                               AND d.hrm_salary_head_id = b.hrm_salary_head_id Where d.hrm_category_id = c.hrm_category_id
                        GROUP BY d.id,d.hrm_location_id,d.hrm_category_id,d.status) aaa ON hrm_acc_processing_data_details_costcenter.id=aaa.id
                        SET  hrm_acc_processing_data_details_costcenter.debit_amount = aaa.debit_amount , hrm_acc_processing_data_details_costcenter.credit_amount = aaa.credit_amount WHERE hrm_acc_processing_data_details_costcenter.year_id=$request->year AND hrm_acc_processing_data_details_costcenter.hrm_month_id=$request->month_name  AND hrm_acc_processing_data_details_costcenter.hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_acc_processing_data_details_costcenter.hrm_location_id in ($location_con) AND hrm_acc_processing_data_details_costcenter.isCasualWorker is null");




            DB::DELETE("DELETE FROM hrm_acc_processing_data_details_costcenter WHERE debit_amount=0 AND credit_amount=0 AND year_id=$request->year AND hrm_month_id=$request->month_name  AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_location_id in ($location_con)");
            
            DB::UPDATE("UPDATE hrm_acc_processing_data_details_costcenter SET debit_amount=(-debit_amount),credit_amount=(-credit_amount) WHERE salary_apply_type=2 AND isSalaryAllow=1 AND year_id=$request->year AND hrm_month_id=$request->month_name AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_location_id in ($location_con)");


            DB::UPDATE("UPDATE hrm_acc_processing_data_details_costcenter SET debit_amount=(debit_amount*2) WHERE  isCompanyContribute=1 AND year_id=$request->year AND hrm_month_id=$request->month_name AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id AND hrm_location_id in ($location_con) ");

            // End for cost Center ============================================



            //START FOR CASUAL WORKER ============================================================================


            DB::DELETE("DELETE FROM hrm_acc_processing_data_details WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
                hrm_location_id in ($location_con)  AND hrm_acc_journal_type_id= $request->hrm_acc_journal_type_id AND isCasualWorker = 1 ");



            DB::insert("INSERT INTO hrm_acc_processing_data_details (hrm_acc_journal_type_id,journal_type_name,hrm_acc_headwise_emp_tag_id,hrm_acc_head_title_id,head_name,hrm_salary_head_id,hrm_location_id,location_name,status,location_type,year_id,hrm_month_id,debit_amount,credit_amount,users_id,salary_apply_type,hrm_category_id,category_name,isCompanyContribute,isSalaryAllow,isCasualWorker)
                            SELECT 
                                i.id AS hrm_acc_journal_type_id,
                                i.journal_type_name,
                                a.id AS hrm_acc_headwise_emp_tag_id,
                                a.hrm_acc_head_title_id,
                                h.head_name,
                                null,
                                e.id AS hrm_location_id,
                                e.location_name,
                                h.status,
                                e.location_type,
                                '$request->year',
                                '$request->month_name',
                                0 AS debit_amount,
                                0 AS credit_amount,
                                '$user_id',
                                null,
                                c.hrm_category_id,
                                f.category_name,
                                h.isCompanyContribute,
                                h.isSalaryAllow,
                                h.isCasualWorker
                            FROM
                                hrm_acc_headwise_emp_tag AS a
                                    JOIN
                                hrm_acc_headwise_emp_tag_location AS b ON b.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_acc_headwise_emp_tag_category c ON c.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id AND e.id in ($location_con)
                                    JOIN
                                hrm_category f ON c.hrm_category_id = f.id
                                    JOIN
                                hrm_acc_head_title h ON a.hrm_acc_head_title_id = h.id AND h.hrm_acc_head_title_id is null 
                                    JOIN
                                hrm_acc_journal_type i ON h.hrm_acc_journal_type_id = i.id AND i.id=$request->hrm_acc_journal_type_id
                                WHERE h.isCasualWorker=1");



            DB::UPDATE("UPDATE hrm_acc_processing_data_details 
                        JOIN (
                            SELECT SUM(aaa.debit_amount) debit_amount,SUM(aaa.credit_amount) credit_amount,aaa.id 

                                FROM( 

                                        SELECT
                                            d.id,
                                            if(d.status='Dr', IF(a.day_of_month <= a.total_present, (a.amount * a.day_of_month) + b.due_adjust - b.adv_adjust ,(a.amount * a.total_present) + b.due_adjust - b.adv_adjust) ,0) as debit_amount,
                                            if(d.status='Cr',IF(a.day_of_month <= a.total_present, (a.amount * a.day_of_month) + b.due_adjust - b.adv_adjust ,(a.amount * a.total_present) + b.due_adjust - b.adv_adjust),0) as credit_amount
                                            ,d.hrm_location_id,d.hrm_category_id,d.status,a.hrm_employee_job_info_id
                                        FROM
                                            pay_register a
                                                JOIN
                                            pay_register_cw b ON a.id = b.pay_register_id
                                                AND a.apply_for = 3
                                                AND a.salary_genarate_type <> 0
                                                AND a.year_id = $request->year  AND a.hrm_month_id= $request->month_name
                                                AND a.hrm_location_id in ($location_con)
                                                JOIN
                                            hrm_employee_job_info c ON a.hrm_employee_job_info_id = c.id
                                                JOIN
                                            hrm_acc_processing_data_details d ON d.hrm_location_id = c.hrm_location_id 
                                               AND d.hrm_acc_journal_type_id = $request->hrm_acc_journal_type_id
                                                Where d.hrm_category_id = c.hrm_category_id AND d.isCasualWorker=1
                                        GROUP BY d.id,d.hrm_location_id,d.hrm_category_id,d.status,a.id) aaa
                                GROUP BY aaa.id
                                ) bbb ON hrm_acc_processing_data_details.id=bbb.id
                                SET  hrm_acc_processing_data_details.debit_amount = bbb.debit_amount , hrm_acc_processing_data_details.credit_amount = bbb.credit_amount  WHERE hrm_acc_processing_data_details.year_id=$request->year AND hrm_acc_processing_data_details.hrm_month_id=$request->month_name  AND hrm_acc_processing_data_details.hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_acc_processing_data_details.hrm_location_id in ($location_con) AND isCasualWorker=1 ");







            DB::DELETE("DELETE FROM hrm_acc_processing_data_details WHERE debit_amount=0 AND credit_amount=0 AND year_id=$request->year AND hrm_month_id=$request->month_name  AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_location_id in ($location_con) AND isCasualWorker=1");
            
            DB::UPDATE("UPDATE hrm_acc_processing_data_details SET debit_amount=(-debit_amount),credit_amount=(-credit_amount) WHERE salary_apply_type=2 AND isSalaryAllow=1 AND year_id=$request->year AND hrm_month_id=$request->month_name AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_location_id in ($location_con)  AND isCasualWorker=1");


            DB::UPDATE("UPDATE hrm_acc_processing_data_details SET debit_amount=(debit_amount*2) WHERE  isCompanyContribute=1 AND year_id=$request->year AND hrm_month_id=$request->month_name AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id AND hrm_location_id in ($location_con) AND isCasualWorker=1");




            // END FOR CASUAL WORKER ================================================================



            // START  CASUAL WORKER COST CENTER =====================================================

            DB::DELETE("DELETE FROM hrm_acc_processing_data_details_costcenter WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
                hrm_location_id in ($location_con)  AND hrm_acc_journal_type_id= $request->hrm_acc_journal_type_id AND isCasualWorker = 1 ");



            DB::INSERT("INSERT INTO hrm_acc_processing_data_details_costcenter (hrm_acc_journal_type_id,journal_type_name,hrm_acc_headwise_emp_tag_id,hrm_acc_head_title_id,head_name,hrm_salary_head_id,hrm_location_id,location_name,status,location_type,year_id,hrm_month_id,debit_amount,credit_amount,users_id,salary_apply_type,hrm_category_id,category_name,isCompanyContribute,isSalaryAllow,isCasualWorker)
                            SELECT 
                                i.id AS hrm_acc_journal_type_id,
                                i.journal_type_name,
                                a.id AS hrm_acc_headwise_emp_tag_id,
                                a.hrm_acc_head_title_id,
                                h.head_name,
                                null,
                                e.id AS hrm_location_id,
                                e.location_name,
                                h.status,
                                e.location_type,
                                '$request->year',
                                '$request->month_name',
                                0 AS debit_amount,
                                0 AS credit_amount,
                                '$user_id',
                                null,
                                c.hrm_category_id,
                                f.category_name,
                                h.isCompanyContribute,
                                h.isSalaryAllow,
                                h.isCasualWorker
                            FROM
                                hrm_acc_headwise_emp_tag AS a
                                    JOIN
                                hrm_acc_headwise_emp_tag_location AS b ON b.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_acc_headwise_emp_tag_category c ON c.hrm_acc_headwise_emp_tag_id = a.id
                                    JOIN
                                hrm_location e ON b.hrm_location_id = e.id AND e.id in ($location_con)
                                    JOIN
                                hrm_category f ON c.hrm_category_id = f.id
                                    JOIN
                                hrm_acc_head_title h ON a.hrm_acc_head_title_id = h.id AND h.hrm_acc_head_title_id is not null 
                                    JOIN
                                hrm_acc_journal_type i ON h.hrm_acc_journal_type_id = i.id AND i.id=$request->hrm_acc_journal_type_id
                                WHERE  h.isCasualWorker=1");





            DB::UPDATE("UPDATE hrm_acc_processing_data_details_costcenter 
                            JOIN (

                                    SELECT SUM(aaa.debit_amount) debit_amount,SUM(aaa.credit_amount) credit_amount,aaa.id 

                                    FROM( 

                                            SELECT
                                                d.id,
                                                if(d.status='Dr', IF(a.day_of_month <= a.total_present, (a.amount * a.day_of_month) + b.due_adjust - b.adv_adjust ,(a.amount * a.total_present) + b.due_adjust - b.adv_adjust) ,0) as debit_amount,
                                                if(d.status='Cr',IF(a.day_of_month <= a.total_present, (a.amount * a.day_of_month) + b.due_adjust - b.adv_adjust ,(a.amount * a.total_present) + b.due_adjust - b.adv_adjust),0) as credit_amount
                                                ,d.hrm_location_id,d.hrm_category_id,d.status,a.hrm_employee_job_info_id
                                            FROM
                                                pay_register a
                                                    JOIN
                                                pay_register_cw b ON a.id = b.pay_register_id
                                                    AND a.apply_for = 3
                                                    AND a.salary_genarate_type <> 0
                                                    AND a.year_id = $request->year  AND a.hrm_month_id= $request->month_name
                                                    AND a.hrm_location_id in ($location_con)
                                                    JOIN
                                                hrm_employee_job_info c ON a.hrm_employee_job_info_id = c.id
                                                    JOIN
                                                hrm_acc_processing_data_details_costcenter d ON d.hrm_location_id = c.hrm_location_id 
                                                   AND d.hrm_acc_journal_type_id = $request->hrm_acc_journal_type_id
                                                    Where d.hrm_category_id = c.hrm_category_id AND d.isCasualWorker=1
                                            GROUP BY d.id,d.hrm_location_id,d.hrm_category_id,d.status,a.id) aaa 
                       GROUP BY aaa.id

                                     ) bbb ON hrm_acc_processing_data_details_costcenter.id=bbb.id
                                     SET  hrm_acc_processing_data_details_costcenter.debit_amount = bbb.debit_amount , hrm_acc_processing_data_details_costcenter.credit_amount = bbb.credit_amount  WHERE hrm_acc_processing_data_details_costcenter.year_id=$request->year AND hrm_acc_processing_data_details_costcenter.hrm_month_id=$request->month_name  AND hrm_acc_processing_data_details_costcenter.hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_acc_processing_data_details_costcenter.hrm_location_id in ($location_con) AND isCasualWorker=1");







            DB::DELETE("DELETE FROM hrm_acc_processing_data_details_costcenter WHERE debit_amount=0 AND credit_amount=0 AND year_id=$request->year AND hrm_month_id=$request->month_name  AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_location_id in ($location_con) AND isCasualWorker=1");
            
            DB::UPDATE("UPDATE hrm_acc_processing_data_details_costcenter SET debit_amount=(-debit_amount),credit_amount=(-credit_amount) WHERE salary_apply_type=2 AND isSalaryAllow=1 AND year_id=$request->year AND hrm_month_id=$request->month_name AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id
                AND hrm_location_id in ($location_con)  AND isCasualWorker=1");


            DB::UPDATE("UPDATE hrm_acc_processing_data_details_costcenter SET debit_amount=(debit_amount*2) WHERE  isCompanyContribute=1 AND year_id=$request->year AND hrm_month_id=$request->month_name AND hrm_acc_journal_type_id=$request->hrm_acc_journal_type_id AND hrm_location_id in ($location_con) AND isCasualWorker=1");

        
            // END  CASUAL WORKER COST CENTER =========================================================

                    
            DB::commit();
            } catch (\Exception $e) {
            DB::rollback();
            return Response::json(array(
                'success'           => false,
                'error_messages'    => true,
                'errors'          => "insert problem !! " . $e->getMessage()
            ));
            }

            return response::json(array(
                'success'    => true,
                'messages'   => 'Successfully Re-Process'
            ));


    }else{

            // $transaction_number =  $this->makeAccJournal($request);


 

                $data = [];

                if ($request->month_name>9){
                    $month = $request->month_name;
                }else{
                    $month = '0'.$request->month_name;
                }
                $refrence_no  = HrmAccJournalType::findOrFail($request->hrm_acc_journal_type_id);
                $date_of_month    = $request->year.'-'.$month.'-01';
                $data['transaction_date'] = date("Y-m-t", strtotime($date_of_month));
                $data['entry_from']       = 'HRM';
                $data['refrence_no']      = $refrence_no->journal_type_name.$request->year.$month ;
               

                $querydata    = DB::select("
                            SELECT 
                                    a.hrm_acc_journal_type_id , 
                                    a.hrm_acc_head_title_id,
                                    b.accounts_ledger_head_id,
                                    b.accounts_ledger_head_name,
                                    a.journal_type_name,
                                    a.status,
                                    SUM(a.debit_amount) AS debit_total,
                                    SUM(a.credit_amount) AS credit_total,
                                    a.year_id,
                                    a.hrm_month_id
                                FROM
                                    hrm_acc_processing_data_details a
                                        JOIN
                                    hrm_acc_head_integration b ON a.hrm_acc_head_title_id = b.hrm_acc_head_title_id
                                WHERE
                                    a.year_id = $request->year AND a.hrm_month_id = $request->month_name
                                        AND a.hrm_acc_journal_type_id = $request->hrm_acc_journal_type_id
                                GROUP BY a.hrm_acc_journal_type_id , a.hrm_acc_head_title_id , a.status , a.journal_type_name , b.accounts_ledger_head_id , b.accounts_ledger_head_name
                                ORDER BY a.status desc

                ");

                foreach($querydata as $keys) {




                    $getCoscenterData= DB::SELECT("SELECT 
                                                        b.accounts_ledger_head_id as acc_cost_centers_id,
                                                        b.accounts_ledger_head_name as cost_center_name,
                                                        a.status,
                                                        SUM(c.debit_amount) AS debit,
                                                        SUM(c.credit_amount) AS credit
                                                    FROM
                                                        hrm_acc_head_title a
                                                            JOIN
                                                        hrm_acc_head_integration b ON a.id = b.hrm_acc_head_title_id
                                                            AND a.hrm_acc_head_title_id = $keys->hrm_acc_head_title_id
                                                            JOIN
                                                        hrm_acc_processing_data_details_costcenter c ON a.id = c.hrm_acc_head_title_id
                                                        AND c.year_id = $keys->year_id AND c.hrm_month_id = $keys->hrm_month_id
                                                        AND c.hrm_acc_journal_type_id = $keys->hrm_acc_journal_type_id
                                                    GROUP BY b.accounts_ledger_head_id
                                                    ORDER BY a.status desc");




                    $data['details'][] = ['accounts_ledger_head_id'   => $keys->accounts_ledger_head_id,
                                       'accounts_ledger_head_name' => $keys->accounts_ledger_head_name,
                                       'status'                    => $keys->status,
                                       'debit'                     => $keys->debit_total,
                                       'credit'                    => $keys->credit_total,
                                       'costCenter'                => $getCoscenterData,
                                    ];
                }

                // dd($data);


                DB::beginTransaction();
                try {

                        $transaction_date   = date('Y-m-d', strtotime(str_replace('/', '-', $data['transaction_date'])));
                        $entry_from         =      $data['entry_from'];
                        $reference_number   = $data['refrence_no'];
                        $voucher_types      = 4;

                        $transaction_number = $this->generateVoucherNumber("acc_account_masters", "transaction_number"," acc_voucher_types_id = 4 AND sales_center_id = 1",4);
                        
                        
                        // dd($transaction_number);   

                        $count_item         = count($data['details']);
                        $debit_amount       = 0;
                        $credit_amount      = 0;

                        for ($i = 0; $i <$count_item; $i++) {
                            $debit_amount  = $debit_amount+floatval($data['details'][$i]['debit']);
                            $credit_amount = $credit_amount+floatval($data['details'][$i]['credit']);
                        }

                        // dd($debit_amount.'-'.$credit_amount );

                        if($debit_amount != $credit_amount){

                            return response::json(array(

                                'success'    => false,
                                'messages'   => 'Debit and Credit not match !! '

                            )); 

                        }


                        $insert_master_data = new AccAccountMaster;
                        $insert_master_data->sales_center_id        = 1;
                        $insert_master_data->transaction_date       = $transaction_date;
                        $insert_master_data->transaction_number     = $transaction_number;
                        $insert_master_data->narration              = $reference_number;
                        $insert_master_data->users_id               = 2;
                        $insert_master_data->valid                  = 1;
                        $insert_master_data->acc_voucher_types_id   = 4;
                        $insert_master_data->entry_from             = "HRM";
                        $insert_master_data->reference_number       = $transaction_number;
                        $insert_master_data->voucher_number         = $reference_number;
                        $insert_master_data->save();


                        $accAccountMastersId = $insert_master_data->id;

                        // dd($count_item);
                        
                    for ($i = 0; $i <$count_item; $i++) {

                        if(floatval($data['details'][$i]['debit'])+floatval($data['details'][$i]['credit'])>0){



                                        $insert_acc_details = new AccAccountDetail;
                                        $insert_acc_details->acc_account_masters_id     = $insert_master_data->id;
                                        $insert_acc_details->acc_chart_of_accounts_id   = $data['details'][$i]['accounts_ledger_head_id'];
                                        $insert_acc_details->valid                      = 1;
                                        $insert_acc_details->narration                  = $reference_number;
                                        $insert_acc_details->debit                      = floatval($data['details'][$i]['debit']);
                                        $insert_acc_details->credit                     = floatval($data['details'][$i]['credit']);
                                        $insert_acc_details->default_sl                 = ($i);
                                        $insert_acc_details->save(); 


                                            
                                        // if(!empty($data['details'][$i]['costCenter'])){


                                        //            $count_item_cs   = count($data['details'][$i]['costCenter']);

                                        //            for ($j = 0; $j <$count_item_cs; $j++) {

                                        //                 $insert_cost_center_entry = new AccCostCenterEntry;
                                        //                 $insert_cost_center_entry->acc_account_details_id     = $insert_acc_details->id;
                                        //                 $insert_cost_center_entry->acc_account_masters_id     = $insert_master_data->id;
                                        //                 $insert_cost_center_entry->valid                      = 1;
                                        //                 $insert_cost_center_entry->debit                      = $data['details'][$i]['costCenter'][$j]->debit;
                                        //                 $insert_cost_center_entry->credit                     = $data['details'][$i]['costCenter'][$j]->credit;   
                                        //                 $insert_cost_center_entry->acc_cost_centers_id        = $data['details'][$i]['costCenter'][$j]->acc_cost_centers_id; 
                                        //                 $insert_cost_center_entry->save();               
                                        //             } 

                                        // }

                        }
                    }


                DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response::json(array(
                        'success'     => false,
                        'messages'    => ["system errors !! " . $e->getMessage()]
                    )); 
                }


                DB::UPDATE("UPDATE hrm_acc_processing_data_details_costcenter SET transaction_number='$accAccountMastersId' WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
                    hrm_location_id in ($location_con)  AND hrm_acc_journal_type_id= $request->hrm_acc_journal_type_id AND isCasualWorker = 1 ");

                DB::UPDATE("UPDATE hrm_acc_processing_data_details SET transaction_number='$accAccountMastersId' WHERE year_id=$request->year AND hrm_month_id = $request->month_name AND 
                    hrm_location_id in ($location_con)  AND hrm_acc_journal_type_id= $request->hrm_acc_journal_type_id");
                



                return response::json(array(
                    'success'    => true,
                    'messages'   => 'A/C data Successfully transfered !!'
                ));

        }    
       

    }




    function generateVoucherNumber($table_name, $column_name, $condition,$voucher_types){

        $search          = DB::connection('mysql2')->select("SELECT MAX(RIGHT($column_name,8)) As invno FROM $table_name  WHERE $condition");
        $voucher_types   = DB::connection('mysql2')->select("SELECT * FROM acc_voucher_types WHERE id = $voucher_types");
        $maxinvoiceno    = 0;



        foreach ($search as $key)
            $maxinvoiceno = $key->invno;


        $yearid     = date("y");
        $monthid    = date("m");
        $datevalue  = $yearid . $monthid;

        $invoice_no = substr($maxinvoiceno,0,4);

        if ($maxinvoiceno == 0){
            $a = "0001";
            $new_invoice_no = $yearid . $monthid . $a;
        } else {

            if ($invoice_no == $datevalue){
                $maxinvoiceno   = substr($maxinvoiceno, 5) + 1;
                $maxinvoiceno   = sprintf("%05s\n", $maxinvoiceno);
                $new_invoice_no = $datevalue . $maxinvoiceno;
            } else {
                $a = "0001";
                $new_invoice_no = $yearid . $monthid . $a;              
            }
        }
        if(!empty($voucher_types)){
            return trim($voucher_types[0]->alias_name . $new_invoice_no);
        }else{
            return trim($new_invoice_no);
        }
        
    }



    public function edit($id)
    {
     
    }

    public function update(Request $request, $id)
    {
      
    }

    public function destroy($id)
    {
       
    }

    public function dropdown(Request $request)
    {
        
    }

    public function dropdownParent(Request $request)
    {
        
    }
}
