<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\CategoryType;

class CategoryController extends Controller
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
        // $cat = CategoryType::all();
        $cat = CategoryType::orderBy('category_name', 'ASC')->paginate($this->pageSize);

        // return view('category.list', compact('slide'));
        return view('category.list',compact('cat'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.create');
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
            'category_name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('category/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $cat = new CategoryType;
            $cat->category_name = $request->category_name;
            $cat->save();

            // sending back with message
            Session::flash('message', 'Category created successfully!');
            return Redirect::to('category');
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
        $cat = CategoryType::where('id_cat', $id)->first();
        return view('category.show',compact('cat'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cat = CategoryType::where('id_cat', $id)->first();
        return view('category.edit', compact('cat'));
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
            'category_name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            $cat = CategoryType::where('id_cat', $id)->first();
            return Redirect::back()
                ->withErrors($validator);
        } else {
            $mp = CategoryType::where('id_cat', $id);
            if($mp->count() > 0) {
                $mp->update([
                    'category_name' => $request->category_name,
                ]);

                Session::flash('success', 'Category successfully updated');
                return Redirect::to('category');
            } else {
                Session::flash('error', 'Category failed to update');
                return Redirect::to('category/edit');
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
        CategoryType::where('id_cat', $id)->delete();

        Session::flash('message', 'Category deleted successfully!');
        return Redirect::back();
    }
}
