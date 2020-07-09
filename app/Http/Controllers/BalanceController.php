<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Balance;
use Auth;

class BalanceController extends Controller
{
    //

    public function index(){

        $balance = Balance::where('user_id',Auth::user()->id)->first();
        return response()->json(compact('balance'),200);

    }

}
