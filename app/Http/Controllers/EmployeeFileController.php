<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\HrmFileType;
use Illuminate\Http\Request;
use App\Models\HrmEmployeeFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class EmployeeFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user_id = auth()->id();

        $default_user_location = DB::select("SELECT b.id,b.location_name FROM `user_location` a JOIN hrm_location b ON a.`hrm_location_id`= b.id and a.`users_id`= $user_id AND a.`default_location`=1");

        return view('employee_file.summary_employee_file_list')
            ->with('default_user_location',  $default_user_location) ;
    }



    public function view_details($id)
    {
        $employee_data=DB::SELECT("SELECT a.id,
                                concat(a.employee_name,' | ',b.employee_code,' | ',d.depertment_name,' | ',e.designation_name) as employee_name
                                from  hrm_employee a
                                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id AND  a.active_status=1 AND b.employee_activity=1 AND a.id=$id
                                JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                                Join hrm_designation e On b.hrm_designation_id=e.id");

        $file_type = DB::SELECT("SELECT id,file_type_name FROM hrm_file_type");

        return view('employee_file.employee_file_list')
            ->with('employee_data',  $employee_data)
            ->with('file_type',  $file_type) ;
    }

    public function create()
    {
        return view('employee_file.create_employee_file');
    }

    public function employeewisefile_list(Request $request)
    {
        $employee_id     = $request->employee_id;
        $file_type       = $request->file_type;
        $date_from       = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
        $date_to         = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));

        $condition       ="";

        if ($file_type!=0){
            $condition       =" AND f.id=$file_type";
        }

        if ($employee_id!=0){
            $condition       =$condition." AND a.id=$employee_id";
        }

        if (isset($request->date_range)){
            $condition  =  $condition." AND c.attached_date between '$date_from' AND '$date_to'" ;
        }

        $user_id = auth()->id();

        $datalist=DB::SELECT("SELECT a.id,
                                concat(a.employee_name,' | ',b.employee_code) as employee_name,
                                LPAD(a.id, 5, '0') as unique_code,
                                d.depertment_name,
                                e.designation_name,
                                c.file_title,
                                c.id as hrm_employee_file_id,
                                c.note,
                                c.attached_date,
                                c.images as file_images,
                                f.file_type_name,
                                a.Images
                                from  hrm_employee a
                                JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id AND  b.id in (SELECT max(id) from hrm_employee_job_info
                                WHERE hrm_employee_id= $employee_id)
                                JOIN hrm_employee_file c ON b.hrm_employee_id=c.hrm_employee_id
                                JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                                Join hrm_designation e On b.hrm_designation_id=e.id
                                JOIN hrm_file_type f ON c.hrm_file_type_id=f.id
                                JOIN user_location g ON b.hrm_location_id = g.hrm_location_id AND g.users_id = $user_id
                                   $condition ");

        return json_encode(array('data' =>$datalist));
    }

    public function summary_employeewisefile_list(Request $request)
    {
        $condition = "";

        if ($request->location) {
            $condition .= " AND b.hrm_location_id = $request->location";
        }

        if ($designation_id = $request->designation_id) {
            $condition .= " AND b.hrm_designation_id = {$designation_id}";
        }

        if ($department_id = $request->department_id) {
            $condition .= " AND b.hrm_depertment_id = {$department_id}";
        }

        if ($category_id = $request->category_id) {
            $condition .= " AND b.hrm_category_id = {$category_id}";
        }

        if ($section_id = $request->section_id) {
            $condition .= " AND b.hrm_section_id = {$section_id}";
        }

        if ($employee_type_id = $request->employee_type_id) {
            $condition .= " AND b.hrm_employment_status_id = {$employee_type_id}";
        }

        $user_id = auth()->id();

        $datalist=DB::SELECT("SELECT
                a.id,
                LPAD(a.id, 5, '0') unique_code,
                CONCAT(a.employee_name,' | ',b.employee_code) employee_name,
                a.Images,
                d.depertment_name,
                e.designation_name,
                e.priority,
                COUNT(f.id) count_id,
                GROUP_CONCAT(DISTINCT f.file_type_name ORDER BY f.file_type_name SEPARATOR '  ;  ') file_type_name
            FROM
                hrm_employee a
                    JOIN
                hrm_employee_job_info b ON a.id = b.hrm_employee_id
                    AND a.active_status = 1
                    AND b.employee_activity = 1
                    $condition
                    JOIN
                hrm_employee_file c ON b.hrm_employee_id = c.hrm_employee_id
                    JOIN
                hrm_depertment d ON b.hrm_depertment_id = d.id
                    JOIN
                hrm_designation e ON b.hrm_designation_id = e.id
                    JOIN
                hrm_file_type f ON c.hrm_file_type_id = f.id
                    JOIN
                user_location g ON b.hrm_location_id = g.hrm_location_id
                    AND g.users_id = $user_id
            GROUP BY a.id , a.employee_name , d.depertment_name , e.designation_name,a.Images,b.employee_code,e.priority
        ");

        return json_encode(array('data' =>$datalist));
    }



    public function filetypecreate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'file_type_name'         => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('document_archive')
                        ->withErrors($validator)
                        ->withInput();
        }

        $insert     = new HrmFileType;
        $insert->file_type_name      = $request->file_type_name;
        $insert->save();

       return back()->with('success','Successfully New File Type Added.');


    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_name'     => 'required',
            'file_type'         => 'required',
            'file_title'      => 'required',
            'image'             => 'required|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $fileName=null;
        $ext = $request->file('image')->getClientOriginalExtension();

        if ($request->hasFile('image')) {
            $path      = public_path() . '/employee_file/';
            $name      = $this->generateRandomString();
            $fileName  = $name.'.'.$ext;


            if ($request->file('image')->move($path,$fileName)) {
            } else {
            }
        }

        $attached_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->attached_date)));

        $insert     = new HrmEmployeeFile;

        $insert->attached_date         = $attached_date;
        $insert->hrm_employee_id       = $request->employee_name;
        $insert->hrm_file_type_id      = $request->file_type;
        $insert->file_title            = $request->file_title;
        $insert->note                  = $request->note;
        $insert->images                = $fileName;
        $insert->save();


        $this->recordActivity(
            1,
           'Created Employee Document Archieve',
            $insert,
            $insert->id,
            'hrm_employee_file'
        );


        if($request->status==1){
                $request->session()->flash('alert-success', 'data has been successfully added!');
                return redirect()->to('document_archive');
        }else{
                return back()->with('success','Successfully Insert');
        }
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function download($id)
    {
        $query = HrmEmployeeFile::where('id', $id)->first();
        $data = $query->images;

        if (!$query || empty($query->images)) {
            return back()->with('error', 'No File Found');
        }

        $path = public_path() . '/employee_file/'.$data;
        return response()->download($path);
    }



    public function view($id)
    {
        $file       = HrmEmployeeFile::findOrFail($id);
        // $path       = public_path() . '/employee_file/'.$file->images;
        $ext        = strtolower(File::extension($file->images));
        $image_path = url()->to('/').'/employee_file/'.$file->images;
        $output     = "";
            // $image_path = 'http://182.160.123.163/hrm/employee_file/'.$file->images;
            // $output = '<img style="width: 100%; height: 100%; max-width: 700px; max-height: 650px;"   src="'.asset($image_path).'"/>';
            // return Response($output);
        if (empty($file->images)) {
            $output = '<p class="text-danger">No File Found</p>';
            return response($output);
        }
        if ($ext == 'pdf') {
            $output = '<iframe id="form-iframe" src="'.$image_path.'" height="600" width="580" border:none; frameborder="0" allowfullscreen"></iframe>';
        }
        elseif ($ext=='doc') {
            $output = '<iframe id="form-iframe" src="'.$image_path.'" height="500" width="1300" border:none; frameborder="0" allowfullscreen"></iframe>';
        }
        elseif ($ext=='docx') {
            $output = '<iframe id="form-iframe" src="'.$image_path.'" height="500" width="1300" border:none; frameborder="0" allowfullscreen"></iframe>';
        }
        elseif ($ext=='xls') {
            $output = '<iframe id="form-iframe" src="'.$image_path.'" height="500" width="1300" border:none; frameborder="0" allowfullscreen"></iframe>';
        }
        elseif ($ext=='xlsx') {
                $output = '<iframe id="form-iframe" src="'.$image_path.'" height="500" width="1300" border:none; frameborder="0" allowfullscreen"></iframe>';
        }
        elseif ($ext=='txt') {
            $output = '<iframe id="form-iframe" src="'.$image_path.'" height="500" width="1300" border:none; frameborder="0" allowfullscreen"></iframe>';
        }
        elseif ($ext=='jpg') {
            $output = '<img style="width: 100%; height: 100%; max-width: 700px; max-height: 650px; "   src="'.asset($image_path).'"/>';
        }
        elseif ($ext=='jpeg') {
            $output = '<img style="width: 100%; height: 100%; max-width: 700px; max-height: 650px;"   src="'.asset($image_path).'"/>';
        }
        elseif ($ext=='png') {
            $output = '<img style="width: 100%; height: 100%; max-width: 700px; max-height: 650px;"   src="'.asset($image_path).'"/>';
        }
        elseif ($ext=='gif') {
            $output = '<img style="width: 100%; height: 100%; max-width: 700px; max-height: 650px;"   src="'.asset($image_path).'"/>';
        }

        return Response($output);
    }

    public function edit($id)
    {
       $data = DB::SELECT("SELECT a.id,
                        concat(a.employee_name,' | ',b.employee_code) as employee_name,
                        d.depertment_name,
                        e.designation_name,
                        c.file_title,
                        c.id as hrm_employee_file_id,
                        c.note,
                        c.attached_date,
                        c.images as file_images,
                        f.file_type_name,
                        f.id as file_type_id,
                        a.Images
                        from  hrm_employee a
                        JOIN hrm_employee_job_info b on a.id=b.hrm_employee_id
                        JOIN hrm_employee_file c ON b.hrm_employee_id=c.hrm_employee_id  AND c.id = $id
                        JOIN hrm_depertment d On b.hrm_depertment_id=d.id
                        Join hrm_designation e On b.hrm_designation_id=e.id
                        JOIN hrm_file_type f ON c.hrm_file_type_id=f.id
                        ");

        return view('employee_file.edit_employee_file')
            ->with('data',$data);
    }

    public function edit_document_archive(Request $request)
    {
        $cancel = HrmEmployeeFile::find($request->hrm_employee_file_id);
        if (empty($cancel)){
            session()->flash('alert-danger', 'Invalid depertment !!');
            return Redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'employee_name'          => 'required',
            'file_type'              => 'required',
            'file_title'             => 'required',
            'hrm_employee_file_id'   => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error','Failed!! Please check all field.');
        }

        $attached_date  = date('Y-m-d', strtotime(str_replace('/', '-', $request->attached_date)));

        $update = HrmEmployeeFile::find($request->hrm_employee_file_id);
        $update->attached_date         = $attached_date;
        $update->hrm_employee_id       = $request->employee_name;
        $update->hrm_file_type_id      = $request->file_type;
        $update->file_title            = $request->file_title;
        $update->note                  = $request->note;
        $update->save();

        $request->session()->flash('alert-success', 'successfully updated !');
        $link = 'document_archive/'.$request->employee_name.'/view_details' ;

        return redirect()->to($link);
    }

    public function cancel(Request $request,$id)
    {
        DB::table('hrm_employee_file')->where('id', '=', $id)->delete();

        return back()->with('success','Successfully Insert');
    }

}
