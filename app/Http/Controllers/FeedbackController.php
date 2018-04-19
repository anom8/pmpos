<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Feedback;
use App\FeedbackRedeem;
use App\UsrFeedback;

class FeedbackController extends Controller
{
    private $pageSize = 10;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $feedback = Feedback::all();
        $feedback = Feedback::orderBy('id_feedback', 'DESC')->paginate($this->pageSize);

        // return view('feedback.list', compact('slide'));
        return view('feedback.list',compact('feedback'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Feedback::where('id_feedback', $id)->delete();

        Session::flash('message', 'Feedback deleted successfully!');
        return Redirect::back();
    }
}
