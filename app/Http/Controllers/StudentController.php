<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function pgStudentLogin(Request $request)
    {
        //email is matric number
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $student  = Student::where('email',$request->email)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {

            return response()->json(['error' => 'The provided credentials are incorrect'], 401);
        }

        $token = $student->createToken('mobile')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $student, 'msg' => 'success', 'info' => 'Login Successful']);
    }
    public function makeApplicantStudent(Request $request)
    {
        Log::info($request);
        // data used
        $applicant = Applicant::find($request['applicant']);
        $check = Student::where('applicant_id', $applicant->id)->first();
        if ($check) {
            return response()->json(['msg'=>'success', 'info'=>'Login as a student']);
        }else{
            $student = new Student();
            $student->email = $applicant->email;
            $student->password = $applicant->password;
            $student->applicant_id  = $applicant->id;
            $student->save();
            return response()->json(['msg'=>'success', 'info'=>'You can now login as a student']);
        }
        // check if already a student
        // Log::info("migrate student");
    }
}
