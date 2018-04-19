<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
use App\Models\Product;
use App\Models\Promotion;
use File;

class TransactionController extends Controller
{
    private $pageSize = 10;

    public function addTransaction(Request $request) {
        $data = $request->json()->all();
        $validation = Validator::make($data, [
            'table_id' => 'required',
            'type' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->getMessageBag()->all(),
                'status' => false,
            ]);
        } else {
            $transaction = new Transaction;
            $transaction->type = $data['type'];
            $transaction->table_id = $data['table_id'];
            $transaction->user_id = 3;                              // TODO: Change to login ID
            $transaction->status = 'pending';
            if ($transaction->save()) {
                return response()->json([
                    'data' => $transaction,
                    'message' => 'Transaction successfully created!',
                    'status' => true,
                ]);
            } else {
                return response()->json([
                    'message' => 'Transaction failed to create!',
                    'status' => false,
                    'data' =>null

                ]);
            }
        }
    }

    public function updateTransaction(Request $request) {
        $data = $request->json()->all();

        $validation = Validator::make($data, [
            'transaction_id' => 'required',
            'items'          => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->getMessageBag()->all(),
                'status'  => false
            ]);
        } else {
            $transaction = Transaction::where('transaction_id', $data['transaction_id'])->firstOrFail();

            if ($transaction->status === 'pending') {
                TransactionDetail::where('transaction_id', $data['transaction_id'])->delete();

                $total = 0;

                foreach ($data['items'] as $key => $item) {
                    $product = Product::where('product_id', $item['product_id'])->firstOrFail();

                    $newTD = new TransactionDetail;
                    $newTD->transaction_id = $data['transaction_id'];
                    $newTD->product_id     = $item['product_id'];
                    $newTD->quantity       = $item['quantity'];
                    $newTD->price          = $product->price;
                    $newTD->subtotal       = $item['quantity'] * $product->price;
                    $newTD->save();

                    $subtotal = $item['quantity'] * $product->price;

                    $total += $subtotal;
                }

                if ($transaction->promotion_id !== NULL) {
                    $promotion = Promotion::where('promotion_id', $transaction->promotion_id)->firstOrFail();

                    if ($promotion->type === 'percent') {
                        $discount = $total * $promotion->value / 100;
                    } else if ($promotion->type === 'value') {
                        $discount = $promotion->value;
                    }
                } else {
                    $discount = 0;
                }

                $grand_total = ($total - $discount) * 1.1;

                Transaction::where('transaction_id', $data['transaction_id'])
                    ->update([
                        'total'       => $total,
                        'grand_total' => $grand_total,
                        'discount'    => $discount,
                        'paid'        => 0,
                        'payable'     => 0
                    ]);

                //$updated_transaction = Transaction::where('transaction_id', $data['transaction_id'])->get();
                $updated_transaction = Transaction::where('transaction_id', $data['transaction_id'])->with('detail')->firstOrFail();
                return response()->json([
                    'data'    => $updated_transaction,
                    'count'   => 1,
                    'message' => 'Success',
                    'status'  => true
                ]);
            }
        }
    }

    public function finishTransaction(Request $request) {
        $data = $request->json()->all();
        $validation = Validator::make($data, [
            'transaction_id' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->getMessageBag()->all(),
                'status' => false,
            ]);
        } else {
            $transaction_id = $data['transaction_id'];
            $transaction = Transaction::where(['transaction_id'=>$transaction_id])->with(['table.branch.store', 'detail']);
            if($transaction) {
                $transaction_fetch = $transaction->first();
                $transaction_fetch->status = 'finished';
                if ($transaction_fetch->save()) {
                    return response()->json([
                        'data' => [
                            'transaction' => $transaction_fetch,
                        ],
                        'message' => 'Update success!',
                        'status' => true,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Update failed!',
                        'status' => false,
                    ]);
                }
            }
        }
    }

    public function historyTransaction(Request $request) {
        $data = $request->all();
        $validation = Validator::make($data, [
            'status' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->getMessageBag()->all(),
                'status' => false,
            ]);
        } else {
            $status = $data['status'];
            if ($status=="all" || $status=="")
                $transaction = Transaction::where([])->with(['table.branch.store', 'detail'])->orderBy('created_at','DESC');
            else
                $transaction = Transaction::where(['status'=>$status])->with(['table.branch.store', 'detail'])->orderBy('created_at','DESC');

            if($transaction) {

                return response()->json([
                    'data' => [
                        'transaction' => $transaction->get(),
                    ],
                    'message' => 'Update success!',
                    'status' => true,
                ]);

            } else {
                return response()->json([
                    'message' => 'Update failed!',
                    'status' => false,
                ]);
            }
        }
    }
}
