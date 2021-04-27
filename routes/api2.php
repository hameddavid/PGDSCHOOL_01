<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\ApplicantController;
// use App\Http\Controllers\AuthController;
use App\Http\Controllers\Helper\PaymentHelper;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProgrammesController;
use App\Http\Controllers\RemitaController;
use App\Models\Admission\application_assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Applicant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('test', function(Request $request){

    return rand(000001,999999);
});


Route::any('billing/per/prog', [PaymentHelper::class , 'billing_per_prog']);

Route::any('student/billing/history', [PaymentHelper::class , 'studentPaymentHistory'])->middleware('auth:sanctum');

Route::any('student/initTransactions', [PaymentController::class , 'studentInitTransactions'])->middleware('auth:sanctum');
Route::any('student/saveRRR', [PaymentController::class , 'saveStudentRRR']);
Route::any('student/studentUpdateTransaction', [PaymentController::class , 'studentUpdateTransaction']);

//remita
Route::post('updateTransaction', [RemitaController::class , 'updateTransaction'])->middleware('auth:sanctum');

Route::post('upload',function(Request $request){
    $name = $request->file('relevantFile')->getClientOriginalName();

    $extension = $request->file('relevantFile')->extension();
    $user = $request->user();
    $path = Storage::putFileAs(
        'Essay', $request->file('relevantFile'), $name.$user->id
    );
    // Storage::delete($path);
    return $path;
})->middleware('auth:sanctum');

// Route::post('resetPassword', [AuthController::class , 'ForgotPassword'])->middleware('guest');
Route::post('checkRRRStatus',[RemitaController::class,'checkRRRStatus']);
Route::post('crossCheckPayment', [RemitaController::class, 'crossCheckPayment'])->middleware('auth:sanctum');
Route::post('updatePaymentStatus',[RemitaController::class, 'updatePaymentStatus'])->middleware('auth:sanctum');

Route::post('getProgrammes', [ProgrammesController::class, 'getProgrammes']);
Route::post('getProgramme', [ProgrammesController::class, 'getProgramme']);

Route::post('viewProfileImage', [ApplicantController::class , 'viewProfileImage'])->middleware('auth:sanctum');

Route::any('test/remita', [RemitaController::class , 'testEndPoint']);

Route::post('notifications', [NotificationController::class , 'getNotifications'])->middleware('auth:sanctum');
Route::post('notification/activate', [NotificationController::class , 'paymentActivate'])->middleware('auth:sanctum');

Route::post('getProgrammeTypesForApplication', [PaymentHelper::class, 'getProgrammeTypesForApplication'])->middleware('auth:sanctum');

Route::any('/t', function(){
    $f = application_assessment::where('id', 2)->first();
    return $f->essay['name'];
});

Route::post('getFeeType', [PaymentHelper::class, 'getFeeType'])->middleware('auth:sanctum');
Route::post('getAdmissionLetter', [ApplicantController::class] , 'getAdmissionLetter' );
