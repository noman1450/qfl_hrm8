<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\{ HrmAssetInformation, HrmAssetStoreLedger };

class AssetInformationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('assetinformation.index');
    }

    public function create()
    {
        return view('assetinformation.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'model' => 'required',
            'asset_type_id' => 'required',
            'brand_id' => 'required',
            'depreciation_rate' => 'required',
        ]);

        if( $validator->fails() ) {
            return response()->json(array(
                'success'   => false,
                'message'  => 'Failure, validation fail!'
            ));
        }

        DB::beginTransaction();
        try {
            $fileUrl = null;

            if ($file = $request->file('imgpath')) {

                $fileUrl = $file->store('asset_images', 'public');
            }

            $asset = new HrmAssetInformation;
            $asset->description = $request->description;
            $asset->model = $request->model;
            $asset->asset_type_id = $request->asset_type_id;
            $asset->brand_id = $request->brand_id;
            $asset->depreciation_rate = $request->depreciation_rate;
            $asset->imgpath = $fileUrl;
            $asset->users_id = auth()->id();
            $asset->save();

            DB::commit();

            $this->recordActivity(
                 1,
                 'Created New Asset',
                 $asset,
                 $asset->id,
                 'hrm_asset'
            );

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(array(
                'success'   => false,
                'message'   => $e->getMessage()
            ));
        }

        return redirect()->route('asset.index')->with('message', 'Success');
    }

    public function edit($id)
    {

         $id =decrypt($id);

        $asset = DB::select("SELECT
                    a.id,
                    a.description,
                    a.model,
                    a.depreciation_rate,
                    a.imgpath,
                    b.brand_name,
                    a.brand_id,
                    a.asset_type_id,
                    c.asset_type_name
                FROM
                    hrm_asset as a
                 JOIN
                    hrm_asset_brand as b on b.id = a.brand_id AND a.id = $id
                JOIN
                    hrm_asset_type as c on c.id = a.asset_type_id
            ")[0];

        return view('assetinformation.edit', compact('asset'));
    }

    public function update(Request $request, HrmAssetInformation $asset)
    {

        $validator = Validator::make($request->all(), [
            'description'    => 'required',
            'model'    => 'required',
        ]);

        if( $validator->fails() ){
            return response()->json(array(
                'success'   => false,
                'messages'  => implode(",",$validator->getMessageBag()->all()),
                'errors'    => $validator->getMessageBag()->toArray()
            ));
        }

        DB::beginTransaction();
        try {
            $asset = HrmAssetInformation::find($asset->id);
            $asset->description = $request->description;
            $asset->model = $request->model;
            $asset->asset_type_id = $request->asset_type_id;
            $asset->brand_id = $request->brand_id;
            $asset->depreciation_rate = $request->depreciation_rate;
            $asset->save();


            if ($file = $request->file('imgpath')) {
                if ($link = public_path('uploads/'.$asset->imgpath)) {
                    @unlink($link);
                }

                $asset->update([
                    'imgpath' => $file->store('asset_images', 'public')
                ]);
            }

            DB::commit();

            $this->recordActivity(
                 1,
                 'Updated New Asset',
                 $asset->getChanges(),
                 $asset->id,
                 'hrm_asset'
            );

        } catch (\Exception $e) {
            DB::rollback();
            return Redirect()->back()->with('error', 'Could not process request!');
        }

        return redirect()->route('asset.index')->with('message', 'Success');
    }

    public function destroy($id)
    {
        $id = decrypt($id);

        $asset_store = HrmAssetStoreLedger::where('product_id', '=', $id)->first();
        $asset = HrmAssetInformation::where('id', $id)->first();

        if($asset_store == null) {
            $asset->delete();

            $this->recordActivity(
                 1,
                 'Deleted New Asset',
                 $asset,
                 $id,
                 'hrm_asset'
            );

            session()->flash('success', 'data has been successfully deleted!');
            return redirect()->route('asset.index');
        } else {
            session()->flash('warning', 'Product Exists On Ledger!');
            return redirect()->route('asset.index');
        }
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function asset_list()
    {
        $asset_list = DB::select("SELECT
                a.id,
                a.description,
                a.model,
                a.depreciation_rate,
                a.imgpath,
                b.brand_name as brand,
                c.asset_type_name as asset_type
            FROM
                hrm_asset as a
             JOIN
                hrm_asset_brand as b on b.id = a.brand_id
            JOIN
                hrm_asset_type as c on c.id = a.asset_type_id
        ");

        return datatables()->of($asset_list)
            ->addColumn('Link', function ($asset_list) {
                return '<a  href="'.url("/asset").'/'.encrypt($asset_list->id).'/edit" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit"> Edit</a>
                <form action="'.url("/asset").'/'.encrypt($asset_list->id).'" method="POST" style="display:inline-block"> '.csrf_field().' '.method_field('DELETE').' <button type="submit" class="block btn btn-danger btn-sm" title="delete" onclick="return confirm(&#39;Are you sure you want to delete this item?&#39;);">Delete</button></form>';
            })
            ->rawColumns(['Link'])
            ->make(true);
    }

    public function asset_list_data(Request $request){
        $data = [];
        if (!empty($request->term)){
            $data = DB::select("SELECT a.id, CONCAT(c.asset_type_name, ' | ', b.brand_name, ' | ', a.model, ' | ', a.description) as text, a.depreciation_rate
                                FROM hrm_asset a
                                JOIN hrm_asset_brand as b on b.id = a.brand_id
                                JOIN hrm_asset_type as c on c.id = a.asset_type_id
                                WHERE c.asset_type_name LIKE '%$request->term%'
                                OR b.brand_name LIKE '%$request->term%'
                                OR a.model LIKE '%$request->term%';");
        }else{
            $data = DB::select("SELECT a.id, CONCAT(c.asset_type_name, ' | ', b.brand_name, ' | ', a.model, ' | ', a.description) as text, a.depreciation_rate
            FROM hrm_asset a
            JOIN hrm_asset_brand as b on b.id = a.brand_id
            JOIN hrm_asset_type as c on c.id = a.asset_type_id");
        }

        return response()->json($data);
    }
}
