<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdmissionStatusMail;

class AdmissionStatusMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $applicants;
    protected $prog;
    protected $application;
    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->applicants->email)->send(new AdmissionStatusMail());
    }
}
