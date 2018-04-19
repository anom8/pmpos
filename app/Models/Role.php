<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = array('name');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'role';
    protected $primaryKey  = 'role_id';
    public $timestamps  = false;
}
