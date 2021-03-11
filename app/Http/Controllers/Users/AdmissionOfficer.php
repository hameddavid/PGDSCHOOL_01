<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Admission\application_assessment;
use App\Models\Admission\application_credentials;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdmissionOfficer extends Controller
{
    public function getApplicants(Request $request)
    {
        try {
            $applicants = Applicant::latest()->get();
            foreach ($applicants as $key => $value) {
                $applications = Application::where('applicant_id', $value->id)->get();
                $applicants[$key]['applications'] = $applications;
            }
            return response()->json(['msg' => 'success', 'applicants' => $applicants]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'Unable to fetch applicants', 'th' => $th], 401);
        }
    }
    public function getApplicationsDetails(Request $request)
    {
    }
    public function getApplications(Request $request)
    {
        $applications = null;
        if ($request->status == 'all') {
            $applications = Application::latest()->get();
        } else {
            $applications = Application::where('status', $request->status)->latest()->get();
        }

        try {
            foreach ($applications as $key => $value) {
                $applicant = Applicant::find($value->applicant_id)->setHidden(['password', 'token']);
                $applications[$key]['applicant'] = $applicant;
                $applications[$key]['assessment'] = application_assessment::where('application_id', $value->id)->first();
                $applications[$key]['applyFor'] = $applications[$key]['assessment']['apply_for'];
                $applications[$key]['programme'] = $this->programmeName($request, $applications[$key]['assessment']['programme_id']);
                $applications[$key]['approvedProgramme'] = $this->programmeName($request, $applications[$key]['assessment']['approved_programme_id']);
            }
            return response()->json(['msg' => 'success', 'applications' => $applications]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'Unable to fetch applications', 'th' => $th], 401);
        }
    }
    public function programmeName(Request $request, $id)
    {
        $programme = Programme::where('id', $id)->first();
        return $programme['programme'];
    }
    public function getForms(Request $request)
    {
        try {
            $application = Application::find($request->applicationId);
            $personalData = DB::table('application_personaldata')->where('applicant_id', $application->applicant_id)->first();
            $credentials = application_credentials::where('application_id', $request->applicationId)->first();
            $assessment = application_assessment::where('application_id', $request->applicationId)->first();
            $programme = Programme::find($assessment->programme_id);
            $assessment['programme'] = $programme->programme;
            $reference = DB::table('application_refree')->where('application_id', $request->applicationId)->get();
            $institutionHistory = DB::table('application_institution')->where('application_id', $request->applicationId)->get();
            $employmentHistory  = DB::table('application_employmenthistory')->where('application_id', $request->applicationId)->get();
            return response()->json([
                'personalData' => $personalData, 'credentials' => $credentials,
                'assessment' => $assessment, 'reference' => $reference, 'institutionHistory' => $institutionHistory,
                'employmentHistory' => $employmentHistory
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'Unable to get forms', 'th' => $th], 401);
        }
    }

    public function admissionApproved(Request $request)
    {
        // return response($request);
        //Check for role and permission
        $validator = Validator::make($request->all(), [
            'applicant' => 'required',
            'applicationId' => 'required',
            'programmeId' => 'required',
            'admsStatus' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'all fields are required!'], 401);
        }
        //Use try/catch for application_assessment
        try {
            $getProgramme = Programme::find($request->programmeId);
            $applicant = Applicant::find($request->applicant);
            if (isset($getProgramme) && $applicant) {
                if ($request->admsStatus == 'approved') {
                    $update = DB::table('application_assessment')->where('application_id', $request->applicationId)->update([
                        'approved_programme_id' => $request->programmeId,
                    ]);
                    $application = Application::find($request->applicationId);
                    $application->status = 'approved';
                    $application->save();
                    //send admission success email to student here
                    return response()->json(['info' => "Application Appproved", 'value' => "Application Appproved", 'msg' => "success"]);
                } else {
                    //send admission decline email to student here
                }
            } else {
                return response()->json(['error' => 'Like there is no programme for this ID'], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error updating programme', 'th' => $th], 401);
        }
    }

    public function admissionDenied(Request $request)
    {
        try {
            $applicant = Applicant::find($request->applicant);
            $application = Application::find($request->applicationId);
            $application->status = $request->admsStatus;
            $application->deny_reason = $request->denyReason;
            $application->save();
            return response()->json(['info' => "Application Denied", 'value' => "Application denied", 'msg' => "success"]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'Error updating programme', 'th' => $th], 401);
        }
    }

    public function getProgrammeForApprove(Request $request)
    {
        $assessment = application_assessment::where('application_id', $request->applicationId)->first();
        $programme = Programme::find($assessment->programme_id);
        return response()->json(['programme' => $programme]);
        // $programme =
    }
}
