<?php

namespace App\Http\Controllers;

use App\Events\RemitaBank;
use App\Models\Payment;
use App\Http\Controllers\PaymentController;
use App\Models\Transaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class RemitaController extends Controller
{
    public function updateTransaction(Request $request)
    {
        $updateTransaction = Transaction::where('rrr', $request->paymentReference)->first();
        $updateTransaction->status = "SUCCESS";
        $updateTransaction->reference = $request->paymentReference;
        $updateTransaction->transactionId = $request->transactionId;
        $updateTransaction->amount = $request->amount;
        $updateTransaction->save();

        //check if caution is paid
        $payment = Payment::find($updateTransaction->payment_id);
        if ($payment->type == 'CAUTION') {
            if($updateTransaction->status == 'success' || $updateTransaction->status == 'SUCCESS'){
                $request['applicant'] = $request->user()->id;
                return app('App\Http\Controllers\StudentController')->makeApplicantStudent($request);
            }
        }
    }
    public function checkRRRStatus(Request $request)
    {
        ini_set('max_execution_time', 0);
        $merchantId = "4161150426";
        $apiKey = "258341";
        $rrr = $request->rrr;
        try {
            $apiHash = hash('sha512', $rrr . $apiKey . $merchantId);
            $client = new Client();
            $response = $client->request('GET', 'https://login.remita.net/remita/ecomm/' . $merchantId . '/' . $rrr . "/" . $apiHash . '/status.reg', []);
            $data = $response->getBody();
            return $data;
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['error' => 'Unable to check payment status', 'msg' => $th], 401);
        }
        return $data;
    }
    public function updatePaymentStatus(Request $request)
    {
        //validate
        if ($request->payment['rrr']) {
        } else {
            return response()->json(['error' => 'Request does not contain rrr'], 401);
        }
        $checkRRR = Transaction::where('rrr', $request->payment['rrr'])->first();
        if (!$checkRRR) {
            return response()->json(['error' => 'RRR is not avalible in our transaction records'], 404);
        }
        if ($request->status == 'SUCCESS') {
            $transaction = Transaction::where('rrr', $request->payment['rrr'])->first();
            // return $transaction;
            if ($transaction->status != 'success') {
                $transaction->status = $request->status;
                $payment_id = $transaction->payment_id;
                // check if it application
                $payment = Payment::find($payment_id);
                if ($payment->type == 'APPLICATION') {
                    //check if it is application fee
                    // if ($payment->details == 'APPLICATION CPGS') {
                    try {
                        $request['amount'] = $request->remitaResponse['amount'];
                        $request['paymentReference'] = $request->payment['rrr'];
                        $request['reference'] = $request->payment['rrr'];
                        //old
                        // $request['paymentId'] = $request->payment['payment_id'];
                        $request['paymentId'] = $transaction->payment_id;

                        $request['transactionId'] = NULL;
                        app('App\Http\Controllers\PaymentController')->paymentApplicationSuccess($request);
                    } catch (\Throwable $th) {
                        throw $th;
                        return response()->json(['error' => 'Network error refresh the page', 'th' => $th], 401);
                    }
                    // PaymentController->paymentApplicationSuccess($request);
                    // }
                }
                $transaction->updated_at = $request->remitaResponse['transactiontime'];
                $transaction->save();
            }


            return response()->json(['msg' => 'success', 'value' => 'Payment Updated']);
        } else {
            $transaction = Transaction::where('rrr', $request->payment['rrr'])->first();
            $transaction->status = $request->status;
            $transaction->save();
            return response()->json(['msg' => 'success', 'value' => 'Payment Updated']);
        }
    }
    public function testEndPoint(Request $request)
    {
        // Log::info($request);
        $data =  $request->getContent();
        $getRRR = stristr($data, "rrr");
        $findCommaRRR = strpos($getRRR, ",");
        $RRRsingleString = substr($getRRR, 0, $findCommaRRR);
        $findRRRString = explode('"', $RRRsingleString);
        $RRRvalue = $findRRRString[2];

        $getAmount = stristr($data, "amount");
        $amountComma = strpos($getAmount, ',');
        $amountsingleString = substr($getAmount, 0, $amountComma);
        $amountNew = explode(':', $amountsingleString);
        $amountValue = $amountNew[1];

        $td = stristr($data, "transactiondate");
        $tdcomma = strpos($td, ',');
        $tdfinal = substr($td, 0, $tdcomma);
        $tdnew = explode('"', $tdfinal);
        $tdvalue = $tdnew[2];

        $status = 'SUCCESS';
        $request = new Request([
            'status' => $status,
            'payment' => ['rrr' => $RRRvalue],
            'remitaResponse' => [
                'amount' => $amountValue,
                'transactiontime' => $tdvalue
            ],
        ]);

        $checkRRR = Transaction::where('rrr', $request->payment['rrr'])->first();
        if (!$checkRRR) {
            return response()->json(['error' => 'RRR is not available in our transaction records'], 404);
        }
        $transaction = Transaction::where('rrr', $request->payment['rrr'])->get();
        if(count($transaction) > 1){
            foreach ($transaction as $key => $value) {
                DB::table('transactions')->where('id',$value->id)->update([
                    'status'=>$request->status
                ]);
            }
        }else{
        $transaction = Transaction::where('rrr', $request->payment['rrr'])->first();
            $transaction->status = $request->status;
            $transaction->amount = $amountValue;
            $transaction->save();
        }
        // $checkRRR[]
        event(new RemitaBank($transaction));
        return "OK";
    }
}
