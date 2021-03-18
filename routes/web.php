<?php

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

Route::get('/', function (Request $request) {

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


