<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Admission\application_assessment;
use App\Models\Admission\application_credentials;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicantProfile;
use App\Models\ApplicationAssessment;
use App\Models\Setting;
use App\Models\Programme;
use App\Models\Department;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Jobs\AdmissionStatusMailJob;



class AdmissionOfficer extends Controller
{


    static function settings($request){
        if ($request->has('session') && $request->has('semester')){
            $settings = Setting::where('semester_name', $request->semester)->where('session_name',$request->session)->first();
            return $settings;
        }
        $settings = Setting::where('status', 'active')->first();
        return $settings; 
    }

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
        $validator = Validator::make($request->all(), [
            'applicant' => 'required',
            'applicationId' => 'required',
            'programmeId' => 'required',
            'admsStatus' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'all fields are required!'], 401);
        }
        try {
            $getProgramme = Programme::find($request->programmeId);
            $dept = $this->get_dept_given_prog($getProgramme->department_id);
            $college = $this->get_faculty_given_dept($dept->college_id);
            $applicant = Applicant::find($request->applicant);
            $profile = ApplicantProfile::where('applicant_id',$request->applicant)->first();
            if (isset($getProgramme) && $applicant) {
                if ($request->admsStatus == 'approved') {
                    $update = ApplicationAssessment::where('application_id', $request->applicationId)->first();
                    $update->approved_programme_id = $request->programmeId;
                    $update->save();
                    $application = Application::find($request->applicationId);
                    $application->status = 'approved';
                    $application->save();
                    $emailParams = ['address'=>$profile->contact_address,
                    'email'=>$applicant->email, 'status'=>$application->status,
                     'title'=>$profile->title, 'surname'=>$applicant->surname,'firstname'=>$applicant->firstname,
                    'session'=>$this->settings($request)->session_name,
                    'semester'=>$this->settings($request)->semester_name,
                    'programme'=>$getProgramme->programme,'progCode'=>$getProgramme->code,
                    'applicant_id'=>$applicant->id, 'date_admitted'=> date("F d, Y"),
                    'apply_for'=>$update->apply_for,'dept'=>$dept->department,'college'=>$college->college ];
                    //return view('emails.admissionApproved', ['emailParams'=>$emailParams]);
                     $adms_job = (new AdmissionStatusMailJob($emailParams))->delay(Carbon::now()->addSeconds(2));
                     dispatch($adms_job);
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


    static function get_dept_given_prog($prog_id){
        try {
            $dept = Department::find($prog_id);
            return $dept;
        } catch (\Throwable $th) {
            
            return response()->json(['error' => 'Error getting department given programme ID', 'th' => $th], 401);
        }
    }
    static function get_faculty_given_dept($faculty_id){
        try {
            $faculty = College::find($faculty_id);
            return $faculty;
        } catch (\Throwable $th) {
            
            return response()->json(['error' => 'Error getting faculty given department ID', 'th' => $th], 401);
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
