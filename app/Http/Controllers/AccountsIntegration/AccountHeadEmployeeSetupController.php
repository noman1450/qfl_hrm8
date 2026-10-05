<?php

namespace App\Http\Controllers\AccountsIntegration;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\AccountsIntegration\HrmAccHeadWiseEmpTag;

class AccountHeadEmployeeSetupController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {


            $condition='';

            if(request()->hrm_acc_journal_type_id){
                $condition .= ' Where i.id='.request()->hrm_acc_journal_type_id;
            }



            $data = DB::select("
                select
                    a.id,
                    a.hrm_acc_head_title_id,
                   concat(h.head_name,'-',h.status) as head_name ,
                    i.journal_type_name,
                    group_concat(e.location_name separator '<br/>') as LocationName,
                    group_concat(g.salary_head separator '<br/>') as SalaryHeadName,
                    group_concat(c.category_name separator '<br/>') as CategoryName
                from
                    hrm_acc_headwise_emp_tag as a
                        join
                    hrm_acc_headwise_emp_tag_category as b on b.hrm_acc_headwise_emp_tag_id = a.id
                        join
                    hrm_category as c on b.hrm_category_id = c.id
                        join
                    hrm_acc_headwise_emp_tag_location as d on d.hrm_acc_headwise_emp_tag_id = a.id
                        join
                    hrm_location as e on e.id = d.hrm_location_id
                        left join
                    hrm_acc_headwise_emp_tag_salaryhead as f on f.hrm_acc_headwise_emp_tag_id = a.id
                        left join
                    hrm_salary_head as g on f.hrm_salary_head_id = g.id
                        join
                    hrm_acc_head_title as h on a.hrm_acc_head_title_id = h.id
                        join
                    hrm_acc_journal_type as i on h.hrm_acc_journal_type_id = i.id
                     $condition
                        group by a.id
            ");

            return datatables()->of($data)
                ->addColumn('Link', function ($data) {
                    return '
                    <a href="'.route('account_head_wise_employee_setup.edit', encrypt($data->id)).'" class="btn btn-sm btn-flat">
                        <i class="glyphicon glyphicon-edit"></i> Edit
                    </a>

                    <form action="'.route('account_head_wise_employee_setup.destroy', encrypt($data->id)).'" method="post" class="deleteHeadWiseEmployeeSetup" style="display:inline-block">
                        '.csrf_field().'
                        '.method_field("delete").'
                        <button type="submit" class="edit btn btn-sm btn-flat" title="Delete Record"><i class="fa fa-trash"></i></button>
                    </form>';
                })
                ->rawColumns(['Link', 'LocationName', 'CategoryName', 'SalaryHeadName'])
                ->make(true);
        }

