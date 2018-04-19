<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = array('name');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'payment_method';
    protected $primaryKey  = 'payment_method_id';
    public $timestamps  = false;
}
