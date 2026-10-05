<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\HRMCVJobRequsition;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\HRMCVJobRequsitionCompensation;
use App\Models\HRMCVJobRequsitionDetail;
use Exception;
use Illuminate\Support\Facades\Validator;

class CVJobRequsitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('cv-job-requsition.index');
    }

    public function datatableList()
    {

        $jobDescriptions = DB::table('hrm_cv_job_requsition')
            ->join('hrm_depertment', 'hrm_cv_job_requsition.hrm_depertment_id', '=', 'hrm_depertment.id')
            ->join('hrm_designation', 'hrm_cv_job_requsition.hrm_designation_id', '=', 'hrm_designation.id')
            ->select('hrm_cv_job_requsition.*', 'hrm_designation.designation_name', 'hrm_depertment.depertment_name')
            ->get();

        return datatables()->of($jobDescriptions)
            ->addColumn('Position', function ($jobDescriptions) {
                return "{$jobDescriptions->designation_name} ({$jobDescriptions->depertment_name})";
            })
            ->addColumn('Status', function ($jobDescriptions) {
                $status = '';

                if ($jobDescriptions->status === 1) {
                    $status = "<span class='badge' style='background-color:#f7913c'>Pending..</span>";
                } elseif ($jobDescriptions->status === 2) {
                    $status = "<span class='badge' style='background-color:#708bef'>Approved</span>";
                } elseif ($jobDescriptions->status === 3) {
                    $status = "<span class='badge' style='background-color:black'>Published</span>";
                } elseif ($jobDescriptions->status === 0) {
                    $status = "<span class='badge' style='background-color:red'>Rejected</span>";
                }

                return $status;
            })
            ->addColumn('Link', function($jobDescriptions) {
                if ($jobDescriptions->status == 1) {
                    $buttons = '
                    <div class="btn-group">
                        <a title="Edit" href="'. route('cv-job-requsitions.edit', encrypt($jobDescriptions->id)) .'" class="btn btn-info btn-sm">
                            Edit
                        </a>'.
                        '<a title="View Deatils" href="'.route('cv-job-requsitions.show', encrypt($jobDescriptions->id)).'" class="btn btn-success btn-sm modalLink">
                            View
                        </a>
                    </div>';
                } else {
                    $publishUnpublished = $jobDescriptions->status == 2 ? 'Publish' : 'Unpublish';

                    $buttons = '
                    <div class="btn-group">
                        <a title="View Deatils" href="'.route('cv-job-requsitions.show', encrypt($jobDescriptions->id)).'" class="btn btn-success btn-sm modalLink">
                            View
                        </a>'.
                        '<a href="'.route('cv-job-requsitions.publish-unpublish', encrypt($jobDescriptions->id)).'" class="btn btn-primary btn-sm">
                            '."{$publishUnpublished}".'
                        </a>
                    </div>';
                }

                return $buttons;
            })
            ->rawColumns(['Link', 'Status'])
            ->make(true);
    }

    public function publishUnpublish($id)
    {
        $requsision = HRMCVJobRequsition::query()->findOrFail(decrypt($id));

        if ($requsision->status == 2) {
            $requsision->update(['status' => 3]);

            $this->recordActivity(
                 1,
                 'Published Job Requisition',
                 null,
                 decrypt($id),
                 'hrm_cv_job_requsition'
            );

        } elseif ($requsision->status == 3) {
            $requsision->update(['status' => 2]);

            $this->recordActivity(
                 1,
                 'Unpublished Job Requisition',
                 null,
                 decrypt($id),
                 'hrm_cv_job_requsition'
            );

        }

        return back();
    }

    public function create()
    {
        return view('cv-job-requsition.create');
    }

    public function store(Request $request)
    {

        $status = false;
        $validator = Validator::make($request->all(), [
            'hrm_depertment_id' => 'required',
            'hrm_designation_id' => 'required',
            'job_title' => 'required|string',
            'vacancy' => 'required|string',

            'job_context' => 'required|string',
            'employment_status' => 'required|string',
            'job_level' => 'required|string',
            'work_place' => 'required|string',
            'job_location' => 'required|string',
            'lunch_facilities' => 'required|string',
            'salary_review' => 'required|string',
            'festival_bonus' => 'required|string',
            'gender' => 'required|string',

            'published_date' => 'required|date',
            'ending_date' => 'required|date',
            'salary_range' => 'required|string',
            'age_range' => 'required|string',
            'is_active' => 'required',

            'hrm_cv_compensation_id.*' => 'nullable',

            'hrm_cv_job_description_id.*' => 'required',
            'point.*' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            try {
                DB::transaction(function () use ($request) {

                    $requsition = new HRMCVJobRequsition;

                    $requsition->job_title          = $request->job_title;
                    $requsition->published_date     = date('Y-m-d', strtotime($request->published_date));
                    $requsition->ending_date        = date('Y-m-d', strtotime($request->ending_date));
                    $requsition->hrm_depertment_id  = $request->hrm_depertment_id;
                    $requsition->hrm_designation_id = $request->hrm_designation_id;

                    $requsition->job_context = $request->job_context;
                    $requsition->employment_status = $request->employment_status;
                    $requsition->job_level = $request->job_level;
                    $requsition->work_place = $request->work_place;
                    $requsition->job_location = $request->job_location;
                    $requsition->lunch_facilities = $request->lunch_facilities;
                    $requsition->salary_review = $request->salary_review;
                    $requsition->festival_bonus = $request->festival_bonus;
                    $requsition->gender = $request->gender;

                    $requsition->is_active          = 1;
                    $requsition->status             = 1;
                    $requsition->users_id           = auth()->id();
                    $requsition->salary_range       = $request->salary_range;
                    $requsition->age_range          = $request->age_range;
                    $requsition->vacancy            = $request->vacancy;
                    $requsition->save();

                    $this->recordActivity(
                        1,
                       'Created New Job Requsition',
                        $requsition,
                        $requsition->id,
                        'hrm_cv_job_requsition'
                    );


                    foreach ($request->hrm_cv_job_description_id as $key => $value) {
                        $requsitionDetail = new HRMCVJobRequsitionDetail;

                        $requsitionDetail->hrm_cv_job_description_id = $request->hrm_cv_job_description_id[$key];
                        $requsitionDetail->point                     = $request->point[$key];
                        $requsitionDetail->is_active                 = 1;
                        $requsitionDetail->hrm_cv_job_requsition_id  = $requsition->id;
                        $requsitionDetail->save();
                    }

                    if ($request->has('hrm_cv_compensation_id')) {
                        foreach ($request->hrm_cv_compensation_id as $comKey => $compensation) {
                            $requsitionCompensation = new HRMCVJobRequsitionCompensation;

                            $requsitionCompensation->hrm_cv_compensation_id     = $request->hrm_cv_compensation_id[$comKey];
                            $requsitionCompensation->hrm_cv_job_requsition_id   = $requsition->id;
                            $requsitionCompensation->save();
                        }
                    }
                });



                $status = true;
                $message = "Requisition has been created Successfully";
            } catch (Exception $e) {
                $message = "Requisition created failed";
            }
        }

        return [
            "status" => $status == true ? "Y" : "N",
            "message" => $message ?? '',
            "data" => $data ?? '',
            "error" => $error ?? ''
        ];
    }

    public function show($id)
    {
        $requsition = HRMCVJobRequsition::query()->findOrFail(decrypt($id));
        $requsitionDetails = HRMCVJobRequsitionDetail::query()->where('hrm_cv_job_requsition_id', $requsition->id)->get();
        $compensations = DB::table('hrm_cv_job_requsition_compensation as a')
            ->join('hrm_cv_compensation as b', 'a.hrm_cv_compensation_id', '=', 'b.id')
            ->where('a.hrm_cv_job_requsition_id', $requsition->id)
            ->orderBy('b.id')
            ->select('b.id', 'b.compensation_name')
            ->get();


        return view('cv-job-requsition.show', compact('requsition', 'requsitionDetails', 'compensations'));
    }

    public function edit($id)
    {
        $jobRequsition = HRMCVJobRequsition::query()->findOrFail(decrypt($id));

        $jobRequsitionDetails = HRMCVJobRequsitionDetail::query()
            ->where('hrm_cv_job_requsition_id', $jobRequsition->id)
            ->get();

        $compensations = DB::table('hrm_cv_job_requsition_compensation as a')
            ->join('hrm_cv_compensation as b', 'a.hrm_cv_compensation_id', '=', 'b.id')
            ->where('a.hrm_cv_job_requsition_id', $jobRequsition->id)
            ->orderBy('b.id')
            ->select('b.id', 'b.compensation_name')
            ->get();

        return view('cv-job-requsition.edit', compact('jobRequsition', 'jobRequsitionDetails', 'compensations'));
    }

    public function update(Request $request, $id)
    {
        $requsition = HRMCVJobRequsition::query()->findOrFail($id);

        $status = false;
        $validator = Validator::make($request->all(), [
            'hrm_depertment_id' => 'required',
            'hrm_designation_id' => 'required',
            'job_title' => 'required|string',
            'vacancy' => 'required|string',
            'job_context' => 'required|string',
            'employment_status' => 'required|string',
            'job_level' => 'required|string',
            'work_place' => 'required|string',
            'job_location' => 'required|string',
            'lunch_facilities' => 'required|string',
            'salary_review' => 'required|string',
            'festival_bonus' => 'required|string',
            'gender' => 'required|string',
            'published_date' => 'required|date',
            'ending_date' => 'required|date',
            'salary_range' => 'required|string',
            'age_range' => 'required|string',
            'is_active' => 'required',
            'hrm_cv_compensation_id.*' => 'nullable',
            'hrm_cv_job_description_id.*' => 'nullable',
            'point.*' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {

            DB::beginTransaction();

            try {
                // DB::transaction(function () use ($requsition, $request) {
                // DB::transaction(function (){

                    $requsition->job_title          = $request->job_title;
                    $requsition->published_date     = date('Y-m-d', strtotime($request->published_date));
                    $requsition->ending_date        = date('Y-m-d', strtotime($request->ending_date));
                    $requsition->hrm_depertment_id  = $request->hrm_depertment_id;
                    $requsition->hrm_designation_id = $request->hrm_designation_id;
                    $requsition->job_context        = $request->job_context;
                    $requsition->employment_status  = $request->employment_status;
                    $requsition->job_level          = $request->job_level;
                    $requsition->work_place         = $request->work_place;
                    $requsition->job_location       = $request->job_location;
                    $requsition->lunch_facilities   = $request->lunch_facilities;
                    $requsition->salary_review      = $request->salary_review;
                    $requsition->festival_bonus     = $request->festival_bonus;
                    $requsition->gender             = $request->gender;
                    $requsition->is_active          = 1;
                    $requsition->status             = 1;
                    $requsition->users_id           = auth()->id();
                    $requsition->salary_range       = $request->salary_range;
                    $requsition->age_range          = $request->age_range;
                    $requsition->vacancy            = $request->vacancy;
                    $requsition->save();


                    // DB::table('hrm_cv_job_requsition_details')->where('hrm_cv_job_requsition_id',$id)->delete();

                  // dd($hello);
                  $hello=  DB::DELETE("DELETE FROM hrm_cv_job_requsition_details WHERE hrm_cv_job_requsition_id=$id");

                    if ($request->has('hrm_cv_job_description_id') && $request->has('point')) {

                        // $jobRequsitionDetails = HRMCVJobRequsitionDetail::query()->where('hrm_cv_job_requsition_id', $requsition->id)->get();
                        // if (! empty($jobRequsitionDetails)) {
                        //     $jobRequsitionDetails->each(function ($jobRequsitionDetail) {
                        //         $jobRequsitionDetail->delete();
                        //     });
                        // }
                        // dd("hello");
                        // dd(count($request->hrm_cv_job_description_id));

                        foreach ($request->hrm_cv_job_description_id as $key => $value) {
                            $requsitionDetail = new HRMCVJobRequsitionDetail;
                            $requsitionDetail->hrm_cv_job_description_id = $request->hrm_cv_job_description_id[$key];
                            $requsitionDetail->point                     = $request->point[$key];
                            $requsitionDetail->is_active                 = 1;
                            $requsitionDetail->hrm_cv_job_requsition_id  = $requsition->id;
                            $requsitionDetail->save();

                            /*$this->recordActivity(
                                 1,
                                 'Updated Job Requsition Deatils',
                                 $requsitionDetail,
                                 $requsitionDetail->id,
                                 'hrm_cv_job_requsition_details'
                            );*/

                        }
                    }

                    if ($request->has('hrm_cv_compensation_id')) {
                        HRMCVJobRequsitionCompensation::query()
                            ->where('hrm_cv_job_requsition_id', $requsition->id)
                            ->get()->each(function ($compensation) {
                                $compensation->delete();
                            });

                        foreach ($request->hrm_cv_compensation_id as $comKey => $compensation) {
                            $requsitionCompensation = new HRMCVJobRequsitionCompensation;

                            $requsitionCompensation->hrm_cv_compensation_id     = $request->hrm_cv_compensation_id[$comKey];
                            $requsitionCompensation->hrm_cv_job_requsition_id   = $requsition->id;
                            $requsitionCompensation->save();
                        }
                    }
                // });

                DB::commit();

                $this->recordActivity(
                     1,
                     'Updated Job Requsition',
                     $requsition->getChanges(),
                     $requsition->id,
                     'hrm_cv_job_requsition'
                );

                $status = true;
                $message = "Requisition has been updated Successfully";
            } catch (Exception $e) {
                $message = "Requisition updated failed";
            }
        }

        return [
            "status" => $status == true ? "Y" : "N",
            "message" => $message ?? '',
            "data" => $data ?? '',
            "error" => $error ?? ''
        ];
    }

    public function approvedReject(Request $request)
    {
        $requsition = HRMCVJobRequsition::find($request->req_id);

        if ($request->status == 2) {
            $requsition->update([
                'status' => $request->status,
                'approved_by_users_id' => auth()->id()
            ]);

            $this->recordActivity(
                 1,
                 'Approved Job Requisition',
                 null,
                 $request->req_id,
                 'hrm_cv_job_requsition'
            );

        } else {
            $requsition->update([
                'status' => $request->status,
                'approved_by_users_id' => auth()->id()
            ]);

            $this->recordActivity(
                 1,
                 'Rejected Job Requisition',
                 null,
                 $request->req_id,
                 'hrm_cv_job_requsition'
            );

        }

        return redirect()->route('cv-job-requsitions.index');
    }

    public function deptWiseJobDescription(Request $request)
    {
        $descriptions = DB::table('hrm_cv_job_description')
            ->where('hrm_depertment_id', $request->hrm_depertment_id)
            ->select(['job_description'])
            ->get();

        return datatables()->of($descriptions)->make(true);
    }
}
