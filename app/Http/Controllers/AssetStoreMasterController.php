<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\{HrmAssetStoreMaster , HrmAssetStoreLedger, HrmAssetPurchaseMaster, HrmAssetPurchaseLedger };

class AssetStoreMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('assetstore.index');
    }

    public function create()
    {
        return view('assetstore.create');
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            "po_date" => 'required',
            "notes" => 'required',
            "serial" => 'required',
            "qty_info" => 'required',
            "price_info" => 'required',
            "product_id" => 'required',
            "depreciation_rate.*" => 'required'
        ])->validate();

        $store = new DataController();
        $store_no = $store->generate_registration('hrm_asset_store_master', 'store_no','PUR-',1);

        DB::beginTransaction();
        try {

            $asset_master = new HrmAssetStoreMaster;
            $asset_master->transaction_type = 1;
            $asset_master->store_no = $store_no;
            $asset_master->transaction_date = date('Y-m-d', str_replace('/', '-', strtotime($request->po_date)));
            $asset_master->is_valid = 1;
            $asset_master->status = 1;
            $asset_master->users_id = auth()->id();
            $asset_master->save();

            $asset_purchase_master = new HrmAssetPurchaseMaster;
            $asset_purchase_master->asset_store_master_id = $asset_master->id;
            $asset_purchase_master->purchase_date = date('Y-m-d', str_replace('/', '-', strtotime($request->po_date)));
            $asset_purchase_master->note = $request->notes;
            $asset_purchase_master->purchase_no = $store_no;
            $asset_purchase_master->is_valid = 1;
            $asset_purchase_master->users_id = auth()->id();
            $asset_purchase_master->save();

            $ids = count($request->product_id);

            for($i=0; $i < $ids; $i++) {

                $asset_ledger = new HrmAssetStoreLedger;
                $asset_ledger->asset_store_master_id = $asset_master->id;
                $asset_ledger->product_id = $request->product_id[$i];
                $asset_ledger->price = $request->price_info[$i];
                $asset_ledger->serial_no = $request->serial[$i];
                $asset_ledger->qty_stockin = $request->qty_info[$i];
                $asset_ledger->is_valid = 1;
                $asset_ledger->hrm_asset_store_ledger_id = $asset_ledger->id;
                $asset_ledger->save();

                DB::UPDATE("UPDATE hrm_asset_store_ledger SET hrm_asset_store_ledger_id= $asset_ledger->id WHERE  id= $asset_ledger->id");

                $asset_purchase_ledger = new HrmAssetPurchaseLedger;
                $asset_purchase_ledger->asset_purchase_master_id = $asset_purchase_master->id;
                $asset_purchase_ledger->product_id = $request->product_id[$i];
                $asset_purchase_ledger->price = $request->price_info[$i];
                $asset_purchase_ledger->serial_no = $request->serial[$i];
                $asset_purchase_ledger->depreciation_rate = $request->depreciation_rate[$i];
                $asset_purchase_ledger->qty_stockin = $request->qty_info[$i];
                $asset_purchase_ledger->is_valid = 1;
                $asset_purchase_ledger->save();
            }

            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Asset Store',
                 $asset_master,
                 $asset_master->id,
                 'hrm_asset_store_master'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Request could not be processed!'
            ]);
        }

        return response()->json([
            'success' => 'Data has been successfully inserted'
        ]);
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $hrmAssetStoreLedger = HrmAssetPurchaseLedger::select(
            'hrm_asset_purchase_ledger.id', 'd.asset_store_master_id',
            'hrm_asset_purchase_ledger.serial_no', 'hrm_asset_purchase_ledger.price',
            'hrm_asset_purchase_ledger.product_id', 'hrm_asset_purchase_ledger.qty_stockin',
            'a.model', 'b.asset_type_name', 'c.brand_name', 'hrm_asset_purchase_ledger.depreciation_rate'
        )
        ->join('hrm_asset as a', 'hrm_asset_purchase_ledger.product_id', '=', 'a.id')
        ->join('hrm_asset_type as b', 'a.asset_type_id', '=', 'b.id')
        ->join('hrm_asset_brand as c', 'a.brand_id', '=', 'c.id')
        ->join('hrm_asset_purchase_master as d', 'd.id', '=', 'hrm_asset_purchase_ledger.asset_purchase_master_id')
        ->join('hrm_asset_store_master as e', 'e.id', '=', 'd.asset_store_master_id')
        ->where('e.id', $id)->get();

        $hrmAssetPurchaseMaster = HrmAssetPurchaseMaster::where('asset_store_master_id', $id)->first();

        return view('assetstore.edit', compact('hrmAssetPurchaseMaster', 'hrmAssetStoreLedger'));
    }

    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            "po_date" => 'required',
            "notes" => 'required',
            "serial" => 'required',
            "qty_info" => 'required',
            "price_info" => 'required',
            "product_id" => 'required',
            "depreciation_rate.*" => 'required'
        ])->validate();

        $activity = DB::SELECT("SELECT id from hrm_asset_store_ledger where is_valid=1 AND qty_stockout>0 and
            hrm_asset_store_ledger_id IN (select id from hrm_asset_store_ledger where is_valid=1  AND asset_store_master_id=$id)");

        if (!empty($activity)) {
            $request->session()->flash('alert-danger', 'This product already assign to employee!');
            return redirect()->to('assetstore');
        }

        $lockStatus = DB::SELECT("SELECT id from hrm_asset_store_master where is_valid = 1 AND status = 2  AND id = $id");

        if (!empty($lockStatus)) {
            $request->session()->flash('alert-danger', 'This purchase is locked by User!');
            return redirect()->to('assetstore');
        }

        $check_update = DB::table('hrm_asset_store_master')->where('is_valid','=',0)->where('id','=',$id)->first();
        if (!empty($check_update)) {
            $request->session()->flash('alert-danger', 'This data already updated!');
            return redirect()->to('assetstore');
        }

        $find_data = DB::table('hrm_asset_store_master')->where('id', $id)->first();
        $find_po   = DB::table('hrm_asset_purchase_master')->where('asset_store_master_id', $id)->first();
        $store_no  = $find_data->store_no;

        DB::beginTransaction();
        try {

            HrmAssetStoreMaster::where('id', $id)->update(['is_valid' => 0]);
            HrmAssetStoreLedger::where('asset_store_master_id', $id)->update(['is_valid' => 0]);
            HrmAssetPurchaseMaster::where('asset_store_master_id', $id)->update(['is_valid' => 0]);
            HrmAssetPurchaseLedger::where('asset_purchase_master_id', $find_po->id)->update(['is_valid' => 0]);

            $asset_master = new HrmAssetStoreMaster;
            $asset_master->transaction_type = 1;
            $asset_master->store_no = $store_no;
            $asset_master->transaction_date = date('Y-m-d', str_replace('/', '-', strtotime($request->po_date)));
            $asset_master->is_valid = 1;
            $asset_master->status = 1;
            $asset_master->users_id = auth()->id();
            $asset_master->save();

            $asset_purchase_master = new HrmAssetPurchaseMaster;
            $asset_purchase_master->asset_store_master_id = $asset_master->id;
            $asset_purchase_master->purchase_date = $request->po_date;
            $asset_purchase_master->note = $request->notes;
            $asset_purchase_master->purchase_no = $store_no;
            $asset_purchase_master->users_id = auth()->user()->id;
            $asset_purchase_master->is_valid = 1;
            $asset_purchase_master->save();



            for($i=0; $i<count($request->product_id); $i++) {

                $asset_ledger = new HrmAssetStoreLedger;
                $asset_ledger->asset_store_master_id = $asset_master->id;
                $asset_ledger->product_id = $request->product_id[$i];
                $asset_ledger->price = $request->price_info[$i];
                $asset_ledger->serial_no = $request->serial_no[$i];
                $asset_ledger->qty_stockin = $request->qty_info[$i];
                $asset_ledger->save();

                DB::UPDATE("UPDATE hrm_asset_store_ledger SET hrm_asset_store_ledger_id= $asset_ledger->id WHERE  id= $asset_ledger->id");


                $asset_purchase_ledger = new HrmAssetPurchaseLedger;
                $asset_purchase_ledger->asset_purchase_master_id = $asset_purchase_master->id;
                $asset_purchase_ledger->product_id = $request->product_id[$i];
                $asset_purchase_ledger->price      = $request->price_info[$i];
                $asset_purchase_ledger->serial_no  = $request->serial_no[$i];
                $asset_purchase_ledger->depreciation_rate = $request->depreciation_rate[$i];
                $asset_purchase_ledger->qty_stockin= $request->qty_info[$i];
                $asset_purchase_ledger->is_valid = 1;
                $asset_purchase_ledger->save();

            }

            DB::commit();

            $this->recordActivity(
                 1,
                 'Updated Asset Store',
                 $asset_master,
                 $asset_master->id,
                 'hrm_asset_store_master'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Request could not be processed!');
        }

        return redirect()->to('assetstore')
            ->with('alert-success', 'data has been successfully updated!');
    }

    public function lock($id)
    {
        $id = decrypt($id);

        try {
            HrmAssetStoreMaster::query()->where('id', $id)
                ->update(['status' => 2]);

                $this->recordActivity(
                     1,
                     'Locked Asset Store',
                     null,
                     $id,
                     'hrm_asset_store_master'
                );

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return response()->json([
            'success' => 'This purchase has been Locked'
        ]);
    }

    public function asset_store_list()
    {
        $asset_list = DB::select("SELECT
                a.id,
                a.status,
                aa.purchase_date,
                aa.note,
                aa.purchase_no,
                SUM(b.qty_stockin) AS qty,
                SUM(b.qty_stockin * b.price) AS total,
                GROUP_CONCAT(CONCAT(c.description,' | ',d.asset_type_name,
                            ' | ',
                            e.brand_name,
                            ' | ',
                            c.model)
                    SEPARATOR '<br>') AS item_description,

                GROUP_CONCAT(CONCAT(b.serial_no)
                    SEPARATOR '<br>') AS serial,

                GROUP_CONCAT(CONCAT(b.depreciation_rate)
                    SEPARATOR '<br>') AS depreciation_rate,

                GROUP_CONCAT(CONCAT(b.price)
                    SEPARATOR '<br>') AS price,
                GROUP_CONCAT(CONCAT(b.qty_stockin)
                    SEPARATOR '<br>') AS qty2,
                GROUP_CONCAT(CONCAT(ROUND(b.price * b.qty_stockin))
                    SEPARATOR '<br>') AS total2
            FROM
                hrm_asset_store_master AS a
                    JOIN
                hrm_asset_purchase_master AS aa ON a.id = aa.asset_store_master_id
                    AND a.is_valid = 1
                    AND aa.is_valid = 1
                    JOIN
                hrm_asset_purchase_ledger AS b ON aa.id = b.asset_purchase_master_id
                    AND b.is_valid = 1
                    JOIN
                hrm_asset AS c ON c.id = b.product_id
                    JOIN
                hrm_asset_type AS d ON d.id = c.asset_type_id
                    JOIN
                hrm_asset_brand AS e ON e.id = c.brand_id
                    -- JOIN
                -- hrm_asset_purchase_ledger as f ON aa.id = f.asset_purchase_master_id
            WHERE
                b.qty_stockin != 0
            GROUP BY a.id , aa.purchase_date , aa.note , aa.purchase_no, aa.id
            ORDER BY a.id DESC
        ");

        return datatables()->of($asset_list)
            ->addColumn('Link', function ($asset_list) {
                $buttons = '';

                if ($asset_list->status == 1) {
                    $buttons .= '<a  href="'.url("/assetstore").'/'.encrypt($asset_list->id).'/edit" class="btn btn-primary btn-sm "><span class="glyphicon glyphicon-edit"> Edit</a>
                        <a  href="'.url("/assetstore").'/'.encrypt($asset_list->id).'/lock" class="btn btn-danger btn-sm lock_asset"><span class="glyphicon glyphicon-lock"> Lock</a>';
                } elseif ($asset_list->status == 2) {
                    $buttons .= '<button type="button" class="btn btn-secondary btn-sm " style="color:blue"><span class="glyphicon glyphicon-lock"> Locked</button>';
                }

                return $buttons;
            })
            ->rawColumns(['Link', 'item_description', 'serial', 'depreciation_rate', 'price', 'qty2', 'total2'])
            ->make(true);
    }

    public function check_unique_serial_no()
    {
        if (request()->id) {
            request()->validate([
                'serial_no' => 'unique:hrm_asset_purchase_ledger,serial_no,'.request()->id
            ]);
        } else {
            request()->validate([
                'serial_no' => 'unique:hrm_asset_purchase_ledger,serial_no'
            ]);
        }

        return response()->json();
    }

    public function availableAsset(){
        if (request()->ajax()) {
            $data = DB::select("SELECT
                    d.hrm_asset_store_ledger_id AS id,
                    CONCAT(c.asset_type_name,
                            ' | ',
                            b.brand_name,
                            ' | ',
                            a.model,
                            ' | ',
                            a.description) AS asset_name,
                    d.price,
                    d.serial_no,
                    SUM(d.qty_stockin - d.qty_stockout) current_stock
                FROM
                    hrm_asset a
                        JOIN
                    hrm_asset_brand AS b ON b.id = a.brand_id

                        JOIN
                    hrm_asset_type AS c ON c.id = a.asset_type_id
                        JOIN
                    hrm_asset_store_ledger AS d ON d.product_id = a.id AND d.is_valid = 1
                GROUP BY d.product_id
                -- , d.serial_no, d.hrm_asset_store_ledger_id, c.asset_type_name, b.brand_name, a.model, d.price
                HAVING SUM(d.qty_stockin - d.qty_stockout) > 0
            ");

            return datatables()->of($data)->make(true);
        }
        return view('assetstore.available_asset');
        // return view('hrm.AssetManagement.available_asset.index');
    }
}
