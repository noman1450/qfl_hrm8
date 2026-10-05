<?php

namespace App\Http\Controllers\AccountsIntegration;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AccountsIntegration\HrmAccHeadTitle;
use Illuminate\Support\Facades\Validator;

class AccountHeadTitleController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {

            $condition='';

            if(request()->hrm_acc_journal_type_id){
                $condition .= ' Where a.hrm_acc_journal_type_id='.request()->hrm_acc_journal_type_id;
            }



            $data = DB::select("
                select
                    a.id,
                    a.head_name,
                    if(a.is_active = 1, 'Active', 'Inactive') as is_active,
                    if(a.isCompanyContribute = 1, 'PF', '-') as isCompanyContribute,
                    if(a.isSalaryAllow = 1, 'Allow (+,-)S&A Head', '-') as isSalaryAllow,
                    if(a.isCasualWorker = 1, 'Allow for CW', '-') as isCasualWorker,
                    c.journal_type_name,
                    b.head_name as parent,
                    a.status
                from
                    hrm_acc_head_title as a
                        left join
                    hrm_acc_head_title as b on a.hrm_acc_head_title_id = b.id
                        join
                    hrm_acc_journal_type as c on a.hrm_acc_journal_type_id = c.id
                         $condition
                        order by a.id desc
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
                ->rawColumns(['Link'])
                ->make(true);
        }

