<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    protected $fillable = array('name', 'address', 'port');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'printer';
    protected $primaryKey  = 'printer_id';
    public $timestamps  = false;
}
