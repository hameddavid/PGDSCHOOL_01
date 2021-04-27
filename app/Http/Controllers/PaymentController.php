<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Application;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Users\AdmissionOfficer;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function Application(Request $request)
    {
        // return $request;
        // APPLICATION FEE PER PROGRAM
        $application = Payment::where('type', 'Application')->where('type','APPLICATION')->where('programme_type',$request->programme_type)->first();
        return response()->json(['msg' => 'success', 'payment' => $application]);
    }
    public function paymentApplicationSuccess(Request $request)
    {
        // return $request;
        $request['status'] = 'SUCCESS';
        $application = $this->createApplication($request);
        $this->UpdateTransaction($request);
        DB::table('application_payment')->insert([
            'transactionId' => $request->transactionId,
            'amount' => $request->amount,
            'status' => $request->status,
            'reference' => $request->paymentReference,
            'application_id' => $application->id
        ]);

        return response()->json(['msg' => 'success', 'value' => 'Application Created', 'info'=>'Application Created']);
    }
    public function createApplication(Request $request)
    {
            $user = $request->user();
        $application = new Application();
        $application->status = 'awaiting submission';
        $application->applicant_id = $user->id;
        $application->rrr = $request->paymentReference;
        $application->save();
        $application = Application::find($application->id);
        $val = AdmissionOfficer::settings($request)->session_name;
        $application->application_number = "RUN/CPGS/".substr($val,2,2)."-". substr($val,7,2)."/".$application->id;
        $application->save();


        //aplication refree
        DB::table('application_refree')->insert(['application_id' => $application->id]);

        //application assessment
        DB::table('application_assessment')->insert(['application_id' => $application->id]);

        //application institution
        DB::table('application_institution')->insert(['application_id' => $application->id]);

        //application credentials
        DB::table('application_credentials')->insert(['application_id' => $application->id]);

        //application personaldata
        try {

        $checkPersonalDetails = DB::table('application_personaldata')->where('applicant_id',$user->id)->first();
        if($checkPersonalDetails){

        }else{
            DB::table('application_personaldata')->insert(['application_id' => $application->id, 'applicant_id'=>$user->id]);
        }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th);
        }

        //Application employmenthistory
        DB::table('application_employmenthistory')->insert(['application_id' => $application->id]);
        return $application;
    }
    public function studentInitTransactions(Request $request)
    {
        $orderID = time();
        $payment = null;
        $amount = null;
        foreach ($request->payments as $key => $value) {
            if($key == 0){
                $payment = Payment::find($value['id']);
            }
            $Authuser = $request->user();
            $amount  = $amount + $value['amount'] ;
            // $payment = Payment::find($request->payment);
            $Authuser->transactions()->attach($value['id'], [
                'amount' => $value['amount'],
                'status' => 'pending',
                'details' => $value['type'],
                'orderId'=>$orderID,
                'created_at'=>Carbon::now(),
                // 'semester_name'=>
                // 'session_name'=>
            ]);
        };
        $payment['orderId'] = $orderID;
        $payment['amount']=$amount;
        return response()->json(["value"=>$payment]);


    }
    public function saveStudentRRR(Request $request)
    {
        $transactions = DB::table('transactions')->where('orderId',$request->orderId)->get();
        foreach ($transactions as $key => $value) {
            // return response()->json([$value]);
            DB::table('transactions')->where('id',$value->id)->update([
                'RRR'=>$request->RRR
            ]);
        }
        return response()->json(['info'=>'RRR saved', 'value'=>true]);
    }
    public function studentUpdateTransaction(Request $request)
    {
        $transactions = DB::table('transactions')->where('rrr',$request->rrr)->get();
        foreach ($transactions as $key => $value) {
            DB::table('transactions')->where('id',$value->id)->update([
                'status'=>'SUCCESS'
            ]);
        }
    }
    public function initTransaction(Request $request)
    {
        $check = $this->checkRRR($request);
        if($check){
            return response()->json(['msg'=>'success','type'=>'checkRRR','value'=>$check, 'info'=>'Transaction Initiated']);
        }
        try {
            $Authuser = $request->user();
            $payment = Payment::find($request->payment);
            $Authuser->transactions()->attach($payment, [
                'amount' => $payment->amount,
                'status' => 'pending',
                'details' => $payment->type . '--' . ' ' . $payment->details,
                'orderId'=>time()
            ]);
            $transaction = $Authuser->transactions()->where('payments.id', $payment->id)->select('transactions.*')->latest('id')->first();
            return response()->json(['msg' => 'success', 'value' => $transaction, 'info'=>'Transaction Initiated']);
        } catch (\Throwable $th) {
            return response()->json(['error'=>'Unable to initiate transaction'],401);
        }

    }
    public function UpdateTransaction(Request $request)
    {
        $user = $request->user();
        // $user = Applicant::find(1);
        try {
            $payment = Payment::find($request->paymentId);
        $transaction = $user->transactions()->where('payments.id', $payment->id)->select('transactions.*')->latest('id')->first();
        $updateTransaction = Transaction::find($transaction->id);
        $updateTransaction->status = $request->status;
        $updateTransaction->reference = $request->reference;
        $updateTransaction->transactionId = $request->transactionId;
        if($request->has('amount')){
            $updateTransaction->amount = $request->amount;
        }
        $updateTransaction->save();
        return response()->json(['msg'=>'success','value'=>$updateTransaction]);
        } catch (\Throwable $th) {
            //throw $th;
            Log::info($th);
            return response()->json(['error'=>'Unable to update transaction', 'th'=>$th],401);
        }

    }
    public function checkRRR(Request $request)
    {
        $user = $request->user();
        $payment = Payment::find($request->payment);
        $info = $user->transactions()->where('payments.id',$payment->id)
            ->select('transactions.*')
            ->where('transactions.status','pending')
            ->whereNotNull('transactions.rrr')
            ->first();
            return $info;

    }
    public function addRemitaRRR(Request $request)
    {
       $check = Transaction::where('rrr',$request->rrr)->first();
       if($check){
        return response()->json(['transaction'=>$check, 'msg'=>'success']);
       }
        $user = $request->user();
        $payment = Payment::find($request->paymentId);
        $transaction = $user->transactions()->where('payments.id', $payment->id)->select('transactions.*')->latest('id')->first();
        $updateTransaction = Transaction::find($transaction->id);
        $updateTransaction->rrr = $request->rrr;
        $updateTransaction->save();
        return response()->json(['transaction'=>$updateTransaction, 'msg'=>'success']);

    }
    public function ApplicantPaymentList(Request $request)
    {
        $user = $request->user();
       $payment =  $user->transactions()->select('transactions.*')
       ->whereNotNull('transactions.rrr')
       ->get();
       $payments = array();
       foreach ($payment as $key => $value) {
           $payments[$key] = $payment[$key]->payment_id;
       }
       $UserPaymentCategory = Payment::wherein('id',$payments)->get();
        return response()->json(['msg'=>'success', 'value'=>$UserPaymentCategory]);
    }
    public function paymentHistory(Request $request)
    {
        // return $request;
        $user = $request->user();
        $history = $user->transactions()->select('transactions.*')
            ->whereNotNull('transactions.rrr')
            ->where('transactions.payment_id',$request->id)
            ->get()->take(10);
        return response()->json(['msg'=>'success','value'=>$history]);

    }

    public function remitaCheckRRR(Request $request)
    {
    //     $merchantId= "2547916";
    //   $apiKey= "1946";
    //     $response = Http::withHeaders([
    //         'X-First' => 'foo',
    //         'X-Second' => 'bar'
    //     ])->post('http://remitademo.net/remita/ecomm/'+$merchantId'/'+$checkRRRStatus+'/'+${apiHash}/status.reg', [
    //         'name' => 'Taylor',
    //     ]);
    }
}
