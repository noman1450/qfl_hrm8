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

use App\Models\HrmCategory;


class CategoryController extends Controller
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
        return view('category.category_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create_category');
    }

    public function categorylist(Request $request){

        return json_encode(array('data' => HrmCategory::where('valid',1)->get()));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category'   => 'required|unique:hrm_category,category_name|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('category/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert = new HrmCategory;
        $insert->category_name          = $request->category;
        $insert->category_description   = $request->description;
        $insert->priority               = $request->priority;
        $insert->ot_rate                = $request->ot_rate;
        $insert->valid                  = 1;
        $insert->users_id               = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Created Category Name',
             null,
             $insert->id,
             'hrm_category'
        );

        $request->session()->flash('alert-success', 'data has been successfully added!');
        return Redirect::to('category');
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
        $category =  HrmCategory::find($id);


        if (empty($category)){
            session()->flash('alert-danger', 'Invalid category !!');
            return Redirect()->back();
        }

        return view('category.edit_category')->with('category',$category);
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
        $validator = Validator::make($request->all(), [
            // 'category'   => 'required|unique:hrm_category,category_name|max:255',
            'category'   => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $insert = HrmCategory::find($id);
        $insert->category_name          = $request->category;
        $insert->category_description   = $request->description;
        $insert->priority               = $request->priority;
        $insert->ot_rate                = $request->ot_rate;
        $insert->valid                  = 1;
        $insert->users_id               = Auth::user()->id;
        $insert->save();

        $this->recordActivity(
             1,
             'Updated Category Name',
             $insert->getChanges(),
             $insert->id,
             'hrm_category'
        );

        $request->session()->flash('alert-success', 'data has been successfully updated !');
        return Redirect::to('category');
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

    public function cancel(Request $request,$id){

        $cancel = HrmCategory::find($id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid category !!');
            return Redirect()->back();
        }
        $cancel->valid              = 0;
        $cancel->users_id           = Auth::user()->id;
        $cancel->save();

        $this->recordActivity(
             1,
             'Deleted Category Name',
             null,
             $id,
             'hrm_category'
        );

        $request->session()->flash('alert-success', 'successfully deleted !');
        return Redirect::to('category');

    }
}
