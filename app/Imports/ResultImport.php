<?php

namespace App\Imports;

use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ResultImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Log::info($row);
        return $row;
        // return new Payment([
        //    'type' => $row['name'],
        //    'amount' => $row['amount'],
        //    'programme_type' => $row['programme_type'],
        //    'installment' => $row['installment'],
        //    'session' => $row['session']
        // ]);
    }
}