        return view('AccountsIntegration.head_title.index');
    }

    public function create()
    {
        $headTitle = null;

        return response()->json(
            view('AccountsIntegration.head_title._form', compact('headTitle'))->render()
        );
    }

    public function store(Request $request)
    {


        $status = false;

        $validator = Validator::make($request->all(), [
            'head_name' => 'required|string|max:100',
            'hrm_acc_journal_type_id' => 'nullable|integer|exists:hrm_acc_journal_type,id',

            'hrm_acc_head_title_id' => 'nullable|integer|exists:hrm_acc_head_title,id',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            DB::beginTransaction();
            try {
                $journalTypeId = null;

                if ($request->hrm_acc_journal_type_id) {
                    $journalTypeId = $request->hrm_acc_journal_type_id;
                } else {
                    $journalTypeId = HrmAccHeadTitle::query()
                        ->firstWhere('id', $request->hrm_acc_head_title_id)->hrm_acc_journal_type_id;
                }

                if($request->isCompanyContribute){
                    $isCompanyContribute= 1;
                }else{
                    $isCompanyContribute= null;
                };


                if($request->isSalaryAllow){
                    $isSalaryAllow= 1;
                }else{
                    $isSalaryAllow= null;
                };



                if($request->isCasualWorker){
                    $isCasualWorker = 1;
                }else{
                    $isCasualWorker  = null;
                };



                HrmAccHeadTitle::create([
                    'head_name' => $request->head_name,
                    'hrm_acc_head_title_id' => $request->hrm_acc_head_title_id,
                    'status' => $request->status,
                    'hrm_acc_journal_type_id' => $journalTypeId,
                    'isCompanyContribute' => $isCompanyContribute,
                    'isSalaryAllow' => $isSalaryAllow,
                    'isCasualWorker' => $isCasualWorker,
                    'is_active' => 1,
                    'users_id' => auth()->id(),
                ]);

                DB::commit();

                $status = true;
                $message = 'Head Title has been created..!';
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

        $headTitle = DB::select("
            SELECT
                a.id,
                a.hrm_acc_journal_type_id,
                a.hrm_acc_head_title_id,
                a.head_name,
                a.is_active,
                c.journal_type_name,
                b.head_name as parent,
                a.status,
                a.isCompanyContribute,
                a.isSalaryAllow,
                a.isCasualWorker
               FROM
                hrm_acc_head_title as a
                    LEFT JOIN
                hrm_acc_head_title as b on a.hrm_acc_head_title_id = b.id
                    JOIN
                hrm_acc_journal_type as c on a.hrm_acc_journal_type_id = c.id
                    WHERE a.id = $id
        ")[0];

        return response()->json(
            view('AccountsIntegration.head_title._form', compact('headTitle'))->render()
        );
    }

    public function update(Request $request, $id)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            // 'head_name' => 'required|string|max:100|unique:hrm_acc_head_title,head_name,'.$id,
            'head_name' => 'required|string|max:100|unique:hrm_acc_head_title,head_name,'.decrypt($id),
            'hrm_acc_journal_type_id' => 'nullable|integer|exists:hrm_acc_journal_type,id',

            'hrm_acc_head_title_id' => 'nullable|integer|exists:hrm_acc_head_title,id',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            DB::beginTransaction();
            try {
                $headTitle = HrmAccHeadTitle::query()->findOrFail(decrypt($id));

                $journalTypeId = null;

                if ($request->hrm_acc_journal_type_id) {
                    $journalTypeId = $request->hrm_acc_journal_type_id;
                } else {
                    $journalTypeId = HrmAccHeadTitle::query()
                        ->firstWhere('id', $request->hrm_acc_head_title_id)->hrm_acc_journal_type_id;
                }


                if($request->isCompanyContribute){
                    $isCompanyContribute= 1;
                }else{
                    $isCompanyContribute= null;
                };


                if($request->isSalaryAllow){
                    $isSalaryAllow= 1;
                }else{
                    $isSalaryAllow= null;
                };



                if($request->isCasualWorker){
                    $isCasualWorker = 1;
                }else{
                    $isCasualWorker  = null;
                };


                $headTitle->update([
                    'head_name' => $request->head_name,
                    'hrm_acc_head_title_id' => $request->hrm_acc_head_title_id,
                    'status' => $request->status,
                    'hrm_acc_journal_type_id' => $journalTypeId,
                    'isCompanyContribute' => $isCompanyContribute,
                    'isSalaryAllow' => $isSalaryAllow,
                    'isCasualWorker' => $isCasualWorker,
                    'is_active' => $request->is_active === 'on' ? 1 : 0,
                    'users_id' => auth()->id(),
                ]);

                DB::commit();

                $status = true;
                $message = 'Head Title has been updated..!';
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
            $headTitle = HrmAccHeadTitle::query()->findOrFail(decrypt($id));

            $headTitle->delete();

            DB::commit();

            $status = true;
            $message = 'Head Title has been deleted..!';
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
        ]);
    }

    public function dropdown(Request $request)
    {
        $data = DB::select("
            select
                a.id,
                concat_ws(' | ', a.head_name, b.head_name, c.journal_type_name) as text
            from
                hrm_acc_head_title as a
                    left join
                hrm_acc_head_title as b on a.hrm_acc_head_title_id = b.id and a.is_active = 1
                    join
                hrm_acc_journal_type as c on a.hrm_acc_journal_type_id = c.id
                    where a.head_name like '%$request->term%'
                    or b.head_name like '%$request->term%'

        ");

        return response()->json($data);
    }

    public function dropdownParent(Request $request)
    {
        $data = DB::select("


            SELECT
                    a.id,
                    CONCAT(c.journal_type_name,' - ',a.head_name,' - ',a.status,' - ', ifnull(b.head_name,'MAIN HEAD') ) as text ,
                    if(a.is_active = 1, 'Active', 'Inactive') as is_active,
                    if(a.isCompanyContribute = 1, 'PF', '-') as isCompanyContribute,
                    if(a.isSalaryAllow = 1, 'S&A Head', '-') as isSalaryAllow,
                    c.journal_type_name,
                    b.head_name as parent,
                    a.status
                from
                    hrm_acc_head_title as a
                        left join
                    hrm_acc_head_title as b on a.hrm_acc_head_title_id = b.id
                        join
                    hrm_acc_journal_type as c on a.hrm_acc_journal_type_id = c.id
                        where 
                         a.is_active = 1
                        and a.head_name like '%$request->term%'

                        order by a.id desc

        ");

        return response()->json($data);


            // select
            //     a.id,
            //     a.head_name as text
            // from
            //     hrm_acc_head_title as a
            //         where a.hrm_acc_head_title_id is null
            //         and a.is_active = 1
            //         and a.head_name like '%$request->term%'

    }
}
