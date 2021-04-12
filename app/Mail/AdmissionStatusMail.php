<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdmissionStatusMail extends Mailable
{

    use Queueable, SerializesModels;

    public $emailParams;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailParams)
    {
        $this->emailParams = $emailParams;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->emailParams['status'] == 'approved')
           {
               return $this->view('emails.admissionApproved' );
            }
        else{
            return $this->view('emails.admissionDeclined');
        }
    }
}