        return view('AccountsIntegration.head_wise_employee.index');
    }

    public function create()
    {
        $data['locations'] = $this->locations();

        $data['categories'] = $this->categories();

        $data['salaryHeads'] = $this->salaryHeads();

        return view('AccountsIntegration.head_wise_employee.create', $data);
    }

    public function store(Request $request)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'hrm_acc_head_title_id' => 'required|integer|exists:hrm_acc_head_title,id|unique:hrm_acc_headwise_emp_tag,hrm_acc_head_title_id',
            'hrm_location_id.*' => 'required|integer|exists:hrm_location,id',
            'hrm_salary_head_id.*' => 'required|integer|exists:hrm_salary_head,id',
            'hrm_category_id.*' => 'nullable|integer|exists:hrm_category,id',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            DB::beginTransaction();
            try {


                $headWiseId = HrmAccHeadWiseEmpTag::create([
                    'hrm_acc_head_title_id' => $request->hrm_acc_head_title_id
                ])->id;

               if($request->hrm_location_id){
                    foreach ($request->hrm_location_id as $location) {
                        DB::table('hrm_acc_headwise_emp_tag_location')->insert([
                            'hrm_acc_headwise_emp_tag_id' => $headWiseId,
                            'hrm_location_id' => $location
                        ]);
                    }  
               } 


               if($request->hrm_salary_head_id){

                    foreach ($request->hrm_salary_head_id as $location) {
                        DB::table('hrm_acc_headwise_emp_tag_salaryhead')->insert([
                            'hrm_acc_headwise_emp_tag_id' => $headWiseId,
                            'hrm_salary_head_id' => $location
                        ]);
                    }
                } 

               if($request->hrm_category_id){

                    if ($request->has('hrm_category_id')) {
                        foreach ($request->hrm_category_id as $category) {
                            DB::table('hrm_acc_headwise_emp_tag_category')->insert([
                                'hrm_acc_headwise_emp_tag_id' => $headWiseId,
                                'hrm_category_id' => $category
                            ]);
                        }
                    }
                }

                DB::commit();

                $status = true;
                $message = 'Head Wise Employee has been created..!';
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

        $data['headWiseEmpSetup'] = DB::select("
            select
                a.id,
                a.hrm_acc_head_title_id,
                b.head_name
            from
                hrm_acc_headwise_emp_tag as a
                    join
                hrm_acc_head_title as b on a.hrm_acc_head_title_id = b.id
                    where a.id = $id
        ")[0];

        $data['locations'] = $this->locations($data['headWiseEmpSetup']);

        $data['categories'] = $this->categories($data['headWiseEmpSetup']);

        $data['salaryHeads'] = $this->salaryHeads($data['headWiseEmpSetup']);

        return view('AccountsIntegration.head_wise_employee.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $status = false;

        $validator = Validator::make($request->all(), [
            'hrm_acc_head_title_id' => 'required|integer|exists:hrm_acc_head_title,id|unique:hrm_acc_headwise_emp_tag,hrm_acc_head_title_id,'.decrypt($id),
            'hrm_location_id.*' => 'required|integer|exists:hrm_location,id',
            'hrm_location_id.*' => 'required|integer|exists:hrm_location,id',
            'hrm_category_id.*' => 'nullable|integer|exists:hrm_category,id',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            $message = 'Check Validation..';
        } else {
            DB::beginTransaction();
            try {
                $headWiseEmpTag = HrmAccHeadWiseEmpTag::query()->findOrFail(decrypt($id));

                $headWiseEmpTag->update([
                    'hrm_acc_head_title_id' => $request->hrm_acc_head_title_id
                ]);


                if ($request->has('hrm_location_id')) {
                    DB::delete("delete from hrm_acc_headwise_emp_tag_location where hrm_acc_headwise_emp_tag_id = $headWiseEmpTag->id");

                    foreach ($request->hrm_location_id as $location) {
                        DB::table('hrm_acc_headwise_emp_tag_location')->insert([
                            'hrm_acc_headwise_emp_tag_id' => decrypt($id),
                            'hrm_location_id' => $location
                        ]);
                    }
                }

                if ($request->has('hrm_salary_head_id')) {
                    DB::delete("delete from hrm_acc_headwise_emp_tag_salaryhead where hrm_acc_headwise_emp_tag_id = $headWiseEmpTag->id");

                    foreach ($request->hrm_salary_head_id as $head) {
                        DB::table('hrm_acc_headwise_emp_tag_salaryhead')->insert([
                            'hrm_acc_headwise_emp_tag_id' => decrypt($id),
                            'hrm_salary_head_id' => $head
                        ]);
                    }
                }

                if ($request->has('hrm_category_id')) {
                    DB::delete("delete from hrm_acc_headwise_emp_tag_category where hrm_acc_headwise_emp_tag_id = $headWiseEmpTag->id");

                    foreach ($request->hrm_category_id as $category) {
                        DB::table('hrm_acc_headwise_emp_tag_category')->insert([
                            'hrm_acc_headwise_emp_tag_id' => decrypt($id),
                            'hrm_category_id' => $category
                        ]);
                    }
                }

                DB::commit();

                $status = true;
                $message = 'Head Wise Employee has been updated..!';
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
            $headWiseEmpTag = HrmAccHeadWiseEmpTag::query()->findOrFail(decrypt($id));

            DB::delete("delete from hrm_acc_headwise_emp_tag_details where hrm_acc_headwise_emp_tag_id = $headWiseEmpTag->id");

            $headWiseEmpTag->delete();

            DB::commit();

            $status = true;
            $message = 'Head Wise Employee has been deleted..!';
        } catch (\Exception $e) {
            DB::rollback();
            $message = $e->getMessage();
        }

        return response()->json([
            'status' => $status,
            'message' => $message ?? '',
        ]);
    }

    protected function locations($head = null)
    {
        $selectedLocations = null;

        if ($head !== null) {
            $selectedLocations = DB::select("
                select
                    a.hrm_location_id,
                    b.location_name
                from
                    hrm_acc_headwise_emp_tag_location as a
                        join
                    hrm_location as b on a.hrm_location_id = b.id
                        where a.hrm_acc_headwise_emp_tag_id = $head->id
            ");
        }

        $locations = DB::table('hrm_location')
            ->where('valid', 1)
            ->pluck('location_name', 'id');

        $tree = "<ul id='location' class='hummingbird-base'>";
            foreach ($locations as $key => $location) {
                $checked = '';

                if ($head !== null) {
                    if (isset($selectedLocations)) {
                        foreach ($selectedLocations as $selected) {

                            $checked .= $key ===  (int) $selected->hrm_location_id ? 'checked' : '';
                        }
                    }
                }

                $tree .= "<li style='margin-bottom:20px;'>
                            <label class='parent-menu'>
                                <input id='location_{$key}' data-id='location_{$key}' {$checked} name='hrm_location_id[]' value='{$key}' type='checkbox' /> {$location}
                            </label>";
            }
        $tree .= "</ul>";

        return $tree;
    }

    protected function categories($head = null)
    {
        $selectedCategories = null;

        if ($head !== null) {
            $selectedCategories = DB::select("
                select
                    a.hrm_category_id,
                    b.category_name
                from
                    hrm_acc_headwise_emp_tag_category as a
                        join
                    hrm_category as b on a.hrm_category_id = b.id
                        where a.hrm_acc_headwise_emp_tag_id = $head->id
            ");
        }

        $categories = DB::table('hrm_category')
            ->where('valid', 1)
            ->pluck('category_name', 'id');

        $tree = "<ul id='category' class='hummingbird-base'>";
            foreach ($categories as $key => $category) {
                $checked = '';

                if ($head !== null) {
                    if (isset($selectedCategories)) {
                        foreach ($selectedCategories as $selected) {
                            $checked .= $key === (int) $selected->hrm_category_id ? 'checked' : '';
                        }
                    }
                }

                $tree .= "<li style='margin-bottom:20px;'>
                            <label class='parent-menu'>
                                <input id='category_{$key}' data-id='category_{$key}' {$checked} name='hrm_category_id[]' value='{$key}' type='checkbox' /> {$category}
                            </label>";
            }
        $tree .= "</ul>";

        return $tree;
    }

    protected function salaryHeads($head = null)
    {
        $selectedHeads = null;

        if ($head !== null) {
            $selectedHeads = DB::select("
                select
                    a.hrm_salary_head_id,
                    b.salary_head
                from
                    hrm_acc_headwise_emp_tag_salaryhead as a
                        join
                    hrm_salary_head as b on a.hrm_salary_head_id = b.id
                        where a.hrm_acc_headwise_emp_tag_id = $head->id
            ");
        }

        $salaryHeads = DB::select("
            select
                a.id,
                concat(a.salary_head, if(b.generate_type=1, '-> Add','-> Deduct') , if(a.apply_for=1,'->S&A','->FB')  ) as salary_head
            from
                hrm_salary_head a
                    join
                hrm_salary_head_group b on a.hrm_salary_head_group_id = b.id
                    and a.active_status = 1
                    order by a.apply_for,b.generate_type
        ");

        $tree = "<ul id='salary-head' class='hummingbird-base'>";
            foreach ($salaryHeads as $salaryHead) {
                $checked = '';

                if ($head !== null) {
                    if (isset($selectedHeads)) {
                        foreach ($selectedHeads as $selected) {
                            $checked .= $salaryHead->id === $selected->hrm_salary_head_id ? 'checked' : '';
                        }
                    }
                }

                $tree .= "<li style='margin-bottom:20px;'>
                            <label class='parent-menu'>
                                <input id='salary_{$salaryHead->id}' data-id='salary_{$salaryHead->id}' {$checked} name='hrm_salary_head_id[]' value='{$salaryHead->id}' type='checkbox' /> {$salaryHead->salary_head}
                            </label>";
            }
        $tree .= "</ul>";

        return $tree;
    }
}
