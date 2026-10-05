<?php

namespace App\Http\Controllers\AccountsIntegration;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AccountsIntegration\HrmAccHeadIntegration;
use App\Models\AccountsIntegration\HrmAccHeadTitle;
use Illuminate\Support\Facades\Validator;

class AccountHeadIntegrationController extends Controller
{
    public function index()
    {


        $parentHeads = DB::SELECT("SELECT 
                                        a.id,
                                        CONCAT(b.journal_type_name,
                                                '-',
                                                a.head_name,
                                                '-',
                                                a.status) AS head_name_details,
                                        a.status,
                                        b.journal_type_name,
                                        b.id AS hrm_acc_journal_type_id,
                                        c.accounts_ledger_head_id,
                                        c.accounts_ledger_head_name
                                    FROM
                                        hrm_acc_head_title a
                                            JOIN
                                        hrm_acc_journal_type b ON a.hrm_acc_journal_type_id = b.id
                                            AND a.hrm_acc_head_title_id IS NULL
                                            LEFT JOIN
                                        hrm_acc_head_integration c ON c.hrm_acc_head_title_id = a.id
                                    ORDER BY b.journal_type_name");


        $costCenter = DB::SELECT("SELECT 
                                        a.id,
                                        CONCAT(b.journal_type_name,
                                                '-',
                                                a.head_name,
                                                '-',
                                                a.status,'- UNDER- ',bb.head_name) AS head_name_details,
                                        a.status,
                                        b.journal_type_name,
                                        b.id AS hrm_acc_journal_type_id,
                                        c.accounts_ledger_head_id,
                                        c.accounts_ledger_head_name
                                    FROM
                                        hrm_acc_head_title a
                                            JOIN
                                        hrm_acc_journal_type b ON a.hrm_acc_journal_type_id = b.id
                                            AND a.hrm_acc_head_title_id IS NOT NULL
                                            JOIN
                                        hrm_acc_head_title bb ON a.hrm_acc_head_title_id = bb.id
                                            LEFT JOIN
                                        hrm_acc_head_integration c ON c.hrm_acc_head_title_id = a.id
                                    ORDER BY b.journal_type_name");



         return view('AccountsIntegration.head_integration.create')
                ->with('parentHeads',$parentHeads)
                ->with('costCenter',$costCenter);




        // // if (request()->ajax()) {
        // //     $data = DB::select("
        // //         select
        // //             a.id,
        // //             a.accounts_ledger_head_name,
        // //             b.head_name
        // //         from
        // //             hrm_acc_head_integration as a
        // //                 join
        // //             hrm_acc_head_title as b on a.hrm_acc_head_title_id = b.id
        // //                 order by a.id desc
        // //     ");

        // //     return datatables()->of($data)
        // //         ->addColumn('Link', function ($data) {
        // //             return '
        // //             <a href="'.route('account_head_integration.edit', encrypt($data->id)).'" data-title="Edit Head Integration" footer-none class="modalLink btn btn-sm btn-flat">
        // //                 <i class="glyphicon glyphicon-edit"></i> Edit
        // //             </a>

        // //             <form action="'.route('account_head_integration.destroy', encrypt($data->id)).'" method="post" class="deleteHeadIntegration" style="display:inline-block">
        // //                 '.csrf_field().'
        // //                 '.method_field("delete").'
        // //                 <button type="submit" class="edit btn btn-sm btn-flat" title="Delete Record"><i class="fa fa-trash"></i></button>
        // //             </form>';
        // //         })
        // //         ->rawColumns(['Link'])
        // //         ->make(true);
        // // }

        // $headIntegration =null;


        // $parentHeads = HrmAccHeadTitle::query()
        //     ->whereNull('hrm_acc_head_title_id')
        //     ->where('is_active', 1)
        //     ->get();

        // return view('AccountsIntegration.head_integration.create', compact('headIntegration', 'parentHeads'))->render();
    }

    public function create()
    {


    }

    public function store(Request $request)
    {


        $status = false;

        $validator = Validator::make($request->all(), [
            'accounts_ledger_head_id.*' => 'required',
            'hrm_acc_head_title_id.*' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Accounts Ledger Head';
        } else {
            DB::beginTransaction();
            try {
                $data = [];
                
                DB::DELETE("DELETE FROM hrm_acc_head_integration");

                foreach ($request->hrm_acc_head_title_id as $key => $value) {

                    
                    if($request->accounts_ledger_head_id[$key]){

                        $hrm_acc_head_title_id    = $request->hrm_acc_head_title_id[$key];  

                        $HrmAccHeadTitleId = HrmAccHeadTitle::findOrFail($hrm_acc_head_title_id);
                        $acc_chart_of_accounts_id = $request->accounts_ledger_head_id[$key];

                        if(empty($HrmAccHeadTitleId->hrm_acc_head_title_id)){

                            $getLedgerName            = DB::connection('mysql2')->Select("SELECT name from acc_chart_of_accounts WHERE id = $acc_chart_of_accounts_id")[0]->name;    

                        }else{

                            $getLedgerName            = DB::connection('mysql2')->Select("SELECT name from acc_cost_centers WHERE id = $acc_chart_of_accounts_id")[0]->name; 
                            
                        }



                    }else{
                        $acc_chart_of_accounts_id = null; 
                        $getLedgerName = null; 
                    }


                    array_push($data, [
                   
                        'hrm_acc_head_title_id' => $request->hrm_acc_head_title_id[$key],
                        'accounts_ledger_head_id' => $acc_chart_of_accounts_id,
                        'accounts_ledger_head_name' => $getLedgerName,

                        'users_id' => auth()->id(),
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString()
                    ]);

                }

                   // dd($data) ;


                DB::table('hrm_acc_head_integration')
                    ->insert($data);

                DB::commit();

                $status = true;
                $message = 'Head Integration has been created..!';
            } catch (\Exception $e) {
                DB::rollback();
                $message = $e->getMessage();
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $headIntegration = DB::select("
            select
                a.id,
                a.hrm_acc_head_title_id,
                a.accounts_ledger_head_id,

                a.accounts_ledger_head_name,
                b.head_name
            from
                hrm_acc_head_integration as a
                    join
                hrm_acc_head_title as b on a.hrm_acc_head_title_id = b.id
                    where a.id = $id
        ")[0];

        return response()->json(
            view('AccountsIntegration.head_integration._form', compact('headIntegration'))->render()
        );
    }

    public function update(Request $request, $id)
    {


        $status = false;

        $validator = Validator::make($request->all(), [
            'accounts_ledger_head_id' => 'required',
            'hrm_acc_head_title_id' => 'required|integer|exists:hrm_acc_head_title,id',
        ]);

        $getLedgerName = DB::connection('mysql2')->Select("SELECT name from acc_chart_of_accounts WHERE id = $request->accounts_ledger_head_id")[0]->name;


        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            DB::beginTransaction();
            try {
                $headIntegration = HrmAccHeadIntegration::query()->findOrFail(decrypt($id));

                $headIntegration->update([
                    'accounts_ledger_head_id' => $request->accounts_ledger_head_id,
                    'accounts_ledger_head_name' => $getLedgerName,
                    'users_id' => auth()->id(),
                ]);

                DB::commit();

                $status = true;
                $message = 'Head Integration has been updated..!';
            } catch (\Exception $e) {
                DB::rollback();
                $message = $e->getMessage();
            }
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
            'error' => $error ?? '',
        ]);
    }

    public function destroy($id)
    {
        $status = false;

        DB::beginTransaction();
        try {
            $headIntegration = HrmAccHeadIntegration::query()->findOrFail(decrypt($id));

            $headIntegration->delete();

            DB::commit();

            $status = true;
            $message = 'Head Integration has been deleted..!';
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
        ]);
    }


// TRPO Database Connection 

    public function getAccountsLedgerHeadId(Request $request){

        $condition  = "";

        if(isset($request->requestStatus)){
            if(!empty($request->requestStatus)){
                $condition = $this->getLedgerCondition(Crypt::decrypt($request->requestStatus));
            }
        }
                        
        $term   = addslashes($request->term);
        $coa    = DB::connection('mysql2')->SELECT("SELECT 
                                a.id,
                                a.name AS text,
                                a.cost_centers_status,
                                a.set_bank_account_details_status,
                                a.maintain_bill_by_bill,
                                a.acc_chart_of_account_groups_id,
                                b.default_group_status,
                                a.mailing_name,
                                0 AS alias_id
                            FROM
                                acc_chart_of_accounts a
                                    JOIN
                                acc_chart_of_account_groups b ON a.acc_chart_of_account_groups_id = b.id
                                    $condition 
                                    
                                AND ((replace(replace(replace(replace(replace(replace(replace(replace(replace(a.name,  ' ', ''),'.',''),'-',''),'/',''),'@',''),'&',''),',',''),'[',''),']','')) LIKE '$term%' OR a.name LIKE '$term%')
                            UNION
                            SELECT 
                                a.id,
                                a.name AS text,
                                a.cost_centers_status,
                                a.set_bank_account_details_status,
                                a.maintain_bill_by_bill,
                                a.acc_chart_of_account_groups_id,
                                b.default_group_status,
                                a.mailing_name,
                                0 AS alias_id
                            FROM
                                acc_chart_of_accounts a
                                    JOIN
                                acc_chart_of_account_groups b ON a.acc_chart_of_account_groups_id = b.id
                                    $condition 
                                AND ((replace(replace(replace(replace(replace(replace(replace(replace(replace(a.name,  ' ', ''),'.',''),'-',''),'/',''),'@',''),'&',''),',',''),'[',''),']','')) LIKE '%$term%' 
                                    OR a.name LIKE '%$term%')");
        
        return response()->json($coa); 
    }





    public function accCostCenter(Request $request){
        $term               = $request->term;
        if(isset($request->condition)){
            $condition      = $request->condition;
        }else{
            $condition      = "";
        }

        $CostCenter    = DB::connection('mysql2')->SELECT("SELECT * FROM(
                                     SELECT '0' AS id,'Primary' AS text
                                     UNION ALL
                                     SELECT id,name AS text FROM acc_cost_centers) aa
                                     WHERE aa.text LIKE '%$term%' $condition ");
        return response()->json($CostCenter);          
    }







}
