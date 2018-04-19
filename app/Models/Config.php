<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = array('code', 'name', 'value');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'config';
    protected $primaryKey  = 'id_config';
    public $timestamps  = false;
}
