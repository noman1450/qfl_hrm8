<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\HRMCVCompensation;
use App\Models\HRMCVJobDescriptionGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CVJobDescriptionGroupSetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('cv-job-description.jobdescription_setup_list');
    }

    public function jobDecriptionList()
    {
        $jobDescriptions = HRMCVJobDescriptionGroup::query()->select(['*'])->get();

        $jobDescriptions = collect($jobDescriptions);

        return datatables()->of($jobDescriptions)->make(true);
    }

    public function jobDescriptionGroups(Request $request)
    {
        $groups = HRMCVJobDescriptionGroup::query()->selectRaw('id, job_description_group_name as text')
                ->where('job_description_group_name', 'like', "%{$request->term}%")
                ->latest('id')->get();

        return response()->json($groups);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_description_group_name' => 'required|string|max:85|unique:hrm_cv_job_description_group,job_description_group_name',
        ]);

        if($validator->fails()) {
            return response()->json(array(
                'success'   => false,
                'message'  => 'Failure, validation fail!'
            ));
        }

        $jobDescription = HRMCVJobDescriptionGroup::find($request->job_description_id);

        if ($jobDescription == null){
            $jobDescription = new HRMCVJobDescriptionGroup;
        }

        $jobDescription->job_description_group_name = $request->job_description_group_name;
        $jobDescription->save();


        $this->recordActivity(
             1,
             $request->job_description_id ? 'Updated Cv Job Description Group Name' : 'Created Cv Job Description Group Name',
             $request->job_description_id ? $jobDescription->getChanges() : $jobDescription,
             $jobDescription->id,
             'hrm_cv_job_description_group'
        );


        return Response::json(array(
            'success'   => true,
            'message'   => 'Success',
        ));
    }

    public function compensationStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'compensation_name' => 'required|string|max:75|unique:hrm_cv_compensation,compensation_name',
        ]);

        if($validator->fails()) {
            return Response::json([
                'success'   => false,
                'message'  => 'Failure, validation fail!'
            ]);
        }

        $compensation = HRMCVCompensation::find($request->compensation_id);

        if ($compensation == null){
            $compensation = new HRMCVCompensation;
        }

        $compensation->compensation_name = $request->compensation_name;
        $compensation->users_id = auth()->id();
        $compensation->save();

        $this->recordActivity(
             1,
             $request->compensation_id ? 'Updated CV Compansation' : 'Created CV Compansation',
             $request->compensation_id ? $compensation->getChanges() : $compensation,
             $compensation->id,
             'hrm_cv_compensation'
        );


        return Response::json([
            'success'   => true,
            'message'   => 'Success',
        ]);
    }

    public function compensationListData()
    {
        $compensations = HRMCVCompensation::query()->select(['*'])->get();

        $compensations = collect($compensations);

        return datatables()->of($compensations)->make(true);
    }
}
