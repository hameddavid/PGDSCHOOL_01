<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    //
    public function PaymentNotification($data)
    {
        $type = $data['type'];
        $user = $data['user'];
        $student = $data['student'];
        $applicant = $data['applicant'];
        $msg = $data['msg'];

    }
    public function getNotifications(Request $request)
    {
        $user  = $request->user();
        $userType = $user->getTable();
        // return ;
        $notifications = Notification::where(strval($userType) , $user->id)->where('activated',false)->get();
        return response()->json(['msg'=>'success', 'notifications'=>$notifications]);
    }
    public function paymentActivate(Request $request)
    {
        Log::info($request);
        // return true;

        $request['notificationId'] = $request->notification['id'];
       if ($request->notification['data']['userType'] == 'applicants') {
            try {
                $request['paymentReference']  = $request->notification['data']['rrr'];
                $request['paymentId'] = $request->notification['data']['payment']['id'];
                $request['amount'] = $request->notification['data']['amount'];
                unset($request['notification']);
                $payment = Payment::find($request['paymentId']);
                if ($payment->type == 'APPLICATION') {
                    $this->activateApplication($request);
                }

                // app('App\Http\Controllers\PaymentController')->paymentApplicationSuccess($request);

                $this->notificationRead($request);

                return response()->json(['msg' => 'success', 'value' => 'Application Created', 'info'=>'Application Created']);
            } catch (\Throwable $th) {
                //throw $th;
                Log::error($th);
            }
       }
    }
    public function notificationRead(Request $request)
    {
        $notification = Notification::find($request->notificationId);
        // return $notification;
        $notification->read_at = Carbon::now();
        $notification->activated = true;
        $notification->save();
    }

    public function activateApplication(Request $request)
    {
        app('App\Http\Controllers\PaymentController')->paymentApplicationSuccess($request);
    }
}
