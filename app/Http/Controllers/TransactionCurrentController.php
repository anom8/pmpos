<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Table;
use App\Models\Promotion;
use App\Library\MyNumber;
use App\Library\MyDate;
use \Mike42\Escpos\Printer;
use \Mike42\Escpos\PrintConnectors\FilePrintConnector;
use \Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use \Mike42\Escpos\EscposImage;
use \Mike42\Escpos\CapabilityProfile;

use File;

class TransactionCurrentController extends Controller
{
    private $pageSize = 10;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'add_transaction']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $table = Table::leftJoin('transaction', function ($join) {
            $join->on('transaction.table_id', '=', 'table.table_id')
                ->where('transaction.status', '=', 'pending')
                ->orderBy('transaction.transaction_id', 'desc');
        })
        ->with('transaction.detail')
        ->groupBy('table.table_id')
        ->orderBy('table.number', 'desc')
        ->get();
        $promotion = Promotion::where(['status', 'active'])->get();
        // $transaction = Transaction::orderBy('transaction_id', 'ASC')->where(['status'=>'pending'])->first();
        // $transaction = [];

        // return view('transaction.list', compact('slide'));
        return view('transaction.list', compact('transaction', 'promotion'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function current(Request $request)
    {
        $id = -1;
        $table = Table::select([
            'table.table_id',
            'number',
            'branch_id',
            'transaction_id',
            'transaction.status'
        ])
        ->leftJoin('transaction', function ($join) {
            $join->on('transaction.table_id', '=', 'table.table_id')
                ->whereIn('transaction.status', ['pending','printbill'])
                ->orderBy('transaction.transaction_id', 'desc');
        })
        // ->with('transaction')
        ->groupBy('table.table_id')
        ->orderBy('table.number', 'asc');

        // var_dump($table->get()->toArray()); exit;

        $count = count($table->get());

        $table_top = $table->limit(ceil($count / 3))->get();
        $table_middle = $table->offset(ceil($count / 3))->get();
        $table_bottom = $table->offset(ceil($count / 3 * 2))->get();

        // var_dump($table_top->toArray()); exit;

        // $transaction = Transaction::orderBy('transaction_id', 'ASC')->where(['status'=>'pending'])->get();
        $transaction = [];
        // $transaction_current = Transaction::with('detail')->orderBy('transaction_id', 'ASC')->where(['status'=>'pending'])->first();
        $transaction_current = Transaction::with('detail')->orderBy('transaction_id', 'ASC')->whereIn('status', ['pending','printbill'])->get();
        $promotion = Promotion::where(['status'=>'active'])->get();
        // var_dump($transaction->detail); exit;
        return view('transaction.current', compact('id', 'transaction', 'transaction_current', 'table', 'table_top', 'table_middle', 'table_bottom', 'promotion'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('transaction.create');
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
            'title' => 'required',
            'url' => 'required|url',
            'image' => 'required|image',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::to('transaction/create')
                ->withInput()
                ->withErrors($validator);
        } else {
            $transaction = new Transaction;

            $transaction->title = $request->title;
            $transaction->desc = $request->desc;
            $transaction->url = $request->url;
            $transaction->status = $request->status;
            $transaction->image = $filename;
            $transaction->expired_at = $expired_at;
            $transaction->type = $request->type;
            if($transaction->save()) {
                // sending back with message
                Session::flash('message', 'Transaction created successfully!');
                return Redirect::to('transaction');
            } else {
                // sending back with error message.
                Session::flash('error', 'Uploaded image is not valid');
                return Redirect::to('transaction/create')
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
    public function show(Request $request, $id)
    {
        $table = Table::select([
            'table.table_id',
            'number',
            'branch_id',
            'transaction.transaction_id',
            'transaction.status'
        ])
        ->leftJoin('transaction', function ($join) {
            $join->on('transaction.table_id', '=', 'table.table_id')
                ->whereIn('transaction.status', ['pending','printbill'])
                ->orderBy('transaction.transaction_id', 'desc');
        })
        // ->with('transaction')
        // ->where('transaction.status', '=', 'pending')
        ->groupBy('table.table_id')
        ->orderBy('table.number', 'asc');

        // echo json_encode($table->get()); exit;

        $count = count($table->get());

        $table_top = $table->limit(ceil($count / 3))->get();
        $table_middle = $table->offset(ceil($count / 3))->get();
        $table_bottom = $table->offset(ceil($count / 3 * 2))->get();

        // $transaction = Transaction::orderBy('transaction_id', 'ASC')->where(['status'=>'pending'])->get();
        $transaction = [];
        if ($id != 0)
            $transaction_current = Transaction::with('detail')->orderBy('transaction_id', 'ASC')->where(['transaction_id'=>$id])->whereIn('status', ['pending','printbill'])->first();
        else
            $transaction_current = Transaction::with('detail')->orderBy('transaction_id', 'ASC')->whereIn('status', ['pending','printbill'])->whereNull('table_id')->get();
        // $promotion = Promotion::where(['status'=>'active'])->get();
        // echo json_encode($table->get()); exit;
        // var_dump($transaction->detail); exit;
        return view('transaction.current', compact('id', 'transaction', 'transaction_current', 'table', 'table_top', 'table_middle','table_bottom', 'promotion'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $transaction = Transaction::where('transaction_id', $id)->first();

        if(isset($transaction->expired_at) && $transaction->expired_at!="") {
            $_ea = date_create_from_format('Y-m-d H:i:s', $transaction->expired_at);
            $transaction->expired_at = date_format($_ea, 'd F Y');
        } else
            $transaction->expired_at = "";

        return view('transaction.edit', compact('transaction'));
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
            'title' => 'required',
            'url' => 'required|url',
            'image' => 'image',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            // $transaction = Transaction::where('transaction_id', $id)->first();
            return Redirect::back()
                ->withInput()
                ->withErrors($validator);
        } else {
            $mp = Transaction::where('transaction_id', $id);
            if($mp->count() > 0) {
                $data = $mp->first();

                if(isset($request->image) && $request->image!=NULL) {

	                // save new image
	                // $sizes = ['large', 'medium', 'small'];
	                // $size_list = ['large'=>1024, 'medium'=>500, 'small'=>350];

	                // $data = $request->all();
	                // $imageName = $request->image->getClientOriginalName();
	                // $image = $request->image->getClientOriginalName();

	                // $filename = sha1(date('YmdHis').pathinfo($imageName, PATHINFO_FILENAME)) . '.' .$request->image->getClientOriginalExtension();

	                // $image_path = $request->image->move(public_path('transaction'), $filename);

	                // $image = Image::make(public_path('transaction') ."/". $filename);
	                // $image_height = $image->height();
	                // $image_width = $image->width();

	                // foreach($sizes as $sz) {
	                //     $path = public_path('transaction/' . $sz . '/' . $filename);
	                //     $img = Image::make(public_path('transaction') ."/". $filename)
	                //             ->resize($size_list[$sz], null, function ($constraint) {
	                //                 $constraint->aspectRatio();
	                //             })->save($path);
	                // }

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
	                $image = $data->image;
	            }

                if(isset($request->expired_at) && $request->expired_at!="") {
                    $_ea = date_create_from_format('d F Y', $request->expired_at);
                    $expired_at = date_format($_ea, 'Y-m-d') . " 23:59:59";
                } else
                    $expired_at = null;

                $mp->update([
                    'title' => $request->title,
                    'desc' => $request->desc,
                    'url' => $request->url,
                    'status' => $request->status,
                    'type' => $request->type,
                    'image' => $image,
                    'expired_at' => $expired_at
                ]);

                Session::flash('message', 'Transaction successfully updated!');
                return Redirect::to('transaction');
            } else {
                Session::flash('message', 'Transaction failed to update');
                return Redirect::to('transaction/edit');
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
        // $transaction_detail = TransactionDetail::where('transaction_id', $id)->get();
        $transaction = Transaction::where('transaction_id', $id)->first();
        $transaction->void_by = auth()->user()->user_id;
        $transaction->status = 'void';
        $transaction->save();

        Session::flash('message', 'Transaction voided successfully!');
        return Redirect::back();
    }

    public function destroyItem($id)
    {
        $item = TransactionDetail::where(['transaction_detail_id'=>$id]);
        $transaction_detail = $item->first();
        $transaction_detail->void_by = auth()->user()->user_id;;
        $transaction_detail->status = 'void';
        return Redirect::back();
    }

    private function updateTransaction($id) {
        $total = 0;
        $transaction = Transaction::where(['transaction_id'=>$id])->first();
        $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
        foreach ($transaction_detail as $td)
            $total += $td->quantity * $td->price;

        $transaction->total = $total;
        $transaction->save();
    }

    public function finishTransaction(Request $request, $id)
    {
        $transaction = Transaction::where(['transaction_id'=>$id, 'status'=>'pending'])->first();
        if ($transaction) {

            // $profile = CapabilityProfile::load("SP2000");
            // $connector = new WindowsPrintConnector("smb://dannyhrnt-pc/struk");
            // $printer = new Printer($connector, $profile);

            // /* Initialize */
            // $printer -> initialize();

            // $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
            // try {
            //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
            //     $logo = EscposImage::load("img/logostruk.png", false);
            //     $printer -> bitImage($logo, Printer::IMG_DEFAULT);
            //     $printer -> feed(2);
            //     $printer -> setJustification(); // Reset
            // } catch (Exception $e) {
            //     /* Images not supported on your PHP, or image file not found */
            //     $printer -> text($e -> getMessage() . "\n");
            // }

            // $trans_id = preg_replace("/[^0-9]/", "", $transaction->created_at);
            // $dt = MyDate::toReadableDate($transaction->created_at, false, true);
            // $table = $transaction->table->number;
            // $printer -> text("Order #: $trans_id \n");
            // $printer -> text("Table  : $table \n");
            // $printer -> text("Kasir  : Admin \n");
            // $printer -> text("Tgl    : $dt");
            // $printer -> feed(1);

            // /* Text */
            // $printer -> text("--------------------------------");
            // $printer -> feed(1);

            // foreach ($transaction_detail as $td) {
            //     $qty = str_pad($td->quantity, (4 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
            //     $name = $td->product->name;
            //     $subt = MyNumber::toReadableAngka($td->subtotal, false);
            //     $subt = str_pad($subt, (30 - (strlen($qty) + 1) - (strlen($subt) + 8)), ' ', STR_PAD_LEFT);
            //     // echo $qty; exit;
            //     // $name = 'Huckleberry';
            //     $printer -> text("$qty  $name  $subt");
            // }

            // $printer -> feed(2);
            // $printer -> cut();
            // try {
            //     $total = MyNumber::toReadableAngka($transaction->total, false);
            //     $printer -> setJustification(Printer::JUSTIFY_RIGHT);
            //     $printer -> text("Total     $total\n");
            //     $printer -> setJustification(); // Reset
            // } catch (Exception $e) {
            //     /* Images not supported on your PHP, or image file not found */
            //     $printer -> text($e -> getMessage() . "\n");
            // }
            
            // $printer -> text("--------------------------------");
            // $printer -> feed(1);
            // $printer -> cut();

            // try {
            //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
            //     $printer -> text("Jl. Kemang Barat No. 117\n");
            //     $printer -> text("Jakarta Selatan\n");
            //     $printer -> text("(021) 8460 485\n");
            //     $printer -> text("www.rvindonesia.com\n\n");
            //     $printer -> text("Terima Kasih\n");
            //     $printer -> setJustification(); // Reset
            //     $printer -> cut();
            // } catch (Exception $e) {
            //     /* Images not supported on your PHP, or image file not found */
            //     $printer -> text($e -> getMessage() . "\n");
            // }

            // $printer -> feed(7);
            // $printer -> cut();
            // $printer -> close();

            // exit;

            $transaction->total = $request->total;
            $transaction->grand_total = $request->grand_total;
            $transaction->promotion_id = $request->promotion_id;
            $transaction->paid = $request->paid;
            $transaction->payable = $request->payable;
            $transaction->status = 'finished';
            $transaction->save();
            Session::flash('message', 'Transaction approved!');
            return Redirect::to('transaction-current');
        } else {
            Session::flash('error', 'Transaction not found!');
            return Redirect::to('transaction-current');
        }
        
    }
}
