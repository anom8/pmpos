<?php
namespace App\Library;

use Illuminate\Support\Facades\DB;
use App\Models\Event;

class MyEvent {
	public static function count() {
        $event = Event::where('status', 1)->whereRaw(DB::raw('DATE(date_start) > "'. date('Y-m-d') .'"'))->count();
        return $event;
    }
}