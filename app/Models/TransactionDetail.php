<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = array('transaction_id', 'product_id', 'quantity', 'price', 'note', 'void_by', 'subtotal');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'transaction_detail';
    protected $primaryKey  = 'transaction_detail_id';
    public $timestamps  = false;

    public function transaction() {
        return $this->hasOne('App\Models\Transaction', 'transaction_id', 'transaction_id');
    }

    public function product() {
        return $this->hasOne('App\Models\Product', 'product_id', 'product_id');
    }
}
