<?php

namespace App\Http\Controllers;

// use DB;
// use Crypt;
// use Redirect;
use Response;
use Illuminate\Http\Request;
use App\Models\HrmResignationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;



class ResignationTypeController extends Controller
{
    function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        return view('resignation_type.index');
    }

    public function create()
    {
        return view('resignation_type.create');
    }

    public function resignationTypeList()
    {
        $view_data = DB::select("SELECT id,type_name FROM hrm_resignation_type WHERE valid = 1");
        $resignation_type_data   = collect($view_data);

        return datatables()->of($resignation_type_data)
            ->addColumn('Link', function ($resignation_type_data) {
            return
                ' <a href="'. url('/resignation_type') . '/' .
                Crypt::encrypt($resignation_type_data->id) .
                '/edit' .'"' .
                'class="btn btn-success btn-sm block btn-flat"><i class="glyphicon glyphicon-edit" id="customer-confrimed"></i> Edit</a>';
            })
            ->rawColumns(['Link'])
            ->make(true);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resignation_type'    => 'required|unique:hrm_resignation_type,type_name|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('resignation_type/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmResignationType;
        $insert->type_name = $request->resignation_type;
        $insert->users_id =Auth::id();
        $insert->save();

        $this->recordActivity(
            1,
           'Created Resignation Type',
            null,
            $insert->id,
            'hrm_resignation_type'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('resignation_type');
    }

    public function edit($id)
    {
        $resignation_type = HrmResignationType::find(decrypt($id));
        if (empty($resignation_type)){
            session()->flash('alert-danger', 'Invalid Resignation Type !!');
            return Redirect()->back();
        }
        return view('resignation_type.edit')->with('resignation_type', $resignation_type);
    }


    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'resignation_type'    => 'required',
            'resignation_type'    => 'required|unique:hrm_resignation_type,type_name|max:255',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmResignationType::find($id);
        $insert->type_name = $request->resignation_type;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Resignation Type',
             $insert->getChanges(),
             $insert->id,
             'hrm_resignation_type'
        );

        $request->session()->flash('alert-success', 'successfully updated');
        return Redirect::to('resignation_type');
    }


    public function destroy($id)
    {
        //
    }

}
