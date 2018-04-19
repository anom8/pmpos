<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Printer;
use Illuminate\Support\Str;
use File;

class ProductController extends Controller
{
    private $pageSize = 10;
    private $file_path = 'product';

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
        // $product = Product::all();
        $product = Product::orderBy('product_id', 'ASC')->paginate($this->pageSize);

        // return view('product.list', compact('slide'));
        return view('product.list', compact('product'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sub_category = SubCategory::pluck('name', 'sub_category_id');
        $printer = Printer::pluck('name', 'printer_id');
        $printer->prepend('-', null);
        return view('product.create', compact('sub_category', 'printer'));
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
            'name' => 'required',
            'price' => 'required',
            'image' => 'image',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('product/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $product = new Product;
            if (Input::has('image') && Input::file('image')->isValid()) {

                $destinationPath = env('UPLOAD_PATH') . $this->file_path; // upload path
                if(!is_dir($destinationPath))
                    File::makeDirectory($destinationPath, $mode = 0777, true, true);
                $filename = sha1(pathinfo(Input::file('image')->getClientOriginalName(), PATHINFO_FILENAME) . date('YmdHis')) .'.'. Input::file('image')->getClientOriginalExtension();
                $avatar_path = Input::file('image')->move($destinationPath, $filename); // uploading file to given path

                $sizes = ['large', 'medium', 'small'];
                $size_list = ['large'=>800, 'medium'=>350, 'small'=>150];

                foreach($sizes as $sz) {
                    $path = $destinationPath .'/'. $sz .'/'. $filename;
                    if(!is_dir($destinationPath .'/'. $sz))
                        File::makeDirectory($destinationPath .'/'. $sz, $mode = 0777, true, true);
                    $img = Image::make($destinationPath .'/'. $filename)
                            ->resize($size_list[$sz], null, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save($path);
                }
                $product->image = $filename;

                // sending back with message
                Session::flash('message', 'Product created successfully!');
                return Redirect::to('product');
            }

            $product->branch_id = 1;                                //TODO
            $product->printer_id = $request->printer_id;
            $product->name = $request->name;
            $product->rfid_code = $request->rfid_code;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->price_gojek = $request->price_gojek;
            $product->sub_category_id = $request->sub_category_id;
            if ($product->save()) {
                Session::flash('message', 'Product created successfully!');
                return Redirect::to('product');
            } else {
                Session::flash('error', 'Product failed to create!');
                return Redirect::back();
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
        $product = Product::where('product_id', $id)->first();
        return view('product.show',compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::where('product_id', $id)->first();
        $sub_category = SubCategory::pluck('name', 'sub_category_id');
        $printer = Printer::pluck('name', 'printer_id');
        $printer->prepend('-', null);
        return view('product.edit', compact('product', 'sub_category', 'printer'));
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
            'price' => 'required',
            'image' => 'image',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            // $product = Product::where('product_id', $id)->first();
            return Redirect::back()
                ->withInput()
                ->withErrors($validator);
        } else {
            $mp = Product::where('product_id', $id);
            if($mp->count() > 0) {
                $data = $mp->first();

                if(isset($request->image) && $request->image!=NULL) {

                    $destinationPath = env('UPLOAD_PATH', '/usr/share/nginx/html/cdn/shortir/media/') . $this->file_path; // upload path
                    if(!is_dir($destinationPath))
                        File::makeDirectory($destinationPath, $mode = 0777, true, true);
                    $filename = sha1(pathinfo(Input::file('image')->getClientOriginalName(), PATHINFO_FILENAME) . date('YmdHis')) .'.'. Input::file('image')->getClientOriginalExtension();
                    $avatar_path = Input::file('image')->move($destinationPath, $filename); // uploading file to given path

                    $sizes = ['large', 'medium', 'small'];
                    $size_list = ['large'=>800, 'medium'=>350, 'small'=>150];

                    foreach($sizes as $sz) {
                        $path = $destinationPath .'/'. $sz .'/'. $filename;
                        if(!is_dir($destinationPath .'/'. $sz))
                            File::makeDirectory($destinationPath .'/'. $sz, $mode = 0777, true, true);
                        $img = Image::make($destinationPath .'/'. $filename)
                                ->resize($size_list[$sz], null, function ($constraint) {
                                    $constraint->aspectRatio();
                                })->save($path);
                    }

	                $image = $filename;
	            } else {
                    // $image = $data->image;
	                $image = null;
	            }

                $mp->update([
                    'name' => $request->name,
                    'branch_id' => 1,                       //TODO
                    'printer_id' => $request->printer_id,                       //TODO
                    'sub_category_id' => $request->sub_category_id,
                    'rfid_code' => $request->rfid_code,
                    'description' => $request->description,
                    'price' => $request->price,
                    'price_gojek' => $request->price_gojek,
                    // 'image' => $image
                ]);

                Session::flash('message', 'Product successfully updated!');
                return Redirect::to('product');
            } else {
                Session::flash('message', 'Product failed to update');
                return Redirect::back();
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
        Product::where('product_id', $id)->delete();

        Session::flash('message', 'Product deleted successfully!');
        return Redirect::back();
    }
}
