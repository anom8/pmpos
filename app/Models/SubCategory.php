<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = array('category_id', 'name');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'sub_category';
    protected $primaryKey  = 'sub_category_id';
    public $timestamps  = false;

    public function category() {
        return $this->hasOne('App\Models\Category', 'category_id', 'category_id');
    }        
}
