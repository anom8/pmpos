<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Serve;
use App\Library\MyNumber;
use App\Library\MyDate;
use \Mike42\Escpos\Printer;
use \Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use \Mike42\Escpos\EscposImage;

use DB;

class TestController extends Controller
{
    private static $printer_ip = '192.168.0.10';

    public function __construct()
    {
        if (request()->ip() == env('PC_1') || request()->ip() == '::1') {
            self::$printer_ip = env('PRINTER_CASHIER_1', '192.168.0.11');
        } else {
            self::$printer_ip = env('PRINTER_CASHIER_2', '192.168.0.15');
        }
    }

    public function bill(Request $request)
    {
        $transaction = (
            Transaction::where('transaction_id', $request->transaction_id)
                ->first()
        );
        $transaction->status = 'printbill';

        if ($transaction->save()) {
            $errorPrinter = [];

            $order_id = $transaction->code();
            $table_id = $transaction->table_id ?
                $transaction->table_id :
                '-';
            $type = $transaction->type === 'disajikan' ?
                'Dine In (HIDANG)' :
                ($transaction->type === 'rames' ?
                'Dine In (KHUSUS)' :
                'Takeaway');

            $cashier = $request->admin_name;
            $datetime = MyDate::toReadableDate($transaction->created_at, false, true);
            $customer = $transaction->name ?
                $transaction->name :
                null;

            $transaction_detail = (
                TransactionDetail::where('transaction_id', $request->transaction_id)
                    ->get()
            );

            $orders = [];

            foreach ($transaction_detail as $key => $value) {
                $qty = str_pad($value->quantity, (3 - strlen($value->quantity)), ' ', STR_PAD_LEFT);
                $name = $value->product->name;
                $space = (48 - (strlen($qty) + strlen($name) + 1) );
                $subt = str_pad(MyNumber::toReadableAngka($value->subtotal, false), $space, ' ', STR_PAD_LEFT);

                if ($value->note) {
                    $orders[] = (
                        $qty . ' ' . $name . $subt . "\n" .
                        '    * ' . $value->note . "\n"
                    );
                } else {
                    $orders[] = $qty . ' ' . $name . $subt . "\n";
                }
            }

            $subtotal = str_pad(MyNumber::toReadableAngka($transaction->total, false), (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);

            if ($transaction->promotion) {
                if ($transaction->promotion->type === 'percent') {
                    $discValue = round($transaction->promotion->value) . "%";
                    $discAmount = MyNumber::toReadableAngka($transaction->total * ($transaction->promotion->value / 100), false);
                    $discEffect = $transaction->total * ($transaction->promotion->value / 100);
                    $tax = MyNumber::toReadableAngka((($transaction->total - $discEffect) * 0.1) , false);
                } else {
                    $discValue = '';
                    $discAmount = MyNumber::toReadableAngka($transaction->promotion->value, false);
                    $tax = MyNumber::toReadableAngka((($transaction->total - $transaction->promotion->value) * 0.1) , false);
                }

                $discount = str_pad('- ' . $discAmount, (30 - (strlen('Diskon (') + strlen($discValue . ')')) ), ' ', STR_PAD_LEFT);
            } else {
                $discount = null;
                $tax = MyNumber::toReadableAngka(($transaction->total * 0.1) , false);
            }

            $totalTax = str_pad('+ ' . $tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
            $grandTotal = str_pad(MyNumber::toReadableAngka($transaction->grand_total, false), (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);

            try {
                $connector = new NetworkPrintConnector(self::$printer_ip, '9100');
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->graphics(EscposImage::load('img/logostruk.png'));
                $printer->setJustification();
                $printer->feed(2);
                $printer->text('Order #         : ' . $order_id . "\n");
                $printer->text('Meja            : ' . $table_id . "\n");
                $printer->text('Tipe            : ' . $type . "\n");
                $printer->text('Kasir           : ' . $cashier . "\n");
                $printer->text('Tanggal         : ' . $datetime . "\n");

                if ($customer) {
                    $printer->text('Name            : ' . $customer . "\n");
                }

                $printer->text('------------------------------------------------' . "\n\n");

                if ($orders) {
                    foreach ($orders as $key => $order) {
                        $printer->text($order);
                    }
                }

                $printer->text("\n" . '------------------------------------------------' . "\n");
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text('Subtotal' . $subtotal . "\n");

                if ($discount) {
                    $printer->text('Diskon (' . $discValue . ')' . $discount . "\n");
                }

                $printer->text('Pajak (10%)' . $totalTax . "\n\n");
                $printer->text('Grand Total' .  $grandTotal . "\n");
                $printer->setJustification();
                $printer->text('------------------------------------------------' . "\n\n");
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text(Branch::address_pluit());
                $printer->text('Terima Kasih' . "\n");
                $printer->setJustification();
                $printer->feed(2);
                $printer->cut();
            } catch(\Exception $e) {
                $errorPrinter[] = $e->getMessage();
            } finally {
                $printer->close();
            }

            return response()->json([
                'status'         => true,
                'transaction_id' => $transaction->transaction_id,
                'table_id'       => $table_id,
                'error_printer'  => $errorPrinter
            ], 200);
        } else {
            return response()->json([
                'status' => false
            ], 500);
        }
    }

    public function finished(Request $request)
    {
        $transaction = (
            Transaction::where('transaction_id', $request->transaction_id)
            ->first()
        );
        $transaction->status = 'finished';

        if ($transaction->save()) {
            $errorPrinter = [];

            $order_id = $transaction->code();
            $table_id = $transaction->table_id ?
                $transaction->table_id :
                '-';
            $type = $transaction->type === 'disajikan' ?
                'Dine In (HIDANG)' :
                ($transaction->type === 'rames' ?
                'Dine In (KHUSUS)' :
                'Takeaway');
            $cashier = $request->admin_name;
            $datetime = MyDate::toReadableDate($transaction->created_at, false, true);
            $payment = $transaction->PaymentMethod->name;
            $note = $transaction->note ?
                $transaction->note :
                null;
            $customer = $transaction->name ?
                $transaction->name :
                null;

            $transaction_detail = (
                TransactionDetail::where('transaction_id', $request->transaction_id)
                    ->get()
            );

            $orders = [];

            foreach ($transaction_detail as $key => $value) {
                $qty = str_pad($value->quantity, (3 - strlen($value->quantity)), ' ', STR_PAD_LEFT);
                $name = $value->product->name;
                $space = (48 - (strlen($qty) + strlen($name) + 1) );
                $subt = str_pad(MyNumber::toReadableAngka($value->subtotal, false), $space, ' ', STR_PAD_LEFT);

                if ($value->note) {
                    $orders[] = (
                        $qty . ' ' . $name . $subt . "\n" .
                        '    * ' . $value->note . "\n"
                    );
                } else {
                    $orders[] = $qty . ' ' . $name . $subt . "\n";
                }
            }

            $subtotal = str_pad(MyNumber::toReadableAngka($transaction->total, false), (30 - strlen('Subtotal')), ' ', STR_PAD_LEFT);

            if ($transaction->promotion) {
                if ($transaction->promotion->type === 'percent') {
                    $discValue = round($transaction->promotion->value) . "%";
                    $discAmount = MyNumber::toReadableAngka($transaction->total * ($transaction->promotion->value / 100), false);
                    $discEffect = $transaction->total * ($transaction->promotion->value / 100);
                    $tax = MyNumber::toReadableAngka((($transaction->total - $discEffect) * 0.1) , false);
                } else {
                    $discValue = '';
                    $discAmount = MyNumber::toReadableAngka($transaction->promotion->value, false);
                    $tax = MyNumber::toReadableAngka((($transaction->total - $transaction->promotion->value) * 0.1) , false);
                }

                $discount = str_pad('- ' . $discAmount, (30 - (strlen('Diskon (') + strlen($discValue . ')')) ), ' ', STR_PAD_LEFT);
            } else {
                $discount = null;
                $tax = MyNumber::toReadableAngka(($transaction->total * 0.1) , false);
            }

            $totalTax = str_pad('+ ' . $tax, (30 - strlen('Pajak (10%)')), ' ', STR_PAD_LEFT);
            $grandTotal = str_pad(MyNumber::toReadableAngka($transaction->grand_total, false), (30 - strlen('Grand Total')), ' ', STR_PAD_LEFT);
            $pay = str_pad(MyNumber::toReadableAngka($transaction->paid, false), (30 - strlen('Bayar')), ' ', STR_PAD_LEFT);
            $payable = str_pad(MyNumber::toReadableAngka($transaction->payable, false), (30 - strlen('Kembali')), ' ', STR_PAD_LEFT);

            try {
                $connector = new NetworkPrintConnector(self::$printer_ip, '9100');
                $printer = new Printer($connector);

                if ($orders) {
                    $i = 1;
                } else {
                    $i = 2;
                }

                for ($i; $i <= 2; $i++) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->graphics(EscposImage::load('img/logostruk.png'));
                    $printer->setJustification();
                    $printer->feed(2);
                    $printer->text('Order #         : ' . $order_id . "\n");
                    $printer->text('Meja            : ' . $table_id . "\n");
                    $printer->text('Tipe            : ' . $type . "\n");
                    $printer->text('Kasir           : ' . $cashier . "\n");
                    $printer->text('Tanggal         : ' . $datetime . "\n");

                    if ($payment) {
                        $printer->text('Pembayaran      : ' . $payment . "\n");
                    }

                    if ($note) {
                        $printer->text('Catatan         : ' . $note . "\n");
                    }

                    if ($customer) {
                        $printer->text('Name            : ' . $customer . "\n");
                    }


                    $printer->text('------------------------------------------------' . "\n\n");

                    if ($orders) {
                        foreach ($orders as $key => $order) {
                            $printer->text($order);
                        }
                    }

                    $printer->text("\n" . '------------------------------------------------' . "\n");
                    $printer->setJustification(Printer::JUSTIFY_RIGHT);
                    $printer->text('Subtotal' . $subtotal . "\n");

                    if ($discount) {
                        $printer->text('Diskon (' . $discValue . ')' . $discount . "\n");
                    }

                    $printer->text('Pajak (10%)' . $totalTax . "\n\n");
                    $printer->text('Grand Total' .  $grandTotal . "\n\n");
                    $printer->text('Bayar' .  $pay . "\n");
                    $printer->text('Kembali' .  $payable . "\n");
                    $printer->setJustification();
                    $printer->text('------------------------------------------------' . "\n\n");
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text(Branch::address_pluit());
                    $printer->text('Terima Kasih' . "\n");
                    $printer->setJustification();
                    $printer->feed(2);
                    $printer->cut();
                }
            } catch(\Exception $e) {
                $errorPrinter[] = $e->getMessage();
            } finally {
                $printer->close();
            }

            return response()->json([
                'status'         => true,
                'transaction_id' => $transaction->transaction_id,
                'table_id'       => $table_id,
                'error_printer'  => $errorPrinter
            ], 200);
        } else {
            return response()->json([
                'status' => false
            ], 500);
        }
    }

    public function send(Request $request)
    {
        $transaction = (
            Transaction::where('transaction_id', $request->transaction_id)
            ->first()
        );

        if ($transaction) {
            $errorPrinter = [];

            $table = $transaction->table_id ?
                '"MEJA '. $transaction->table_id . '"':
                '"TAKEAWAY"';
            $cashier = $request->admin_name;
            $datetime = MyDate::toReadableDate($transaction->created_at, false, true);
            $customer = $transaction->name ?
                $transaction->name :
                null;

            $transaction_detail = (
                TransactionDetail::where('transaction_id', $request->transaction_id)
                    ->get()
            );

            $orders = [];

            foreach ($transaction_detail as $key => $value) {
                if ($value->product->sub_category->category->category_id === 2) {
                    $serve = (
                        Serve::where('transaction_id', $value->transaction_id)
                        ->where('product_id', $value->product_id)
                        ->first()
                    );

                    if ($serve) {
                        $sentQty = $value->quantity - $serve->qty;

                        DB::table('serve')
                            ->where('serve_id', $serve->serve_id)
                            ->update([
                                'qty' => $value->quantity
                            ]);
                    } else {
                        $sentQty = $value->quantity;

                        DB::table('serve')->insert([
                            'transaction_id' => $value->transaction_id,
                            'product_id' => $value->product_id,
                            'qty' => $sentQty
                        ]);
                    }

                    if ($sentQty) {
                        $qty = str_pad($sentQty, (3 - strlen($sentQty)), ' ', STR_PAD_LEFT);
                        $name = $value->product->name;

                        if ($value->note) {
                            $orders[] = (
                                $qty . ' ' . $name . "\n" .
                                '    * ' . $value->note . "\n"
                            );
                        } else {
                            $orders[] = $qty . ' ' . $name . "\n";
                        }
                    }
                }
            }

            if ($orders) {
                try {
                    $connector = new NetworkPrintConnector('192.168.0.12', '9100');
                    $printer = new Printer($connector);
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
                    $printer->text($table . "\n\n");
                    $printer->selectPrintMode();
                    $printer->setJustification();
                    $printer->text('Kasir           : ' . $cashier . "\n");
                    $printer->text('Tanggal         : ' . $datetime . "\n");

                    if ($customer) {
                        $printer->text('Name            : ' . $customer . "\n");
                    }

                    $printer->text('------------------------------------------------' . "\n\n");
                    $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);

                    foreach ($orders as $key => $order) {
                        $printer->text($order);
                    }

                    $printer->selectPrintMode();
                    $printer->text("\n" . '------------------------------------------------' . "\n");
                    $printer->setJustification();
                    $printer->feed(2);
                    $printer->cut();
                } catch(\Exception $e) {
                    $errorPrinter[] = $e->getMessage();
                } finally {
                    $printer->close();
                }
            }

            return response()->json([
                'status'         => true,
                'transaction_id' => $transaction->transaction_id,
                'table_id'       => $transaction->table_id,
                'error_printer'  => $errorPrinter
            ], 200);
        } else {
            return response()->json([
                'status' => false
            ], 500);
        }
    }
}
