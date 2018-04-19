<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = array('name');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'category';
    protected $primaryKey  = 'category_id';
    public $timestamps  = false;

    
}
