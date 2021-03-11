<?php

namespace App\Http\Controllers;

use App\Models\Programme;
use Illuminate\Http\Request;

class ProgrammesController extends Controller
{
    public function getProgrammes(Request $request)
    {
        $programmes = Programme::all();
        return response()->json(['programmes'=>$programmes]);
    }
    public function getProgramme(Request $request)
    {
        $programme = Programme::find($request->id);
        return response()->json(['programme'=>$programme]);
    }
}
