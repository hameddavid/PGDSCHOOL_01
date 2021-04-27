<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Helper\PaymentHelper;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Users\AdmissionOfficer;
use App\Http\Controllers\Users\BursaryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::any("paymentTypes",[PaymentHelper::class , 'paymentTypes']);
Route::any('/upload/fee/categories', [BursaryController::class , 'uploadFeeCategories']);

Route::post('/all/applicants/payments', [BursaryController::class , 'all_applicant_fee_paid']);

Route::any('/settings', [AdmissionOfficer::class , 'settings']);


Route::any('/fetch/applicants/per/dept/coord', [AdmissionOfficer::class , 'fetch_applicants_per_dept_for_coord']);


Route::any('/getApplicants', [AdmissionOfficer::class , 'getApplicants']);
Route::any('/getApplications', [AdmissionOfficer::class , 'getApplications']);
Route::any('/getForms', [AdmissionOfficer::class , 'getForms']);

Route::any('/downloadFile', [AuthController::class , 'downloadFile']);
Route::any('/importProgrammes', [AdminController::class , 'importProgrammes']);
Route::any('/getProgrammeForApprove', [AdmissionOfficer::class , 'getProgrammeForApprove']);
Route::any('adminAuth' , [AuthController::class , 'adminLogin']);
Route::any('auto/login/staff' , [AuthController::class , 'auto_login_staff']);
Route::any('/admissionApproved', [AdmissionOfficer::class , 'admissionApproved']);
Route::any('/pgcoord/adms/recommendation', [AdmissionOfficer::class , 'pg_coord_adms_recommendation_action']);
Route::any('/pgcoord/approved/recommendation/list', [AdmissionOfficer::class , 'pg_coord_approved_recommendation_list']);
Route::any('/pgcoord/disapproved/recommendation/list', [AdmissionOfficer::class , 'pg_coord_disapproved_recommendation_list']);
Route::any('/admissionDenied', [AdmissionOfficer::class, 'admissionDenied']);


Route::any('/get/pg/coords/for/hod', [AdmissionOfficer::class, 'get_pg_coord_in_this_dept_giving_deptName']);
Route::any('/enable/or/disable/pg/coords', [AdmissionOfficer::class, 'enable_disable_pg_coords']);

Route::any('/settings', [AdmissionOfficer::class, 'settings2']);






Route::any('logout' , [AuthController::class , 'admin_logout'])->middleware('auth:sanctum');



Route::any('/all/payment', [BursaryController::class, 'allPayment']);
// Route::any('test', [StudentController::class , 'makeApplicantStudent']);
