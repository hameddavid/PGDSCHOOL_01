<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Transaction;
use App\Exports\PaymentExport;
use App\Exports\ApplicantPaymentReports;
use App\Imports\PaymentImport;
use App\Imports\ResultImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Users\AdmissionOfficer;

class BursaryController extends Controller
{


    public function reportCRUD(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'programme_type' => 'required',
            'type' => 'required',
            'startDate' => 'required',
            'endDate' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'all fields are required!'], 401);
        }

        
    }
    

    public function allPayment(Request $request)
    {
        try {
            if ($request->has('type') && $request->filled('type')) {
                $payment = Payment::where('type', 'like', $request->type)->get();
                return response()->json(['msg' => 'success', 'payment' => $payment]);
            } else if ($request->has('programme_type') && $request->filled('programme_type')) {
                $payment = Payment::where('programme_type', 'like', $request->programme_type)->get();
                return response()->json(['msg' => 'success', 'payment' => $payment]);
            } else if ($request->has('type') && $request->filled('type') && $request->has('programme_type') && $request->filled('programme_type')) {
                $payment = Payment::where('programme_type', 'like', $request->programme_type)->where('type', 'like', $request->type)->get();
                return response()->json(['msg' => 'success', 'payment' => $payment]);
            } else {
                $payment = Payment::all();
                return response()->json(['msg' => 'success', 'payment' => $payment]);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Unable to fetch payment', 'th' => $th], 401);
        }
    }

    public function uploadFeeCategories(Request $request)
    {
        $validator = Validator::make(
            [ 'fee'      => $request->fee,
                'extension' => strtolower($request->fee->getClientOriginalExtension())],
            [ 'fee'          => 'required',
                'extension'      => 'required|in:csv,xlsx,xls' ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => 'select proper excel file to import'], 401);
        }
        Excel::import(new PaymentImport, request()->file('fee'));

        return response()->json(['success' => 'fee uploaded successfully'], 201);
    }


    public function uploadResult(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'result' => 'required']);
        if ($validator->fails()) {
            return response()->json(['error' => 'select proper excel file to import'], 401);
        }
       $data =  Excel::toArray(new ResultImport, request()->file('result'));
        dd($data[0]);
        return response()->json(['success' => 'fee uploaded successfully'], 201);
    }


    public function all_applicant_fee_paid(Request $request)
    {
        $semester = null;
        $session = null;
        $transaction = null;
        if ($request->semester == null)
        
        {$semester = AdmissionOfficer::settings($request)->semester_name;}else{$semester=$request->semester;}
        if ($request->session == null)
        {$session = AdmissionOfficer::settings($request)->session_name;}else{$session=$request->session;}
        if($request->has('applicationFee') && $request->filled('applicationFee')){
            $transaction = $this->sql_all_applicant_fee_paid('APPLICATION',$semester,$session,$degree=$request->degree);
        }
        else if($request->has('acceptanceFee') && $request->filled('acceptanceFee')){
           
            $transaction = $this->sql_all_applicant_fee_paid('ACCEPTANCE',$semester,$session,$degree=$request->degree);
        }
        else if($request->has('mcFee') && $request->filled('mcFee')){ 
            $transaction = $this->sql_all_applicant_fee_paid('CAUTION',$semester,$session,$degree=$request->degree);

        }
        else if($request->has('application_number') && $request->filled('application_number')){ 
            $transaction =  DB::table('transactions')
            ->select('transactions.rrr','payments.type','transactions.amount','transactions.status','payments.programme_type',
            'applicants.email','applicants.mobile','applicants.surname','applicants.lastname')
            ->join('applicants','transactions.transaction_id','applicants.id')
            ->join('payments','transactions.payment_id','payments.id')
            ->join('applications','applicants.id','applications.applicant_id')
            ->where('applications.application_number',$request->application_number)
            ->whereNotNull('transactions.rrr') 
            ->orderBy('transactions.created_at')->get();
        }
        else{
            $transaction = $this->sql_all_applicant_fee_paid(null,$semester,$session,$degree=$request->degree);

        }
        return response()->json(['msg' => 'success', 'transaction' => $transaction]);


    }




    public function sql_all_applicant_fee_paid($param1,$semester,$session,$degree)
    {
        try {

            if($param1 == null){
                return  DB::table('transactions')
                ->select('transactions.rrr','payments.type','transactions.amount','transactions.status','payments.programme_type',
                'applicants.email','applicants.mobile','applicants.surname','applicants.lastname')
                ->join('applicants','transactions.transaction_id','applicants.id')
                ->join('payments','transactions.payment_id','payments.id')
                ->where('transactions.semester_name',$semester)
                ->where('transactions.session_name',$session)
                ->whereNotNull('transactions.rrr') 
                ->when($degree, function($query) use($degree){
                    $query->where('payments.programme_type', $degree);
                })
                ->orderBy('transactions.created_at')->get();
            }
    
            
          return  DB::table('transactions')
            ->select('transactions.rrr','payments.type','transactions.amount','transactions.status','payments.programme_type',
            'applicants.email','applicants.mobile','applicants.surname','applicants.lastname')
            ->join('applicants','transactions.transaction_id','applicants.id')
            ->join('payments','transactions.payment_id','payments.id')
            ->where('payments.type', $param1)->where('transactions.semester_name',$semester)
            ->where('transactions.session_name',$session)->whereNotNull('transactions.rrr')
            ->orderBy('transactions.created_at')
            ->when($degree, function($query) use($degree){
                $query->where('payments.programme_type', $degree);
            })
            ->orderBy('transactions.created_at')->get();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }



    public function applicants_payments_reports(Request $request){

        $validator = Validator::make($request->all(), [
            'session' => 'required',
            'type'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'session/type is required'], 401);
        }

        $check_session = str_replace('/','_',$request->session);
       return Excel::download(new ApplicantPaymentReports($request->session, $request->type), "{$check_session}_{$request->type}_REPORT.xlsx");

    }






















}
