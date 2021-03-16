<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Helper\PaymentHelper;
use App\Http\Controllers\Users\AdmissionOfficer;
use App\Http\Controllers\Users\BursaryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::any("paymentTypes",[PaymentHelper::class , 'paymentTypes']);
Route::any('/upload/fee/categories', [BursaryController::class , 'uploadFeeCategories']);

Route::any('/settings', [AdmissionOfficer::class , 'settings']);


Route::any('/getApplicants', [AdmissionOfficer::class , 'getApplicants']);
Route::any('/getApplications', [AdmissionOfficer::class , 'getApplications']);
Route::any('/getForms', [AdmissionOfficer::class , 'getForms']);

Route::any('/downloadFile', [AuthController::class , 'downloadFile']);
Route::any('/importProgrammes', [AdminController::class , 'importProgrammes']);
Route::any('/getProgrammeForApprove', [AdmissionOfficer::class , 'getProgrammeForApprove']);
Route::any('adminAuth' , [AuthController::class , 'adminLogin']);
Route::any('/admissionApproved', [AdmissionOfficer::class , 'admissionApproved']);
Route::any('/admissionDenied', [AdmissionOfficer::class, 'admissionDenied']);



Route::any('/all/payment', [BursaryController::class, 'allPayment']);
