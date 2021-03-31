<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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
    return response()->json($final);
    $iNofOfParams = count($a_params);

    if ($iNofOfParams < 5) {
        return response()->json(['status_code'=>400, 'msg'=>'Insufficient number of parameters in request!']);
    }
        return response()->json($a_params);
        $validate = Validator::make($a_params,[
            'uid'=>'required',
            'status'=>'required',
            'sch'=>'required',
            'deptid'=>'required',
            'progid'=>'required',
            'level'=>'sometimes',
            'title'=>'required',
            'firstname'=>'required',
            'lastname'=>'required',
            'othername'=>'required',
            'staffno'=>'required',
            'email'=>'required',
            'phone'=>'required',
            'img'=>'required',
            'sign'=>'required'
        ]);

        if($validate->fails()){
            return response()->json(['status_code'=>400, 'msg'=>'Bad request']);
        }

        $response = ['status_code'=>200, 'msg'=>'success', 'staff' => $a_params];
        return response()->json($response);


//lecturer_id
// firstname
// surname
// phone
// campus_ext
// email
// deleted
// login_name
// program_id_FK
// lecturer_category
// picture
// is_verified
// signature
// semester_last_seen
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
