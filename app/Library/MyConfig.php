<?php
namespace App\Library;

use App\Config;

class MyConfig {
	public static function getConfig($code=null) {
		if($code!=null) {
			$config = Config::where('code', $code);
			if($config->count() > 0) {
				return $config->first();
			}
		}

		return false;
	}
}