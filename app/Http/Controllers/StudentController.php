<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Application;
use App\Models\Programme;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{

    static function settings($request){
        if ($request->has('session') && $request->has('semester')){
            $settings = Setting::where('semester_name', $request->semester)->where('session_name',$request->session)->first();
            return $settings;
        }
        $settings = Setting::where('status', 'active')->first();
        return $settings;
    }

    public function pgStudentLogin(Request $request)
    {
        //email is matric number
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $student  = Student::where('email',$request->email)->first();
        $applicant = Applicant::find($student->applicant_id);
        $application_assessment = DB::table('application_assessment')->where('application_id',$student->application_id)->first();
        if (!$student || !Hash::check($request->password, $student->password)) {

            return response()->json(['error' => 'The provided credentials are incorrect'], 401);
        }

        $token = $student->createToken('mobile')->plainTextToken;
        $student['mobile']= $applicant->mobile;
        $student['surname'] = $applicant->surname;
        $student['firstname'] = $applicant->firstname;
        $student['lastname'] = $applicant->lastname;
        return response()->json(['token' => $token, 'user' => $student, 'student'=> $application_assessment, 'msg' => 'success', 'info' => 'Login Successful']);
    }
    public function makeApplicantStudent(Request $request)
    {
        Log::info($request);
        $application = Application::where('applicant_id',$request['applicant'])->latest('updated_at')->first();
        // data used
        $applicant = Applicant::find($request['applicant']);
        $check = Student::where('applicant_id', $applicant->id)->first();
        $application_assessment = DB::table('application_assessment')->where('application_id',$application->id)->first();
        $session_name = $this->settings($request)->session_name;
        $progCode = Programme::find($application_assessment->approved_programme_id);
        $matric_no = "RUN/REG/PG/ADM/".$progCode->code.'/'.$session_name.'/'.$applicant->id;
        if ($check) {
            return response()->json(['msg'=>'success', 'info'=>'Login as a student']);
        }else{
            $student = new Student();
            $student->email = $applicant->email;
            $student->password = $applicant->password;
            $student->applicant_id  = $applicant->id;
            $student->application_id = $application->id;
            $student->matric_no = $matric_no;
            $student->save();
            return response()->json(['msg'=>'success', 'info'=>'You can now login as a student']);
        }
        // check if already a student
        // Log::info("migrate student");
    }
    public function createAdmissionMatric()
    {

    }
}
