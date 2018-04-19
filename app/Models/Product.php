<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = array('branch_id', 'sub_category_id', 'printer_id', 'rfid_code', 'name', 'description', 'price', 'price_gojek', 'stock');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'product';
    protected $primaryKey  = 'product_id';
    public $timestamps  = false;
    // private $file_path = 'product';

    public function branch() {
        return $this->hasOne('App\Models\Branch', 'branch_id', 'branch_id');
    }

    public function sub_category() {
        return $this->hasOne('App\Models\SubCategory', 'sub_category_id', 'sub_category_id');
    }

    public function printer() {
        return $this->hasOne('App\Models\Printer', 'printer_id', 'printer_id');
    }

    // public function thumbnail($size=null) {
    //     $sizes = ['small', 'medium', 'large'];
    //     // $av = "assets/images/default_avatar.png";
    //     $av = env('CDN_URL') .'/default.png';
    //     // if($this->image!=null || $this->image!="") {
    //     //     $path = '/';
    //     //     if($size!=null && in_array($size, $sizes))
    //     //         $path = '/'. $size .'/';

    //     //     $av = env('CDN_URL') .'/'. $this->file_path . $path . $this->image;
    //     // }
    //     return $av;
    // }
}
