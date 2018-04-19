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
use App\Models\Table;
use App\Models\Transaction;
use File;

class TableController extends Controller
{
    private $pageSize = 10;

    public function getList(Request $request) {
        $getDB = Table::select([
            'table_id',
            'number',
            'branch_id'
        ])->with('branch.store');

        if(isset($_GET['limit'])) {
            $getDB->limit($_GET['limit']);
        }

        $result = $getDB->get();

        return response()->json([
            'data'    => $result,
            'count'   => $getDB->count(),
            'message' => 'Success',
            'status'  => true
        ]);
    }

    public function getCurrent(Request $request) {
        // $transaction_current = (
        //     Transaction::with('detail')
        //         ->where('type', '!=', 'takeaway')
        //         ->where('status', 'pending')
        //         ->orderBy('transaction_id', 'ASC')
        //         ->get()
        // );
        $table = (
            Table::orderBy('number', 'ASC')
                ->get()
        );
        foreach($table as $keyT => $t) {
            $table[$keyT]['transaction'] = (
                Transaction::with('detail')
                    ->where('table_id', '=', $t->table_id)
                    ->where('type', '!=', 'takeaway')
                    ->where('status', 'pending')
                    ->orderBy('transaction_id', 'ASC')
                    ->get()
            );
        }

        $transaction_current = [];
        foreach($table as $key => $val) {
            $transaction_current[$key]["transaction_id"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['transaction_id'] :
                NULL;
            $transaction_current[$key]["table_id"] = $val->table_id;
            $transaction_current[$key]["user_id"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['user_id'] :
                NULL;
            $transaction_current[$key]["payment_method_id"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['payment_method_id'] :
                NULL;
            $transaction_current[$key]["promotion_id"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['promotion_id'] :
                NULL;
            $transaction_current[$key]["type"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['type'] :
                NULL;
            $transaction_current[$key]["price_category"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['price_category'] :
                NULL;
            $transaction_current[$key]["total"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['total'] :
                NULL;
            $transaction_current[$key]["grand_total"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['grand_total'] :
                NULL;
            $transaction_current[$key]["discount"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['discount'] :
                NULL;
            $transaction_current[$key]["paid"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['paid'] :
                NULL;
            $transaction_current[$key]["payable"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['payable'] :
                NULL;
            $transaction_current[$key]["created_at"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['created_at'] :
                NULL;
            $transaction_current[$key]["updated_at"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['updated_at'] :
                NULL;
            $transaction_current[$key]["void_by"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['void_by'] :
                NULL;
            $transaction_current[$key]["note"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['note'] :
                NULL;
            $transaction_current[$key]["name"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['name'] :
                NULL;
            $transaction_current[$key]["remarks"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['remarks'] :
                NULL;
            $transaction_current[$key]["status"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['status'] :
                NULL;
            $transaction_current[$key]["detail"] = ( !empty($val->transaction) && isset($val->transaction[0] ) ) ?
                $val->transaction[0]['detail'] :
                NULL;
        }

        return response()->json([
            'data'      => $transaction_current,
            // 'count'  => $transaction_current->count(),
            'count'     => count($transaction_current),
            'status'    => true
        ]);
    }

    public function getDetail(Request $request, $id) {
        $getDB = Table::select([
            'table.table_id',
            'number',
            'branch_id'
        ])
        ->with('transaction.detail')
        // ->leftJoin('transaction', 'transaction.table_id', 'table.table_id')
        ->join('transaction', function ($join) {
            $join->on('transaction.table_id', '=', 'table.table_id')
                ->where('transaction.status', '=', 'pending');
        })
        ->where('transaction.table_id', '=', $id)
        ->groupBy('table.table_id')
        ->orderBy('table.number', 'desc');

        if ($getDB->count() > 0) {
            $result = $getDB->first();

            return response()->json([
                'data' => $result,
                'count' => $getDB->count(),
                'status' => true
            ]);
        } else {
            return response()->json([
                'data' => null,
                'count' => $getDB->count(),
                'status' => false
            ]);
        }
    }
}
