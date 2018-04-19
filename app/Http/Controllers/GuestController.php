<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Guest;
use Illuminate\Support\Str;
use File;

class GuestController extends Controller
{
    private $pageSize = 10;
    // private $file_path = 'guest';

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
        // $guest = Guest::all();
        $guest = Guest::orderBy('guest_id', 'ASC')->where('guest_id', '!=', 0)->paginate($this->pageSize);

        // return view('guest.list', compact('slide'));
        return view('guest.list', compact('guest'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('guest.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('guest/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $guest = new Guest;
            $guest->name = $request->name;
            $guest->email = $request->email;
            $guest->phone = $request->phone;
            $guest->domicile = $request->domicile;

            if ($guest->save()) {
                // sending back with message
                Session::flash('message', 'Guest created successfully!');
                return Redirect::to('guest');
            }
            else {
                // sending back with error message.
                Session::flash('error', 'Uploaded image is not valid');
                return Redirect::to('guest/create')
                    ->withInput();
            }
        }
    }


    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $guest = Guest::where('guest_id', $id)->first();
        return view('guest.show',compact('guest'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $guest = Guest::where('guest_id', $id)->first();

        if(isset($guest->expired_at) && $guest->expired_at!="") {
            $_ea = date_create_from_format('Y-m-d H:i:s', $guest->expired_at);
            $guest->expired_at = date_format($_ea, 'd F Y');
        } else
            $guest->expired_at = "";

        return view('guest.edit', compact('guest'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
            'name' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            // $guest = Guest::where('guest_id', $id)->first();
            return Redirect::back()
                ->withInput()
                ->withErrors($validator);
        } else {
            $mp = Guest::where('guest_id', $id);
            if($mp->count() > 0) {
                $mp->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'domicile' => $request->domicile
                ]);

                Session::flash('message', 'Guest successfully updated!');
                return Redirect::to('guest');
            } else {
                Session::flash('message', 'Guest failed to update');
                return Redirect::to('guest/edit');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Guest::where('guest_id', $id)->delete();

        Session::flash('message', 'Guest deleted successfully!');
        return Redirect::back();
    }
}
