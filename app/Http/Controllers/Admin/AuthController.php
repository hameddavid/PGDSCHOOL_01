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
    public function downloadFile(Request $request)
    {
        $file = Storage::get($request->path);
        return response($file, 200)->header('Content-Type', 'image/jpeg');
    }
}
