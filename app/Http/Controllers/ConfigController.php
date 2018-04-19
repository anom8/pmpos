<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Berkayk\OneSignal\OneSignalFacade as OneSignal;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Config;

class ConfigController extends Controller
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
        // $config = Config::all();
        $config = Config::orderBy('id_config', 'ASC')->paginate($this->pageSize);

        // return view('config.list', compact('slide'));
        return view('config.list',compact('config'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('config.create');
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
            'code' => 'required',
            'name' => 'required',
            'value' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('config/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $config = new Config;
            $config->code = $request->code;
            $config->name = $request->name;
            $config->value = $request->value;
            $config->save();

            // sending back with message
            Session::flash('message', 'Config created successfully!');
            return Redirect::to('config');
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
        $config = Config::where('id_config', $id)->first();
        return view('config.show',compact('config'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config = Config::where('id_config', $id)->first();
        return view('config.edit', compact('config'));
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
            'name' => 'required',
            'value' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            $config = Config::where('id_config', $id)->first();
            return Redirect::back()
                ->withErrors($validator);
        } else {
            $mp = Config::where('id_config', $id);
            if($mp->count() > 0) {
                $mp->update([
                    'name' => $request->name,
                    'value' => $request->value,
                ]);

                Session::flash('success', 'Config successfully updated');
                return Redirect::to('config');
            } else {
                Session::flash('error', 'Config failed to update');
                return Redirect::to('config/edit');
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
        Config::where('id_config', $id)->delete();

        Session::flash('message', 'Config deleted successfully!');
        return Redirect::back();
    }
}
