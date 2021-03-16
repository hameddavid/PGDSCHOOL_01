<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentHelper extends Controller
{
    public function checkAcceptance(Request $request)
    {
        $application = Application::find($request->applicationId);
        $transaction = Transaction::where('rrr', $application->rrr)->first();
        $payment = Payment::find($transaction->payment_id);
        $acceptance = Payment::where('programme_type', $payment->programme_type)
            ->where('type', 'ACCEPTANCE')->first();
        $user = $request->user();
        $check =null;
        try {
            if($application->payments['ACCEPTANCE']){
                $check = $user->transactions()->select('transactions.id AS transaction',
                'transactions.transaction_type', 'transactions.transaction_id','transactions.payment_id AS id', 'transactions.status','transactions.amount','transactions.details',
                'transactions.reference','transactions.transactionId','transactions.rrr','transactions.orderId')
                ->where('transactions.rrr', $application->payments['ACCEPTANCE'])->first();
                return response()->json(['msg' => 'success', 'acceptance' => $check ]);

            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        // $check = $user->transactions()->where('payments.id', $acceptance->id)->select('transactions.id AS transaction',
        // 'transactions.transaction_type', 'transactions.transaction_id','transactions.payment_id AS id', 'transactions.status','transactions.amount','transactions.details',
        // 'transactions.reference','transactions.transactionId','transactions.rrr','transactions.orderId')
        //     ->whereNotNull('transactions.rrr')->first();
        // if ($check) {
        //     return response()->json(['msg' => 'success', 'acceptance' => $check ]);
        // }
        return response()->json(['acceptance' => $acceptance, 'msg' => 'success']);
    }
    public function checkCaution(Request $request)
    {
        $application = Application::find($request->applicationId);
        $transaction = Transaction::where('rrr', $application->rrr)->first();
        $payment = Payment::find($transaction->payment_id);
        $caution = Payment::where('programme_type', $payment->programme_type)
            ->where('type', 'CAUTION')->first();
        $user = $request->user();
        $check =null;
        try {
            if($application->payments['CAUTION']){
                $check = $user->transactions()->select('transactions.id AS transaction',
                'transactions.transaction_type', 'transactions.transaction_id','transactions.payment_id AS id', 'transactions.status','transactions.amount','transactions.details',
                'transactions.reference','transactions.transactionId','transactions.rrr','transactions.orderId')
                ->where('transactions.rrr', $application->payments['CAUTION'])->first();
                    return response()->json(['msg' => 'success', 'caution' => $check ]);

            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        // $check = $user->transactions()->where('payments.id', $caution->id)->select('transactions.id AS transaction',
        // 'transactions.transaction_type', 'transactions.transaction_id','transactions.payment_id AS id', 'transactions.status','transactions.amount','transactions.details',
        // 'transactions.reference','transactions.transactionId','transactions.rrr','transactions.orderId')
        // ->whereNotNull('transactions.rrr')->first();
        // if ($check) {
        //     return response()->json(['msg' => 'success', 'caution' => $check ]);
        // }
        return response()->json(['caution' => $caution, 'msg' => 'success']);
    }
    public function applicationPayments(Request $request)
    {
        // $payment = array();
        // $payment[$request->type] = $request->rrr;
        // $user = $request->user();
        $application = Application::find($request->applicationId);
        $payments = $application->payments;
        unset($payments[$request->payment['type']]);
        $payments[$request->payment['type']] = $request->rrr;
        $application->payments = $payments;
        Log::info($payments);
        $application->save();
        return response('saved');
    }

    public function getProgrammeTypesForApplication()
    {
        $types = Payment::where('type','APPLICATION')->get('programme_type');
        return response()->json(['types'=>$types,'msg'=>'success']);
    }

    public function paymentTypes(Request $request)
    {
        $types = Payment::where('programme_type', $request->programme_type)->get('type');
        return response()->json(['types'=>$types , 'msg'=>'success']);
    }

    public function getFeeType(Request $request)
    {
        //get transactions to check if paid for completely
        $payment = Payment::where('status','student')->where('optional',$request->optional)->get();
        return response()->json(['msg'=>'success', 'payment'=>$payment]);
    }
}
