<?php

namespace App\Http\Controllers\AccountsIntegration;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AccountsIntegration\HrmAccJournalType;
use App\Models\HrmSalaryHead;
use Exception;
use Illuminate\Support\Facades\Validator;

class AccountJournalTypeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = DB::select("
                SELECT id, journal_type_name FROM hrm_acc_journal_type
            ");

            return datatables()->of($data)
                ->addColumn('Link', function ($data) {
                    return '
                    <a href="'.route('account_journal_type.edit', encrypt($data->id)).'" data-title="Edit Journal Type" footer-none class="modalLink btn btn-sm btn-flat">
                        <i class="glyphicon glyphicon-edit"></i> Edit
                    </a>

                    <form action="'.route('account_journal_type.destroy', encrypt($data->id)).'" method="post" class="deleteJournalType" style="display:inline-block">
                        '.csrf_field().'
                        '.method_field("delete").'
                        <button type="submit" class="edit btn btn-sm btn-flat" title="Delete Record"><i class="fa fa-trash"></i></button>
                    </form>';
                })
                ->rawColumns(['Link'])
                ->make(true);
        }
        return view('AccountsIntegration.journal_type.index');
    }

    public function create()
    {
        $journal = null;

        return response()->json(
            view('AccountsIntegration.journal_type._form', compact('journal'))->render()
        );
    }

    public function store(Request $request)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'journal_type_name' => 'required|string|max:45|unique:hrm_acc_journal_type,journal_type_name',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            DB::beginTransaction();
            try {
                HrmAccJournalType::create([
                    'journal_type_name' => $request->journal_type_name,
                ]);

                DB::commit();

                $status = true;
                $message = 'Journal Type has been created..!';
            } catch (Exception $e) {
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
        $journal = HrmAccJournalType::query()->findOrFail(decrypt($id));

        return response()->json(
            view('AccountsIntegration.journal_type._form', compact('journal'))->render()
        );
    }

    public function update(Request $request, $id)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'journal_type_name' => 'required|string|max:45|unique:hrm_acc_journal_type,journal_type_name,'.decrypt($id),
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            DB::beginTransaction();
            try {
                $journal = HrmAccJournalType::query()->findOrFail(decrypt($id));

                $journal->update([
                    'journal_type_name' => $request->journal_type_name,
                ]);

                DB::commit();

                $status = true;
                $message = 'Journal Type has been updated..!';
            } catch (Exception $e) {
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
            $journal = HrmAccJournalType::query()->findOrFail(decrypt($id));

            $journal->delete();

            DB::commit();

            $status = true;
            $message = 'Journal Type has been deleted..!';
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
            select id, journal_type_name as text from hrm_acc_journal_type where journal_type_name like '%$request->term%'
        ");

        return response()->json($data);
    }
}
