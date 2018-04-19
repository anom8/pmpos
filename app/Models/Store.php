<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = array('name');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'store';
    protected $primaryKey  = 'store_id';
    public $timestamps  = false;

    public function branch() {
        return $this->hasMany('App\Models\Branch', 'store_id', 'store_id');
    }
}
