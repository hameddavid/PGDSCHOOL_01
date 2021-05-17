<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class ApplicantPaymentReports implements FromCollection, WithHeadings
{

    private $session;
    private $type;

    public function __construct($session,$type) 
    {
        $this->session = str_replace('"','',$session);
        $this->type = str_replace('"','',$type);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       
         $report = DB::table('transactions')
        ->select('transactions.id','transactions.rrr','payments.type',
        'transactions.amount','transactions.status','payments.programme_type',
        'applicants.email','applicants.mobile','applicants.surname','applicants.lastname')
        ->join('applicants','transactions.transaction_id','applicants.id')
        ->join('payments','transactions.payment_id','payments.id')
        ->join('applications','applicants.id','applications.applicant_id')
        ->where('payments.type', $this->type)
        ->where('transactions.session_name',$this->session)
        ->whereNotNull('transactions.rrr')
        ->orderBy('transactions.id')->get();

        return $report;
       
    }


    public function headings(): array
    {
        return [
            'ID',
            'RRR',
            'TYPE',
            'AMOUNT',
            'STATUS',
            'PROGRAMME',
            'EMAIL',
            'MOBILE',
            'SURNAME',
            'LAST NAME',
        ];
    }










}
