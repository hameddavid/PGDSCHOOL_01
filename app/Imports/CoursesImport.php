<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class CoursesImport implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $data = [];

        DB::table('courses')->upsert(
            
        )
        //foreach($collection as $row ){
            // $data[] = array(
            //     "matric" => $row[0],
            //     "Name" => $row[1],
            //     "Details" => $row[2]
            // );

       // }
        
    }
}
