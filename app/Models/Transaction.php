<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class Transaction extends Model
{
    protected $fillable = array('table_id', 'user_id', 'payment_method_id', 'promotion_id', 'type', 'price_category', 'total', 'grand_total', 'discount', 'paid', 'payable', 'created_at', 'updated_at', 'note', 'name', 'remarks', 'status');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'transaction';
    protected $primaryKey  = 'transaction_id';
    // public $timestamps  = false;

    public function detail() {
        return $this->hasMany('App\Models\TransactionDetail', 'transaction_id', 'transaction_id');
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }

    public function voidBy() {
        return $this->hasOne('App\Models\User', 'user_id', 'void_by');
    }

    public function table() {
        return $this->hasOne('App\Models\Table', 'table_id', 'table_id');
    }

    public function paymentMethod() {
        return $this->hasOne('App\Models\PaymentMethod', 'payment_method_id', 'payment_method_id');
    }

    public function promotion() {
        return $this->hasOne('App\Models\Promotion', 'promotion_id', 'promotion_id');
    }

    public function code() {
        $date = new DateTime($this->created_at);
        return $date->format('ym'). str_pad($this->transaction_id, 8, '0', STR_PAD_LEFT);
    }
}
