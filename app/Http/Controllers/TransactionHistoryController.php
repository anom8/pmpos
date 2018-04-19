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
use Maatwebsite\Excel\Facades\Excel;
use GuzzleHttp\Client;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Printer as PrinterModel;
use App\Models\Guest;
use App\Models\User;
use App\Models\Csvdatas;
use App\Library\MyNumber;
use App\Library\MyDate;
use \Mike42\Escpos\Printer;
use \Mike42\Escpos\PrintConnectors\FilePrintConnector;
use \Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use \Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use \Mike42\Escpos\EscposImage;
use \Mike42\Escpos\CapabilityProfile;
use stdClass;
use File;

class TransactionHistoryController extends Controller
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
        $this->middleware('auth');

        if (request()->ip() == env('PC_1') || request()->ip() == '::1')
            self::$printer_ip = env('PRINTER_CASHIER_1', '192.168.0.11');
        else
            self::$printer_ip = env('PRINTER_CASHIER_2', '192.168.0.15');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $total_transaction = new stdClass;

        // $transaction = Transaction::all();
        $where = [];
        if (isset($_GET['date_start']) && !empty($_GET['date_start'])) {
            $date1 = date('Y-m-d', strtotime($_GET['date_start'])) . ' 00:00:00';
            // echo $date1; exit;
            array_push($where,
                ['transaction.created_at', '>=', $date1]
            );

            if (isset($_GET['date_end']) && !empty($_GET['date_end'])) {
                $date2 = date('Y-m-d', strtotime($_GET['date_end'])) . ' 23:59:59';
                array_push($where,
                    ['transaction.created_at', '<=', $date2]
                );
            } else {
                $date2 = date('Y-m-d', strtotime($_GET['date_start'])) . ' 23:59:59';
                array_push($where,
                    ['transaction.created_at', '<=', $date2]
                );
            }
        } else {
            $date = date('Y-m-d');
            array_push($where,
                ['transaction.created_at', 'like', '%'.$date.'%']
            );
        }

        // var_dump($where); exit;

        if (isset($_GET['user']) && !empty($_GET['user'])) {
            array_push($where,
                ['user_id', '=', $_GET['user']]
            );
        }

        $total_transaction->total = Transaction::where($where);
        $total_transaction->pending = Transaction::where('status', '=', 'pending')->where($where);
        $total_transaction->finished = Transaction::where('status', '=', 'finished')->where($where);
        $total_transaction->void = Transaction::where('status', '=', 'void')->where($where);
        $total_transaction->lost = Transaction::where('status', '=', 'lost')->where($where);
        $total_transaction->printbill = Transaction::where('status', '=', 'printbill')->where($where);

        if (isset($_GET['sid']) && !empty($_GET['sid'])) {
            array_push($where,
                ['status', '=', $_GET['sid']]
            );
        }

        DB::statement(DB::raw('set @rownum=0'));
        $_transaction = Transaction::select([
                            DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                            'transaction.*',
                        ])
                        ->where($where)
                        ->orderBy('transaction_id', 'DESC');

        $grand_total = $_transaction->sum('grand_total');
        $count_trans = $_transaction->count('transaction_id');
        $transaction = $_transaction->paginate($this->pageSize);
        // $user = User::whereIn('role_id', ['1','5'])->pluck('name', 'user_id');
        $user = User::pluck('name', 'user_id');
        $user->prepend('- All -', 0);
        // dd($_transaction->toSql()); exit;

        return view('transaction.history', compact('transaction', 'total_transaction', 'user', 'grand_total', 'count_trans'))
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
        $order = Transaction::where(['transaction_id'=>$id])->first();
        if ($order) {

            $printer_model = PrinterModel::where(['printer_id'=>1]);
            if ($printer_model->count() > 0) {
                $p_item = $printer_model->first();

                $connector = new NetworkPrintConnector(self::$printer_ip, 9100);
                // $connector = new NetworkPrintConnector($p_item->address, $p_item->port);
                $printer = new Printer($connector);

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

                $trans_id = preg_replace("/[^0-9]/", "", $order->created_at);
                $dt = MyDate::toReadableDate($order->created_at, false, true);
                $table = '-';
                if ($order->table)
                    $table = $order->table->number;

                $pm = '-';
                if ($order->payment_method_id != null)
                    $pm = $order->PaymentMethod->name;

                $kasir = auth()->user()->name;

                $note = '';
                if ($order->note != '')
                    $note = $order->note;

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
                try {
                    $total = MyNumber::toReadableAngka($order->total, false);
                    $printer -> setJustification(Printer::JUSTIFY_RIGHT);
                    // $printer -> text("Total     $total\n");
                    $printer -> setJustification(); // Reset
                } catch (Exception $e) {
                    /* Images not supported on your PHP, or image file not found */
                    $printer -> text($e -> getMessage() . "\n");
                }

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

                try {
                    $printer -> setJustification(Printer::JUSTIFY_CENTER);
                    $printer -> text("\n");
                    $printer -> text(Branch::address_pluit());
                    $printer -> text("Terima Kasih\n");
                    $printer -> setJustification(); // Reset
                    // $printer -> cut();
                } catch (Exception $e) {
                    /* Images not supported on your PHP, or image file not found */
                    $printer -> text($e -> getMessage() . "\n");
                }

                $printer -> feed(2);
                $printer -> cut();
                $printer -> close();
            }

            Session::flash('message', 'Printed successfully!');
            return Redirect::back();
        } else {
            Session::flash('error', 'Print failed!');
            return Redirect::back();
        }

    }

    public function excelStockOut(Request $request) {
        $excel_date = '';
        $where = [];
        if (isset($_GET['date']) && !empty($_GET['date'])) {
            $date = date('Y-m-d', strtotime($_GET['date']));
            $excel_date = ' - ' . $_GET['date'];
            array_push($where,
                ['transaction.created_at', 'like', '%'.$date.'%']
            );
        }

        if (isset($_GET['user']) && !empty($_GET['user'])) {
            array_push($where,
                ['user_id', '=', $_GET['user']]
            );
        }

        if (isset($_GET['sid']) && !empty($_GET['sid'])) {
            array_push($where,
                ['status', '=', $_GET['sid']]
            );
        }

        $stock_out = TransactionDetail::select(['product.name as Name', DB::raw('quantity as Quantity')])
            ->leftJoin('transaction', 'transaction.transaction_id', '=', 'transaction_detail.transaction_id')
            ->leftJoin('product', 'product.product_id', '=', 'transaction_detail.product_id')
            ->groupBy('product.product_id')->get()->toArray();

        Excel::create('Padang Merdeka - Stock Out' . $excel_date, function($excel) use($stock_out) {
            $excel->sheet('Stock Out', function($sheet) use($stock_out) {
                // $sheet->mergeCells('A1:C1');

                $sheet->cell('A1:B1', function($cell) {
                    $cell->setAlignment('center');
                });

                $sheet->fromArray($stock_out);
                $sheet->setAllBorders('solid');

            });
        })->export('xls');

        return Redirect::back();
    }

    public function excelReport(Request $request) {
        // return view('transaction.excel-report', compact('items'));
        $where = [];
        $excel_date = '';
        if (isset($_GET['date_start']) && !empty($_GET['date_start'])) {
            $date = date('Y-m-d', strtotime($_GET['date_start'])) . ' 00:00:00';
            $excel_date = $_GET['date_start'];
            array_push($where,
                ['transaction.created_at', '>=', $date]
            );

            if (isset($_GET['date_end']) && !empty($_GET['date_end'])) {
                $date = date('Y-m-d', strtotime($_GET['date_end'])) . ' 23:59:59';
                $excel_date .= ' - ' . $_GET['date_end'];
                array_push($where,
                    ['transaction.created_at', '<=', $date]
                );
            } else {
                $date = date('Y-m-d', strtotime($_GET['date_start'])) . ' 23:59:59';
                array_push($where,
                    ['transaction.created_at', '<=', $date]
                );
            }
        } else {
            $date = date('Y-m-d');
            $excel_date = date('d-m-Y');
            array_push($where,
                ['transaction.created_at', 'like', '%'.$date.'%']
            );
        }

        if (isset($_GET['user']) && !empty($_GET['user'])) {
            array_push($where,
                ['user_id', '=', $_GET['user']]
            );
        }

        if (isset($_GET['sid']) && !empty($_GET['sid'])) {
            array_push($where,
                ['status', '=', $_GET['sid']]
            );
        }

        Excel::create('Report '. $excel_date, function($excel) use($where, $excel_date) {

            $excel->sheet('Menu Items', function($sheet) use($where, $excel_date) {

                $items = TransactionDetail::select([
                        DB::raw('product.name'),
                        DB::raw('transaction_detail.price'),
                        DB::raw('sum(transaction_detail.quantity) as qty'),
                        DB::raw('sum(transaction_detail.subtotal) as sbt')
                    ])
                    ->leftJoin('product', 'product.product_id', '=', 'transaction_detail.product_id')
                    ->leftJoin('transaction', 'transaction.transaction_id', '=', 'transaction_detail.transaction_id')
                    ->groupBy('product.product_id', 'transaction_detail.price')
                    ->where($where)
                    ->get();

                $sheet->loadView('transaction.excel-report-items', compact('items', 'excel_date'));

            });

            $excel->sheet('Finished Transaction', function($sheet) use($where, $excel_date) {

                $transaction_get = Transaction::selectRaw('*, sum(grand_total) as sum')
                        ->with('paymentMethod')
                        ->where([
                            ['status', '=', 'finished']
                        ])
                        ->where($where)
                        ->groupBy('transaction_id')
                        ->get();

                $sheet->loadView('transaction.excel-report-transaction-detail', compact('transaction_get', 'excel_date'));

            });

            $excel->sheet('Void Transaction', function($sheet) use($where, $excel_date) {

                $transaction_get = Transaction::selectRaw('*, sum(grand_total) as sum')
                        ->with('paymentMethod')
                        ->where([
                            ['status', '=', 'void']
                        ])
                        ->where($where)
                        ->groupBy('transaction_id')
                        ->get();

                $sheet->loadView('transaction.excel-report-transaction-detail', compact('transaction_get', 'excel_date'));

            });

            $excel->sheet('Lost Transaction', function($sheet) use($where, $excel_date) {

                $transaction_get = Transaction::selectRaw('*, sum(grand_total) as sum')
                        ->with('paymentMethod')
                        ->where([
                            ['status', '=', 'lost']
                        ])
                        ->where($where)
                        ->groupBy('transaction_id')
                        ->get();

                $sheet->loadView('transaction.excel-report-transaction-detail', compact('transaction_get', 'excel_date'));

            });

            $excel->sheet('Transaction Summary', function($sheet) use($where, $excel_date) {

                // Gross Sales
                $transaction_sales = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
                    ->with('paymentMethod')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->first();
                $t_gross_total = $transaction_sales->sum;

                // Discount Sales
                $transaction_discount = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
                    ->where($where)
                    ->whereRaw('status = "finished" AND promotion_id != 3')
                    ->first();
                $t_discount = $transaction_discount->discount;

                // FOC Sales
                $transaction_foc = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
                    ->where($where)
                    ->whereRaw('status = "finished" AND promotion_id = 3')
                    ->first();
                $t_foc = $transaction_foc->discount;

                // Net Sales
                $t_net_total = $t_gross_total - ($t_discount + $t_foc);

                // Tax
                $t_tax_total = $t_net_total * 0.1;

                // Total Sales
                $t_sales_total = $t_net_total + $t_tax_total;

                // No. of Receipt
                $no_rc = Transaction::selectRaw('count(*) as count, sum(grand_total) as sum')
                    ->where($where)
                    ->whereRaw('status = "finished"')
                    ->first();
                $t_no_of_receipt = $no_rc->count;

                // Avg per Receipt
                $t_avg_receipt = 0;
                if ($no_rc->count > 0)
                    $t_avg_receipt = round($no_rc->sum / $no_rc->count, 2);



                // Sales by Media
                $transaction_by_media = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->groupBy('payment_method_id')
                    ->get();

                $transaction_by_media_total = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->first();


                // Sales by Cashier
                $transaction_by_cashier = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->groupBy('user_id')
                    ->get();

                $transaction_by_cashier_total = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->first();


                // Sales by Price Category
                $transaction_by_pc = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->groupBy('price_category')
                    ->get();

                $transaction_by_pc_total = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->first();


                // Sales by Type
                $transaction_by_type = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->groupBy('type')
                    ->get();

                $transaction_by_type_total = Transaction::selectRaw('*, count(transaction_id) as count, sum(grand_total) as sum')
                    ->with('paymentMethod', 'user')
                    ->where($where)
                    ->where(['status'=>'finished'])
                    ->first();

                $sheet->loadView('transaction.excel-report-transaction',
                    compact('excel_date', 't_gross_total', 't_discount', 't_foc', 't_net_total', 't_tax_total', 't_sales_total', 't_no_of_receipt', 't_avg_receipt', 'transaction_by_media', 'transaction_by_media_total', 'transaction_by_cashier', 'transaction_by_cashier_total', 'transaction_by_pc', 'transaction_by_pc_total', 'transaction_by_type', 'transaction_by_type_total')
                );

            });

        })->export('xls');
    }

    public function csvReport(Request $request)
    {
        // return view('transaction.excel-report', compact('items'));
        $where = [];
        $excel_date = '';
        if (isset($_GET['date_start']) && !empty($_GET['date_start'])) {
            $date = date('Y-m-d', strtotime($_GET['date_start'])) . ' 00:00:00';
            $excel_date = $_GET['date_start'];
            array_push($where,
                ['transaction.created_at', '>=', $date]
            );

            if (isset($_GET['date_end']) && !empty($_GET['date_end'])) {
                $date = date('Y-m-d', strtotime($_GET['date_end'])) . ' 23:59:59';
                $excel_date .= ' - ' . $_GET['date_end'];
                array_push($where,
                    ['transaction.created_at', '<=', $date]
                );
            } else {
                $date = date('Y-m-d', strtotime($_GET['date_start'])) . ' 23:59:59';
                array_push($where,
                    ['transaction.created_at', '<=', $date]
                );
            }
        } else {
            $date = date('Y-m-d');
            $excel_date = date('d-m-Y');
            array_push($where,
                ['transaction.created_at', 'like', '%'.$date.'%']
            );
        }

        if (isset($_GET['user']) && !empty($_GET['user'])) {
            array_push($where,
                ['user_id', '=', $_GET['user']]
            );
        }

        if (isset($_GET['sid']) && !empty($_GET['sid'])) {
            array_push($where,
                ['status', '=', $_GET['sid']]
            );
        }

        // Gross Sales
        $transaction_sales = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
            ->with('paymentMethod')
            ->where($where)
            ->where(['status'=>'finished'])
            ->first();
        $t_gross_total = $transaction_sales->sum;

        // Discount Sales
        $transaction_discount = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
            ->where($where)
            ->whereRaw('status = "finished" AND promotion_id != 3')
            ->first();
        $t_discount = $transaction_discount->discount;

        // FOC Sales
        $transaction_foc = Transaction::selectRaw('*, sum(total) as sum, sum(discount) as discount')
            ->where($where)
            ->whereRaw('status = "finished" AND promotion_id = 3')
            ->first();
        $t_foc = $transaction_foc->discount;

        // Net Sales
        $t_net_total = $t_gross_total - ($t_discount + $t_foc);

        // Tax
        $t_tax_total = $t_net_total * 0.1;

        // Total Sales
        $t_sales_total = $t_net_total + $t_tax_total;

        // No. of Receipt
        $no_rc = Transaction::selectRaw('count(*) as count, sum(grand_total) as sum')
            ->where($where)
            ->whereRaw('status = "finished"')
            ->first();
        $t_no_of_receipt = $no_rc->count;

        // Avg per Receipt
        $t_avg_receipt = 0;
        if ($no_rc->count > 0)
            $t_avg_receipt = round($no_rc->sum / $no_rc->count, 2);

        $input_csvdatas = new Csvdatas();
        $input_csvdatas->report_name = 'Report '. $excel_date;
        $input_csvdatas->report_date = date('Y-m-d H:i:s');
        $input_csvdatas->gross_sales = $t_gross_total;
        $input_csvdatas->discount = $t_discount;
        $input_csvdatas->free_of_charge = $t_foc;
        $input_csvdatas->net_sales = $t_net_total;
        $input_csvdatas->tax_ten_percent_total = $t_tax_total;
        $input_csvdatas->no_of_receipt = $t_no_of_receipt;
        $input_csvdatas->average_receipt = $t_avg_receipt;
        $input_csvdatas->total_sales = $t_sales_total;
        $input_csvdatas->save();

        $get = Csvdatas::where('report_name', '=', 'Report '. $excel_date)
                ->orderBy('id', 'desc')
                ->select(
                    'report_name',
                    'report_date',
                    'gross_sales',
                    'discount',
                    'free_of_charge',
                    'net_sales',
                    'tax_ten_percent_total',
                    'no_of_receipt',
                    'average_receipt',
                    'total_sales'
                )
                ->limit(1)
                ->get()
                ->toArray();

        Excel::create('Report '. $excel_date, function($excel) use($get) {
            $excel->sheet('Report', function($sheet) use($get) {
                $sheet->fromArray($get);
            });

        })->export('csv');
    }

}
