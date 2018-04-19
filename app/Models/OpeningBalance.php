<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningBalance extends Model
{
    protected $fillable = array('user_id', 'date', 'open_at', 'close_at', 'balance');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'opening_balance';
    protected $primaryKey  = 'opening_balance_id';
    public $timestamps  = false;
    
    public function user() {
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }
}
