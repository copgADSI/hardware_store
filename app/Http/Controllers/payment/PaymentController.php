<?php

namespace App\Http\Controllers\payment;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function poorPayment(Request $request)
    {
        try {
            $request->validate([
                'number' => 'required|numeric',
                'cvv' => 'required|numeric',
                'expiration_date' => 'required',
                'name_on_card' => 'required',
                'phone' => 'required'
            ]);
            return response()->json($request->all());
        } catch (Exception $ex) {
            return response()->json(['response' => 'error'], 500);
        }
    }
}
