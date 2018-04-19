<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = array('store_id', 'name', 'address');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'branch';
    protected $primaryKey  = 'branch_id';
    public $timestamps  = false;

    public function store() {
        return $this->hasOne('App\Models\Store', 'store_id', 'store_id');
    }

    public static function address_pluit() {
    	$address = "Jl. Pluit Indah Raya No. 34\nJakarta Utara\n(021) 2266 9155\n";
    	return $address;
    }

    public static function address_kemang() {
    	$address = "Jl. Kemang Barat No. 117\nJakarta Selatan\n(021) 8460 485\n\n";
    	return $address;
    }

    public static function address_lada() {
    	$address = "Jl. Lada No. 1, Kota\nJakarta Utara\n(021) 6919811\nwww.padang-merdeka.com\n\n";
    	return $address;
    }
}
