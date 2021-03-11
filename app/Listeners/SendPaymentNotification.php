<?php

namespace App\Listeners;

use App\Events\RemitaBank;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RemitaBank  $event
     * @return void
     */
    public function handle(RemitaBank $event)
    {
        // $userId = null;
        //transaction_id is the userID due to morph relationship
        $user = strval($event->data['transaction_type'])::find($event->data['transaction_id'])->setHidden(
            ['password',
            'picture',
            'created_at',
            'updated_at',
            'email',
            'mobile',
            'email',
            'email_verified_at'
            ]);
        // Log::info($user);
        $payment = Payment::find($event->data['payment_id'])->setHidden([
            'created_at',
            'updated_at'
        ]);
        $userType = strval($user->getTable());
        $notificationType = 'payment';
        $notification = new Notification();
        $notification->type = $notificationType;
        if ($userType == 'applicants') {
            $notification->applicants = $event->data['transaction_id'];
        }
        if ($userType == 'students') {
            $notification->students = $event->data['transaction_id'];
        }
        $notificationData = [
            'type' => $notificationType,
            'payment' => $payment,
            'user' => $user,
            'userType' => $userType,
            'rrr'=> $event->data['rrr'],
            'amount'=>$event->data['amount']
        ];

        $notification->data = $notificationData;
        $notification->msg = "Payment Confirmed";
        $notification->save();
        Log::info($notification);
    }
}
