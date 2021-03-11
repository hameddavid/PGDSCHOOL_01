<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdmissionStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicants;
    public $prog;
    public $application;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($applicants, $prog, $application)
    {
        $this->applicants = $applicants;
        $this->prog = $prog;
        $this->application = $application;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->application->status == 'approved')
           {return $this->view('email.admissionApproved');} 
        else{
            return $this->view('email.admissionDeclined');  
        }
    }
}
