<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Guest;
use App\Library\MyNumber;
use App\Library\MyDate;
use \Mike42\Escpos\Printer;
use \Mike42\Escpos\PrintConnectors\FilePrintConnector;
use \Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use \Mike42\Escpos\EscposImage;
use \Mike42\Escpos\CapabilityProfile;
use stdClass;
use File;

class TransactionHistoryController extends Controller
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
        $total_transaction = new stdClass;
        $total_transaction->total = Transaction::count();
        $total_transaction->pending = Transaction::where('status', '=', 'pending')->count();
        $total_transaction->finished = Transaction::where('status', '=', 'finished')->count();
        // $transaction = Transaction::all();
        DB::statement(DB::raw('set @rownum=0'));
        $_transaction = Transaction::select([
                    DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                    'transaction.*',
                ])
                ->orderBy('transaction_id', 'DESC');
        
        if(isset($_GET['sid'])) {
            if(isset($_GET['search']) && $_GET['search']!="") {
                $_transaction->where([
                    ['status', '=', $_GET['sid']],
                    ['transaction.created_at', 'like', '%'.$_GET['search'].'%'],
                ])->orWhere([
                    ['status', '=', $_GET['sid']],
                ]);
            } else {
                $_transaction->where([
                    ['status', '=', $_GET['sid']],
                ]);
            }
        } else {
            if(isset($_GET['search']) && $_GET['search']!="") {
                $_transaction->where([
                    ['transaction.created_at', 'like', '%'.$_GET['search'].'%'],
                ]);
            }
        }

        $transaction = $_transaction->paginate($this->pageSize);
        // dd($_transaction->toSql()); exit;

        return view('transaction.history', compact('transaction', 'total_transaction'))
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
        $transaction = Transaction::orderBy('transaction_id', 'ASC')->where(['transaction_id'=>$id])->first();
        // var_dump($transaction->detail); exit;
        return view('transaction.history-detail', compact('transaction'))
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
        $transaction_detail = TransactionDetail::where('transaction_id', $id)->delete();
        $transaction = Transaction::where('transaction_id', $id)->delete();

        Session::flash('message', 'Transaction deleted successfully!');
        return Redirect::to('transaction-history');
    }

    public function printTransaction(Request $request, $id)
    {
        $transaction = Transaction::where(['transaction_id'=>$id])->first();
        if ($transaction) {

            // var_dump($transaction);
            // exit;

            $profile = CapabilityProfile::load("SP2000");
            $connector = new WindowsPrintConnector("smb://dannyhrnt-pc/janz1");
            $printer = new Printer($connector, $profile);

            /* Initialize */
            $printer -> initialize();

            $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
            try {
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $logo = EscposImage::load("img/logostruk.png", false);
                $printer -> bitImage($logo, Printer::IMG_DEFAULT);
                $printer -> feed(2);
                $printer -> setJustification(); // Reset
            } catch (Exception $e) {
                /* Images not supported on your PHP, or image file not found */
                $printer -> text($e -> getMessage() . "\n");
            }

            $trans_id = date('YmdHis');
            $dt = MyDate::toReadableDate($transaction->created_at, false, true);
            $table = $transaction->table->number;
            $printer -> text("Order #: $trans_id \n");
            $printer -> text("Table  : $table \n");
            $printer -> text("Kasir  : Admin \n");
            $printer -> text("Tgl    : $dt");
            $printer -> feed(1);

            /* Text */
            $printer -> text("--------------------------------");
            $printer -> feed(1);

            foreach ($transaction_detail as $td) {
                $qty = str_pad($td->quantity, (4 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
                $name = $td->product->name;
                $subt = MyNumber::toReadableAngka($td->subtotal, false);
                $subt = str_pad($subt, (30 - (strlen($qty) + 1) - (strlen($subt) + 8)), ' ', STR_PAD_LEFT);
                // echo $qty; exit;
                // $name = 'Huckleberry';
                $printer -> text("$qty  $name  $subt");
            }

            $printer -> feed(2);
            $printer -> cut();
            try {
                $total = MyNumber::toReadableAngka($transaction->total, false);
                $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                $printer -> text("Total     $total\n");
                $printer -> setJustification(); // Reset
            } catch (Exception $e) {
                /* Images not supported on your PHP, or image file not found */
                $printer -> text($e -> getMessage() . "\n");
            }
            
            $printer -> text("--------------------------------");
            $printer -> feed(1);
            $printer -> cut();

            try {
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                // $printer -> text("Jl. Kemang Barat No. 117\n");
                // $printer -> text("Jakarta Selatan\n");
                // $printer -> text("(021) 8460 485\n");
                // $printer -> text("www.rvindonesia.com\n\n");
                $printer -> text(Branch::address_pluit());
                $printer -> text("Terima Kasih\n");
                $printer -> setJustification(); // Reset
                $printer -> cut();
            } catch (Exception $e) {
                /* Images not supported on your PHP, or image file not found */
                $printer -> text($e -> getMessage() . "\n");
            }

            $printer -> feed(7);
            $printer -> cut();
            $printer -> close();

            // exit;

            $transaction->status = 'finished';
            $transaction->save();
            Session::flash('message', 'Printed successfully!');
            return Redirect::back();
        } else {
            Session::flash('error', 'Print failed!');
            return Redirect::back();
        }
        
    }
}
