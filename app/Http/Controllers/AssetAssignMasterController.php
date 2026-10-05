<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\{ HrmAssetStoreMaster, HrmAssetStoreLedger,HrmAssetAssignMaster, HrmAssetAssignLedger};

class AssetAssignMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('assetassign.index');
    }

    public function create()
    {
        return view('assetassign.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // "hrm_asset_store_master_id" => 'required',
            "product_id" => 'required',
            "price"      => 'required',
            "serial_no"  => 'required',
            'qty_stockout'  => 'required|numeric',
            'current_stock' => 'required|numeric'
        ]);

        $validator->after(function ($validator) use ($request) {
            if($request->qty_stockout > $request->current_stock){
                $validator->errors()->add('qty_stockout', 'Assign Quantity cannot be greater than Available Quantity.');
            }
        });

        if( $validator->fails() ){
            return response()->json(array(
                'success'   => false,
                'message'  => 'Failure, validation fail!'
            ));
        }


        $store = new DataController();
        $store_no = $store->generate_registration('hrm_asset_store_master', 'store_no','ASG-',2);

        $assign_date    = date('Y-m-d', strtotime(str_replace('/', '-', $request->assign_date)));



        DB::beginTransaction();
        try {

            $asset_master = new HrmAssetStoreMaster;
            $asset_master->transaction_type = 2;
            $asset_master->store_no=$store_no;
            $asset_master->transaction_date = $assign_date ;
            $asset_master->users_id = auth()->id();
            $asset_master->status = 1;
            $asset_master->save();

            $asset_assign_master = new HrmAssetAssignMaster;
            if ($request->hrm_employee_id) {
                $asset_assign_master->hrm_employee_id = $request->hrm_employee_id;
            }
            elseif ($request->hrm_depertments_id) {
                $asset_assign_master->hrm_depertment_id = $request->hrm_depertments_id;
            }

            $asset_assign_master->hrm_asset_store_master_id = $asset_master->id;
            $asset_assign_master->assign_date = $assign_date ;
            $asset_assign_master->assign_no = $store_no;
            $asset_assign_master->is_valid = 1;
            $asset_assign_master->transaction_type = 2;
            $asset_assign_master->status = 1;
            $asset_assign_master->lock_status = 1;
            $asset_assign_master->users_id = auth()->id();
            $asset_assign_master->save();



            $asset_assign_ledger = new HrmAssetAssignLedger;
            $asset_assign_ledger->asset_assign_master_id = $asset_assign_master->id;
            $asset_assign_ledger->product_id = $request->product_id;
            $asset_assign_ledger->price = $request->price;
            $asset_assign_ledger->serial_no = $request->serial_no;
            $asset_assign_ledger->qty_stockout = $request->qty_stockout;
            $asset_assign_ledger->is_valid = 1;
            $asset_assign_ledger->save();



            $asset_ledger = new HrmAssetStoreLedger;
            $asset_ledger->asset_store_master_id = $asset_master->id;
            $asset_ledger->product_id = $request->product_id;
            $asset_ledger->price =  $request->price;
            $asset_ledger->serial_no = $request->serial_no;
            $asset_ledger->qty_stockout = $request->qty_stockout;
            $asset_ledger->qty_stockin=0;
            $asset_ledger->hrm_asset_store_ledger_id=$request->asset_store_ledger_id;
            $asset_ledger->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 'Created Asset Assign',
                 $asset_assign_master,
                 $asset_assign_master->id,
                 'hrm_asset_assign_master'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'success'   => false,
                'message'   => 'Failure!'
            ));
        }

        return response()->json(array(
            'success'   => true,
            'message'   => 'Success!'
         ));
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $assign_master = HrmAssetAssignMaster::find($id);

        if (!empty($assign_master->hrm_employee_id)) {
            $emp = true;


            $data = DB::select("SELECT
                    a.id,
                    CONCAT(d.asset_type_name, ' | ', e.brand_name, ' | ', c.model, ' | ', b.serial_no) as asset,
                    CONCAT(f.employee_name, ' | ', g.employee_code, ' | ', j.designation_name, ' | ', i.depertment_name, ' | ', h.location_name) as employee_name,
                    f.id as employee_id,
                    bb.id as ledger_id,
                    c.id as hrm_asset_id,
                    i.depertment_name as department,
                    j.designation_name as designation,
                    bb.price as price,
                    bb.serial_no as serial_no,
                    bb.qty_stockout
                FROM
                    hrm_asset_assign_master as a
                JOIN
                    hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id AND a.is_valid = 1 AND b.is_valid=1
                    AND a.id=$id
                JOIN
                    hrm_asset_store_master aa ON aa.id=a.hrm_asset_store_master_id AND aa.is_valid=1
                JOIN
                    hrm_asset_store_ledger bb ON aa.id=bb.asset_store_master_id AND bb.is_valid=1
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
                    hrm_location h On g.hrm_location_id=h.id
                JOIN
                    hrm_depertment i On g.hrm_depertment_id=i.id
                JOIN
                    hrm_designation j On g.hrm_designation_id=j.id
            ");
        } else {
            $emp = false;

            $data = DB::select("SELECT
                    a.id,
                    CONCAT_WS(' | ', c.description,d.asset_type_name, e.brand_name, c.model, b.serial_no) as asset,
                    f.depertment_name,
                    a.hrm_depertment_id,

                    bb.id as ledger_id,
                    c.id as hrm_asset_id,
                    bb.price as price,
                    bb.serial_no as serial_no,
                    bb.qty_stockout
                FROM
                    hrm_asset_assign_master as a
                JOIN
                    hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id AND a.is_valid = 1 AND b.is_valid=1
                    AND a.id= $id
                JOIN
                    hrm_asset_store_master aa ON aa.id=a.hrm_asset_store_master_id AND aa.is_valid=1
                JOIN
                    hrm_asset_store_ledger bb ON aa.id=bb.asset_store_master_id AND bb.is_valid=1
                JOIN
                    hrm_asset as c ON c.id = b.product_id
                JOIN
                    hrm_asset_type as d ON d.id = c.asset_type_id
                JOIN
                    hrm_asset_brand as e ON e.id = c.brand_id
                JOIN
                    hrm_depertment f On a.hrm_depertment_id = f.id
            ");
        }
        $asset_id = $data[0]->hrm_asset_id;
        $stock = DB::select("SELECT
                                    d.hrm_asset_store_ledger_id AS id,
                                    CONCAT(c.asset_type_name,
                                            ' | ',
                                            b.brand_name,
                                            ' | ',
                                            a.model,
                                            ' | ',
                                            d.serial_no,
                                            ' | ',
                                            a.description ) AS text,
                                    d.price,
                                    d.product_id,
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
                                WHERE  a.id = $asset_id
                                GROUP BY
                                    a.id
                                    -- d.product_id , d.serial_no , d.hrm_asset_store_ledger_id, c.asset_type_name,b.brand_name,a.model,d.price
                                HAVING
                                    SUM(d.qty_stockin - d.qty_stockout) > 0");

        // dd($stock);
        return view('assetassign.edit', compact('data', 'emp', 'id','stock'));
    }

    public function update(Request $request, $id)
    {
        $activity = DB::table('hrm_asset_assign_master')->where('is_valid', 0)
            ->where('id', $id)->first();

        if (!empty($activity)){
            return response()->json(array(
                'success'   => false,
                'messages'  => 'This data already updated, Please check!',
            ));
        }

        $assign_date    = date('Y-m-d', strtotime(str_replace('/', '-', $request->assign_date)));
        $find_data = DB::table('hrm_asset_assign_master')->where('id','=',$id)->first();

        DB::beginTransaction();
        try {

            HrmAssetStoreMaster::where('id',$find_data->hrm_asset_store_master_id)->update(['is_valid' => 0]);
            HrmAssetStoreLedger::where('asset_store_master_id',$find_data->hrm_asset_store_master_id)->update(['is_valid' => 0]);
            HrmAssetAssignMaster::where('id',$id)->update(['is_valid' => 0]);
            HrmAssetAssignLedger::where('asset_assign_master_id',$id)->update(['is_valid' => 0]);

            // dd("working");

            $asset_master = new HrmAssetStoreMaster;
            $asset_master->transaction_type = 2;
            $asset_master->store_no=$find_data->assign_no;
            $asset_master->transaction_date = $assign_date ;
            $asset_master->users_id = auth()->user()->id;
            $asset_master->status = 1;
            $asset_master->save();


            $asset_assign_master = new HrmAssetAssignMaster;

            if ($request->hrm_employee_id) {
                $asset_assign_master->hrm_employee_id = $request->hrm_employee_id;
            }
            elseif ($request->hrm_depertments_id) {
                $asset_assign_master->hrm_depertments_id = $request->hrm_depertments_id;
            }

            $asset_assign_master->hrm_asset_store_master_id = $asset_master->id;
            $asset_assign_master->assign_date = $assign_date ;
            $asset_assign_master->assign_no = $find_data->assign_no;
            $asset_assign_master->is_valid = 1;
            $asset_assign_master->transaction_type = 2;
            $asset_assign_master->status = 1;
            $asset_assign_master->lock_status = 1;
            $asset_assign_master->users_id = auth()->id();
            $asset_assign_master->save();



            $asset_assign_ledger = new HrmAssetAssignLedger;
            $asset_assign_ledger->asset_assign_master_id = $asset_assign_master->id;
            $asset_assign_ledger->product_id = $request->product_id;
            $asset_assign_ledger->price = $request->price;
            $asset_assign_ledger->serial_no = $request->serial_no;
            $asset_assign_ledger->qty_stockout = $request->qty_stockout;
            $asset_assign_ledger->is_valid = 1;
            $asset_assign_ledger->save();


            $asset_ledger = new HrmAssetStoreLedger;
            $asset_ledger->asset_store_master_id = $asset_master->id;
            $asset_ledger->product_id = $request->product_id;
            $asset_ledger->price =  $request->price;
            $asset_ledger->serial_no = $request->serial_no;
            $asset_ledger->qty_stockout = $request->qty_stockout;
            $asset_ledger->qty_stockin=0;
            $asset_ledger->hrm_asset_store_ledger_id=$request->asset_store_ledger_id;
            $asset_ledger->save();


            DB::commit();

            $this->recordActivity(
                 1,
                 'Updated Asset Assign',
                 $asset_assign_master,
                 $asset_assign_master->id,
                 'hrm_asset_assign_master'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'success'   => false,
                'message'   => 'Failure!'
            ));
        }
        return response()->json(array(
            'success'   => true,
            'message'   => 'Success!'
        ));
    }

    public function lock($id)
    {
        $id = decrypt($id);

        try {
            HrmAssetAssignMaster::query()
                ->where('id', $id)
                ->update(['lock_status' => 2]);

                $this->recordActivity(
                     1,
                     'Locked Asset Assign',
                     null,
                     $id,
                     'hrm_asset_assign_master'
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

    public function asset_assign_list()
    {
       $from_date = date('Y-m-d', strtotime(str_replace('/', '-', request()->from_date)));
        $to_date   = date('Y-m-d', strtotime(str_replace('/', '-', request()->to_date)));


        // ================= EMPLOYEE ASSIGN =================
        $query1 = DB::table('hrm_asset_assign_master as a')

            ->join('hrm_asset_store_master as aa', function($join) use ($from_date, $to_date){
                $join->on('a.hrm_asset_store_master_id', '=', 'aa.id')
                    ->where('aa.is_valid', 1)
                    ->whereBetween('a.assign_date', [$from_date, $to_date]);
            })
            ->join('hrm_asset_store_ledger as asl', 'asl.asset_store_master_id', '=', 'aa.id')
            ->join('hrm_asset_assign_ledger as b', function($join){
                $join->on('a.id', '=', 'b.asset_assign_master_id')
                    ->where('b.is_valid', 1);
            })

            ->join('hrm_asset as c', 'c.id', '=', 'b.product_id')
            ->join('hrm_asset_type as d', 'd.id', '=', 'c.asset_type_id')
            ->join('hrm_asset_brand as e', 'e.id', '=', 'c.brand_id')
            ->join('hrm_employee as f', 'f.id', '=', 'a.hrm_employee_id')

            ->join('hrm_employee_job_info as g', function($join){
                $join->on('f.id', '=', 'g.hrm_employee_id')
                    ->where('g.employee_activity', 1);
            })

            ->join('hrm_location as h', 'g.hrm_location_id', '=', 'h.id')
            ->join('hrm_depertment as i', 'g.hrm_depertment_id', '=', 'i.id')
            ->join('hrm_designation as j', 'g.hrm_designation_id', '=', 'j.id')

            ->where('a.is_valid', 1)
            ->where('a.status', 1)
            ->where('f.active_status', 1)

            ->select([
                'a.id',
                'a.lock_status',
                DB::raw("CONCAT_WS(' | ', d.asset_type_name, e.brand_name, c.model, b.serial_no, c.description) as asset"),
                DB::raw("CONCAT_WS(' | ', f.employee_name, j.designation_name, i.depertment_name) as employee"),
                'a.hrm_employee_id as employee_id',
                'b.product_id',
                'aa.store_no',
                'aa.transaction_date as created_at',
                'asl.qty_stockout',
            ]);


        // ================= DEPARTMENT ASSIGN =================
        $query2 = DB::table('hrm_asset_assign_master as a')

            ->join('hrm_asset_store_master as aa', function($join) use ($from_date, $to_date){
                $join->on('a.hrm_asset_store_master_id', '=', 'aa.id')
                    ->where('aa.is_valid', 1)
                    ->whereBetween('a.assign_date', [$from_date, $to_date]);
            })
            ->join('hrm_asset_store_ledger as asl', 'asl.asset_store_master_id', '=', 'aa.id')

            ->join('hrm_asset_assign_ledger as b', function($join){
                $join->on('a.id', '=', 'b.asset_assign_master_id')
                    ->where('b.is_valid', 1);
            })

            ->join('hrm_asset as c', 'c.id', '=', 'b.product_id')
            ->join('hrm_asset_type as d', 'd.id', '=', 'c.asset_type_id')
            ->join('hrm_asset_brand as e', 'e.id', '=', 'c.brand_id')
            ->join('hrm_depertment as f', 'a.hrm_depertment_id', '=', 'f.id')

            ->where('a.is_valid', 1)
            ->where('a.status', 1)

            ->select([
                'a.id',
                'a.lock_status',
                DB::raw("CONCAT_WS(' | ', d.asset_type_name, e.brand_name, c.model, b.serial_no, c.description) as asset"),
                DB::raw("f.depertment_name as employee"),
                DB::raw("0 as employee_id"),
                'b.product_id',
                'aa.store_no',
                'aa.transaction_date as created_at',
                'asl.qty_stockout',
            ]);


                 // ================= FINAL UNION =================
                 $data = $query1->unionAll($query2)->get();
        // dd($data);
        return datatables()->of($data)
            ->addColumn('Link', function ($asset_assign_list) {
                $buttons = '';

                if ($asset_assign_list->lock_status == 1) {
                    $buttons .= '<a  href="'.url("/assetassign").'/'.encrypt($asset_assign_list->id).'/edit" class="btn btn-primary btn-sm btn-block"><span class="glyphicon glyphicon-edit"> Edit</a>
                        <a  href="'.url("/assetassign").'/'.encrypt($asset_assign_list->id).'/lock" class="btn btn-danger btn-sm btn-block lock_asset"><span class="glyphicon glyphicon-lock"> Lock</a>';
                } elseif ($asset_assign_list->lock_status == 2) {
                    $buttons .= '<button type="button" class="btn btn-secondary btn-sm btn-block" style="color:blue"><span class="glyphicon glyphicon-lock"> Locked</button>';
                }

                return $buttons;
            })
            ->rawColumns(['Link'])
            ->make(true);
    }

    public function asset_assign_list_inactive()
    {
        $asset_assign_list = DB::select(
            "SELECT
                a.id,
                CONCAT_WS(' | ', d.asset_type_name, e.brand_name, c.model, b.serial_no, c.description) as asset,
                CONCAT_WS(' | ', f.employee_name) as employee,
                a.created_at,
                a.hrm_employee_id as employee_id,
                b.product_id as product_id,
                CASE
                    WHEN a.status = 2 THEN 'Reuse'
                    WHEN a.status = 3 THEN 'Reject'
                    Else 'dono'
                END AS statustext
            FROM
                hrm_asset_assign_master as a
            JOIN
                hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id
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
                hrm_location h On g.hrm_location_id=h.id
            JOIN
                hrm_depertment i On g.hrm_depertment_id=i.id
            JOIN
                hrm_designation j On g.hrm_designation_id=j.id
            WHERE
                a.is_valid = 1 AND b.is_valid = 1 AND a.status != 1

            UNION ALL
            SELECT
                a.id,
                CONCAT_WS(' | ', d.asset_type_name, e.brand_name, c.model, b.serial_no, c.description) as asset,
                f.depertment_name as employee,
                a.created_at,
                a.hrm_employee_id as employee_id,
                b.product_id as product_id,
                CASE
                    WHEN a.status = 2 THEN 'Reuse'
                    WHEN a.status = 3 THEN 'Reject'
                    Else 'dono'
                END AS statustext
            FROM
                hrm_asset_assign_master as a
            JOIN
                hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id
            JOIN
                hrm_asset as c ON c.id = b.product_id
            JOIN
                hrm_asset_type as d ON d.id = c.asset_type_id
            JOIN
                hrm_asset_brand as e ON e.id = c.brand_id
            JOIN
                hrm_depertment f On a.hrm_depertment_id=f.id
            WHERE
                a.is_valid = 1 AND b.is_valid = 1 AND a.status != 1
            ");

        return json_encode(array('data' => $asset_assign_list));
    }

    public function asset_data(Request $request){
        $data = [];

        // dd($request->all());
        if (!empty($request->term)){
            $data = DB::select("SELECT
                                    d.hrm_asset_store_ledger_id AS id,
                                    CONCAT(c.asset_type_name,
                                            ' | ',
                                            b.brand_name,
                                            ' | ',
                                            a.model,
                                            ' | ',
                                            d.serial_no,
                                            ' | ',
                                            a.description ) AS text,
                                    d.price,
                                    d.product_id,
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
                                WHERE
                                    c.asset_type_name LIKE '%$request->term%'
                                    OR
                                    b.brand_name LIKE '%$request->term%'
                                    OR
                                    a.model LIKE '%$request->term%'
                                    OR
                                    d.serial_no LIKE '%$request->term%'
                                    OR
                                    a.description LIKE '%$request->term%'
                                GROUP BY
                                a.id
                                    -- d.product_id , d.serial_no , d.hrm_asset_store_ledger_id, c.asset_type_name,b.brand_name,a.model,d.price
                                HAVING
                                    SUM(d.qty_stockin - d.qty_stockout) > 0");

        }else{

            $data = DB::select("SELECT
                                    d.hrm_asset_store_ledger_id AS id,
                                    CONCAT(c.asset_type_name,
                                            ' | ',
                                            b.brand_name,
                                            ' | ',
                                            a.model,
                                            ' | ',
                                            d.serial_no,
                                            ' | ',
                                            a.description) AS text,
                                    d.price,
                                    d.product_id,
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
                                GROUP BY
                                    a.id
                                    -- d.product_id , d.serial_no, d.hrm_asset_store_ledger_id, c.asset_type_name, b.brand_name, a.model, d.price
                                HAVING
                                    SUM(d.qty_stockin - d.qty_stockout) > 0");

        }
        return response()->json($data);
    }



    public function asset_data_old(Request $request){
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT
                                    f.id AS id,
                                    CONCAT(c.asset_type_name,
                                            ' | ',
                                            b.brand_name,
                                            ' | ',
                                            a.model,
                                            ' | ',
                                            d.serial_no) AS text,
                                    d.price,
                                    d.product_id,
                                    d.serial_no,
                                    d.id as asset_store_ledger_id
                                FROM
                                    hrm_asset a
                                JOIN
                                    hrm_asset_brand AS b ON b.id = a.brand_id
                                JOIN
                                    hrm_asset_type AS c ON c.id = a.asset_type_id
                                JOIN
                                    hrm_asset_store_ledger AS d ON d.product_id = a.id
                                JOIN
                                    hrm_asset_store_master AS e ON d.asset_store_master_id = e.id
                                JOIN
                                    hrm_asset_assign_master AS f ON f.hrm_asset_store_master_id = e.id
                                WHERE
                                    d.is_valid = 1
                                GROUP BY
                                    d.product_id , d.serial_no , asset_store_ledger_id, id ,c.asset_type_name,b.brand_name,a.model,d.price");

        }else{

            $data = DB::select("SELECT
                                    f.id AS id,
                                    CONCAT(c.asset_type_name,
                                            ' | ',
                                            b.brand_name,
                                            ' | ',
                                            a.model,
                                            ' | ',
                                            d.serial_no) AS text,
                                    d.price,
                                    d.product_id,
                                    d.serial_no,
                                    d.id as asset_store_ledger_id
                                FROM
                                    hrm_asset a
                                JOIN
                                    hrm_asset_brand AS b ON b.id = a.brand_id
                                JOIN
                                    hrm_asset_type AS c ON c.id = a.asset_type_id
                                JOIN
                                    hrm_asset_store_ledger AS d ON d.product_id = a.id
                                JOIN
                                    hrm_asset_store_master AS e ON d.asset_store_master_id = e.id
                                JOIN
                                    hrm_asset_assign_master AS f ON f.hrm_asset_store_master_id = e.id
                                WHERE
                                    d.is_valid = 1
                                GROUP BY
                                    d.product_id , d.serial_no , asset_store_ledger_id, id, c.asset_type_name,b.brand_name,a.model,d.price");

        }
        return response()->json($data);
    }

    public function asset_assign_list_select()
    {
        $asset_assign_list = [];
        $asset_assign_list = DB::select(
            "SELECT
                a.id,
                CONCAT(d.asset_type_name, ' | ', e.brand_name, ' | ', c.model, ' | ', b.serial_no) as asset,
                CONCAT(f.employee_name, ' | ', g.employee_code, ' | ', h.location_name) as employee,
                f.Images,
                i.depertment_name,
                j.designation_name,
                a.created_at
            FROM
                hrm_asset_assign_master as a
            JOIN
                hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id
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
                hrm_location h On g.hrm_location_id=h.id
            JOIN
                hrm_depertment i On g.hrm_depertment_id=i.id
            JOIN
                hrm_designation j On g.hrm_designation_id=j.id
            WHERE
                b.is_valid = 1 "
        );

        return response()->json($asset_assign_list);
    }


    public function assigned_list_employee(Request $request)
    {
        $assigned_list_employee = [];
        $assigned_list_employee = DB::select("SELECT
                a.hrm_employee_id as id,
                CONCAT_WS(' | ', f.employee_name, g.employee_code, j.designation_name, i.depertment_name, h.location_name) as text
            FROM
                hrm_asset_assign_master as a
            JOIN
                hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id AND a.is_valid = 1 AND b.is_valid = 1 AND a.status = 1
            JOIN
                hrm_employee as f ON f.id = a.hrm_employee_id
            JOIN
                hrm_employee_job_info g ON f.id=g.hrm_employee_id AND  f.active_status=1 AND g.employee_activity=1
            JOIN
                hrm_location h On g.hrm_location_id=h.id
            JOIN
                hrm_depertment i On g.hrm_depertment_id=i.id
            JOIN
                hrm_designation j On g.hrm_designation_id=j.id
            WHERE
                    f.employee_name LIKE '%$request->term%'
                OR
                    g.employee_code LIKE '%$request->term%'
                OR
                    j.designation_name LIKE '%$request->term%'
                OR
                    i.depertment_name LIKE '%$request->term%'
            GROUP BY id, text
        ");

        return response()->json($assigned_list_employee);
    }

    public function assigned_list_department(Request $request)
    {
        $assigned_list_department = DB::select("SELECT
                a.hrm_depertment_id as id,
                c.depertment_name as text
            FROM
                hrm_asset_assign_master as a
            JOIN
                hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id AND a.is_valid = 1 AND b.is_valid = 1 AND a.status = 1
            JOIN
                hrm_depertment as c ON c.id = a.hrm_depertment_id
            WHERE
                c.depertment_name LIKE '%$request->term%'
            GROUP BY id, text");

        return response()->json($assigned_list_department);
    }

    public function asset_assign_list_get(Request $request)
    {
        $asset_assign_list = [];

        if($request->employee_id != null) {
            $asset_assign_list = DB::select("SELECT
                    a.id,
                    CONCAT(d.asset_type_name, ' | ', e.brand_name, ' | ', c.model, ' | ', b.serial_no, ' | ', c.description) as text,
                    b.product_id as product_id
                FROM
                    hrm_asset_assign_master as a
                JOIN
                    hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id AND a.hrm_employee_id = $request->employee_id AND a.is_valid = 1 AND b.is_valid = 1 AND a.status = 1
                JOIN
                    hrm_asset as c ON c.id = b.product_id
                JOIN
                    hrm_asset_type as d ON d.id = c.asset_type_id
                JOIN
                    hrm_asset_brand as e ON e.id = c.brand_id
            ");
        } else {
            $asset_assign_list = DB::select("SELECT
                    a.id,
                    CONCAT(d.asset_type_name, ' | ', e.brand_name, ' | ', c.model, ' | ', b.serial_no, ' | ', c.description) as text,
                    b.product_id as product_id
                FROM
                    hrm_asset_assign_master as a
                JOIN
                    hrm_asset_assign_ledger AS b ON a.id = b.asset_assign_master_id AND a.hrm_depertment_id = $request->department_id AND a.is_valid = 1 AND b.is_valid = 1 AND a.status = 1
                JOIN
                    hrm_asset as c ON c.id = b.product_id
                JOIN
                    hrm_asset_type as d ON d.id = c.asset_type_id
                JOIN
                    hrm_asset_brand as e ON e.id = c.brand_id
            ");
        }

        return response()->json($asset_assign_list);
    }
}
