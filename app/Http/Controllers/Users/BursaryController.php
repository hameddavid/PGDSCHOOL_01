<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Exports\PaymentExport;
use App\Imports\PaymentImport;
use Maatwebsite\Excel\Facades\Excel;

class BursaryController extends Controller
{


    public function reportCRUD(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'programme_type' => 'required',
            'type' => 'required',
            'startDate' => 'required',
            'endDate' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'all fields are required!'], 401);
        }

        
    }
    

    public function allPayment(Request $request)
    {
        try {
            if ($request->has('type') && $request->filled('type')) {
                $payment = Payment::where('type', 'like', $request->type)->get();
                return response()->json(['msg' => 'success', 'payment' => $payment]);
            } else if ($request->has('programme_type') && $request->filled('programme_type')) {
                $payment = Payment::where('programme_type', 'like', $request->programme_type)->get();
                return response()->json(['msg' => 'success', 'payment' => $payment]);
            } else if ($request->has('type') && $request->filled('type') && $request->has('programme_type') && $request->filled('programme_type')) {
                $payment = Payment::where('programme_type', 'like', $request->programme_type)->where('type', 'like', $request->type)->get();
                return response()->json(['msg' => 'success', 'payment' => $payment]);
            } else {
                $payment = Payment::all();
                return response()->json(['msg' => 'success', 'payment' => $payment]);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Unable to fetch payment', 'th' => $th], 401);
        }
    }

    public function uploadFeeCategories()
    {
        // return request()->file('fee');
        Excel::import(new PaymentImport, request()->file('fee'));

        return response()->json(['success' => 'fee uploaded successfully'], 201);
    }
}
