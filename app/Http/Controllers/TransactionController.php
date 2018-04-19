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
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Table;
use App\Models\Printer as PrinterModel;
use App\Models\PaymentMethod;
use App\Models\OpeningBalance;
use App\Models\Promotion;
use App\Library\MyNumber;
use App\Library\MyDate;
use \Mike42\Escpos\Printer;
use \Mike42\Escpos\PrintConnectors\FilePrintConnector;
use \Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use \Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use \Mike42\Escpos\EscposImage;
use \Mike42\Escpos\CapabilityProfile;
use Illuminate\Support\Facades\DB;

use File;
use DateTime;

class TransactionController extends Controller
{
    private $pageSize = 10;
    private static $printer_ip = '192.168.0.10';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'add_transaction']);

        if (request()->ip() == env('PC_1') || request()->ip() == '::1') {
            self::$printer_ip = env('PRINTER_CASHIER_1', '192.168.0.11');
        } else {
            self::$printer_ip = env('PRINTER_CASHIER_2', '192.168.0.15');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $transaction = Transaction::all();
        $transaction = Transaction::orderBy('transaction_id', 'DESC')->paginate($this->pageSize);

        // return view('transaction.list', compact('slide'));
        return view('transaction.list', compact('transaction'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // echo request()->ip(); exit;

        $products = Product::with('sub_category.category')->orderBy('name', 'asc')->get();
        $table = Table::pluck('number', 'table_id');
        $promotion = Promotion::where(['status'=>'active'])->orderBy('promotion_id', 'desc')->get();
        $payment_method = PaymentMethod::pluck('name', 'payment_method_id');
        $table->prepend('-', 0);
        $sub_categories = SubCategory::get();
        return view('transaction.create', compact('products', 'sub_categories', 'table', 'payment_method', 'promotion'));
    }

    public function createWithTable($id=null)
    {
        // echo request()->ip(); exit;
        $id_table = $id;
        $products = Product::with('sub_category.category')->orderBy('name', 'asc')->get();
        $table = Table::pluck('number', 'table_id');
        $promotion = Promotion::where(['status'=>'active'])->orderBy('promotion_id', 'desc')->get();
        $payment_method = PaymentMethod::pluck('name', 'payment_method_id');
        $table->prepend('-', 0);
        $sub_categories = SubCategory::get();
        return view('transaction.create', compact('id_table', 'products', 'sub_categories', 'table', 'payment_method', 'promotion'));
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
        $transaction = Transaction::orderBy('transaction_id', 'ASC')->where(['status'=>'pending'])->get();
        $transaction_current = Transaction::with('detail')->orderBy('transaction_id', 'ASC')->where(['transaction_id'=>$id, 'status'=>'pending'])->first();
        // var_dump($transaction->detail); exit;
        return view('transaction.current', compact('transaction', 'transaction_current'))
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
        $products = Product::with('sub_category.category')->orderBy('name', 'asc')->get();
        $table = Table::pluck('number', 'table_id');
        $payment_method = PaymentMethod::pluck('name', 'payment_method_id');
        $table->prepend('-', 0);
        $sub_categories = SubCategory::get();
        $promotion = Promotion::where(['status'=>'active'])->orderBy('promotion_id', 'desc')->get();
        $transaction = Transaction::where('transaction_id', $id)->with('detail')->first();
        return view('transaction.create', compact('transaction', 'products', 'table', 'sub_categories', 'payment_method', 'promotion'));
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
        // $transaction_detail = TransactionDetail::where('transaction_id', $id)->get();
        $transaction = Transaction::where('transaction_id', $id)->first();
        $transaction->void_by = auth()->user()->user_id;
        $transaction->status = 'void';
        $transaction->save();

        Session::flash('message', 'Transaction voided successfully!');
        return Redirect::back();
    }

    public function newOrder(Request $request)
    {
        $order = new Transaction;

        $order->user_id = $request->data['user_id'];
        $order->table_id = $request->data['table_id'] == 0 ? null : $request->data['table_id'];
        $order->promotion_id = $request->data['promotion_id'] == 0 ? null : $request->data['promotion_id'];
        $order->type = $request->data['type'];
        $order->payment_method_id = $request->data['payment_method'];
        $order->price_category = $request->data['price'];
        $order->total = $request->data['total'];
        $order->grand_total = $request->data['grand_total'];
        $order->discount = $request->data['discount'];
        $order->paid = $request->data['paid'];
        $order->payable = $request->data['payable'];
        $order->note = $request->data['note'];
        $order->name = $request->data['name'];
        $order->status = $request->data['status'];

        $error_printer = [];

        // Check Error
        // $check_main = Transaction::whereBetween('created_at', [DB::raw('NOW() - INTERVAL 1 MINUTE'), DB::raw('NOW()')]);
        $check_main = Transaction::where(['table_id'=>$order->table_id])->whereRaw('created_at BETWEEN NOW() - INTERVAL 1 MINUTE AND NOW()');

        // echo json_encode($check_main->get()->toArray());

        if ($order->table_id!= null && $check_main->count() > 0) {
            return response()->json(['status' => false], 500);
        }

        if($order->save()) {
            $print_list = [];
            if ($request->input('data.kitchen_list') !== null) {

                for ($i=0; $i < count($request->input('data.kitchen_list')); $i++) {
                    $t_qty = $request->input('data.kitchen_list.'.$i.".qty");
                    $t_note = $request->input('data.kitchen_list.'.$i.".note");
                    $t_id = $request->input('data.kitchen_list.'.$i.".id");

                    $product = Product::where(['product_id'=>$t_id])->first();
                    $printer_id = $request->input('data.kitchen_list.'.$i.".printer");

                    if ($printer_id == "" || $printer_id == 0) {
                        if ($order->type == "disajikan")
                            continue;
                        else
                            $printer_id = 4;
                    }

                    if (!isset($print_list[$printer_id]))
                        $print_list[$printer_id] = [];

                    array_push($print_list[$printer_id], [
                        'id' => $t_id,
                        'name' => $product->name,
                        'qty' => $t_qty,
                        'note' => $t_note
                    ]);;
                }

                foreach ($print_list as $key=>$value) {
                    // break;
                    $printer = PrinterModel::where(['printer_id'=>$key]);
                    if ($printer->count() > 0) {
                        $p_item = $printer->first();
                        // echo $p_item->name . "<br>";

                        try {
                            $fp = fsockopen($p_item->address, $p_item->port, $errno, $errstr, 2);
                            if ($fp) {

                                // WORKING
                                $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
                                $printer = new Printer($connector);

                                /* Initialize */
                                $printer -> initialize();
                                $dt = MyDate::toReadableDate($order->created_at, false, true);
                                $user_id = auth()->user()->name;
                                $printer_name = strtoupper($p_item->name);
                                $table = '-';
                                if ($order->table)
                                    $table = $order->table->number;
                                $type = $order->type;
                                try {

                                    /* Initialize */
                                    $printer -> initialize();
                                    $dt = MyDate::toReadableDate($order->created_at, false, true);
                                    $user_id = auth()->user()->user_id;
                                    $printer_name = strtoupper($p_item->name);
                                    $table = '-';
                                    if ($order->table)
                                        $table = $order->table->number;

                                    $type = $order->type;
                                    switch($type) {
                                        case 'disajikan': $type = "Dine In (HIDANG)"; break;
                                        case 'rames': $type = "Dine In (KHUSUS)"; break;
                                        default: $type = "Takeaway"; break;
                                    }

                                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                    $printer -> text("\n");
                                    $printer -> text("<< $printer_name >>\n");
                                    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                                    $printer -> text("Meja $table\n");
                                    $printer -> selectPrintMode();
                                    $printer -> setJustification(); // Reset
                                    $printer -> feed(2);
                                    $printer -> text("Tipe   : $type \n");
                                    $printer -> text("Kasir  : $user_id\n");
                                    $printer -> text("Tgl    : $dt\n");
                                    if ($order->name != '') {
                                        $nm = $order->name;
                                        $printer -> text("Nama   : $nm\n");
                                    }
                                    $printer -> feed(2);

                                    /* Text */
                                    $printer -> text("------------------------------------------------");
                                    $printer -> feed(2);
                                    $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

                                    foreach ($value as $item) {
                                        // echo $item['qty'] ." - ". $item['name'] ." - ". $item['note'] ."<br>";
                                        $qty = str_pad($item['qty'], 3, ' ', STR_PAD_LEFT);
                                        $name = $item['name'];
                                        $note = $item['note'];

                                        $printer -> text("$qty  $name\n");
                                        if ($note != '')
                                            $printer -> text("     **$note\n");
                                        $printer -> feed(1);
                                    }
                                    $printer -> selectPrintMode();
                                    $printer -> text("------------------------------------------------");
                                    $printer -> feed(2);
                                    $printer -> cut();

                                } finally {
                                    $printer -> close();
                                }
                            }
                        } catch(\Exception $e) {
                            // echo "Error Printer " . $p_item->name;
                            $error_printer[] = "<strong>". $p_item->name ."</strong> Error / Kertas Habis";
                        } finally {

                        }
                    }
                }
            }

            // $order_detail = [];
            for ($i=0; $i < count($request->input('data.items')); $i++) {
                // $order_detail[] = [
                $order_detail = [
                    'transaction_id' => $order->transaction_id,
                    'product_id' => $request->input('data.items.'.$i.".id"),
                    'quantity' => $request->input('data.items.'.$i.".quantity"),
                    'price' => $request->input('data.items.'.$i.".price"),
                    'note' => $request->input('data.items.'.$i.".note"),
                    'subtotal' => $request->input('data.items.'.$i.".subtotal")
                ];

                $check_transaction = TransactionDetail::where(['transaction_id'=>$order->transaction_id, 'product_id'=>$request->input('data.items.'.$i.".id")]);

                if($check_transaction->count() == 0)
                    \DB::table('transaction_detail')->insert($order_detail);
            }

            // if(\DB::table('transaction_detail')->insert($order_detail)) {

            // } else {
            //     return response()->json(['status' => false], 500);
            // }

            if ($request->data['status'] == 'finished') {
                $id = $order->transaction_id;
                // $printer_model = PrinterModel::where(['printer_id'=>1]);
                // if ($printer_model->count() > 0) {
                //     $p_item = $printer_model->first();

                    // $connector = new WindowsPrintConnector("smb://localhost/bar");
                    // $printer = new Printer($connector);

                try {
                    $fp = fsockopen(self::$printer_ip, 9100, $errno, $errstr, 2);
                    if ($fp) {

                        for ($i=0; $i<2; $i++):
                        // break;

                            $connector = new NetworkPrintConnector(self::$printer_ip, 9100);
                            $printer = new Printer($connector);

                            try {

                                /* Initialize */
                                $printer -> initialize();

                                $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
                                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                $logo = EscposImage::load("img/logostruk.png", false);
                                $printer -> bitImage($logo, Printer::IMG_DEFAULT);
                                $printer -> feed(2);
                                $printer -> setJustification(); // Reset

                                $trans_id = $order->code();
                                $dt = MyDate::toReadableDate($order->created_at, false, true);
                                $table = '-';
                                if ($order->table)
                                    $table = $order->table->number;

                                $pm = '-';
                                if ($order->payment_method_id != null)
                                    $pm = $order->PaymentMethod->name;

                                $note = '';
                                if ($order->note != '')
                                    $note = $order->note;

                                $kasir = auth()->user()->name;

                                $type = $order->type;
                                switch($type) {
                                    case 'disajikan': $type = "Dine In (HIDANG)"; break;
                                    case 'rames': $type = "Dine In (KHUSUS)"; break;
                                    default: $type = "Takeaway"; break;
                                }

                                $printer -> text("Order #       : $trans_id \n");
                                $printer -> text("Meja          : $table \n");
                                $printer -> text("Tipe          : $type \n");
                                $printer -> text("Kasir         : $kasir \n");
                                $printer -> text("Tanggal       : $dt\n");
                                $printer -> text("Pembayaran    : $pm\n");
                                if ($note != '')
                                    $printer -> text("Note          : $note\n");

                                if ($order->name != '') {
                                    $nm = $order->name;
                                    $printer -> text("Nama          : $nm\n");
                                }

                                /* Text */
                                $printer -> text("------------------------------------------------");
                                $printer -> feed(2);

                                foreach ($transaction_detail as $td) {
                                    $qty = str_pad($td->quantity, (3 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
                                    $name = $td->product->name;
                                    $subt = MyNumber::toReadableAngka($td->subtotal, false);
                                    $space = (48 - (strlen($qty) + strlen($name) + 4) );
                                    $subt = str_pad($subt, $space, ' ', STR_PAD_LEFT);
                                    $note = $td->note;
                                    // echo $qty; exit;
                                    // $name = 'Huckleberry';
                                    $printer -> text("$qty  $name  $subt");
                                    if ($note != '')
                                        $printer -> text("     **$note\n");
                                }

                                $printer -> feed(2);
                                // $printer -> cut();
                                $total = MyNumber::toReadableAngka($order->total, false);
                                $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                                // $printer -> text("Total     $total\n");
                                $printer -> setJustification(); // Reset

                                $printer -> text("------------------------------------------------");
                                $printer -> feed(1);
                                // $printer -> cut();

                                try {
                                    $subtotal = MyNumber::toReadableAngka($order->total, false);
                                    $subtotal = str_pad($subtotal, (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);
                                    $discount_value = "";
                                    $discount_amount = "";
                                    $discount = "";
                                    if ($order->promotion) {
                                        if ($order->promotion->type == 'percent') {
                                            $discount_value = round($order->promotion->value)."%";
                                            $discount_amount = MyNumber::toReadableAngka($order->total * ($order->promotion->value / 100), false);
                                            $disc_effect = $order->total * ($order->promotion->value / 100);
                                            $tax = MyNumber::toReadableAngka( (($order->total - $disc_effect) * 0.1) , false);
                                        } else {
                                            $discount_value = "";
                                            $discount_amount = MyNumber::toReadableAngka($order->promotion->value, false);
                                            $tax = MyNumber::toReadableAngka( (($order->total - $order->promotion->value) * 0.1) , false);
                                        }
                                        $discount = str_pad($discount_amount, (30 - (strlen('Diskon ') + strlen($discount_value)) ), ' ', STR_PAD_LEFT);

                                    } else {
                                        $tax = MyNumber::toReadableAngka( ($order->total * 0.1) , false);
                                    }


                                    $tax = str_pad($tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
                                    $grandtotal = MyNumber::toReadableAngka($order->grand_total, false);
                                    $grandtotal = str_pad($grandtotal, (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);
                                    $paid = MyNumber::toReadableAngka($order->paid, false);
                                    $paid = str_pad($paid, (30 - strlen('Bayar')), ' ', STR_PAD_LEFT);
                                    $payable = MyNumber::toReadableAngka($order->payable, false);
                                    $payable = str_pad($payable, (30 - strlen('Kembali')), ' ', STR_PAD_LEFT);

                                    $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                                    $printer -> text("Subtotal $subtotal\n");
                                    if ($discount != "")
                                        $printer -> text("Diskon $discount_value $discount\n");
                                    $printer -> text("Pajak (10%) $tax\n");
                                    $printer -> text("Grand Total $grandtotal\n");
                                    $printer -> text("Bayar $paid\n");
                                    $printer -> text("Kembali $payable\n");
                                    $printer -> setJustification(); // Reset
                                } catch (Exception $e) {
                                    /* Images not supported on your PHP, or image file not found */
                                    $printer -> text($e -> getMessage() . "\n");
                                }

                                $printer -> text("------------------------------------------------");
                                $printer -> feed(1);

                                if ($discount != "" && $discount_value == 100) {
                                    $printer -> feed(3);
                                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                    $printer -> text("Signature: ___________________________");
                                    $printer -> setJustification(); // Reset
                                    $printer -> feed(2);
                                }

                                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                $printer -> text("\n");
                                $printer -> text(Branch::address_pluit());
                                $printer -> text("Terima Kasih\n");
                                $printer -> setJustification(); // Reset

                                $printer -> feed(2);
                                $printer -> cut();

                            } finally {
                                $printer -> close();
                            }
                        endfor;

                    }
                } catch(\Exception $e) {
                    // echo "Error Printer " . $p_item->name;
                    $error_printer[] = "<strong>Cashier</strong> Printer Error / Kertas Habis";
                } finally {

                }
                // }
            }

            return response()->json(['status' => true, 'transaction_id' => $order->transaction_id, 'table_id' => $order->table_id, 'error_printer' => $error_printer], 200);
        } else {
            return response()->json(['status' => false], 500);
        }
    }

    public function updateOrder(Request $request, $id)
    {
        $error_printer = [];
        $o = Transaction::where(['transaction_id'=>$id]);
        if ($o->count() > 0) {
            $order = $o->first();
            $order->user_id = $request->data['user_id'];
            $order->table_id = $request->data['table_id'] == 0 ? null : $request->data['table_id'];
            $order->promotion_id = $request->data['promotion_id'] == 0 ? null : $request->data['promotion_id'];
            $order->type = $request->data['type'];
            $order->payment_method_id = $request->data['payment_method'];
            $order->price_category = $request->data['price'];
            $order->total = $request->data['total'];
            $order->grand_total = $request->data['grand_total'];
            $order->discount = $request->data['discount'];
            $order->paid = $request->data['paid'];
            $order->payable = $request->data['payable'];
            $order->note = $request->data['note'];
            $order->name = $request->data['name'];
            $order->status = $request->data['status'];



            if($order->save()) {
                $order->detail()->delete();

                $order_detail = [];
                for ($i=0; $i < count($request->input('data.items')); $i++) {
                    // $order_detail[] = [
                    // echo '<pre>';print_r($request->input('data.items.'.$i.".pricegojek"));exit;
                    $order_price = $request->input('data.items.'.$i.".price");
                    if($request->data['price'] == 'gojek') {
                        $order_price = $request->input('data.items.'.$i.".pricegojek");
                    }
                    $order_detail = [
                        'transaction_id' => $order->transaction_id,
                        'product_id' => $request->input('data.items.'.$i.".id"),
                        'quantity' => $request->input('data.items.'.$i.".quantity"),
                        'price' => $order_price,
                        'note' => $request->input('data.items.'.$i.".note"),
                        'subtotal' => $request->input('data.items.'.$i.".subtotal")
                    ];

                    $check_transaction = TransactionDetail::where(['transaction_id'=>$order->transaction_id, 'product_id'=>$request->input('data.items.'.$i.".id")]);

                    if($check_transaction->count() == 0)
                        \DB::table('transaction_detail')->insert($order_detail);
                }

                $print_list = [];
                if ($request->input('data.kitchen_list') !== null) {

                    for ($i=0; $i < count($request->input('data.kitchen_list')); $i++) {
                        $t_qty = $request->input('data.kitchen_list.'.$i.".qty");
                        $t_note = $request->input('data.kitchen_list.'.$i.".note");
                        $t_id = $request->input('data.kitchen_list.'.$i.".id");

                        $product = Product::where(['product_id'=>$t_id])->first();
                        $printer_id = $request->input('data.kitchen_list.'.$i.".printer");
                        if ($printer_id == "" || $printer_id == 0)
                            continue;
                        if (!isset($print_list[$printer_id]))
                            $print_list[$printer_id] = [];
                        array_push($print_list[$printer_id], [
                            'id' => $t_id,
                            'name' => $product->name,
                            'qty' => $t_qty,
                            'note' => $t_note
                        ]);;
                    }

                    foreach ($print_list as $key=>$value) {
                        $printer_model = PrinterModel::where(['printer_id'=>$key]);
                        if ($printer_model->count() > 0) {
                            $p_item = $printer_model->first();
                            $dt = MyDate::toReadableDate($order->created_at, false, true);
                            $user_id = auth()->user()->name;
                            $printer_name = strtoupper($p_item->name);
                            $table = '-';
                            if ($order->table)
                                $table = $order->table->number;
                            // $type =ucfirst($order->type);

                            $type = $order->type;
                            switch($type) {
                                case 'disajikan': $type = "Dine In (HIDANG)"; break;
                                case 'rames': $type = "Dine In (KHUSUS)"; break;
                                default: $type = "Takeaway"; break;
                            }

                            try {
                                $fp = fsockopen($p_item->address, $p_item->port, $errno, $errstr, 2);
                                if ($fp) {

                                    $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
                                    $printer = new Printer($connector);

                                    try {

                                        /* Initialize */
                                        $printer -> initialize();
                                        $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                        $printer -> text("\n");
                                        $printer -> text("<< $printer_name >>\n");
                                        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                                        $printer -> text("Meja $table\n");
                                        $printer -> selectPrintMode();
                                        $printer -> setJustification(); // Reset

                                        $printer -> feed(2);
                                        $printer -> text("Tipe   : $type \n");
                                        $printer -> text("Kasir  : $user_id\n");
                                        $printer -> text("Tgl    : $dt\n");
                                        if ($order->name != '') {
                                            $nm = $order->name;
                                            $printer -> text("Nama   : $nm\n");
                                        }
                                        $printer -> feed(2);

                                        /* Text */
                                        $printer -> text("------------------------------------------------");
                                        $printer -> feed(2);
                                        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

                                        // echo $printer_name . "<br>";

                                        foreach ($value as $item) {
                                            // echo $item['qty'] ." - ". $item['name'] ." - ". $item['note'] ."<br>";
                                            $qty = str_pad($item['qty'], 3, ' ', STR_PAD_LEFT);
                                            $name = $item['name'];
                                            $note = $item['note'];

                                            $printer -> text("$qty  $name\n");
                                            if ($note != '')
                                                $printer -> text("     **$note\n");
                                            $printer -> feed(1);

                                            // echo "$qty $name $note <br>";
                                        }
                                        // echo "<br>";
                                        $printer -> selectPrintMode();
                                        $printer -> text("------------------------------------------------");
                                        $printer -> feed(2);
                                        $printer -> cut();

                                    } finally {
                                        $printer -> close();
                                    }

                                }
                            } catch(\Exception $e) {
                                // echo "Error Printer " . $p_item->name;
                                $error_printer[] = "<strong>". $p_item->name ."</strong> Error / Kertas Habis";
                            } finally {

                            }
                        }
                    }
                }

                // if(\DB::table('transaction_detail')->insert($order_detail)) {

                    if ($request->data['status'] == 'finished') {

                        try {
                            $fp = fsockopen(self::$printer_ip, 9100, $errno, $errstr, 2);
                            if ($fp) {

                                // $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
                                // $printer = new Printer($connector);



                                for ($i=0; $i<2; $i++):

                                    // $printer_model = PrinterModel::where(['printer_id'=>1]);
                                    // if ($printer_model->count() > 0) {
                                    //     $p_item = $printer_model->first();

                                        $connector = new NetworkPrintConnector(self::$printer_ip, 9100);
                                        // $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
                                        $printer = new Printer($connector);
                                        // $connector = new WindowsPrintConnector("smb://localhost/bar");
                                        // $printer = new Printer($connector);

                                        try {

                                            /* Initialize */
                                            $printer -> initialize();

                                            $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
                                            $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                            $logo = EscposImage::load("img/logostruk.png", false);
                                            $printer -> bitImage($logo, Printer::IMG_DEFAULT);
                                            $printer -> feed(2);
                                            $printer -> setJustification(); // Reset

                                            $trans_id = $order->code();
                                            $dt = MyDate::toReadableDate($order->created_at, false, true);
                                            $table = '-';
                                            if ($order->table)
                                                $table = $order->table->number;

                                            $pm = '-';
                                            if ($order->payment_method_id != null)
                                                $pm = $order->PaymentMethod->name;

                                            $note = '';
                                            if ($order->note != '')
                                                $note = $order->note;

                                            $kasir = auth()->user()->name;

                                            $type = $order->type;
                                            switch($type) {
                                                case 'disajikan': $type = "Dine In (HIDANG)"; break;
                                                case 'rames': $type = "Dine In (KHUSUS)"; break;
                                                default: $type = "Takeaway"; break;
                                            }

                                            $printer -> text("Order #       : $trans_id \n");
                                            $printer -> text("Meja          : $table \n");
                                            $printer -> text("Tipe          : $type \n");
                                            $printer -> text("Kasir         : $kasir \n");
                                            $printer -> text("Tanggal       : $dt\n");
                                            $printer -> text("Pembayaran    : $pm\n");
                                            if ($note != '')
                                                $printer -> text("Note          : $note\n");

                                            if ($order->name != '') {
                                                $nm = $order->name;
                                                $printer -> text("Nama          : $nm\n");
                                            }

                                            /* Text */
                                            $printer -> text("------------------------------------------------");
                                            $printer -> feed(2);

                                            foreach ($transaction_detail as $td) {
                                                $qty = str_pad($td->quantity, (3 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
                                                $name = $td->product->name;
                                                $subt = MyNumber::toReadableAngka($td->subtotal, false);
                                                $space = (48 - (strlen($qty) + strlen($name) + 4) );
                                                $subt = str_pad($subt, $space, ' ', STR_PAD_LEFT);
                                                $note = $td->note;
                                                // echo $qty; exit;
                                                // $name = 'Huckleberry';
                                                $printer -> text("$qty  $name  $subt");
                                                if ($note != '')
                                                    $printer -> text("     **$note\n");
                                            }

                                            $printer -> feed(2);
                                            // $printer -> cut();
                                            $total = MyNumber::toReadableAngka($order->total, false);
                                            $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                                            // $printer -> text("Total     $total\n");
                                            $printer -> setJustification(); // Reset

                                            $printer -> text("------------------------------------------------");
                                            $printer -> feed(1);
                                            // $printer -> cut();

                                            $subtotal = MyNumber::toReadableAngka($order->total, false);
                                            $subtotal = str_pad($subtotal, (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);
                                            $discount_value = "";
                                            $discount_amount = "";
                                            $discount = "";
                                            if ($order->promotion) {
                                                if ($order->promotion->type == 'percent') {
                                                    $discount_value = round($order->promotion->value)."%";
                                                    $discount_amount = MyNumber::toReadableAngka($order->total * ($order->promotion->value / 100), false);
                                                    $disc_effect = $order->total * ($order->promotion->value / 100);
                                                    $tax = MyNumber::toReadableAngka( (($order->total - $disc_effect) * 0.1) , false);
                                                } else {
                                                    $discount_value = "";
                                                    $discount_amount = MyNumber::toReadableAngka($order->promotion->value, false);
                                                    $tax = MyNumber::toReadableAngka( (($order->total - $order->promotion->value) * 0.1) , false);
                                                }
                                                $discount = str_pad($discount_amount, (30 - (strlen('Diskon ') + strlen($discount_value)) ), ' ', STR_PAD_LEFT);

                                            } else {
                                                $tax = MyNumber::toReadableAngka( ($order->total * 0.1) , false);
                                            }


                                            $tax = str_pad($tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
                                            $grandtotal = MyNumber::toReadableAngka($order->grand_total, false);
                                            $grandtotal = str_pad($grandtotal, (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);
                                            $paid = MyNumber::toReadableAngka($order->paid, false);
                                            $paid = str_pad($paid, (30 - strlen('Bayar')), ' ', STR_PAD_LEFT);
                                            $payable = MyNumber::toReadableAngka($order->payable, false);
                                            $payable = str_pad($payable, (30 - strlen('Kembali')), ' ', STR_PAD_LEFT);

                                            $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                                            $printer -> text("Subtotal $subtotal\n");
                                            if ($discount != "")
                                                $printer -> text("Diskon $discount_value $discount\n");
                                            $printer -> text("Pajak (10%) $tax\n");
                                            $printer -> text("Grand Total $grandtotal\n");
                                            $printer -> text("Bayar $paid\n");
                                            $printer -> text("Kembali $payable\n");
                                            $printer -> setJustification(); // Reset

                                            $printer -> text("------------------------------------------------");
                                            $printer -> feed(1);

                                            if ($discount != "" && $discount_value == 100) {
                                                $printer -> feed(3);
                                                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                                $printer -> text("Signature: ___________________________");
                                                $printer -> setJustification(); // Reset
                                                $printer -> feed(2);
                                            }

                                            $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                            $printer -> text("\n");
                                            $printer -> text(Branch::address_pluit());
                                            $printer -> text("Terima Kasih\n");
                                            $printer -> setJustification(); // Reset

                                            $printer -> feed(2);
                                            $printer -> cut();

                                        } finally {
                                            $printer -> close();
                                        }
                                    // }

                                endfor;

                            }
                        } catch(\Exception $e) {
                            // echo "Error Printer " . $p_item->name;
                            $error_printer[] = "<strong>Cashier</strong> Printer Error / Kertas Habis";
                        } finally {

                        }
                    }
                // }
                return response()->json(['status' => true, 'transaction_id' => $order->transaction_id, 'table_id' => $order->table_id, 'error_printer' => $error_printer], 200);
            }
        }
        return response()->json(['status' => false], 500);
    }

    public function printBill(Request $request, $id)
    {
        $error_printer = [];
        $o = Transaction::where(['transaction_id'=>$id]);
        if ($o->count() > 0) {
            $order = $o->first();
            $order->user_id = $request->data['user_id'];
            $order->table_id = $request->data['table_id'] == 0 ? null : $request->data['table_id'];
            $order->promotion_id = $request->data['promotion_id'] == 0 ? null : $request->data['promotion_id'];
            $order->type = $request->data['type'];
            $order->payment_method_id = $request->data['payment_method'];
            $order->price_category = $request->data['price'];
            $order->total = $request->data['total'];
            $order->grand_total = $request->data['grand_total'];
            $order->discount = $request->data['discount'];
            $order->paid = $request->data['paid'];
            $order->payable = $request->data['payable'];
            $order->note = $request->data['note'];
            $order->name = $request->data['name'];
            $order->status = $request->data['status'];

            if($order->save()) {
                $order->detail()->delete();

                // $order_detail = [];
                for ($i=0; $i < count($request->input('data.items')); $i++) {
                    // $order_detail[] = [
                    $order_detail = [
                        'transaction_id' => $order->transaction_id,
                        'product_id' => $request->input('data.items.'.$i.".id"),
                        'quantity' => $request->input('data.items.'.$i.".quantity"),
                        'price' => $request->input('data.items.'.$i.".price"),
                        'note' => $request->input('data.items.'.$i.".note"),
                        'subtotal' => $request->input('data.items.'.$i.".subtotal")
                    ];
                    $check_transaction = TransactionDetail::where(['transaction_id'=>$order->transaction_id, 'product_id'=>$request->input('data.items.'.$i.".id")]);

                    if($check_transaction->count() == 0)
                        \DB::table('transaction_detail')->insert($order_detail);
                }

                $print_list = [];

                // if(\DB::table('transaction_detail')->insert($order_detail)) {

                // }
                    // if ($request->data['status'] == 'finished') {
                        // for ($i=0; $i<2; $i++):

                            // $printer_model = PrinterModel::where(['printer_id'=>1]);
                            // if ($printer_model->count() > 0) {
                            //     $p_item = $printer_model->first();

                    try {
                        $fp = fsockopen(self::$printer_ip, 9100, $errno, $errstr, 2);
                        if ($fp) {

                            $connector = new NetworkPrintConnector(self::$printer_ip, 9100);
                            // $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
                            $printer = new Printer($connector);

                            // $connector = new WindowsPrintConnector("smb://localhost/bar");
                            // $printer = new Printer($connector);

                            try {

                                /* Initialize */
                                $printer -> initialize();

                                $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
                                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                $logo = EscposImage::load("img/logostruk.png", false);
                                $printer -> bitImage($logo, Printer::IMG_DEFAULT);
                                $printer -> feed(2);
                                $printer -> setJustification(); // Reset

                                $trans_id = $order->code();
                                $dt = MyDate::toReadableDate($order->created_at, false, true);
                                $table = '-';
                                if ($order->table)
                                    $table = $order->table->number;

                                $pm = '-';
                                if ($order->payment_method_id != null)
                                    $pm = $order->PaymentMethod->name;

                                $kasir = auth()->user()->name;

                                $type = $order->type;
                                switch($type) {
                                    case 'disajikan': $type = "Dine In (HIDANG)"; break;
                                    case 'rames': $type = "Dine In (KHUSUS)"; break;
                                    default: $type = "Takeaway"; break;
                                }

                                $printer -> text("Order #       : $trans_id \n");
                                $printer -> text("Meja          : $table \n");
                                $printer -> text("Tipe          : $type \n");
                                $printer -> text("Kasir         : $kasir \n");
                                $printer -> text("Tanggal       : $dt\n");
                                // $printer -> text("Pembayaran    : $pm\n");

                                if ($order->name != '') {
                                    $nm = $order->name;
                                    $printer -> text("Nama          : $nm\n");
                                }

                                /* Text */
                                $printer -> text("------------------------------------------------");
                                $printer -> feed(2);

                                foreach ($transaction_detail as $td) {
                                    $qty = str_pad($td->quantity, (3 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
                                    $name = $td->product->name;
                                    $subt = MyNumber::toReadableAngka($td->subtotal, false);
                                    $space = (48 - (strlen($qty) + strlen($name) + 4) );
                                    $subt = str_pad($subt, $space, ' ', STR_PAD_LEFT);
                                    $note = $td->note;
                                    // echo $qty; exit;
                                    // $name = 'Huckleberry';
                                    $printer -> text("$qty  $name  $subt");
                                    if ($note != '')
                                        $printer -> text("     **$note\n");
                                }

                                $printer -> feed(2);
                                // $printer -> cut();
                                $total = MyNumber::toReadableAngka($order->total, false);
                                $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                                // $printer -> text("Total     $total\n");
                                $printer -> setJustification(); // Reset

                                $printer -> text("------------------------------------------------");
                                $printer -> feed(1);
                                // $printer -> cut();

                                $subtotal = MyNumber::toReadableAngka($order->total, false);
                                $subtotal = str_pad($subtotal, (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);
                                $discount_value = "";
                                $discount_amount = "";
                                $discount = "";
                                if ($order->promotion) {
                                    if ($order->promotion->type == 'percent') {
                                        $discount_value = round($order->promotion->value)."%";
                                        $discount_amount = MyNumber::toReadableAngka($order->total * ($order->promotion->value / 100), false);
                                        $disc_effect = $order->total * ($order->promotion->value / 100);
                                        $tax = MyNumber::toReadableAngka( (($order->total - $disc_effect) * 0.1) , false);
                                    } else {
                                        $discount_value = "";
                                        $discount_amount = MyNumber::toReadableAngka($order->promotion->value, false);
                                        $tax = MyNumber::toReadableAngka( (($order->total - $order->promotion->value) * 0.1) , false);
                                    }
                                    $discount = str_pad($discount_amount, (30 - (strlen('Diskon ') + strlen($discount_value)) ), ' ', STR_PAD_LEFT);

                                } else {
                                    $tax = MyNumber::toReadableAngka( ($order->total * 0.1) , false);
                                }


                                $tax = str_pad($tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
                                $grandtotal = MyNumber::toReadableAngka($order->grand_total, false);
                                $grandtotal = str_pad($grandtotal, (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);
                                // $paid = MyNumber::toReadableAngka($order->paid, false);
                                // $paid = str_pad($paid, (30 - strlen('Bayar')), ' ', STR_PAD_LEFT);
                                // $payable = MyNumber::toReadableAngka($order->payable, false);
                                // $payable = str_pad($payable, (30 - strlen('Kembali')), ' ', STR_PAD_LEFT);

                                $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                                $printer -> text("Subtotal $subtotal\n");
                                if ($discount != "")
                                    $printer -> text("Diskon $discount_value $discount\n");
                                $printer -> text("Pajak (10%) $tax\n");
                                $printer -> text("Grand Total $grandtotal\n");
                                // $printer -> text("Bayar $paid\n");
                                // $printer -> text("Kembali $payable\n");
                                $printer -> setJustification(); // Reset

                                $printer -> text("------------------------------------------------");
                                $printer -> feed(1);

                                if ($discount != "" && $discount_value == 100) {
                                    $printer -> feed(3);
                                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                    $printer -> text("Signature: ___________________________");
                                    $printer -> setJustification(); // Reset
                                    $printer -> feed(2);
                                }

                                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                $printer -> text("\n");
                                $printer -> text(Branch::address_pluit());
                                $printer -> text("Terima Kasih\n");
                                $printer -> setJustification(); // Reset

                                $printer -> feed(2);
                                $printer -> cut();

                            } finally {
                                $printer -> close();
                            }

                        }
                    } catch(\Exception $e) {
                        // echo "Error Printer " . $p_item->name;
                        $error_printer[] = "<strong>Cashier</strong> Printer Error / Kertas Habis";
                    } finally {

                    }
                            // }

                        // endfor;
                    // }
                // }
                return response()->json(['status' => true, 'transaction_id' => $order->transaction_id, 'table_id' => $order->table_id, 'error_printer' => $error_printer], 200);
            }
        }
        return response()->json(['status' => false], 500);
    }

    public function printDailyHistory(Request $request) {

        $printer_model = PrinterModel::where(['printer_id'=>1]);
        if ($printer_model->count() > 0) {
            $p_item = $printer_model->first();

            $connector = new NetworkPrintConnector(self::$printer_ip, 9100);
            // $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
            $printer = new Printer($connector);

            try {

                /* Initialize */
                $printer -> initialize();

                // $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                $logo = EscposImage::load("img/logostruk.png", false);
                $printer -> bitImage($logo, Printer::IMG_DEFAULT);
                $printer -> feed(2);
                $printer -> setJustification(); // Reset

                $dt = MyDate::toReadableDate(date('Y-m-d H:i:s'), false, true);
                $kasir = auth()->user()->name;

                $printer -> text("Kasir         : $kasir \n");
                $printer -> text("Tanggal       : $dt\n");

                /* Text */
                // $printer -> text("================================================");
                $printer -> feed(2);


                // OPENING BALANCE
                $opening_balance = OpeningBalance::selectRaw('*, count(opening_balance_id) as count, sum(balance) as sum')
                    ->with('user')
                    ->whereRaw('date = curdate()')
                    ->groupBy('user_id')
                    ->get();

                $printer -> text("OPENING BALANCE\n");
                $printer -> text("================================================");
                $total_ob = 0;
                foreach($opening_balance as $ob) {
                    $name = $ob->user->name;
                    $total_ob = $ob->sum;
                    $count = str_pad($ob->count, 4, ' ', STR_PAD_LEFT);
                    $sum = str_pad(\MyNumber::toReadableAngka($ob->sum, FALSE), (48 - (strlen($name))), ' ', STR_PAD_LEFT);
                    // echo $count . " ". $name . $sum ."<br>";
                    $printer -> text("$name$sum\n");
                }

                $name = "** TOTAL **";
                $count = str_pad(' ', 4, ' ', STR_PAD_LEFT);
                $sum = str_pad(\MyNumber::toReadableAngka($total_ob, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                $printer -> text("$count $name$sum\n");
                // $printer -> text("$name$sum\n");
                $printer -> feed(1);

                // SALES BY MEDIA
                $transaction_by_media = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                ->with('paymentMethod')
                ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                ->where(['status'=>'finished'])
                ->groupBy('payment_method_id')
                ->get();

                $printer -> text("SALES BY MEDIA\n");
                $printer -> text("================================================");
                foreach($transaction_by_media as $tbm) {
                    $name = $tbm->paymentMethod->name;
                    $count = str_pad($tbm->count, 4, ' ', STR_PAD_LEFT);
                    $sum = str_pad(\MyNumber::toReadableAngka($tbm->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                    // echo $count . " ". $name . $sum ."<br>";
                    $printer -> text("$count $name$sum\n");
                }
                // echo "------------------------------------------------<br>";

                // TOTAL
                $transaction_by_media_total = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod')
                    ->where(['status'=>'finished'])
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->first();

                $name = "** TOTAL **";
                $count = str_pad(' ', 4, ' ', STR_PAD_LEFT);
                $sum = str_pad(\MyNumber::toReadableAngka($transaction_by_media_total->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                $printer -> text("$count $name$sum\n");
                $printer -> text("================================================");
                $printer -> feed(1);

                // PER MEDIA
                $transaction_by_media = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod')
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->where(['status'=>'finished'])
                    ->groupBy('payment_method_id')
                    ->get();

                foreach($transaction_by_media as $tbm) {
                    $name = "** " .$tbm->paymentMethod->name;
                    $printer -> text("$name\n");

                    $transaction_get = Transaction::selectRaw('*, sum(grand_total) as sum')
                        ->with('paymentMethod')
                        ->where([
                            ['payment_method_id', '=', $tbm->payment_method_id],
                            ['status', '=', 'finished']
                        ])
                        ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                        ->groupBy('transaction_id')
                        ->get();

                    foreach($transaction_get as $tbm_dt) {
                        $name = $tbm_dt->code();
                        if ($tbm_dt->note!="")
                            $name .= " (". $tbm_dt->note .")";
                        $sum = str_pad(\MyNumber::toReadableAngka($tbm_dt->sum, FALSE), (48 - (strlen($name))), ' ', STR_PAD_LEFT);
                        $printer -> text("$name$sum\n");
                    }
                    $printer -> feed(1);
                }

                // MENU DETAILS
                $printer -> text("MENU DETAILS\n");
                $printer -> text("================================================");
                $productSales = (
                    DB::table('transaction_detail')
                        ->select(DB::raw('
                            product.name as product_name,
                            count(product.name) as product_count
                        '))
                        ->join('product', 'transaction_detail.product_id', '=', 'product.product_id')
                        ->join('transaction', 'transaction_detail.transaction_id', '=', 'transaction.transaction_id')
                        ->whereRaw('
                            transaction.created_at BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                            AND transaction.status = "finished"
                        ')
                        ->groupBy('product.product_id')
                        ->orderBy('product.name', 'ASC')
                        ->get()
                );

                foreach ($productSales as $key => $productSale) {
                    $name = $productSale->product_name;
                    $sum = str_pad(\MyNumber::toReadableAngka($productSale->product_count, FALSE), (48 - (strlen($name))), ' ', STR_PAD_LEFT);
                    $printer -> text("$name$sum\n");
                }

                $printer -> feed(1);
                // MENU DETAILS

                // SALES DETAILS
                $printer -> text("SALES DETAILS\n");
                $printer -> text("================================================");
                // GET TOTAL SALES
                $transaction_sales = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
                    ->with('paymentMethod')
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY) AND status = "finished"')
                    // ->where(['status'=>'finished'])
                    ->first();

                $name = "Gross Sales";
                $t_gross_total = $transaction_sales->sum;
                $sales_total = str_pad(\MyNumber::toReadableAngka($transaction_sales->sum, FALSE), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                $printer -> text("$name$sales_total\n");

                $transaction_discount = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY) AND status = "finished" AND promotion_id != 3')
                    // ->where(['status'=>'finished'])
                    // ->whereNotIn('promotion_id', [3])
                    ->first();


                $name = "Discount";
                $t_discount = $transaction_discount->discount;
                $discount_total = str_pad(\MyNumber::toReadableAngka($transaction_discount->discount, FALSE), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                $printer -> text("$name$discount_total\n");

                // FOC
                $transaction_foc = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY) AND status = "finished" AND promotion_id = 3')
                    // ->where(['status'=>'finished', 'promotion_id'=>3])
                    ->first();

                $name = "Free of Charge";
                // $foc_total = $transaction_foc->discount;
                $t_foc = $transaction_foc->discount;
                $foc_total = str_pad(\MyNumber::toReadableAngka($transaction_foc->discount, FALSE), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                // echo $name . $foc_total ."<br>";
                $printer -> text("$name$foc_total\n");

                $name = "Net Sales";
                $net_total = $t_gross_total - ($t_discount + $t_foc);
                $t_net_total = $net_total;
                $net_total = str_pad(\MyNumber::toReadableAngka($net_total, FALSE), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                // echo $name . $net_total ."<br>";
                $printer -> text("$name$net_total\n");

                $name = "Tax (10%)";
                $tax_total = $t_net_total * 0.1;
                $t_tax = $tax_total;
                $tax_total = str_pad(\MyNumber::toReadableAngka($tax_total, FALSE), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                // echo $name . $tax_total ."<br>";
                $printer -> text("$name$tax_total\n");

                $name = "Total Sales";
                $sales_total = $t_net_total + $t_tax;
                $t_sales_total = $sales_total;
                $sales_total = str_pad(\MyNumber::toReadableAngka($sales_total, FALSE), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                // echo $name . $tax_total ."<br>";
                $printer -> text("$name$sales_total\n");

                // SALES DETAILS
                $printer -> feed(1);
                $printer -> text("CASHIER TOTAL\n");
                $printer -> text("================================================");

                $transaction_by_cashier = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->where(['status'=>'finished'])
                    ->groupBy('user_id')
                    ->get();

                $total_sum = 0;
                $total_count = 0;
                foreach($transaction_by_cashier as $tbm) {
                    $name = $tbm->user->name;
                    $total_sum += $tbm->sum;
                    $total_count += $tbm->count;
                    $count = str_pad($tbm->count, 4, ' ', STR_PAD_LEFT);
                    $sum = str_pad(\MyNumber::toReadableAngka($tbm->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                    // echo $count ."_". $name . $sum ."<br>";
                    $printer -> text("$count $name$sum\n");
                }

                $name = str_pad("** TOTAL **", 4, ' ', STR_PAD_LEFT);
                $total_count = str_pad($total_count, 4, ' ', STR_PAD_LEFT);
                $total_sum = str_pad(\MyNumber::toReadableAngka($total_sum, FALSE), (48 - (strlen($name) + strlen($total_count) + 1)), ' ', STR_PAD_LEFT);
                $printer -> text("$total_count $name$total_sum\n");

                // CAT PRICE DETAILS
                $printer -> feed(1);
                $printer -> text("CATEGORY PRICE\n");
                $printer -> text("================================================");

                $transaction_by_pc = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->where(['status'=>'finished'])
                    ->groupBy('price_category')
                    ->get();

                $total_sum = 0;
                $total_count = 0;
                foreach($transaction_by_pc as $tbm) {
                    if ($tbm->price_category === 'general') {
                        $tbm->price_category = 'outlet';
                    }

                    $name = ucfirst($tbm->price_category);
                    $total_sum += $tbm->sum;
                    $total_count += $tbm->count;
                    $count = str_pad($tbm->count, 4, ' ', STR_PAD_LEFT);
                    $sum = str_pad(\MyNumber::toReadableAngka($tbm->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                    $printer -> text("$count $name$sum\n");
                }

                $name = str_pad("** TOTAL **", 4, ' ', STR_PAD_LEFT);
                $total_count = str_pad($total_count, 4, ' ', STR_PAD_LEFT);
                $total_sum = str_pad(\MyNumber::toReadableAngka($total_sum, FALSE), (48 - (strlen($name) + strlen($total_count) + 1)), ' ', STR_PAD_LEFT);
                // echo $total_count ."_". $name . $total_sum ."<br>";
                $printer -> text("$total_count $name$total_sum\n");

                // TYPE DETAILS
                $printer -> feed(1);
                $printer -> text("TYPE\n");
                $printer -> text("================================================");

                $transaction_by_type = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->where(['status'=>'finished'])
                    ->groupBy('type')
                    ->get();

                $total_sum = 0;
                $total_count = 0;
                foreach($transaction_by_type as $tbm) {
                    $name = $tbm->type;
                    if ($name == 'disajikan')
                        $name = 'Hidang';
                    else if ($name == 'rames')
                        $name = 'Rames Meja';
                    else
                        $name = 'Takeaway';
                    $total_sum += $tbm->sum;
                    $total_count += $tbm->count;
                    $count = str_pad($tbm->count, 4, ' ', STR_PAD_LEFT);
                    $sum = str_pad(\MyNumber::toReadableAngka($tbm->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                    // echo $count ."_". $name . $sum ."<br>";
                    $printer -> text("$count $name$sum\n");
                }

                $name = str_pad("** TOTAL **", 4, ' ', STR_PAD_LEFT);
                $total_count = str_pad($total_count, 4, ' ', STR_PAD_LEFT);
                $total_sum = str_pad(\MyNumber::toReadableAngka($total_sum, FALSE), (48 - (strlen($name) + strlen($total_count) + 1)), ' ', STR_PAD_LEFT);
                // echo $total_count ."_". $name . $total_sum ."<br>";
                $printer -> text("$total_count $name$total_sum\n");

                // TIME BREAKDOWN
                $printer -> feed(1);
                $printer -> text("TIME BREAKDOWN\n");
                $printer -> text("================================================");

                $total_sum = 0;
                $total_count = 0;
                $transaction_by_morning = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->whereRaw('created_at BETWEEN curdate() AND CONCAT(CURDATE(), " 17:59:59")')
                    ->where(['status'=>'finished']);

                // foreach($transaction_by_type as $tbm) {
                if ($transaction_by_morning->count() > 0) {
                    $tb_m = $transaction_by_morning->first();
                    $name = "Morning (Open - 6 PM)";
                    $total_sum += $tb_m->sum;
                    $total_count += $tb_m->count;
                    $count = str_pad($tb_m->count, 4, ' ', STR_PAD_LEFT);
                    $sum = str_pad(\MyNumber::toReadableAngka($tb_m->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                    // echo $count ."_". $name . $sum ."<br>";
                    $printer -> text("$count $name$sum\n");
                }

                $transaction_by_afternoon = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->whereRaw('created_at BETWEEN CONCAT(CURDATE(), " 18:00:00") AND CONCAT(CURDATE(), " 23:59:59")')
                    ->where(['status'=>'finished']);

                // foreach($transaction_by_type as $tbm) {
                if ($transaction_by_afternoon->count() > 0) {
                    $tb_m = $transaction_by_afternoon->first();
                    $name = "Afternoon (6 PM - Close)";
                    $total_sum += $tb_m->sum;
                    $total_count += $tb_m->count;
                    $count = str_pad($tb_m->count, 4, ' ', STR_PAD_LEFT);
                    $sum = str_pad(\MyNumber::toReadableAngka($tb_m->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                    // echo $count ."_". $name . $sum ."<br>";
                    $printer -> text("$count $name$sum\n");
                }
                // }

                $name = str_pad("** TOTAL **", 4, ' ', STR_PAD_LEFT);
                $total_count = str_pad($total_count, 4, ' ', STR_PAD_LEFT);
                $total_sum = str_pad(\MyNumber::toReadableAngka($total_sum, FALSE), (48 - (strlen($name) + strlen($total_count) + 1)), ' ', STR_PAD_LEFT);
                // echo $total_count ."_". $name . $total_sum ."<br>";
                $printer -> text("$total_count $name$total_sum\n");


                // SALES DETAILS
                $printer -> feed(1);
                $printer -> text("TRANSACTION DETAIL\n");
                $printer -> text("================================================");

                // FIRST RECEIPT
                $first_rc = Transaction::whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                ->where(['status'=>'finished'])
                ->limit(1)
                ->orderBy('transaction_id', 'asc');

                if ($first_rc->count() > 0) {
                    $frc = $first_rc->first();
                    $name = "First Receipt";
                    $code = str_pad($frc->code(), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                    // echo $name . $code ."<br>";
                    $printer -> text("$name$code\n");
                }

                // LAST RECEIPT
                $last_rc = Transaction::whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                ->where(['status'=>'finished'])
                ->limit(1)
                ->orderBy('transaction_id', 'desc');

                if ($last_rc->count() > 0) {
                    $lrc = $last_rc->first();
                    $name = "Last Receipt";
                    $code = str_pad($lrc->code(), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                    // echo $name . $code ."<br>";
                    $printer -> text("$name$code\n");
                }

                // COUNT RECEIPT
                $no_rc = Transaction::selectRaw('count(*) as count, sum(grand_total) as sum')
                // ->where(['status'=>'finished'])
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY) and status = "finished"')
                    ->first();

                // dd($no_rc);

                $name = "No. of Receipt";
                $code = str_pad($no_rc->count, (48 - strlen($name)), ' ', STR_PAD_LEFT);
                // echo $name . $code ."<br>";
                $printer -> text("$name$code\n");

                $name = "Avg per Receipt";
                if ($last_rc->count() > 0) {
                    $code = str_pad(\MyNumber::toReadableAngka($no_rc->sum / $no_rc->count, FALSE), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                } else {
                    $code = str_pad(\MyNumber::toReadableAngka(0, FALSE), (48 - strlen($name)), ' ', STR_PAD_LEFT);
                }
                // echo $name . $code ."<br>";
                $printer -> text("$name$code\n");

                // VOID
                $printer -> feed(1);
                $printer -> text("VOID DETAIL\n");
                $printer -> text("================================================");

                $transaction_get = Transaction::selectRaw('*, sum(grand_total) as sum')
                    ->with('paymentMethod')
                    ->where([
                        ['status', '=', 'void']
                    ])
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->groupBy('transaction_id')
                    ->get();

                foreach($transaction_get as $tbm_dt) {
                    $name = $tbm_dt->code() ." by SPV";
                    $sum = str_pad(\MyNumber::toReadableAngka($tbm_dt->sum, FALSE), (48 - (strlen($name))), ' ', STR_PAD_LEFT);
                    // echo $name . $sum ."<br>";
                    $printer -> text("$name$sum\n");
                    $remarks = $tbm_dt->remarks;
                    if ($remarks!='')
                        $printer -> text("   **$remarks\n");
                }

                // TOTAL VOID
                $transaction_get = Transaction::selectRaw('*, sum(grand_total) as sum')
                    ->with('paymentMethod')
                    ->where([
                        ['status', '=', 'void']
                    ])
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->first();
                $name = "** TOTAL **";
                $count = str_pad(' ', 4, ' ', STR_PAD_LEFT);
                $sum = str_pad(\MyNumber::toReadableAngka($transaction_get->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                    // echo $name . $sum ."<br>";
                $printer -> text("$count $name$sum\n");

                // LOST
                $printer -> feed(1);
                $printer -> text("LOST BILL DETAIL\n");
                $printer -> text("================================================");

                $transaction_get = Transaction::selectRaw('*, sum(grand_total) as sum')
                    ->with('paymentMethod')
                    ->where([
                        ['status', '=', 'lost']
                    ])
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->groupBy('transaction_id')
                    ->get();

                foreach($transaction_get as $tbm_dt) {
                    $name = $tbm_dt->code();
                    $sum = str_pad(\MyNumber::toReadableAngka($tbm_dt->sum, FALSE), (48 - (strlen($name))), ' ', STR_PAD_LEFT);
                    // echo $name . $sum ."<br>";
                    $printer -> text("$name$sum\n");
                }

                // TOTAL LOST
                $transaction_get = Transaction::selectRaw('*, sum(grand_total) as sum')
                    ->with('paymentMethod')
                    ->where([
                        ['status', '=', 'lost']
                    ])
                    ->whereRaw('created_at BETWEEN curdate() AND DATE_ADD(curdate(), INTERVAL 1 DAY)')
                    ->first();
                $name = "** TOTAL **";
                $count = str_pad(' ', 4, ' ', STR_PAD_LEFT);
                $sum = str_pad(\MyNumber::toReadableAngka($transaction_get->sum, FALSE), (48 - (strlen($name) + strlen($count) + 1)), ' ', STR_PAD_LEFT);
                    // echo $name . $sum ."<br>";
                $printer -> text("$count $name$sum\n");
                $printer -> feed(2);
                $printer -> cut();

            } finally {
                $printer -> close();
            }

            // $connector = new WindowsPrintConnector("smb://localhost/bar");
            // $printer = new Printer($connector);
        }
        return Redirect::back();

    }

    public function voidOrder(Request $request, $id)
    {
        $o = Transaction::where(['transaction_id'=>$id]);
        if ($o->count() > 0) {
            $order = $o->first();
            $order->void_by = $request->data['user_id'];
            $order->remarks = $request->data['remarks'];
            $order->status = 'void';

            if ($order->save())
                return response()->json(['status' => true, 'transaction_id' => $order->transaction_id, 'table_id' => $order->table_id], 200);
        }
        return response()->json(['status' => false], 500);
    }

    public function lostOrder(Request $request, $id)
    {
        $o = Transaction::where(['transaction_id'=>$id]);
        if ($o->count() > 0) {
            $order = $o->first();
            $order->void_by = $request->data['user_id'];
            // $order->remarks = $request->data['remarks'];
            $order->status = 'lost';

            if ($order->save())
                return response()->json(['status' => true, 'transaction_id' => $order->transaction_id, 'table_id' => $order->table_id], 200);
        }
        return response()->json(['status' => false], 500);
    }

    public function refreshCurrentOrderList(Request $request) {
        $transaction_current = Transaction::orderBy('transaction_id', 'ASC')->whereIn('status', ['pending','printbill'])->get();
        $html = '';
        foreach($transaction_current as $tc):
            $table = ($tc->table_id) ? '<strong>MEJA '. $tc->table_id .'</strong>' : 'Takeaway';
            $name = ($tc->name) ? $tc->name : '-';
            $note = ($tc->note) ? $tc->note : '-';
            $html .= "
            <tr>
                <td>". $tc->code() ."</td>
                <td>". \MyDate::toReadableDate($tc->created_at, FALSE, TRUE) ."</td>
                <td>". $table ."</td>
                <td>". $name ."</td>
                <td>". $note ."</td>
                <td class='text-right'>". \MyNumber::toReadableAngka($tc->grand_total, FALSE) ."</td>
                <td>
                    <a href='". route('transaction.edit', $tc->transaction_id) ."' class='btn btn-info btn-sm btn-block'><i class='fa fa-edit'></i> Edit</a>
                </td>
            </tr>";
        endforeach;

        echo $html;
        exit;
    }












    public function save_btn(Request $request, $id)
    {
        $update = self::update_process($request, $id)->getData();
        if($update->status == 1) {
            $error_printer = [];
            // if(isset($process->original['error_printer'])) {
            //     $error_printer[] = $process->original['error_printer'];
            // }
            return response()->json(['status' => true, 'transaction_id' => $update->order_detail->transaction_id, 'table_id' => $update->order_detail->table_id, 'error_printer' => $error_printer], 200);
        } else {
            return response()->json(['status' => false, 'message' => $update->message], 400);
        }
    }

    //process send receipt to kitchen
    public function send_to_kitchen(Request $request, $id)
    {
        $update = self::update_process($request, $id)->getData();
        if($update->status == 1) {
            //print receipt to kitchen
            $process = self::kitchen_printer($request, $update->order_detail);
            $error_printer = [];
            if(isset($process->original['error_printer'])) {
                $error_printer[] = $process->original['error_printer'];
            }
            return response()->json(['status' => true, 'transaction_id' => $process->original['transaction_id'], 'table_id' => $process->original['table_id'], 'error_printer' => $error_printer], 200);
        } else {
            return response()->json(['status' => false, 'message' => $update->message], 400);
        }
    }

    //print receipt to kitchen
    public function kitchen_printer(Request $request, $order)
    {
        $print_list = [];

        if ($request->input('data.kitchen_list') !== null) {
            for ($i=0; $i < count($request->input('data.kitchen_list')); $i++) {
                $t_qty = $request->input('data.kitchen_list.'.$i.".qty");
                $t_note = $request->input('data.kitchen_list.'.$i.".note");
                $t_id = $request->input('data.kitchen_list.'.$i.".id");

                $product = Product::where(['product_id'=>$t_id])->first();
                $printer_id = $request->input('data.kitchen_list.'.$i.".printer");
                if ($printer_id == "" || $printer_id == 0) {
                    continue;
                }
                if (!isset($print_list[$printer_id])) {
                    $print_list[$printer_id] = [];
                }
                array_push($print_list[$printer_id], [
                    'id' => $t_id,
                    'name' => $product->name,
                    'qty' => $t_qty,
                    'note' => $t_note
                ]);
            }

            foreach ($print_list as $key=>$value) {
                $printer_model = PrinterModel::where(['printer_id'=>$key]);
                if ($printer_model->count() > 0) {
                    $p_item = $printer_model->first();
                    $dt = MyDate::toReadableDate($order->created_at, false, true);
                    $user_id = auth()->user()->name;
                    $printer_name = strtoupper($p_item->name);
                    $table = '-';
                    if ($order->table_id) {
                        $table = $order->table_id;
                    }

                    $type = $order->type;
                    switch($type) {
                        case 'disajikan': $type = "Dine In (HIDANG)"; break;
                        case 'rames': $type = "Dine In (KHUSUS)"; break;
                        default: $type = "Takeaway"; break;
                    }

                    try {
                        $fp = fsockopen($p_item->address, $p_item->port, $errno, $errstr, 2);
                        if ($fp) {

                            $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
                            $printer = new Printer($connector);

                            try {

                                /* Initialize */
                                $printer -> initialize();
                                $printer -> setJustification(Printer::JUSTIFY_CENTER);
                                $printer -> text("\n");
                                $printer -> text("<< $printer_name >>\n");
                                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                                $printer -> text("Meja $table\n");
                                $printer -> selectPrintMode();
                                $printer -> setJustification(); // Reset

                                $printer -> feed(2);
                                $printer -> text("Tipe   : $type \n");
                                $printer -> text("Kasir  : $user_id\n");
                                $printer -> text("Tgl    : $dt\n");
                                if ($order->name != '') {
                                    $nm = $order->name;
                                    $printer -> text("Nama   : $nm\n");
                                }
                                $printer -> feed(2);

                                /* Text */
                                $printer -> text("------------------------------------------------");
                                $printer -> feed(2);
                                $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

                                foreach ($value as $item) {
                                    $qty = str_pad($item['qty'], 3, ' ', STR_PAD_LEFT);
                                    $name = $item['name'];
                                    $note = $item['note'];
                                    $printer -> text("$qty  $name\n");
                                    if ($note != '') {
                                        $printer -> text("     **$note\n");
                                    }
                                    $printer -> feed(1);
                                }

                                $printer -> selectPrintMode();
                                $printer -> text("------------------------------------------------");
                                $printer -> feed(2);
                                $printer -> cut();

                            } finally {
                                $printer -> close();
                            }

                        }
                    } catch(\Exception $e) {
                        $error_printer = "<strong>". $p_item->name ."</strong> Error / Kertas Habis";
                    } finally {

                    }
                }
            }

        } else {
            $error_printer = "Failed send receipt to kitchen";
        }
        return response(array(
                'error_printer'     => $error_printer,
                'table_id'          => $order->table_id,
                'transaction_id'    => $order->transaction_id
            ));
    }

    //print bill
    public function print_bill(Request $request, $id)
    {
        $update = self::update_process($request, $id)->getData();

        if($update->status == 1) {

            $order = $update->order_detail;

            $error_printer = [];

            try {

                $fp = fsockopen(self::$printer_ip, 9100, $errno, $errstr, 2);

                if ($fp) {

                    $connector = new NetworkPrintConnector(self::$printer_ip, 9100);
                    // $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
                    $printer = new Printer($connector);

                    // $connector = new WindowsPrintConnector("smb://localhost/bar");
                    // $printer = new Printer($connector);

                    try {

                        /* Initialize */
                        $printer -> initialize();
                        $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        $logo = EscposImage::load("img/logostruk.png", false);
                        $printer -> bitImage($logo, Printer::IMG_DEFAULT);
                        $printer -> feed(2);
                        $printer -> setJustification(); // Reset

                        $transaction = new Transaction();
                        $trans_id = $transaction->code();
                        $dt = MyDate::toReadableDate($order->created_at, false, true);

                        $table = '-';
                        if ($order->table_id) {
                            $table = $order->table_id;
                        }

                        // $PaymentMethod = PaymentMethod::where(['payment_method_id'=>$order->payment_method_id])->get();
                        // $pm = '-';
                        // if ($order->payment_method_id != null) {
                        //     $pm = $PaymentMethod[0]->name;
                        // }

                        $kasir = auth()->user()->name;

                        $type = $order->type;
                        switch($type) {
                            case 'disajikan': $type = "Dine In (HIDANG)"; break;
                            case 'rames': $type = "Dine In (KHUSUS)"; break;
                            default: $type = "Takeaway"; break;
                        }

                        $printer -> text("Order #       : $trans_id \n");
                        $printer -> text("Meja          : $table \n");
                        $printer -> text("Tipe          : $type \n");
                        $printer -> text("Kasir         : $kasir \n");
                        $printer -> text("Tanggal       : $dt\n");
                        // $printer -> text("Pembayaran    : $pm\n");

                        if ($order->name != '') {
                            $nm = $order->name;
                            $printer -> text("Nama          : $nm\n");
                        }

                        /* Text */
                        $printer -> text("------------------------------------------------");
                        $printer -> feed(2);

                        $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
                        foreach ($transaction_detail as $td) {
                            $qty = str_pad($td->quantity, (3 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
                            $name = $td->product->name;
                            $subt = MyNumber::toReadableAngka($td->subtotal, false);
                            $space = (48 - (strlen($qty) + strlen($name) + 4) );
                            $subt = str_pad($subt, $space, ' ', STR_PAD_LEFT);
                            $note = $td->note;
                            // echo $qty; exit;
                            // $name = 'Huckleberry';
                            $printer -> text("$qty  $name  $subt");
                            if ($note != '')
                                $printer -> text("     **$note\n");
                        }

                        $printer -> feed(2);
                        // $printer -> cut();
                        $total = MyNumber::toReadableAngka($order->total, false);
                        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                        // $printer -> text("Total     $total\n");
                        $printer -> setJustification(); // Reset

                        $printer -> text("------------------------------------------------");
                        $printer -> feed(1);
                        // $printer -> cut();

                        $subtotal = MyNumber::toReadableAngka($order->total, false);
                        $subtotal = str_pad($subtotal, (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);
                        $discount_value = "";
                        $discount_amount = "";
                        $discount = "";

                        if ($order->promotion_id) {
                            $promotion = Promotion::where(['promotion_id'=>$order->promotion_id])->get();
                            if ($promotion[0]->type == 'percent') {
                                $discount_value = round($promotion[0]->value)."%";
                                $discount_amount = MyNumber::toReadableAngka($order->total * ($promotion[0]->value / 100), false);
                                $disc_effect = $order->total * ($promotion[0]->value / 100);
                                $tax = MyNumber::toReadableAngka( (($order->total - $disc_effect) * 0.1) , false);
                            } else {
                                $discount_value = "";
                                $discount_amount = MyNumber::toReadableAngka($promotion[0]->value, false);
                                $tax = MyNumber::toReadableAngka( (($order->total - $promotion[0]->value) * 0.1) , false);
                            }
                            $discount = str_pad($discount_amount, (30 - (strlen('Diskon ') + strlen($discount_value)) ), ' ', STR_PAD_LEFT);
                        } else {
                            $tax = MyNumber::toReadableAngka( ($order->total * 0.1) , false);
                        }

                        $tax = str_pad($tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
                        $grandtotal = MyNumber::toReadableAngka($order->grand_total, false);
                        $grandtotal = str_pad($grandtotal, (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);
                        // $paid = MyNumber::toReadableAngka($order->paid, false);
                        // $paid = str_pad($paid, (30 - strlen('Bayar')), ' ', STR_PAD_LEFT);
                        // $payable = MyNumber::toReadableAngka($order->payable, false);
                        // $payable = str_pad($payable, (30 - strlen('Kembali')), ' ', STR_PAD_LEFT);

                        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                        $printer -> text("Subtotal $subtotal\n");
                        if ($discount != "") {
                            $printer -> text("Diskon $discount_value $discount\n");
                        }
                        $printer -> text("Pajak (10%) $tax\n");
                        $printer -> text("Grand Total $grandtotal\n");
                        // $printer -> text("Bayar $paid\n");
                        // $printer -> text("Kembali $payable\n");
                        $printer -> setJustification(); // Reset

                        $printer -> text("------------------------------------------------");
                        $printer -> feed(1);

                        if ($discount != "" && $discount_value == 100) {
                            $printer -> feed(3);
                            $printer -> setJustification(Printer::JUSTIFY_CENTER);
                            $printer -> text("Signature: ___________________________");
                            $printer -> setJustification(); // Reset
                            $printer -> feed(2);
                        }

                        $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        $printer -> text("\n");
                        $printer -> text("Jl. Pluit Indah Raya No. 34\n");
                        $printer -> text("Jakarta Utara\n");
                        $printer -> text("(021) 2266 9155\n");
                        $printer -> text("Terima Kasih\n");
                        $printer -> setJustification(); // Reset

                        $printer -> feed(2);
                        $printer -> cut();

                    } finally {
                        $printer -> close();
                    }

                }


                // $trans_id = $transaction->code();
                // $dt = MyDate::toReadableDate($order->created_at, false, true);
                // $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();

                // $process_print = "\n img/logostruk.png \n";

                // $table = '-';
                // if ($order->table_id) {
                //     $table = $order->table_id;
                // }

                // $pm = '-';
                // $PaymentMethod = PaymentMethod::where(['payment_method_id'=>$order->payment_method_id])->get();
                // if ($order->payment_method_id != null) {
                //     $pm = $PaymentMethod[0]->name;
                // }

                // $kasir = auth()->user()->name;

                // $type = $order->type;
                // switch($type) {
                //     case 'disajikan': $type = "Dine In (HIDANG)"; break;
                //     case 'rames': $type = "Dine In (KHUSUS)"; break;
                //     default: $type = "Takeaway"; break;
                // }

                // $process_print .= "Order #       : $trans_id \n";
                // $process_print .= "Meja          : $table \n";
                // $process_print .= "Tipe          : $type \n";
                // $process_print .= "Kasir         : $kasir \n";
                // $process_print .= "Tanggal       : $dt\n";
                // // $process_print .= "Pembayaran    : $pm\n";

                // if ($order->name != '') {
                //     $nm = $order->name;
                //     $process_print .= "Nama          : $nm\n";
                // }
                // /* Text */
                // $process_print .= "------------------------------------------------\n";

                // foreach ($transaction_detail as $td) {
                //     $qty = str_pad($td->quantity, (3 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
                //     $name = $td->product->name;
                //     $subt = MyNumber::toReadableAngka($td->subtotal, false);
                //     $space = (48 - (strlen($qty) + strlen($name) + 4) );
                //     $subt = str_pad($subt, $space, ' ', STR_PAD_LEFT);
                //     $note = $td->note;
                //     // echo $qty; exit;
                //     // $name = 'Huckleberry';
                //     $process_print .= "$qty  $name  $subt \n";
                //     if ($note != '') {
                //         $process_print .= "     **$note\n";
                //     }
                // }

                // $total = MyNumber::toReadableAngka($order->total, false);

                // $process_print .= "------------------------------------------------\n";
                // $subtotal = MyNumber::toReadableAngka($order->total, false);
                // $subtotal = str_pad($subtotal, (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);
                // $discount_value = "";
                // $discount_amount = "";
                // $discount = "";
                // if ($order->promotion_id) {
                //     $promotion = Promotion::where(['promotion_id'=>$order->promotion_id])->get();
                // // echo '<pre>';print_r($promotion[0]);exit;
                //     if ($promotion[0]->type == 'percent') {
                //         $discount_value = round($promotion[0]->value)."%";
                //         $discount_amount = MyNumber::toReadableAngka($order->total * ($promotion[0]->value / 100), false);
                //         $disc_effect = $order->total * ($promotion[0]->value / 100);
                //         $tax = MyNumber::toReadableAngka( (($order->total - $disc_effect) * 0.1) , false);
                //     } else {
                //         $discount_value = "";
                //         $discount_amount = MyNumber::toReadableAngka($promotion[0]->value, false);
                //         $tax = MyNumber::toReadableAngka( (($order->total - $promotion[0]->value) * 0.1) , false);
                //     }
                //     $discount = str_pad($discount_amount, (30 - (strlen('Diskon ') + strlen($discount_value)) ), ' ', STR_PAD_LEFT);

                // } else {
                //     $tax = MyNumber::toReadableAngka( ($order->total * 0.1) , false);
                // }

                // $tax = str_pad($tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
                // $grandtotal = MyNumber::toReadableAngka($order->grand_total, false);
                // $grandtotal = str_pad($grandtotal, (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);
                // // $paid = MyNumber::toReadableAngka($order->paid, false);
                // // $paid = str_pad($paid, (30 - strlen('Bayar')), ' ', STR_PAD_LEFT);
                // // $payable = MyNumber::toReadableAngka($order->payable, false);
                // // $payable = str_pad($payable, (30 - strlen('Kembali')), ' ', STR_PAD_LEFT);

                // $process_print .= "Subtotal $subtotal\n";
                // if ($discount != "") {
                //     $process_print .= "Diskon $discount_value $discount\n";
                // }
                // $process_print .= "Pajak (10%) $tax\n";
                // $process_print .= "Grand Total $grandtotal\n";
                // // $printer -> text("Bayar $paid\n");
                // // $printer -> text("Kembali $payable\n");

                // $process_print .= "------------------------------------------------";

                // if ($discount != "" && $discount_value == 100) {
                //     $process_print .= "Signature: ___________________________";
                // }

                // $process_print .= "\n";
                // $process_print .= "Jl. Pluit Indah Raya No. 34\n";
                // $process_print .= "Jakarta Utara\n";
                // $process_print .= "(021) 2266 9155\n";
                // $process_print .= "Terima Kasih\n";

                // echo '<pre>';print_r($process_print);exit;


            } catch(\Exception $e) {
                // echo "Error Printer " . $p_item->name;
                $error_printer[] = "<strong>Cashier</strong> Printer Error / Kertas Habis";
            } finally {

            }

            return response()->json(
                    [
                        'status'            => true,
                        'transaction_id'    => $order->transaction_id,
                        'table_id'          => $order->table_id,
                        'error_printer'     => $error_printer
                    ], 200
                );

        } else {
            return response()->json(['status' => false, 'message' => $update->message], 400);
        }
    }

    //finish order
    public function finish_order(Request $request, $id)
    {
        $update = self::update_process($request, $id)->getData();
        if($update->status == 1) {
            $process = self::finish_order_process($request, $id, $update->order_detail);
            $error_printer = [];
            if(isset($process->original['error_printer'])) {
                $error_printer[] = $process->original['error_printer'];
            }
            return response()->json(['status' => true, 'transaction_id' => $process->original['transaction_id'], 'table_id' => $process->original['table_id'], 'error_printer' => $error_printer], 200);
        } else {
            return response()->json(['status' => false, 'message' => $update->message], 400);
        }
    }

    //process print receipt
    public function finish_order_process(Request $request, $id, $order_detail)
    {
        $order = $order_detail;

        $error_printer = "";

        try {

            $fp = fsockopen(self::$printer_ip, 9100, $errno, $errstr, 2);

            if ($fp) {

                $connector = new NetworkPrintConnector(self::$printer_ip, 9100);
                $printer = new Printer($connector);

                for ($i=0; $i<2; $i++) {

                    try {
                        /* Initialize */
                        // $printer -> initialize();

                        // $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
                        // $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        // $logo = EscposImage::load("img/logostruk.png", false);
                        // $printer -> bitImage($logo, Printer::IMG_DEFAULT);
                        // $printer -> feed(2);
                        // $printer -> setJustification(); // Reset

                        // $transaction = new Transaction();
                        // $trans_id = $transaction->code();

                        // $dt = MyDate::toReadableDate($order->created_at, false, true);

                        // $table = '-';
                        // if ($order->table_id) {
                        //     $table = $order->table_id;
                        // }

                        // $pm = '-';
                        // if ($order->payment_method_id != null) {
                        //     $pm = $order->PaymentMethod->name;
                        // }

                        // $note = '';
                        // if ($order->note != '') {
                        //     $note = $order->note;
                        // }

                        // $kasir = auth()->user()->name;

                        // $type = $order->type;
                        // switch($type) {
                        //     case 'disajikan': $type = "Dine In (HIDANG)"; break;
                        //     case 'rames': $type = "Dine In (KHUSUS)"; break;
                        //     default: $type = "Takeaway"; break;
                        // }

                        // $printer -> text("Order #       : $trans_id \n");
                        // $printer -> text("Meja          : $table \n");
                        // $printer -> text("Tipe          : $type \n");
                        // $printer -> text("Kasir         : $kasir \n");
                        // $printer -> text("Tanggal       : $dt\n");
                        // $printer -> text("Pembayaran    : $pm\n");

                        // if ($note != ''){
                        //     $printer -> text("Note          : $note\n");
                        // }

                        // if ($order->name != '') {
                        //     $nm = $order->name;
                        //     $printer -> text("Nama          : $nm\n");
                        // }

                        // /* Text */
                        // $printer -> text("------------------------------------------------");
                        // $printer -> feed(2);

                        // foreach ($transaction_detail as $td) {
                        //     $qty = str_pad($td->quantity, (3 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
                        //     $name = $td->product->name;
                        //     $subt = MyNumber::toReadableAngka($td->subtotal, false);
                        //     $space = (48 - (strlen($qty) + strlen($name) + 4) );
                        //     $subt = str_pad($subt, $space, ' ', STR_PAD_LEFT);
                        //     $note = $td->note;

                        //     $printer -> text("$qty  $name  $subt");
                        //     if ($note != '') {
                        //         $printer -> text("     **$note\n");
                        //     }
                        // }

                        // $printer -> feed(2);
                        // // $printer -> cut();
                        // $total = MyNumber::toReadableAngka($order->total, false);
                        // $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                        // // $printer -> text("Total     $total\n");
                        // $printer -> setJustification(); // Reset

                        // $printer -> text("------------------------------------------------");
                        // $printer -> feed(1);
                        // // $printer -> cut();

                        // $subtotal = MyNumber::toReadableAngka($order->total, false);
                        // $subtotal = str_pad($subtotal, (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);
                        // $discount_value = "";
                        // $discount_amount = "";
                        // $discount = "";

                        // if ($order->promotion_id) {
                        //     $promotion = Promotion::where(['promotion_id'=>$order->promotion_id])->get();
                        //     if ($promotion[0]->type == 'percent') {
                        //         $discount_value = round($promotion[0]->value)."%";
                        //         $discount_amount = MyNumber::toReadableAngka($order->total * ($promotion[0]->value / 100), false);
                        //         $disc_effect = $order->total * ($promotion[0]->value / 100);
                        //         $tax = MyNumber::toReadableAngka( (($order->total - $disc_effect) * 0.1) , false);
                        //     } else {
                        //         $discount_value = "";
                        //         $discount_amount = MyNumber::toReadableAngka($promotion[0]->value, false);
                        //         $tax = MyNumber::toReadableAngka( (($order->total - $promotion[0]->value) * 0.1) , false);
                        //     }
                        //     $discount = str_pad($discount_amount, (30 - (strlen('Diskon ') + strlen($discount_value)) ), ' ', STR_PAD_LEFT);
                        // } else {
                        //     $tax = MyNumber::toReadableAngka( ($order->total * 0.1) , false);
                        // }

                        // $tax = str_pad($tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
                        // $grandtotal = MyNumber::toReadableAngka($order->grand_total, false);
                        // $grandtotal = str_pad($grandtotal, (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);
                        // $paid = MyNumber::toReadableAngka($order->paid, false);
                        // $paid = str_pad($paid, (30 - strlen('Bayar')), ' ', STR_PAD_LEFT);
                        // $payable = MyNumber::toReadableAngka($order->payable, false);
                        // $payable = str_pad($payable, (30 - strlen('Kembali')), ' ', STR_PAD_LEFT);

                        // $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                        // $printer -> text("Subtotal $subtotal\n");
                        // if ($discount != "") {
                        //     $printer -> text("Diskon $discount_value $discount\n");
                        // }
                        // $printer -> text("Pajak (10%) $tax\n");
                        // $printer -> text("Grand Total $grandtotal\n");
                        // $printer -> text("Bayar $paid\n");
                        // $printer -> text("Kembali $payable\n");
                        // $printer -> setJustification(); // Reset

                        // $printer -> text("------------------------------------------------");
                        // $printer -> feed(1);

                        // if ($discount != "" && $discount_value == 100) {
                        //     $printer -> feed(3);
                        //     $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        //     $printer -> text("Signature: ___________________________");
                        //     $printer -> setJustification(); // Reset
                        //     $printer -> feed(2);
                        // }

                        // $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        // $printer -> text("\n");
                        // $printer -> text("Jl. Pluit Indah Raya No. 34\n");
                        // $printer -> text("Jakarta Utara\n");
                        // $printer -> text("(021) 2266 9155\n");
                        // $printer -> text("Terima Kasih\n");

                        // $printer -> setJustification(); // Reset

                        // $printer -> feed(2);
                        // $printer -> cut();

                        /* Initialize */
                        $printer -> initialize();

                        $transaction_detail = TransactionDetail::where(['transaction_id'=>$id])->get();
                        $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        $logo = EscposImage::load("img/logostruk.png", false);
                        $printer -> bitImage($logo, Printer::IMG_DEFAULT);
                        $printer -> feed(2);
                        $printer -> setJustification(); // Reset

                        $trans_id = $order->code();
                        $dt = MyDate::toReadableDate($order->created_at, false, true);
                        $table = '-';
                        if ($order->table)
                            $table = $order->table->number;

                        $pm = '-';
                        if ($order->payment_method_id != null)
                            $pm = $order->PaymentMethod->name;

                        $note = '';
                        if ($order->note != '')
                            $note = $order->note;

                        $kasir = auth()->user()->name;

                        $type = $order->type;
                        switch($type) {
                            case 'disajikan': $type = "Dine In (HIDANG)"; break;
                            case 'rames': $type = "Dine In (KHUSUS)"; break;
                            default: $type = "Takeaway"; break;
                        }

                        $printer -> text("Order #       : $trans_id \n");
                        $printer -> text("Meja          : $table \n");
                        $printer -> text("Tipe          : $type \n");
                        $printer -> text("Kasir         : $kasir \n");
                        $printer -> text("Tanggal       : $dt\n");
                        $printer -> text("Pembayaran    : $pm\n");
                        if ($note != '')
                            $printer -> text("Note          : $note\n");

                        if ($order->name != '') {
                            $nm = $order->name;
                            $printer -> text("Nama          : $nm\n");
                        }

                        /* Text */
                        $printer -> text("------------------------------------------------");
                        $printer -> feed(2);

                        foreach ($transaction_detail as $td) {
                            $qty = str_pad($td->quantity, (3 - strlen($td->quantity)), ' ', STR_PAD_LEFT); // 5
                            $name = $td->product->name;
                            $subt = MyNumber::toReadableAngka($td->subtotal, false);
                            $space = (48 - (strlen($qty) + strlen($name) + 4) );
                            $subt = str_pad($subt, $space, ' ', STR_PAD_LEFT);
                            $note = $td->note;
                            // echo $qty; exit;
                            // $name = 'Huckleberry';
                            $printer -> text("$qty  $name  $subt");
                            if ($note != '')
                                $printer -> text("     **$note\n");
                        }

                        $printer -> feed(2);
                        // $printer -> cut();
                        $total = MyNumber::toReadableAngka($order->total, false);
                        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                        // $printer -> text("Total     $total\n");
                        $printer -> setJustification(); // Reset

                        $printer -> text("------------------------------------------------");
                        $printer -> feed(1);
                        // $printer -> cut();

                        $subtotal = MyNumber::toReadableAngka($order->total, false);
                        $subtotal = str_pad($subtotal, (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);
                        $discount_value = "";
                        $discount_amount = "";
                        $discount = "";
                        if ($order->promotion) {
                            if ($order->promotion->type == 'percent') {
                                $discount_value = round($order->promotion->value)."%";
                                $discount_amount = MyNumber::toReadableAngka($order->total * ($order->promotion->value / 100), false);
                                $disc_effect = $order->total * ($order->promotion->value / 100);
                                $tax = MyNumber::toReadableAngka( (($order->total - $disc_effect) * 0.1) , false);
                            } else {
                                $discount_value = "";
                                $discount_amount = MyNumber::toReadableAngka($order->promotion->value, false);
                                $tax = MyNumber::toReadableAngka( (($order->total - $order->promotion->value) * 0.1) , false);
                            }
                            $discount = str_pad($discount_amount, (30 - (strlen('Diskon ') + strlen($discount_value)) ), ' ', STR_PAD_LEFT);

                        } else {
                            $tax = MyNumber::toReadableAngka( ($order->total * 0.1) , false);
                        }


                        $tax = str_pad($tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
                        $grandtotal = MyNumber::toReadableAngka($order->grand_total, false);
                        $grandtotal = str_pad($grandtotal, (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);
                        $paid = MyNumber::toReadableAngka($order->paid, false);
                        $paid = str_pad($paid, (30 - strlen('Bayar')), ' ', STR_PAD_LEFT);
                        $payable = MyNumber::toReadableAngka($order->payable, false);
                        $payable = str_pad($payable, (30 - strlen('Kembali')), ' ', STR_PAD_LEFT);

                        $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                        $printer -> text("Subtotal $subtotal\n");
                        if ($discount != "")
                            $printer -> text("Diskon $discount_value $discount\n");
                        $printer -> text("Pajak (10%) $tax\n");
                        $printer -> text("Grand Total $grandtotal\n");
                        $printer -> text("Bayar $paid\n");
                        $printer -> text("Kembali $payable\n");
                        $printer -> setJustification(); // Reset

                        $printer -> text("------------------------------------------------");
                        $printer -> feed(1);

                        if ($discount != "" && $discount_value == 100) {
                            $printer -> feed(3);
                            $printer -> setJustification(Printer::JUSTIFY_CENTER);
                            $printer -> text("Signature: ___________________________");
                            $printer -> setJustification(); // Reset
                            $printer -> feed(2);
                        }

                        $printer -> setJustification(Printer::JUSTIFY_CENTER);
                        $printer -> text("\n");
                        $printer -> text("Jl. Pluit Indah Raya No. 34\n");
                        $printer -> text("Jakarta Utara\n");
                        $printer -> text("(021) 2266 9155\n");
                        $printer -> text("Terima Kasih\n");
                        $printer -> setJustification(); // Reset

                        $printer -> feed(2);
                        $printer -> cut();

                    } finally {
                        $printer -> close();
                    }

                };

            }

        } catch(\Exception $e) {
            $error_printer = "<strong>Cashier</strong> Printer Error / Kertas Habis";
        } finally {

        }

        return response(array(
                'transaction_id'    => $order->transaction_id,
                'table_id'          => $order->table_id,
                'error_printer'     => $error_printer
            ));
    }

    //process new transaction
    public function new_order (Request $request)
    {
        $order = new Transaction;

        $order->user_id = $request->data['user_id'];
        $order->table_id = $request->data['table_id'] == 0 ? null : $request->data['table_id'];
        $order->promotion_id = $request->data['promotion_id'] == 0 ? null : $request->data['promotion_id'];
        $order->type = $request->data['type'];
        $order->payment_method_id = $request->data['payment_method'];
        $order->price_category = $request->data['price'];
        $order->total = $request->data['total'];
        $order->grand_total = $request->data['grand_total'];
        $order->discount = $request->data['discount'];
        $order->paid = $request->data['paid'];
        $order->payable = $request->data['payable'];
        $order->note = $request->data['note'];
        $order->name = $request->data['name'];
        $order->status = $request->data['status'];

        $error_printer = [];

        // Check Error
        $check_main = Transaction::where(['table_id' => $order->table_id])
            ->whereRaw('created_at BETWEEN NOW() - INTERVAL 1 MINUTE AND NOW()');

        if ($order->table_id!= null && $check_main->count() > 0) {
            return response()->json(['status' => false], 500);
        }

        if($order->save()) {
            //print to kitchen
            if ($request->input('data.kitchen_list') !== null) {
                $kitchen_process = self::kitchen_printer($request, $order);
                if(isset($kitchen_process->original['error_printer'])) {
                    $error_printer[] = $kitchen_process->original['error_printer'];
                }
            }

            //save transaction item
            for ($i=0; $i < count($request->input('data.items')); $i++) {
                $order_detail = [
                    'transaction_id'    => $order->transaction_id,
                    'product_id'        => $request->input('data.items.'.$i.".id"),
                    'quantity'          => $request->input('data.items.'.$i.".quantity"),
                    'price' => $request->input('data.items.'.$i.".price"),
                    'note' => $request->input('data.items.'.$i.".note"),
                    'subtotal'      => $request->input('data.items.'.$i.".subtotal")
                ];
                $check_transaction = TransactionDetail::where(['transaction_id'=>$order->transaction_id, 'product_id'=>$request->input('data.items.'.$i.".id")]);
                if($check_transaction->count() == 0)
                    \DB::table('transaction_detail')->insert($order_detail);
            }

            //if finished e.g from takeway
            if ($request->data['status'] == 'finished') {
                $id = $order->transaction_id;
                $finish = self::finish_order_process($request, $id, $order);
                if(isset($finish->original['error_printer'])) {
                    $error_printer[] = $finish->original['error_printer'];
                }
            }

            return response()->json(['status' => true, 'transaction_id' => $order->transaction_id, 'table_id' => $order->table_id, 'error_printer' => $error_printer], 200);

        } else {
            return response()->json(['status' => false], 500);
        }
    }

    //process updating transaction
    public function update_process(Request $request, $id)
    {
        $o = Transaction::where(['transaction_id' => $id]);
        if ($o->count() > 0) {
            $order = $o->first();
            $order->user_id = $request->data['user_id'];
            $order->table_id = $request->data['table_id'] == 0 ? null : $request->data['table_id'];
            $order->promotion_id = $request->data['promotion_id'] == 0 ? null : $request->data['promotion_id'];
            $order->type = $request->data['type'];
            $order->payment_method_id = $request->data['payment_method'];
            $order->price_category = $request->data['price'];
            $order->total = $request->data['total'];
            $order->grand_total = $request->data['grand_total'];
            $order->discount = $request->data['discount'];
            $order->paid = $request->data['paid'];
            $order->payable = $request->data['payable'];
            $order->note = $request->data['note'];
            $order->name = $request->data['name'];
            $order->status = $request->data['status'];

            if($order->save()) {
                //delete old product detail
                $order->detail()->delete();

                foreach($request->input('data.items') as $key => $item) {
                    $order_detail = [
                        'transaction_id' => $id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'note' => $item['note'],
                        'subtotal' => $item['subtotal']
                    ];

                    $check_transaction = TransactionDetail::where([
                            'transaction_id'    =>$order->transaction_id,
                            'product_id'        =>$item['id']
                        ]);

                    if($check_transaction->count() == 0) {
                        \DB::table('transaction_detail')->insert($order_detail);
                    }
                }
            }

            return response()->json(
                [
                    'status'        => true,
                    'message'       => 'Success update transaction',
                    'order_detail'  => $order
                ], 200
            );

        } else {
            return response()->json(['status' => false, 'message' => 'Transaction not found'], 400);
        }
    }

}
