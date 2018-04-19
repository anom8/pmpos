<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = array('name', 'description', 'type', 'value', 'created_at', 'start_date', 'expired_date', 'status');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'promotion';
    protected $primaryKey  = 'promotion_id';
    public $timestamps  = false;

    public function branch() {
        return $this->hasMany('App\Models\Branch', 'store_id', 'store_id');
    }
}
