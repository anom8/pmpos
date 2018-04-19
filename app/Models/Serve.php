<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serve extends Model
{
    protected $table = 'serve';

    protected $fillable = [
        'transaction_detail_id', 'transaction_id', 'qty'
    ];
}
