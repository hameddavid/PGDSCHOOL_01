<?php

namespace App\Http\Controllers;

use App\Jobs\SendVerifyEmailJob;
use App\Mail\VerifyEmail;
use App\Models\Admission\application_assessment;
use App\Models\Admission\application_credentials;
use App\Models\Admission\application_institution;
use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApplicantController extends Controller
{
    public function pgCreateAccount(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'surname' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'mobile' => 'required|unique:applicants',
            'email' => 'required|unique:applicants',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Plesae provide all required fields'], 401);
        }

        $token = rand(000001, 999999);
        try {
            $applicant = new Applicant;
            $applicant->surname = $request->surname;
            $applicant->firstname = $request->firstname;
            $applicant->lastname = $request->lastname;
            $applicant->mobile = $request->mobile;
            $applicant->email = $request->email;
            $applicant->password = Hash::make($request->password);
            $applicant->token = $token;
            $applicant->save();

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Unable to create account try again'], 401);
        }
        try {
            Mail::to($applicant->email)
                ->queue(new VerifyEmail($token));
                unset($applicant->token);
                unset($applicant->password);
            return response()->json(['applicant' => $applicant, 'msg' => 'success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'Error sending verification mail'], 401);
        }
    }
    public function pgVerifyEmail(Request $request)
    {
        //    return $request;
        $applicant = Applicant::find($request->id);
        if ($request->token == $applicant->token) {
            $applicant->email_verified_at = Carbon::now();
            $applicant->save();
            return response()->json(['msg' => 'success', 'value' => 'email verified', 'info' => 'Email Verified']);
        } else {
            return response()->json(['error' => 'unable to verify your email', 'value' => 'unable to verify email'], 401);
        }
    }
    public function pgApplicant(Request $request)
    {
        $applicant = Applicant::where('email', $request->email)->first();
        // $profileImage = DB::table('application_personaldata')->where('application_id',$applicant->id)->first();
        // try {
        //     $applicant['picture'] = $profileImage->picture;
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }
        return $applicant;
    }
    public function pgStudent(Request $request)
    {
        $student  = Student::where('martic_no',$request->email)->first();
        return $student;
    }
    public function pgLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $applicant = $this->pgApplicant($request);
        // $student = $this->pgStudent($request);
        if ($applicant) {
            return $this->genToken($request, $applicant);
        }
        // else if ($applicant) {
        //     return $this->genToken($request, $applicant);
        // }
        return response()->json(['error' => 'We dont have your records'], 401);
    }
    public function genToken(Request $request, $user)
    {

        if (!$user || !Hash::check($request->password, $user->password)) {
            // throw ValidationException::withMessages([
            //     'email'=> ['The provided credentials are incorrect.']
            //   ]
            // );
            return response()->json(['error' => 'The provided credentials are incorrect'], 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user, 'msg' => 'success', 'info' => 'Login Successful']);
    }

    public function pgResendVerificationMail(Request $request)
    {
        $applicant = Applicant::find($request->id);
        if (!$applicant->token) {
            $token = rand(000001, 999999);
            $applicant->token = $token;
            $applicant->save();
        }
        Mail::to($applicant->email)
            ->queue(new VerifyEmail($applicant->token));
        // $job = Mail::to($applicant->email)->send(new VerifyEmail($applicant->token))->delay(Carbon::now()->addSeconds(1));
        // dd($applicant);
        // $job = (new SendVerifyEmailJob($applicant->email, $applicant->token))->delay(Carbon::now()->addSeconds(1));
        // dispatch($job);
        return response()->json(['msg' => 'success', 'value' => 'Email sent']);
    }
    //applicant
    public function pgApplicationLists(Request $request)
    {
        $user = $request->user();

        if ($user->type) {
            $query = Application::where('applicant_id', $user->id);
            $applications = $query->get();
            foreach ($applications as $key => $value) {
                //     $value['assessment'] = Application::where('applicant_id',$user->id)->join('application_assessment', function($join) use($value){
                //        $join->on('applications.id', '=','application_assessment.application_id')
                //        ->where('application_assessment.application_id',$value->id);
                //    })->select('application_assessment.*')->first();

                $value['assessment'] = application_assessment::where('application_id', $value->id)->first();

                $value['credentials'] = application_credentials::where('application_id', $value->id)->first();

                $value['employmenthistory'] = Application::where('applicant_id', $user->id)->join('application_employmenthistory', function ($join) use ($value) {
                    $join->on('applications.id', '=', 'application_employmenthistory.application_id')
                        ->where('application_employmenthistory.application_id', $value->id);
                })->select('application_employmenthistory.*')->get();

                $value['institution'] = Application::where('applicant_id', $user->id)->join('application_institution', function ($join) use ($value) {
                    $join->on('applications.id', '=', 'application_institution.application_id')
                        ->where('application_institution.application_id', $value->id);
                })->select('application_institution.*')->get();
                //update
                $value['personaldetails'] = DB::table('application_personaldata')->where('applicant_id', $user->id)->first();
                // $value['personaldetails'] = Application::where('applicant_id', $user->id)->join('application_personaldata', function ($join) use ($value) {
                //     $join->on('applications.id', '=', 'application_personaldata.application_id')
                //         ->where('application_personaldata.application_id', $value->id);
                // })->select('application_personaldata.*')->first();

                $value['refree'] = Application::where('applicant_id', $user->id)->join('application_refree', function ($join) use ($value) {
                    $join->on('applications.id', '=', 'application_refree.application_id')
                        ->where('application_refree.application_id', $value->id);
                })->select('application_refree.*')->get();

                //    $applications[$key] = $value;
            }


            // $applications['institution'] = $query->join('application_institution','applications.id','=','application_institution.application_id')
            //                     ->select('application_institution.*')->get();
            // $applications['personaldata'] = $query->join('application_personaldata','applications.id','=','application_personaldata.application_id')
            //                     ->select('application_personaldata.*')->get();
            // $applications['refree'] = $query->join('application_refree','applications.id','=','application_refree.application_id')
            //                     ->select('application_refree.*')->get();

            return response()->json(['msg' => 'success', 'value' => $applications]);
        } else {
            $applications = Application::where('applicant_id', $user->applicant_id)->get();
            return response()->json(['msg' => 'success', 'value' => $applications]);
        }
    }

    public function pgSaveApplicationForm(Request $request)
    {
        $personalDetails = $request->personalDetails;
        $personalDetails_id = $personalDetails['id'];
        unset($personalDetails['is_form_completed']);
        unset($personalDetails['id']);
        //update
        DB::table('application_personaldata')->where('id', $personalDetails_id)->update(
            $personalDetails
        );
        $institutionDetails = $request->institutionDetails;
        foreach ($institutionDetails as $key => $value) {
            $institutionId = $value['id'];
            if($key == 0){
                unset($value['is_form_completed']);
            }
            unset($value['id']);
            DB::table('application_institution')->where('id', $institutionId)->update($value);
        }
        $employmentHistory = $request->employmentHistory;
        foreach ($employmentHistory as $key => $value) {
            $employmentHistory_id = $value['id'];
            if($key == 0){
                unset($value['is_form_completed']);
            }
            unset($value['id']);
            DB::table('application_employmenthistory')->where('id', $employmentHistory_id)->update($value);
        }
        // assessment save
        $assestmentForm = $request->assestmentForm;
        $assestmentForm_id = $assestmentForm['id'];
        unset($assestmentForm['id']);
        application_assessment::where('id', $assestmentForm_id)->update([
            'nysc_completed' => $assestmentForm['nysc_completed'],
            'choose_campus' => $assestmentForm['choose_campus'],
            // 'essay' => $assestmentForm['essay'],
            'academic_distinction_prize' => $assestmentForm['academic_distinction_prize'],
            'publications' => $assestmentForm['publications'],
            'college_attending_currently' => $assestmentForm['college_attending_currently'],
            'relevant_info' => $assestmentForm['relevant_info'],
            // 'relevant_file' => $assestmentForm['relevant_file'],
            'apply_for' => $assestmentForm['apply_for'],
            'programme_id' => $assestmentForm['programme_id']
        ]);

        //reference Save
        $reference = $request->reference;
        foreach ($reference as $key => $value) {
            $reference_id = $value['id'];
            if($key == 0 ){
                unset($value['is_form_completed']);
            }
            unset($value['id']);
            DB::table('application_refree')->where('id', $reference_id)->update($value);
        }
        return $request;
    }

    public function addAndRemoveInstitution(Request $request)
    {
        //check current list
        if ($request->type == 'add') {
            DB::table('application_institution')->insert(['application_id' => $request->applicationId]);
            $institutions = DB::table('application_institution')->where('application_id', $request->applicationId)->get();
            return $institutions;
        } else {
            //remove
            DB::table('application_institution')->where('id', $request->institutionId)->delete();
            $institutions = DB::table('application_institution')->where('application_id', $request->applicationId)->get();
            return $institutions;
        }
        return response()->json(['error' => 'addAndRemoveInstitution', 'type' => $request->type]);
    }
    public function addAndRemoveEmployment(Request $request)
    {
        if ($request->type == 'add') {
            DB::table('application_employmenthistory')->insert(['application_id' => $request->applicationId]);
            $employmentHistory = DB::table('application_employmenthistory')->where('application_id', $request->applicationId)->get();
            return $employmentHistory;
        } else {
            DB::table('application_employmenthistory')->where('id', $request->employmentId)->delete();
            $employmentHistory = DB::table('application_employmenthistory')->where('application_id', $request->applicationId)->get();
            return $employmentHistory;
        }
        return response()->json(['error' => 'addAndRemoveEmployment', 'type' => $request->type]);
    }
    public function addAndRemoveRefree(Request $request)
    {
        if ($request->type == 'add') {
            DB::table('application_refree')->insert(['application_id' => $request->applicationId]);
            $refrees = DB::table('application_refree')->where('application_id', $request->applicationId)->get();
            return $refrees;
        } else {
            DB::table('application_refree')->where('id', $request->refreeId)->delete();
            $refrees = DB::table('application_refree')->where('application_id', $request->applicationId)->get();
            return $refrees;
        }
        return response()->json(['error' => 'addAndRemoveRefree', 'type' => $request->type]);
    }

    public function uploadEssay(Request $request)
    {
        $filename = $request->file('essay')->getClientOriginalName();
        $user = $request->user();
        $assestment = application_assessment::find($request->id);
        if (is_null($assestment->essay)) {
            $path = Storage::putFileAs('Essay', $request->file('essay'), $user->surname . $user->firstname . $user->lastname . $user->id . $filename);
            $essay = ['path' => $path, 'name' => $filename];
            $assestment->essay = $essay;
            $assestment->save();
            return response()->json(['msg' => 'success', 'value' => 'File Uploaded', 'assessment' => $assestment]);
        } else {
            Storage::delete($assestment->essay['path']);
            $path = Storage::putFileAs('Essay', $request->file('essay'), $user->surname . $user->firstname . $user->lastname . $user->id . $filename);
            $essay = ['path' => $path, 'name' => $filename];
            $assestment->essay = $essay;
            $assestment->save();
            return response()->json(['msg' => 'success', 'value' => 'File Overide Successful', 'assessment' => $assestment]);
        }
    }

    public function relevantFile(Request $request)
    {
        $files  = array();
        $filename1 = $request->file('relevantFile1')->getClientOriginalName();
        $filename2 = '';

        $user = $request->user();
        $assestment = application_assessment::find($request->id);
        if (is_null($assestment->relevant_file)) {
            $path1 = Storage::putFileAs('RelevantFiles', $request->file('relevantFile1'), $user->surname . $user->firstname . $user->lastname . $user->id . $filename1);
            $relevantFile1 = ['path' => $path1, 'name' => $filename1];

            array_push($files, $relevantFile1);
            $assestment->relevant_file = $files;
            $assestment->save();
            return response()->json(['msg' => 'success', 'value' => 'File Uploaded', 'assessment' => $assestment]);
        } else {
            if (count($assestment->relevant_file) == 2) {
                return response()->json(['msg' => 'success', 'value' => 'Relevant Upload Limit Reached', 'assessment' => $assestment]);
            }
            // Storage::delete([$assestment->relevant_file[0]['path'], $assestment->relevant_file[1]['path']]);
            $path1 = Storage::putFileAs('RelevantFiles', $request->file('relevantFile1'), $user->surname . $user->firstname . $user->lastname . $user->id . $filename1);
            $relevantFile1 = ['path' => $path1, 'name' => $filename1];
            // if($request->relevantFile2 != 'undefined'){
            //     $path2 = Storage::putFileAs('RelevantFiles', $request->file('relevantFile2'), $user->surname.$user->firstname.$user->lastname.$user->id.$filename2 );
            //     $relevantFile2 = ['path'=>$path2, 'name'=>$filename2];
            // }
            $files = $assestment->relevant_file;
            array_push($files, $relevantFile1);
            $assestment->relevant_file = $files;
            $assestment->save();
            return response()->json(['msg' => 'success', 'value' => 'File Uploaded', 'assessment' => $assestment]);
        }
    }
    public function applicantDeleteRelevantFile(Request $request)
    {

        $assestment = application_assessment::find($request->assessmentFormID);
        $path = $assestment->relevant_file[$request->index]['path'];
        if ($path == $request->path) {
            $value = $assestment->relevant_file;
            Storage::delete($request->path);
            unset($value[$request->index]);
            $assestment->relevant_file = $value;
            $assestment->save();
            return response()->json(['msg' => 'success', 'value' => 'File Deleted', 'assessment' => $assestment]);
        }
        return $path;
    }
    public function credentialsUpload(Request $request, $filekey, $id)
    {
        // return $request;
        $user = $request->user();
        $credentials = application_credentials::find($id);
        $files = array();
        if ($credentials->credentials == null) {
            $filename = $request->file($filekey)->getClientOriginalName();
            $path = Storage::putFileAs('Credentials', $request->file($filekey), $user->surname . $user->firstname . $user->lastname . $user->id . $filename);
            $credential = ['path' => $path, 'name' => $filename, 'type' => $filekey];
            array_push($files, $credential);
            $credentials->credentials = $files;
            $credentials->save();
            return $credentials;
        } else {
            $filename = $request->file($filekey)->getClientOriginalName();
            $path = Storage::putFileAs('Credentials', $request->file($filekey), $user->surname . $user->firstname . $user->lastname . $user->id . $filename);
            $credential = ['path' => $path, 'name' => $filename, 'type' => $filekey];
            $files = $credentials->credentials;
            array_push($files, $credential);
            $credentials->credentials = $files;
            $credentials->save();
            return $credentials;
        }
        // return $request->file($filekey)->getClientOriginalName();
        // $filename = $request->file($filekey)->getClientOriginalName();
        // $path = Storage::putFileAs('Credentials', $request->file($filekey), $user->surname.$user->firstname.$user->lastname.$user->id.$filename);

    }
    public function deleteCredential(Request $request)
    {
        // return $request->path;
        $credential = application_credentials::find($request->id);
        $value = $credential->credentials;
        unset($value[$request->index]);
        $credential->credentials = count($value) < 1 ? Null : $value;
        $credential->save();
        Storage::delete($request->path);
        return $credential;
    }

    public function checkEmailorMobile(Request $request)
    {
        if ($request->type == 'email') {
            $applicant = Applicant::where('email', $request->value)->first();
            if ($applicant) {
                return response()->json(['value' => true]);
            } else {
                return response()->json(['value' => false]);
            }
        } else {
            $applicant = Applicant::where('mobile', $request->value)->first();
            if ($applicant) {
                return response()->json(['value' => true]);
            } else {
                return response()->json(['value' => false]);
            }
        }
    }

    public function uploadProfileImage(Request $request)
    {
        $user = $request->user();

        $checkIfExists =  $user;
        if ($checkIfExists->picture) {
            Storage::delete($checkIfExists->picture);
        }

        try {
            $filename = $request->file('profileImage')->getClientOriginalName();
            $path = Storage::putFileAs('ProfileImage', $request->file('profileImage'), $user->surname . $user->firstname . $user->lastname . $user->id . $filename);
            $applicant = Applicant::find($user->id);
            $applicant->picture = $path;
            $applicant->save();
            // DB::table('application_personaldata')->where('applicant_id',$user->id)->update(['picture'=>$path]);
            return response()->json(['info' => 'Profile Image Uploaded', 'msg' => 'success']);
        } catch (\Throwable $th) {
            // throw $th
            return response()->json(['error' => 'image upload failed', 'th' => $th], 401);
        }
    }

    //view profile image

    public function viewProfileImage(Request $request)
    {
        //get Image for Admin
        if($request->has('imgPath')){
            $file = Storage::get($request->imgPath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }

        $user = $request->user();
        $id = null;
        if ($user->type == 'applicant') {
            $id = $user->id;
        } else {
            //student
        }
        // $profileImage = DB::table('application_personaldata')->where('application_id',$id)->first();
        $file = Storage::get($user->picture);
        return response($file, 200)->header('Content-Type', 'image/jpeg');
    }

    public function submitApplication(Request $request)
    {
        // Log::info($request);
        // $user = $request->user();
        $application = Application::find($request->applicationId);
        $application->status = 'submitted under processing';
        $application->save();
        return response()->json(['info'=>'Application Submitted', 'msg'=>'success']);
    }
    public function validateForm(Request $request )
    {

        ini_set('max_execution_time', 0);
        $validatorMsg = array();
        $personalDataValidator = array_key_exists('personalDetails' , $request->validator) ? $this->validatePersonalData($request->applicationForm['personalDetails']) : true;
        $assessmentValidator = array_key_exists('assessmentForm', $request->validator)? $this->validateAssessment($request->applicationForm['assestmentForm']) : true;
        $credentialsValidator = array_key_exists('credentials' , $request->validator) ? $this->validateCredentials($request->applicationForm['credentials']) : true;
        $employmentValidator = array_key_exists('employmentHistory' , $request->validator) ? $this->validateEmployment($request->applicationForm['employmentHistory']) : true;
        $institutionValidator= array_key_exists('institutionDetails' , $request->validator)  ?  $this->validateInstitution($request->applicationForm['institutionDetails']) : true;
        $refreeValidator= array_key_exists('reference' , $request->validator) ? $this->validateReferee($request->applicationForm['reference']) : true;
        is_array($personalDataValidator) ? $validatorMsg['personalDetails'] = $personalDataValidator : null ;
        is_array($assessmentValidator) ? $validatorMsg['assessmentForm'] = $assessmentValidator : null ;
        is_array($credentialsValidator) ? $validatorMsg['credentials'] = $credentialsValidator : null ;
        is_array($employmentValidator) ? $validatorMsg['employmentHistory'] = $employmentValidator : null ;
        is_array($institutionValidator) ? $validatorMsg['institutionDetails'] = $institutionValidator : null ;
        is_array($refreeValidator) ? $validatorMsg['reference'] = $refreeValidator  : null ;
        if(count($validatorMsg) > 0){
        return response()->json(['validatorMsg'=>$validatorMsg, 'value'=>"Form is incompleted", 'msg'=>'fail']);
        }else{
            $request['applicationId'] = $request->applicationForm['id'];
            $this->submitApplication($request);
            return response()->json(['info'=>'Application submitted', 'msg'=>'success']);
        }

    }
    public function validatePersonalData($PersonalData)
    {
        $validator = array();
        $validatePersonalData = DB::table('application_personaldata')->where('id', $PersonalData['id'])->first();
        unset($validatePersonalData->created_at);
        unset($validatePersonalData->updated_at);
        unset($validatePersonalData->picture);
        foreach ($validatePersonalData as $key => $value) {
            if(is_null($validatePersonalData->$key)){
                array_push($validator , $key);
            }
        }
        if (count($validator) == 0 ) {
            DB::table('application_personaldata')->where('id', $PersonalData['id'])->update([
                'is_form_completed'=>true
            ]);
            return true;
        }
        return $validator;
    }
    public function validateAssessment($assessment)
    {
        $validator = array();
        $validateAssessment = DB::table('application_assessment')->where('id', $assessment['id'])->first();
        unset($validateAssessment->created_at);
        unset($validateAssessment->updated_at);
        unset($validateAssessment->academic_distinction_prize);
        unset($validateAssessment->publications);
        unset($validateAssessment->relevant_info);
        unset($validateAssessment->relevant_file);
        unset($validateAssessment->approved_programme_id);
        foreach ($validateAssessment as $key => $value) {
            if(is_null($validateAssessment->$key)){
                array_push($validator, $key);
            }
        }
        if (count($validator) == 0) {
            DB::table('application_assessment')->where('id', $assessment['id'])->update([
                'is_form_completed'=>true
            ]);
            return true;
        }

        return $validator;
    }
    public function validateCredentials($credential)
    {
        $validator = array();
        $validateCredential = DB::table('application_credentials')->where('id' , $credential['id'])->first();
        unset($validateCredential->created_at);
        unset($validateCredential->updated_at);
        foreach ($validateCredential as $key => $value) {
            if(is_null($validateCredential->$key)){
                array_push($validator, $key);
            }
        }
        if (count($validator) == 0 ) {
            DB::table('application_credentials')->where('id' , $credential['id'])->update([
                'is_form_completed'=>true
            ]);
            return true;
        }
        return $validator;
    }
    public function validateEmployment($employment)
    {
        $validator = array();
        $validateEmployment = DB::table('application_employmenthistory')->where('id', $employment[0]['id'])->first();
        unset($validateEmployment->created_at);
        unset($validateEmployment->updated_at);
        unset($validateEmployment->last_salary_per_annum);
        foreach ($validateEmployment as $key => $value) {
            if(is_null($validateEmployment->$key)){
                array_push($validator, $key);
            }
        }
        if (count($validator) == 0 ) {
            DB::table('application_employmenthistory')->where('id', $employment[0]['id'])->update([
                'is_form_completed'=>true
            ]);
            return true;
        }
        return $validator;
    }
    public function validateInstitution($institution)
    {
        $validator = array();
        $validateInstitution = DB::table('application_institution')->where('id', $institution[0]['id'])->first();
        unset($validateInstitution->created_at);
        unset($validateInstitution->updated_at);
        foreach ($validateInstitution as $key => $value) {
            if (is_null($validateInstitution->$key)) {
                array_push($validator, $key);
            }
        }
        if (count($validator) == 0 ) {
            DB::table('application_institution')->where('id', $institution[0]['id'])->update([
                'is_form_completed'=>true
            ]);
            return true;
        }
        return $validator;
    }
    public function validateReferee($referee)
    {
        $validator = array();
        $validateRefree = DB::table('application_refree')->where('id', $referee[0]['id'])->first();
        $validateRefree2 = '';
        try {
        $validateRefree2 = DB::table('application_refree')->where('id', $referee[1]['id'])->first();
        } catch (\Throwable $th) {
            // throw $th;
        $validateRefree2 = null;
        }
        if($validateRefree2){

        }else{
            $key = 'minimum of at least two referees';
            array_push($validator, $key);
            return $validator;
        }
        unset($validateRefree->created_at);
        unset($validateRefree->updated_at);
        unset($validateRefree2->created_at);
        unset($validateRefree2->updated_at);
        foreach ($validateRefree as $key => $value) {
            if (is_null($validateRefree->$key)) {
                array_push($validator, $key);
            }
        }
        foreach ($validateRefree2 as $key => $value) {
            if (is_null($validateRefree2->$key)) {
                array_push($validator, $key);
            }
        }
        if (count($validator) == 0 ) {
            DB::table('application_refree')->where('id', $referee[0]['id'])->update([
                'is_form_completed'=>true
            ]);
            // if()
            return true;
        }
        return $validator;
    }

    public function getAdmissionLetter(Request $request)
    {
        $letter = Application::find($request->applicationId);
        return response()->json(['msg'=>'success', 'value'=>$letter->admissionLetter]);
    }



}
