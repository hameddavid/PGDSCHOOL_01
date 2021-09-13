<?php

use App\Http\Controllers\StudentController;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Users\AdmissionOfficer;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::get('/up', function (Request $request) {
//    $apps = Application::all();
//    foreach ($apps as $app){
//     $application = Application::find($app->id);
//     $val = AdmissionOfficer::settings($request)->session_name;
//     $application->application_number = "RUN/CPGS/".substr($val,2,2)."-". substr($val,7,2)."/".$application->id;
//     $application->save();
//     //return $app;
//    }
//    return $apps;
// });

Route::get('/', function (Request $request) {
    return "hello";
    return view('welcome');
});



Route::get('g', function(){
    $n = Notification::find(1);

    return $n;
});

Route::get("adminTest", function(){
    return view('Tests.admin');
});

Route::get("email", [AdmissionOfficer::class, 'admissionApproved']);


// Route::get("manual" , [StudentController::class, 'manual']);
