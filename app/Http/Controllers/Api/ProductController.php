<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Library\UrlHelper;
use App\Models\Product;

class ProductController extends Controller
{
    // private $file_path = 'product';

    public function getList(Request $request) {
		$getDB = Product::select([
            'product_id',
            'branch_id',
            'rfid_code',
            'name',
            'price',
            'stock'
        ])->with('branch.store')
        ->orderBy('name', 'asc');

        if(isset($_GET['limit']))
			$getDB->limit($_GET['limit']);

        $result = $getDB->get();

        return response()->json([
            'data'    => $result,
            'count'   => $getDB->count(),
            'message' => 'Success',
            'status'  => true
        ]);
    }

    public function getListRfid(Request $request) {
        $getDB = (
            DB::table('rfid_product')
                ->select([
                    'rfid_code',
                    'product_id'
                ])
                ->orderBy('product_id', 'ASC')
                ->get()
        );

        return response()->json([
            'data'    => $getDB,
            'count'   => $getDB->count(),
            'message' => 'Success',
            'status'  => true
        ]);
    }
}
