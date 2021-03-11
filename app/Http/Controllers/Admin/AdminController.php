<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use App\Models\Programme;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class AdminController extends Controller
{
    public function importProgrammes(Request $request)
    {

        $file = $request->file('file')->getRealPath();
        $collecton = (new FastExcel)->import($file);
        // $collecton = (new FastExcel)->import($file);
        foreach ($collecton as $key => $value) {
            $college = College::where('college' , $collecton[$key]['college'])->first();
            if(!$college){
                $college = new College;
                $college->college = $collecton[$key]['college'];
                $college->save();
            }
            $department = Department::where('department', $collecton[$key]['department'])->first();
            if(!$department){
                $department = new Department;
                $department->department = $collecton[$key]['department'];
                $department->college_id = $college->id;
                $department->save();
            }
            $programme = Programme::where('programme',$collecton[$key]['college'])->first();
            if(!$programme){
                $programme = new Programme;
                $programme->programme = $collecton[$key]['programme'];
                $programme->department_id = $department->id;
                $programme->save();
            }
        }
        return 'done';
    }
}
