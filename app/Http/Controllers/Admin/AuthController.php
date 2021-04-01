<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Users\AdmissionOfficer;
use App\Models\User;
use App\Models\PGLecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function adminLogin(Request $request)
    {
        // dd($request->all());
        $validate = Validator::make($request->all(),[
            'email'=> 'required|email',
            'password'=>'required'
        ]);

        if($validate->fails()){
            return response()->json(['status_code'=>400, 'msg'=>'Bad request']);
        }

        if(!Auth::attempt(['email'=> $request->email, 'password'=>$request->password])){
            return response()->json([ 'error'=>'Unauthorize'],401);
        }

        $user = User::where('email', $request->email)->first();
        $roles = [];
        $createToken = $user->createToken('authToken')->plainTextToken;
        foreach($user->roles as $role){array_push($roles, $role->role);}
        $response = ['status_code'=>200, 'msg'=>'success', 'user' => $user, 'token'=>$createToken];
        return response()->json($response);
    }





    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status_code'=>200,
            'msg'=>'logout successfully',
        ]);
    }



    public function auto_login_staff(Request $request)
    {
    $params = base64_decode($request->params);
    $a_params = explode("&", $params);
    $final = [];
    for ($i=0; $i < count($a_params) ; $i++) {
        $el = explode('=', $a_params[$i]);
        $final[$el[0]] = $el[1];
    }
        $validate = Validator::make($final,['uid'=>'required','status'=>'required',
            //'sch'=>'required',
            'deptid'=>'required','deptname'=>'required','degree'=>'sometimes','level'=>'sometimes',
            'title'=>'required','firstname'=>'required','lastname'=>'required',
            //'othername'=>'required',
            'staffno'=>'required', 'email'=>'required','phone'=>'required', 'img'=>'required','sign'=>'required'
        ]);


        if($validate->fails()){
            return response()->json(['status_code'=>400, 'msg'=>'Bad request,Insufficient number of parameters in request! ']);
        }

        $name = $final['status']."_".$final['uid']."_".$final['deptid'];
        $record = PGLecturer::find($name);
        if($record){
           if($record->is_verified == 10 && $record->semester_last_seen == AdmissionOfficer::settings($request)->semester_name ){
            $createToken = $record->createToken('autoLogin')->plainTextToken;
            $response = ['status_code'=>200, 'msg'=>'success', 'record' => $record, 'token'=>$createToken];
            return response()->json($response);

           }
           elseif($record->is_verified == 0){
               if($record->lecturer_category == "PG-COORD"){
                return response()->json( ['status_code'=>201, 'msg'=>'Access Denied, Contact HOD For Approval']);
               }
               elseif($record->lecturer_category == "HOD"){
            return response()->json(['status_code'=>201, 'msg'=>'Access Denied, Contact Admin For Approval']);
               }
               else{ return response()->json(['status_code'=>201, 'msg'=>'Access Denied, Contact Admin']); }
           }

        }
        $staff = new PGLecturer;
        $staff->id = $name;
        $staff->firstname = $final['firstname'];
        $staff->surname = $final['lastname'];
        $staff->phone = $final['phone'];
        $staff->email = $final['email'];
        $staff->deleted = 'N';
        $staff->login_name = $name;
        $staff->deptid = $final['deptid'];
        $staff->deptname =$final['deptname'];
        $staff->program_id_FK = $final['progid']? $final['progid']:0;
        $staff->lecturer_category = $final['status'];
        $staff->picture = $final['img'];
        $staff->signature = $final['sign'];
        $staff->semester_last_seen = AdmissionOfficer::settings($request)->semester_name;
        $staff->session_last_seen =AdmissionOfficer::settings($request)->session_name ;
        $staff->is_verified = 0;
        $staff->save();
        if($staff->lecturer_category == "PG-COORD"){
            return response()->json(['status'=>'success', 'msg'=>'Account Created,Contact HOD For Approval']);
        }
        elseif($staff->lecturer_category == "HOD"){
            return response()->json(['status'=>'success', 'msg'=>'Account Created,Contact Admin For Approval']);
        }
        else{
            return response()->json(['status'=>'success', 'msg'=>'Account Created,Contact Admin']);

        }
       
















        if(!Auth::attempt(['email'=> $request->email, 'password'=>$request->password])){
            return response()->json([ 'error'=>'Unauthorize'],401);
        }

        $user = User::where('email', $request->email)->first();
        $roles = [];
        $createToken = $user->createToken('autoLoginStaff')->plainTextToken;
        foreach($user->roles as $role){array_push($roles, $role->role);}
        $response = ['status_code'=>200, 'msg'=>'success', 'user' => $user, 'token'=>$createToken];
        return response()->json($response);
    }



    public function downloadFile(Request $request)
    {
        $file = Storage::get($request->path);
        return response($file, 200)->header('Content-Type', 'image/jpeg');
    }
}
