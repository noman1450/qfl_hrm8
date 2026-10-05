<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\{ HrmAssetReturnType, HrmAssetReturnMaster, HrmAssetStoreMaster, HrmAssetStoreLedger, HrmAssetAssignMaster};

class AssetReturnMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('assetreturn.index');
    }

    public function create()
    {
        return view('assetreturn.create');
    }

    public function store(Request $request)
    {
        $find_data = HrmAssetAssignMaster::join('hrm_asset_store_master as a', 'a.id', '=', 'hrm_asset_assign_master.hrm_asset_store_master_id')
            ->join('hrm_asset_store_ledger as b', 'b.asset_store_master_id', '=', 'a.id')
            ->where('hrm_asset_assign_master.id','=',$request->hrm_asset_assign_master_id)->first();

        $return_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->return_date)));
        $store       = new DataController();
        $store_no    = $store->generate_registration('hrm_asset_store_master', 'store_no', 'RET-', 3);
        $return_no   = $this->generate_registration('hrm_asset_return_master', 'return_no', 'RET-');

        $Return_Type = HrmAssetReturnType::find($request->asset_return_type_id);


        DB::beginTransaction();
        try {
            $asset_master = new HrmAssetStoreMaster;
            $asset_master->transaction_type = 3;
            $asset_master->store_no=$store_no;
            $asset_master->transaction_date= $return_date;
            $asset_master->is_valid = 1;
            $asset_master->status = 1;
            $asset_master->users_id = auth()->id();
            $asset_master->save();




            $asset_return = new HrmAssetReturnMaster;
            $asset_return->hrm_asset_assign_master_id = $request->hrm_asset_assign_master_id;
            $asset_return->hrm_asset_store_master_id = $asset_master->id;
            $asset_return->hrm_asset_return_type_id = $request->asset_return_type_id;
            $asset_return->note = $request->note;
            $asset_return->return_date= $return_date;
            $asset_return->return_no= $return_no;
            $asset_return->is_valid = 1;
            $asset_return->lock_status = 1;
            $asset_return->save();

            if($Return_Type->return_status == 1) {

                $asset_ledger = new HrmAssetStoreLedger;
                $asset_ledger->asset_store_master_id = $asset_master->id;
                $asset_ledger->product_id = $find_data->product_id;
                $asset_ledger->price =  $find_data->price;
                $asset_ledger->serial_no = $find_data->serial_no;
                $asset_ledger->qty_stockin = 1;
                $asset_ledger->qty_stockout = 0;
                $asset_ledger->hrm_asset_store_ledger_id=$find_data->hrm_asset_store_ledger_id;
                $asset_ledger->is_valid = 1;
                $asset_ledger->save();

                $asset_assign = HrmAssetAssignMaster::find($request->hrm_asset_assign_master_id);
                $asset_assign->status = 2;
                $asset_assign->save();
            }


            if($request->asset_return_type_id == 2){

                $asset_ledger = new HrmAssetStoreLedger;
                $asset_ledger->asset_store_master_id = $asset_master->id;
                $asset_ledger->product_id = $find_data->product_id;
                $asset_ledger->price =  $find_data->price;
                $asset_ledger->serial_no = $find_data->serial_no;
                $asset_ledger->qty_stockin = 1;
                $asset_ledger->qty_stockout = 1;
                $asset_ledger->hrm_asset_store_ledger_id=$find_data->hrm_asset_store_ledger_id;
                $asset_ledger->is_valid = 1;
                $asset_ledger->save();

                $asset_assign = HrmAssetAssignMaster::find($request->hrm_asset_assign_master_id);
                $asset_assign->status = 3;
                $asset_assign->save();
            }

            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Asset Return',
                 $asset_return,
                 $asset_return->id,
                 'hrm_asset_return_master'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return Redirect()->back()->with('error', 'Request could not be processed');
        }

         return redirect()->to('assetreturn')
            ->with('alert-success', 'Returned successfully!');
    }

    public function generate_registration($table_name, $column_name,$alies) {

		$search = DB::select("SELECT MAX(RIGHT($column_name,8)) As invno FROM $table_name");

		foreach ($search as $key)
			$maxinvoiceno = $key->invno;

		$yearid 	= date("y");
		$monthid 	= date("m");
		$datevalue 	= $yearid . $monthid;
		$invoice_no = substr($maxinvoiceno, 0,4);

		if ($maxinvoiceno==0){
			$a = "0001";
			$new_invoice_no = $yearid . $monthid . $a;
		} else {
			if ($invoice_no==$datevalue){
				$maxinvoiceno = trim(substr($maxinvoiceno, 4)) + 1;
				$maxinvoiceno = sprintf("%04s", $maxinvoiceno);
				$new_invoice_no = $datevalue . $maxinvoiceno;
			} else {
				$a = "0001";
				$new_invoice_no = $yearid . $monthid . $a;
			}
		}

		$new_invoice_no = $alies.$new_invoice_no;
		return $new_invoice_no;
	}

    public function destroy($id)
    {
        $id = decrypt($id);

        $AssetReturnMaster = HrmAssetReturnMaster::find($id);
        $HrmAssetStoreLedger = HrmAssetStoreLedger::where('asset_store_master_id',$AssetReturnMaster->hrm_asset_store_master_id)->where('is_valid', 1)->first();


        if ($HrmAssetStoreLedger != null) {
            $hrm_asset_store_ledger_id = $HrmAssetStoreLedger->hrm_asset_store_ledger_id;
            $main_id = $HrmAssetStoreLedger->id;

            $activity = DB::SELECT("SELECT id FROM hrm_asset_store_ledger WHERE qty_stockout > 0 and is_valid = 1 AND hrm_asset_store_ledger_id = $hrm_asset_store_ledger_id and id > $main_id");

            if(!empty($activity)) {
                return back()->with('alert-danger', 'This product already assign in another employee!');
            }
        }

        DB::beginTransaction();
        try {
            DB::UPDATE("UPDATE hrm_asset_return_master SET is_valid=0 WHERE id=$id");
            DB::UPDATE("UPDATE hrm_asset_store_master SET is_valid=0 WHERE id=$AssetReturnMaster->hrm_asset_store_master_id");
            DB::UPDATE("UPDATE hrm_asset_store_ledger SET is_valid=0 WHERE asset_store_master_id=$AssetReturnMaster->hrm_asset_store_master_id");
            DB::UPDATE("UPDATE hrm_asset_assign_master SET status=1 WHERE id=$AssetReturnMaster->hrm_asset_assign_master_id");

            DB::commit();

            $this->recordActivity(
                     1,
                     'Deleted Asset Return',
                     null,
                     $id,
                     'hrm_asset_return_master'
                );

        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            return Redirect()->back()->with('error', 'Request could not be processed');
        }

        return back()->with('alert-success', 'Successfully Deleted!');
    }

    public function lock($id)
    {
        try {
            HrmAssetReturnMaster::query()
                ->where('id', decrypt($id))
                ->update(['lock_status' => 2]);

                $this->recordActivity(
                     1,
                     'Locked Asset Return',
                     null,
                     decrypt($id),
                     'hrm_asset_return_master'
                );

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => 'This asset has been Locked'
        ]);
    }

    public function asset_return_list()
    {
        $asset_return_list = DB::select("SELECT
            k.id as id,
            k.lock_status,
            CONCAT_WS(' | ', d.asset_type_name, e.brand_name, c.model, b.serial_no, c.description) as asset,
            CONCAT_WS(' | ', f.employee_name, j.designation_name, i.depertment_name) as employee,
            k.return_date as created_at,
            k.return_no,
            l.asset_return_type_name as return_type
            FROM
                hrm_asset_assign_master as a
            JOIN
                hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id  and a.is_valid = 1 and b.is_valid =1 AND a.status != 1
            JOIN
                hrm_asset as c ON c.id = b.product_id
            JOIN
                hrm_asset_type as d ON d.id = c.asset_type_id
            JOIN
                hrm_asset_brand as e ON e.id = c.brand_id
            JOIN
                hrm_employee as f ON f.id = a.hrm_employee_id
            JOIN
                hrm_employee_job_info g ON f.id=g.hrm_employee_id AND  f.active_status=1 AND g.employee_activity=1
            JOIN
                hrm_location h ON g.hrm_location_id=h.id
            JOIN
                hrm_depertment i ON g.hrm_depertment_id=i.id
            JOIN
                hrm_designation j ON g.hrm_designation_id=j.id
            JOIN
                hrm_asset_return_master as k ON a.id = k.hrm_asset_assign_master_id and k.is_valid=1
            JOIN
                hrm_asset_return_type as l ON l.id = k.hrm_asset_return_type_id

            UNION ALL

            SELECT
                k.id as id,
                k.lock_status,
                CONCAT_WS(' | ', d.asset_type_name, e.brand_name, c.model, b.serial_no, c.description) as asset,
                f.depertment_name as employee,
                k.return_date as created_at,
                k.return_no,
                l.asset_return_type_name as return_type
            FROM
                hrm_asset_assign_master as a
            JOIN
                hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id  and a.is_valid = 1 and b.is_valid =1 AND a.status != 1
            JOIN
                hrm_asset as c ON c.id = b.product_id
            JOIN
                hrm_asset_type as d ON d.id = c.asset_type_id
            JOIN
                hrm_asset_brand as e ON e.id = c.brand_id
            JOIN
                hrm_depertment as f ON f.id = a.hrm_depertment_id
            JOIN
                hrm_asset_return_master as k ON a.id = k.hrm_asset_assign_master_id and k.is_valid=1
            JOIN
                hrm_asset_return_type as l ON l.id = k.hrm_asset_return_type_id
        ");

        return datatables()->of($asset_return_list)
            ->addColumn('Link', function ($asset_return_list) {
                $buttons = '';

                if ($asset_return_list->lock_status == 1) {
                    $buttons .= '<form action="'.url("/assetreturn").'/'.encrypt($asset_return_list->id).'" method="POST" style="display:inline-block"> '.csrf_field().' '.method_field('DELETE').' <button type="submit" class="block btn btn-danger btn-sm" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);">Delete</button></form>
                        <a  href="'.url("/assetreturn").'/'.encrypt($asset_return_list->id).'/lock" class="btn btn-danger btn-sm lock_asset"><span class="glyphicon glyphicon-lock"> Lock</a>';
                } elseif ($asset_return_list->lock_status == 2) {
                    $buttons .= '<button type="button" class="btn btn-secondary btn-sm btn-block" style="color:blue"><span class="glyphicon glyphicon-lock"> Locked</button>';
                }

                return $buttons;
            })
            ->rawColumns(['Link'])
            ->make(true);
    }
}
