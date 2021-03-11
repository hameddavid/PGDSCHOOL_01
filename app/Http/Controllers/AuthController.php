<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // applicant
    public function ForgotPassword(Request $request)
    {
       $applicant = Applicant::where('email', $request->email)->first();
       if($applicant){
        $token = rand(000001, 999999);
        $applicant->token = $token;
        $applicant->save();
        Mail::to($applicant->email)->send(new ForgotPassword($token));
       }
    }
    public function allowReset(Request $request)
    {
        $applicant = Applicant::where('token', $request->token)->where('email', $request->email)->first();
        if ($applicant) {
            
            return response()->json(['msg'=>'success', 'value'=>true, 'info'=>'Allow Reset']);
        }else{
            return response()->json(['msg'=>'failed', 'value'=>false, 'info'=>'Reset Denied']);
        }
    }

    //protected with auth
    public function ResetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed',
        ]);
        $user = $request->user();
        $applicant = Applicant::find($user->id);
        $applicant->password = Hash::make($request->password);
        $applicant->save();
            return response()->json(['msg'=>'success','value'=>'Password Changed']);
    }
}
