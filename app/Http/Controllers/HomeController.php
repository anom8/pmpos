<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\OpeningBalance;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $date = date('Y-m-d');
        $opening_balance = OpeningBalance::where(['date'=>$date, 'user_id'=>auth()->user()->user_id])->orderBy('open_at', 'desc');
        if ($opening_balance->count() > 0) {
            $ob = $opening_balance->first();
        } else {
            $ob = null;
        }

        return view('home', compact('ob'));
    }

    public function openBalance(Request $request) {
        $opening_balance = new OpeningBalance;
        $opening_balance->user_id = auth()->user()->user_id;
        $opening_balance->balance = $request->balance;
        $opening_balance->date = date('Y-m-d');
        $opening_balance->open_at = date('Y-m-d H:i:s');
        $opening_balance->save();
        return Redirect::back();
    }

    public function closeBalance(Request $request) {
        $date = date('Y-m-d');
        $opening_balance = OpeningBalance::where(['date'=>$date]);
        if ($opening_balance->count() > 0) {
            $ob = $opening_balance->first();
            $ob->close_at = date('Y-m-d H:i:s');
            $ob->save();
        }
        return Redirect::back();
    }
}
