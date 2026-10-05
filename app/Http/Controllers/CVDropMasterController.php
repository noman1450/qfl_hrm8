<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\HRMCVDropMaster;
use App\Models\HRMCVJobRequsition;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\HRMCVDropDetail;
use App\Models\HRMCVJobRequsitionDetail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Redirect;
use Response;

class CVDropMasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $ldate = date('Y-m-d');
        $requsitions = HRMCVJobRequsition::whereDate('ending_date','>=',$ldate)->where('is_active','=',1)->where('status','=', 3)->get();
        $companyInfo = DB::SELECT("SELECT * FROM `company_information` LIMIT 1");
        
        return view('career-opportunity.index', compact('requsitions','companyInfo'));
    }

    public function applyForm($id)
    {
        // $data['requsition'] = HRMCVJobRequsition::query()->findOrFail($id);

        // $data['requsitionDetails'] = HRMCVJobRequsitionDetail::query()
        //     ->where('hrm_cv_job_requsition_id', $data['requsition']->id)
        //     ->where('is_active', 1)
        //     ->get();

        // $data['notes'] = HRMCVDropDetail::query()->select('note')->get();

        $requsitionDetails = DB::SELECT("SELECT
                                            a.id,c.job_description_group_name,b.job_description,a.hrm_cv_job_requsition_id
                                        FROM
                                            hrm_cv_job_requsition_details a
                                                JOIN
                                            hrm_cv_job_description b ON a.hrm_cv_job_description_id = b.id
                                                AND a.is_active = 1 AND a.hrm_cv_job_requsition_id = $id
                                                AND b.allow_points_calculation =1
                                                JOIN
                                            hrm_cv_job_description_group c ON b.hrm_cv_job_description_group_id = c.id
                                        Order By c.job_description_group_name");

        $reserve = 'blank';



        return view('career-opportunity.apply-form')->with('requsitionDetails',$requsitionDetails)->with('reserve',$reserve);
    }



    public function submitApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_name' => 'required|string|max:75',
            'applicant_contact_no' => 'required|string|max:45',
            'applicant_email' => 'required|email|string|max:45',
            'present_address' => 'required|string|max:100',
            'hrm_cv_job_description_id.*' => 'required',
            'note.*' => 'nullable',
            'attachment' => 'required|mimes:pdf',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        $check = DB::SELECT("SELECT id FROM hrm_cv_drop_master WHERE applicant_email='$request->applicant_email' AND hrm_cv_job_requsition_id=$request->hrm_cv_job_requsition_id");

        if(!empty($check)){
                return response::json(array(
                    'success'   => false,
                    'messages'  => 'Your CV is Already Submitted.!'
                ));

        }


        DB::transaction(function () use ($request) {
            $file = $request->file('attachment');

            $path  = public_path().'/cv-bank/cv/';
            $ext = $file->getClientOriginalExtension();
            $hash = Str::random(10).'_'.time();
            $fileName = "cv-{$hash}.{$ext}";
            $file->move($path, $fileName);
            $fileUrl = '/cv-bank/cv/'.$fileName;

            $dropMaster = new HRMCVDropMaster;
            $dropMaster->applicant_name             = $request->applicant_name;
            $dropMaster->applicant_contact_no       = $request->applicant_contact_no;
            $dropMaster->applicant_email            = $request->applicant_email;
            $dropMaster->present_address            = $request->present_address;
            $dropMaster->attachment                 = $fileUrl;
            $dropMaster->hrm_cv_job_requsition_id   = $request->hrm_cv_job_requsition_id;
            $dropMaster->is_active                  = 1;
            $dropMaster->status                     = 1;
            $dropMaster->save();

            foreach ($request->hrm_cv_job_requsition_details_id as $key => $value) {
                $dropDetail = new HRMCVDropDetail;
                $dropDetail->hrm_cv_drop_master_id = $dropMaster->id;
                $dropDetail->note = $request->note[$key];
                $dropDetail->hrm_cv_job_requsition_details_id = $request->hrm_cv_job_requsition_details_id[$key];
                $dropDetail->is_active = 1;
                $dropDetail->save();
            }
        });

        return response::json(array(
            'success'   => true,
            'messages'  => 'Thank You ! Succesfully Your CV Submitted !'
        ));


        // return back();
        // $request->session()->flash('alert-success', 'Succesfully Submitted Your CV !');
        // return Redirect::to('career-opportunities');
    }

    public function cvDropList()
    {
        return view('career-opportunity.cv-drop-list');
    }

    public function cvDatatableList(Request $request)
    {



        $condition = '';
        $hs_condition = '';

        if(!empty($request->position_id)){
            $condition = 'AND e.id ='.$request->position_id;
        }

        if(!empty($request->points)){
            $hs_condition =' Having SUM(c.point)'.$request->point_type.$request->points;
        }


        // $cvLists = HRMCVDropMaster::query()
        //     ->select([
        //         'id', 'applicant_name', 'applicant_contact_no', 'applicant_email',
        //         'present_address', 'is_active', 'status'
        //     ])->get();



        $cvLists = DB::SELECT("SELECT
                                    a.id,
                                    a.applicant_name,
                                    a.applicant_contact_no,
                                    a.applicant_email,
                                    a.present_address,
                                    a.attachment,
                                    DATE(a.created_at) cv_submission_date,
                                    CONCAT(e.published_date,' / ',
                                    e.ending_date) as dateline,
                                    SUM(c.point) AS points,
                                    CONCAT(f.designation_name,
                                            ' (',
                                            g.depertment_name,
                                            ')') AS position,
                                    GROUP_CONCAT(CONCAT(d.job_description,'  ->Point = ',c.point)
                                        SEPARATOR '<br>') AS job_description,
                                    GROUP_CONCAT(CONCAT(b.note)
                                        SEPARATOR '<br>') AS note
                                FROM
                                    hrm_cv_drop_master a
                                        JOIN
                                    hrm_cv_drop_details b ON a.id = b.hrm_cv_drop_master_id
                                        AND a.is_active = 1 AND a.status = $request->status
                                        JOIN
                                    hrm_cv_job_requsition_details c ON b.hrm_cv_job_requsition_details_id = c.id
                                        JOIN
                                    hrm_cv_job_description d ON c.hrm_cv_job_description_id = d.id
                                        JOIN
                                    hrm_cv_job_requsition e ON c.hrm_cv_job_requsition_id = e.id
                                    $condition
                                        JOIN
                                    hrm_designation f ON e.hrm_designation_id = f.id
                                        JOIN
                                    hrm_depertment g ON e.hrm_depertment_id = g.id
                                GROUP BY a.id , a.applicant_name , a.applicant_contact_no , a.applicant_email , a.present_address , a.attachment , f.designation_name , g.depertment_name , e.published_date , e.ending_date , a.created_at
                                $hs_condition
                                    ");

        $cvLists = collect($cvLists);


        return datatables()->of($cvLists)
            ->addColumn('Link', function($cvLists) {
                return '
                <div class="btn-group">
                    <a href="'. route('applicant.cv', encrypt($cvLists->id)) .'" class="btn btn-success btn-xs modalLink">
                        <span class="glyphicon glyphicon-eye-open"></span> View CV
                    </a>
                </div>
               <div class="btn-group">
                    <a onclick="return confirm(\'Do you want to Switch this Interview?\');" href="'. route('applicant.cvinterview', encrypt($cvLists->id)) .'" class="btn btn-warning btn-xs">
                        <span class="glyphicon glyphicon-ok"></span> Interview
                    </a>
                </div>

               <div class="btn-group">
                    <a onclick="return confirm(\'Do you want to Switch this Reserve?\');" href="'. route('applicant.cvreserve', encrypt($cvLists->id)) .'" class="btn btn-info btn-xs">
                        <span class="glyphicon glyphicon-share-alt"></span> Reserve
                    </a>
                </div>

               <div class="btn-group">
                    <a onclick="return confirm(\'Do you want to Finally Delete?\');"  href="'. route('applicant.cvdestroy', encrypt($cvLists->id)) .'" class="btn btn-danger btn-xs">
                        <span class="glyphicon glyphicon-trash"></span> Delete
                    </a>
                </div>

                ';
            })
            ->rawColumns(['Link','job_description','note'])
            ->make(true);
    }

    public function show($id)
    {
        $applicant = HRMCVDropMaster::query()->findOrFail(decrypt($id));
        // dd($applicant);
        return view('career-opportunity.show', compact('applicant'));
    }

    public function destroy(Request $request,$id)
    {
        $id = decrypt($id);
        $applicant = HRMCVDropMaster::query()->findOrFail($id);

        if(!empty($applicant)){

            if ($applicant->attachment != null){

                $file_delete =  public_path() ."$applicant->attachment";
                 if (file_exists($file_delete)) {unlink($file_delete);}
            }

            DB::UPDATE("UPDATE hrm_cv_drop_master SET is_active=0,status=0 WHERE id=$id");

            $request->session()->flash('alert-success', 'Succesfully Deleted !');
            return Redirect::to('cv-drop-list');

        };
    }

    public function cvinterview(Request $request,$id)
    {
        $id = decrypt($id);
        $applicant = HRMCVDropMaster::query()->findOrFail($id);

        if(!empty($applicant)){

            DB::UPDATE("UPDATE hrm_cv_drop_master SET status=2 WHERE id=$id");

            $request->session()->flash('alert-success', 'Succesfully Switch To Interview !');
            return Redirect::to('cv-drop-list');

        };
    }


    public function cvreserve(Request $request,$id)
    {
        $id = decrypt($id);
        $applicant = HRMCVDropMaster::query()->findOrFail($id);

        if(!empty($applicant)){

            DB::UPDATE("UPDATE hrm_cv_drop_master SET status=3 WHERE id=$id");

            $request->session()->flash('alert-success', 'Succesfully Switch To Reserve  !');
            return Redirect::to('cv-drop-list');

        };
    }




}
