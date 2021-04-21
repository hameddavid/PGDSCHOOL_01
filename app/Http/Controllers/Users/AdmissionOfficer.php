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
use App\Models\PGLecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Jobs\AdmissionStatusMailJob;
use App\Models\Notification;

class AdmissionOfficer extends Controller
{


    public static  function settings($request){
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
        $dept = null;
        $programmes = null;
        if($request->has('deptName') && $request->filled('deptName')){
            //no dept in pg school
            $dept = $this->get_dept_given_deptName($request->deptName);
            $programmes = $this->get_progs_given_deptID($dept->id);

        if ($request->status == 'all') {
          $applications = Application::select('applications.*')->join('application_assessment', function($join) use($programmes){
                $join->on('applications.id', '=', 'application_assessment.application_id')
                ->whereIn('application_assessment.programme_id',$programmes);
            })->latest()->get();

            $applications = collect($applications);
            // return $applications;

        } else {
           // $applications = Application::where('status', $request->status)->latest()->get();
           $status = $request->status;
           $applications = Application::select('applications.*')->join('application_assessment', function($join) use($programmes){
                $join->on('applications.id', '=', 'application_assessment.application_id')
                ->whereIn('application_assessment.programme_id',$programmes);
            })->where('applications.status', $status)->latest()->get();

            $applications = collect($applications);
        }
    }
        //admin fetch
        else{
            $applications = Application::when($request->status == 'all' ,function($q) use($request){
                $q->whereNotNull('status');
            })->when($request->status != 'all', function($q) use($request) {
                $q->where('status', $request->status);
            })->latest()->get();

        }

        try {

            foreach ($applications as $key => $value) {
                //return $value->applicant_id;

                $applicant = Applicant::find($value->applicant_id)->setHidden(['password', 'token']);
                $applications[$key]['applicant'] = $applicant;
                $applications[$key]['assessment'] = application_assessment::where('application_id', $value->id)->first();
                $applications[$key]['applyFor'] = $applications[$key]['assessment']['apply_for'];
                $applications[$key]['programme'] = $this->programmeName($request, $applications[$key]['assessment']['programme_id']);
                $applications[$key]['approvedProgramme'] = $this->programmeName($request, $applications[$key]['assessment']['approved_programme_id']);
            }
            return response()->json(['msg' => 'success', 'applications' => $applications]);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['error' => 'Unable to fetch applications', 'th' => $th], 401);
        }
    }

    public function pg_coord_approved_recommendation_list(Request $request)
    {
        $applications = null;
        if ($request->has('deptName') && $request->filled('deptName')) {
            $dept = $this->get_dept_given_deptName($request->deptName);
            $programmes = $this->get_progs_given_deptID($dept->id);
            $applications = Application::where('coord_recommendation', 10)
            ->join('application_assessment', function($join) use($programmes){
                $join->on('applications.id', '=', 'application_assessment.application_id')
            ->whereIn('application_assessment.programme_id',$programmes);
            })->select('applications.*')
            ->latest()->get()?:null;
        }else{
            $applications = Application::where('coord_recommendation', 10)->latest()->get()?:null;
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
            throw $th;
            return response()->json(['error' => 'Unable to fetch Coordinators recommended Application(s)', 'th' => $th], 401);
        }
    }


    public function pg_coord_disapproved_recommendation_list(Request $request)
    {
        $applications = null;
            if ($request->has('deptName') && $request->filled('deptName')) {
                $dept = $this->get_dept_given_deptName($request->deptName);
                $programmes = $this->get_progs_given_deptID($dept->id);
                $applications = Application::where('coord_recommendation', -1)
                ->join('application_assessment', function($join) use($programmes){
                    $join->on('applications.id', '=', 'application_assessment.application_id')
                ->whereIn('application_assessment.programme_id',$programmes);
                })->select('applications.*')
                ->latest()->get()?:null;
            }else{
                $applications = Application::where('coord_recommendation', -1)->latest()->get()?:null;
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
            throw $th;
            return response()->json(['error' => 'Unable to fetch Coordinators recommended Application(s)', 'th' => $th], 401);
        }
    }


    public function programmeName(Request $request, $id)
    {
        // $programme = Programme::where('id', $id)->first();
        $programme = Programme::find($id);
        return $programme ? $programme->programme : "Not Avaliable" ;
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
            'admsStatus' => 'required',
            'semester_name' => 'sometimes|string',
            'session_name' => 'sometimes|string',
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
                    $application->semester_admitted = $request->semester_name;
                    $application->session_admitted = $request->session_name;
                    $application->save();
                    $emailParams = [
                        'address'=>$profile->contact_address,
                    'email'=>$applicant->email, 'status'=>$application->status,
                     'title'=>$profile->title, 'surname'=>$applicant->surname,
                     'firstname'=>$applicant->firstname,'lastname'=>$applicant->lastname,
                     'duration'=>$getProgramme->duration,
                    'session'=>$request->session_name,
                    'semester'=>$request->semester_name,
                    'programme'=>$getProgramme->programme,'progCode'=>$getProgramme->programme_id,
                    'applicant_id'=>$applicant->id, 'date_admitted'=> date("F d, Y"),
                    'apply_for'=>$update->apply_for,'dept'=>$dept->department,'college'=>$college->college
                 ];
                    DB::table('applications')->where('id',$request->applicationId)->update([
                        'admissionLetter'=> view('emails.admissionApproved', ['emailParams'=>$emailParams])
                    ]);
                    // return view('emails.admissionApproved', ['emailParams'=>$emailParams]);
                     $adms_job = (new AdmissionStatusMailJob($emailParams))->delay(Carbon::now()->addSeconds(2));
                     dispatch($adms_job);
                    return response()->json(['info' => "Application Appproved", 'value' => "Application Appproved", 'msg' => "success"]);
                } elseif($request->admsStatus == 'denied') {

                    $applicant = Applicant::find($request->applicant);
                    $application = Application::find($request->applicationId);
                    $application->status = $request->admsStatus;
                    //$application->disapproved_message = $request->denyReason;
                    $application->save();
                    return response()->json(['info' => "Application Denied", 'value' => "Application denied", 'msg' => "success"]);


                }
            } else {
                return response()->json(['error' => 'Like there is no programme for this ID'], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error updating programme', 'th' => $th], 401);
        }
    }


    public function pg_coord_adms_recommendation_action(Request $request)
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
            $applicant = Applicant::find($request->applicant);
            if (isset($getProgramme) && $applicant) {
                if ($request->admsStatus == 'RECOMMENDED') {
                    $update = ApplicationAssessment::where('application_id', $request->applicationId)->first();
                    $update->approved_programme_id = $request->programmeId;
                    $update->save();
                    $application = Application::find($request->applicationId);
                    $application->coord_recommendation = 10;
                    $application->save();

                    return response()->json(['info' => "Admission Appproved", 'value' => "Admission Appproved", 'msg' => "success"]);
                } elseif($request->admsStatus=='NRECOMMENDED') {
                    $validator = Validator::make($request->all(), [
                        'applicant' => 'required',
                        'applicationId' => 'required',
                        'programmeId' => 'required',
                        'admsStatus' => 'required',
                        'message' => 'required'
                    ]);
                    if ($validator->fails()) {
                        return response()->json(['error' => 'all fields are required and Message!'], 401);
                    }

                    $update = ApplicationAssessment::where('application_id', $request->applicationId)->first();
                    $update->approved_programme_id = 0;
                    $update->save();
                    $application = Application::find($request->applicationId);
                    $application->coord_recommendation = -1;
                    $application->disapproved_message = $request->message;
                    $application->save();

                    return response()->json(['info' => "Admission Disappproved", 'value' => "Admission Disappproved", 'msg' => "success"]);

                }else{
                    return response()->json(['error' => 'Like there is Error with Admission Status sent'], 401);

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
            if($dept){return $dept;}
            return "Department cannot be found!";
        } catch (\Throwable $th) {

            return response()->json(['error' => 'Error getting department given programme ID', 'th' => $th], 401);
        }
    }
    static function get_faculty_given_dept($faculty_id){
        try {
            $faculty = College::find($faculty_id);
            if($faculty){return $faculty;}
            return "Faculty cannot be found!";
        } catch (\Throwable $th) {

            return response()->json(['error' => 'Error getting faculty given department ID', 'th' => $th], 401);
        }
    }


    public function admissionPending(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'applicant' => 'required',
            'applicationId' => 'required',
            'pending_message'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Application required'], 401);
        }
        $application = Application::find($request->applicationId);
        $application->status = 'pending';
        $application->pending_message = $request->pending_message ;
        $application->save();
        $notification = new Notification;
        $notification->type = "Admisssion";
        $notification->applicants = $application->applicant_id;
        $data = [
            "pending_message"=>$request->pending_message,
            "type"=>"Admission",
        ];
        $notification->data = $data;
        $notification->save();
        return response()->json(['msg'=>'success' , 'value'=> "Admission Pending"]);
    }

    public function getProgrammeForApprove(Request $request)
    {
        $assessment = application_assessment::where('application_id', $request->applicationId)->first();
        $programme = Programme::find($assessment->programme_id);
        return response()->json(['programme' => $programme]);
        // $programme =
    }


    public function fetch_applicants_per_dept_for_coord(Request $request)
    {
        // $deptName = 'COMPUTER SCIENCE';
        $validator = Validator::make($request->all(), [ 'deptName' => 'required' ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'department name is required!'], 401);
        }
       $dept = $this->get_dept_given_deptName($request->deptName);
       $programmes = $this->get_progs_given_deptID($dept->id);
       return $programmes;

    }


    static function get_progs_given_deptID($deptID){
        try {
            $programmes = Programme::where('department_id',$deptID)->pluck('id');
            if($programmes){return $programmes;}
            return "Department cannot be found!";
        } catch (\Throwable $th) {

            return response()->json(['error' => 'Error getting programme(s) given department ID', 'th' => $th], 401);
        }
    }
    static function get_dept_given_deptName($deptName){
        try {
            $dept = Department::where('department',$deptName)->first();
            if($dept){return $dept;}
            return "Department cannot be found!";
        } catch (\Throwable $th) {

            return response()->json(['error' => 'Error getting department given department name', 'th' => $th], 401);
        }
    }



    public function get_pg_coord_in_this_dept_giving_deptName(Request $request)
    {

        try {
            if($request->has('deptName') && $request->filled('deptName')){
                $validator = Validator::make($request->all(), [ 'deptName' => 'required' ]);
                if ($validator->fails()) {
                    return response()->json(['error' => 'department name is required!'], 401);
                }
                $pg_coords = PGLecturer::where('lecturer_category', 'PG-COORD')->where('deptname', $request->deptName)->orderBy('created_at')->get();
                return response()->json(['pg_coords' => $pg_coords]);
            }
            elseif($request->has('role') && $request->role == "admin"){
                $all_pg_users = PGLecturer::orderBy('created_at')->get();
                return response()->json(['pg_coords' => $all_pg_users]);
            }

           else{
            return response()->json([ 'error'=>'No parameter sent'],401);
           }

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error fetching PG COORD(s) for HOD', 'th' => $th], 401);

        }
    }



    public function enable_disable_pg_coords(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'id' => 'required' ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Lecturer ID is required!'], 401);
        }

        try {
            $record = PGLecturer::find($request->id);
            if($record){
                 if($record->is_verified == 0){
                     $record->is_verified = 10;
                     $record->semester_last_seen =  $this->settings($request)->semester_name;
                     $record->session_last_seen = $this->settings($request)->session_name;
                     $record->save();
                    return response()->json(['Enable-status' => 'Lecturer Enabled']);}
                 else{$record->is_verified=0;
                    $record->save();
                    return response()->json(['Disable-status' => 'Lecturer Disabled']);}
            }
            return response()->json(['Error' => 'Error enabling/disabling lecturer']);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error enabling/disabling lecturer', 'th' => $th], 401);

        }
    }

    public static  function settings2(){

        $settings = Setting::orderBy('created_at')->get();
        return response()->json(['settings' => $settings ]);
    }

}



