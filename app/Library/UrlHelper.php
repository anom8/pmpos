<?php
namespace App\Library;
use App\Ads;

class UrlHelper {
	public static function toUrl($str = '') {
		if ($str === 'http://' OR $str === 'https://' OR $str === '') {
	        return '';
	    }

	    $url = parse_url($str);
	    
	    if ( ! $url OR ! isset($url['scheme'])) {
	        return 'http://'.$str;
	    }

	    return $str;
	}

	public static function redirect($str = '', $id_index=NULL) {
		$str 	= self::toUrl($str);
		$url 	= encrypt($str);
		$date 	= encrypt(date('Y-m-d H:i:s'));
		$token	= substr(md5(uniqid(mt_rand(), true)), 0, 20);
		if($id_index!=NULL) {
			$ads = Ads::where('id_ads', $id_index);
			if($ads->count() > 0) {
				$ads_get = $ads->first();
				$ads->update([
					'count_viewed' => $ads_get->count_viewed + 1
				]);
			}
			$idx = encrypt($id_index);
		}
		else
			$idx = "";

		$redirs	= url("/url?u=". $url ."&d=". $date ."&t=". $token ."&x=". $idx);
		

		return $redirs;

	}
}