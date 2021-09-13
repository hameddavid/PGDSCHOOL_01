<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Applicant;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\Helper\PaymentHelper;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Jobs\SendVerifyEmailJob;
use App\Mail\VerifyEmail;
use App\Models\Application;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
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

//ForgotPassword
Route::post('forgotPassword',[AuthController::class, 'ForgotPassword'])->middleware('guest');
Route::post('resetPassword',[AuthController::class, "ResetPassword"])->middleware('guest');
//ForgotPassword


Route::post('testt', [PaymentController::class ,'createApplication']);



Route::post('pgCreateAccount', [ApplicantController::class ,'pgCreateAccount']);
Route::post('pgLogin', [ApplicantController::class , 'pgLogin']);
Route::post('pgStudentLogin', [StudentController::class , 'pgStudentLogin']);
Route::post('pgVerifyEmail',[ApplicantController::class, 'pgVerifyEmail']);
Route::middleware('auth:sanctum')->any('pgSignOut', function(Request $request){
  $user = $request->user();
  $user->tokens()->delete();
    return response()->json(['info'=>'Signed out', 'msg'=>'success']);
});
Route::post('forceLogin',function(Request $request){
    return 'user needs to login';
})->name('login');
Route::post('pgResendVerificationMail',[ApplicantController::class, 'pgResendVerificationMail'])->middleware('auth:sanctum');
Route::post('pgSaveApplicationForm', [ApplicantController::class, 'pgSaveApplicationForm'])->middleware('auth:sanctum');
Route::post('pgApplicationLists',[ApplicantController::class , 'pgApplicationLists'])->middleware('auth:sanctum');

Route::get('/applicationFee', [PaymentController::class , 'Application']);
Route::post('/paymentApplicationSuccess', [PaymentController::class , 'paymentApplicationSuccess'])->middleware('auth:sanctum');
Route::post('/initTransaction',[PaymentController::class , 'initTransaction'])->middleware('auth:sanctum');
Route::post('/addRemitaRRR',[PaymentController::class , 'addRemitaRRR'])->middleware('auth:sanctum');
Route::post('/applicantPaymentList', [PaymentController::class, 'ApplicantPaymentList'])->middleware('auth:sanctum');
Route::post('fetchPaymentHistory' , [PaymentController::class , 'paymentHistory'])->middleware('auth:sanctum');
// Application forms
Route::post('uploadProfileImage', [ApplicantController::class , 'uploadProfileImage'])->middleware('auth:sanctum');
Route::post('/addAndRemoveInstitution', [ApplicantController::class , 'addAndRemoveInstitution'])->middleware('auth:sanctum');
Route::post('/addAndRemoveEmployment',  [ApplicantController::class, 'addAndRemoveEmployment'])->middleware('auth:sanctum');
Route::post('/addAndRemoveRefree', [ApplicantController::class, 'addAndRemoveRefree'])->middleware('auth:sanctum');
Route::post('/uploadEssay',[ApplicantController::class, 'uploadEssay'])->middleware('auth:sanctum');
Route::post('uploadRelevantFile', [ApplicantController::class, 'relevantFile'])->middleware('auth:sanctum');
Route::delete('applicantDeleteRelevantFile', [ApplicantController::class, 'applicantDeleteRelevantFile'])->middleware('auth:sanctum');
Route::post('credentialsUpload/{filekey}/{id}',[ApplicantController::class, 'credentialsUpload'])->middleware('auth:sanctum');
Route::delete('deleteCredential',[ApplicantController::class , 'deleteCredential' ])->middleware('auth:sanctum');
Route::post('submitApplication',[ApplicantController::class, 'submitApplication'])->middleware('auth:sanctum');
Route::post('validateForm', [ApplicantController::class , 'validateForm'])->middleware('auth:sanctum');

Route::post('checkEmailorMobile',[ApplicantController::class, 'checkEmailorMobile']);
//test
// Route::post('/initTransaction',[PaymentController::class , 'initTransaction']);
Route::post('/cR',[PaymentController::class , 'checkRRR']);


//After Admission is approved
Route::post('checkAcceptance', [PaymentHelper::class , 'checkAcceptance'])->middleware('auth:sanctum');
Route::post('checkCaution', [PaymentHelper::class , 'checkCaution'])->middleware('auth:sanctum');
Route::post('applicationPayments',[PaymentHelper::class , 'applicationPayments'])->middleware('auth:sanctum');

// Route::post('pgResendVerificationMail', function(){
//     return 'why now';
// })->middleware('auth:sanctum');

//
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        // 'device_name' => 'required',
    ]);

    $user = Applicant::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token =  $user->createToken('mobile')->plainTextToken;
    return response()->json(['token'=>$token]);
});
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
//verify

Route::get('/check', function(Request $request){
    $user = User::where('email', $request->email);
    $data = $user->markEmailAsVerified();
    return $data;
});

//send mail


Route::post('/email', function(Request $request){
    // Notification::route('mail', 'abayomipaulhenryhill@yahoo.com')
    //         ->notify(new VerifyEmailNotification());
    // SendVerifyEmailJob::dispatchAfterResponse();
    // Mail::to('abayomipaulhenryhill@gmail.com')->send(new VerifyEmail());
    // $job = (new SendVerifyEmailJob())->delay(Carbon::now()->addSeconds(1));
    // dispatch($job);
    // $user = Applicant::find(3);
    // dd($user);
    // $delay = now()->addSeconds(1);
    // Notification::send($user, new VerifyEmailNotification());

    // $user->notify((new VerifyEmailNotification()));
    return 'Email sent';
});
Route::post('/log',function(){
    Log::info('test the log oooo');
});
