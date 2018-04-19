<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = array('branch_id', 'number');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'table';
    protected $primaryKey  = 'table_id';
    public $timestamps  = false;

    public function branch() {
        return $this->hasOne('App\Models\Branch', 'branch_id', 'branch_id');
    }

    public function transaction() {
        return $this->hasOne('App\Models\Transaction', 'table_id', 'table_id');
    }
}
