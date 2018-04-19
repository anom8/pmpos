<?php
namespace App\Library;

use Illuminate\Support\Facades\DB;
use App\Activity as ActivityModel;
use App\UsrActivity as UsrActivityModel;
use App\CategoryType;
use App\UsrActivity;
use App\UsrLike;
use App\UsrPost;
use App\Usr_;
use stdClass;

class Activity {
    private static $short_url;
    private static $avatar_url;
    private static $thumbnail_url;
    private static $thumbnail_lg_url;
    private static $thumbnail_shortir_url;
    private static $logo_url;
    private static $file_path;

    public static function init()
    {
        self::$short_url = "http://redaksi.shortir.com/";
        self::$avatar_url = env('CDN_URL', '') ."/user/avatar/small/";
        self::$thumbnail_url = env('CDN_URL', '') ."/user/post/small/";
        self::$thumbnail_lg_url = env('CDN_URL', '') ."/user/post/large/";
        self::$thumbnail_shortir_url = env('CDN_URL', '') ."/user/post/small/";
        self::$logo_url = env('CDN_URL', '') ."/partner/logo/small/";
        self::$file_path = 'user/post';
    }

	public static function getUsrActivity($id_usr, $limit=null, $user=null) {
		// $avatar_url = "http://xadminx.shortir.com/avatar/small/";
  //       $thumbnail_url = "http://xadminx.shortir.com/thumbnail_post/small/";
        self::init();
		$cek = UsrActivityModel::where('id_usr_to', $id_usr);
		if($cek->count() > 0) {

			// $avatar_url = 'http://xadminx.shortir.com/avatar/small/';
   //          $thumbnail_url = 'http://xadminx.shortir.com/thumbnail_post/small/';
   //          $thumbnail_lg_url = 'http://xadminx.shortir.com/thumbnail_post/large/';
   //          $logo_url = 'http://xadminx.shortir.com/logoMitra/';

            $getDB = $cek->select([
                'usr_.id', 
                'usr_post.id_usr_post as id_post', 
                'usr_post.id_usr', 
                'usr_post.id_media',
                'usr_post.id_media_post',
                'category_type.id_cat', 
                // 'usr_post.title_post', 
                DB::raw("IF(usr_post.published_edit = 0, usr_post.old_title_post, usr_post.title_post) as title_post"),
                // 'usr_post.desc_post as content', 
                DB::raw("IF(usr_post.published_edit = 0, usr_post.old_desc_post, usr_post.desc_post) as content"),
                'category_type.category_name', 
                'usr_post.url_post as link_short', 
                'media_post.url as link_source', 
                'category_type.color', 
                'category_type.BgColor',
                'usr_post.from_usr', 
                DB::raw('DATE_FORMAT(usr_post.log_post,"%Y-%m-%dT%T") AS log_post'),
                'media_post.log_post as log_post_media',
                'usr_.full_name',
                'activity.content as activity',
                'usr_activity.status',
                'usr_activity.id_usr_from',
				DB::raw('DATE_FORMAT(usr_activity.log_activity, "%Y-%m-%dT%T") AS log_activity'),
                'media.url as url_media',
                DB::raw("IF(usr_post.published_edit = 0, CONCAT( '".self::$thumbnail_url."' , usr_post.old_thumbnail_post), CONCAT( '".self::$thumbnail_url."' , usr_post.thumbnail_post)) as thumbnail_post"), 
                DB::raw("IF(usr_post.published_edit = 0, CONCAT( '".self::$thumbnail_lg_url."' , usr_post.old_thumbnail_post), CONCAT( '".self::$thumbnail_lg_url."' , usr_post.thumbnail_post)) as thumbnail_post_large"), 
                // DB::raw("CONCAT( '".$thumbnail_url."' , usr_post.thumbnail_post) as thumbnail_post"), 
                // DB::raw("CONCAT( '".$thumbnail_lg_url."' , usr_post.thumbnail_post) as thumbnail_post_large"), 
                DB::raw("CONCAT( '".self::$avatar_url."' , usr_profile.avatar) as avatar"),
                DB::raw("NULL as video_id"),
                DB::raw("NULL as video_duration"),
                // DB::raw("CONCAT( '".$thumbnail_url."' , usr_post.thumbnail_post) as thumbnail_post"),
                DB::raw("CONCAT( '".self::$avatar_url."' , usr_profile.avatar) as avatar"),
                'media.name as media_name',
                DB::raw("CONCAT( '".self::$logo_url."' , media.logo) as media_logo"),
                // DB::raw("COUNT(usr_likes.id_usr_like) as likes_count"),
            ])
            // ->join('usr_', 'usr_post.id_usr', '=', 'usr_.id')
            ->join('usr_', 'usr_.id', '=', 'usr_activity.id_usr_from')
            ->join('usr_profile', 'usr_profile.id_usr', '=', 'usr_.id')
			->join('usr_post', 'usr_post.id_usr_post', '=', 'usr_activity.id_post')
			->join('activity', 'activity.id_activity', '=', 'usr_activity.id_activity')
            ->join('category_type', 'category_type.id_cat', '=', 'usr_post.id_cat')
            ->leftJoin('media', 'media.id_media', '=', 'usr_post.id_media')
            ->leftJoin('media_post', 'media_post.id_media_post', '=', 'usr_post.id_media_post')
            ->where('id_usr_to', $id_usr)
            ->orderBy('log_activity', 'desc')
            ->orderBy('usr_post.log_post','desc')
            ->orderBy('usr_post.id_usr_post','desc');

            $count = $getDB->count();

            if($limit!=null)
                $getDB = $getDB->limit($limit);

            $result = $getDB->get();


            $counter = 0;
            foreach($result as $res) {
                $arr[$counter] = new stdClass;

                $countLike = UsrLike::where('id_post', $res->id_post)->count();
                if(isset($id_usr) && $id_usr!=null) {
                    $getLike = UsrLike::where('id_post', $res->id_post)
                                ->where('id_usr', $id_usr)
                                ->where('from', 2)
                                ->count();
                    if($getLike > 0)
                        $likes = true;
                    else
                        $likes = false;
                } else 
                    $likes = false;
                    
                $res->likes = $likes;
                $res->likes_count = $countLike;
                $res->edit = true;


                // Profile Likers
                $getPr = Usr_::select([
                        'usr_.id as id_usr',
                        'usr_.full_name',
                        'usr_.email',
                        'usr_.phone',
                        'usr_.banned',
                        'usr_.log',
                        'usr_.latest_post',
                        'usr_.push_notif',
                        'usr_profile.location',
                        'usr_profile.quotes',
                        'usr_profile.website',
                        'usr_profile.gender',
                        DB::raw("CONCAT( '".self::$avatar_url."' , usr_profile.avatar) as avatar"),
                    ])
                    ->leftJoin('usr_profile', 'usr_profile.id_usr', '=', 'usr_.id')
                    ->leftJoin('usr_point', 'usr_point.id_usr', '=', 'usr_.id')
                    ->where([
                        'usr_.id' => $res->id_usr_from,
                    ]);

                $res->log_post_media = ($res->log_post_media!=null) ? self::indonesian_date($res->log_post_media):null;
                $arr[$counter]->data = $res;

                if ($getPr->count() > 0) {
                    $arr[$counter]->profile = $res;
                    $arr[$counter]->profile = $getPr->first();
                    $arr[$counter]->profile->likes = UsrLike::where('id_usr', $res->id_usr_from)->count();
                    $arr[$counter]->profile->posts = UsrPost::where('id_usr', $res->id_usr_from)->count();
                } else {
                    $arr[$counter]->profile = null;
                }

                // Profile Current User
                // Profile Likers
                $getCU = Usr_::select([
                        'usr_.id as id_usr',
                        'usr_.full_name',
                        'usr_.email',
                        'usr_.phone',
                        'usr_.banned',
                        'usr_.log',
                        'usr_.latest_post',
                        'usr_.push_notif',
                        'usr_profile.location',
                        'usr_profile.quotes',
                        'usr_profile.website',
                        'usr_profile.gender',
                        DB::raw("CONCAT( '".self::$avatar_url."' , usr_profile.avatar) as avatar"),
                    ])
                    ->leftJoin('usr_profile', 'usr_profile.id_usr', '=', 'usr_.id')
                    ->leftJoin('usr_point', 'usr_point.id_usr', '=', 'usr_.id')
                    ->where([
                        'usr_.id' => $id_usr,
                    ]);

                $arr[$counter]->activity = new stdClass;
                $arr[$counter]->activity->activity = $res->activity;
                $arr[$counter]->activity->log_activity = $res->log_activity;
                $arr[$counter]->activity->status = $res->status;
                $arr[$counter]->activity->id_usr_from = $res->id_usr_from;
                if ($getCU->count() > 0) {
                    $arr[$counter]->activity->profile = $res;
                    $arr[$counter]->activity->profile = $getCU->first();
                    $arr[$counter]->activity->profile->likes = UsrLike::where('id_usr', $id_usr)->count();
                    $arr[$counter]->activity->profile->posts = UsrPost::where('id_usr', $id_usr)->count();
                } else {
                    $arr[$counter]->activity->profile = null;
                }

                unset($arr[$counter]->data->ativity);
                unset($arr[$counter]->data->log_activity);
                unset($arr[$counter]->data->status);
                unset($arr[$counter]->data->id_usr_from);

                $counter++;
            }

			return ['data'=>$arr, 'count'=>$count];
		} else {
			return false;
		}
	}

	public static function addUsrActivity($id_usr_from, $id_usr_to, $id_activity, $id_post, $from=2) {
		// $avatar_url = "http://xadminx.shortir.com/avatar/small/";
  //       $thumbnail_url = "http://xadminx.shortir.com/thumbnail_post/small/";
        self::init();
		$cek = ActivityModel::where('id_activity', $id_activity);
		if($cek->count() > 0) {
			$get = $cek->first();
			if($id_usr_from==$id_usr_to)
				return false;

			$cekExist = UsrActivityModel::where([
							'id_usr_from' => $id_usr_from,
							'id_usr_to' => $id_usr_to,
							'id_activity' => $id_activity,
							'id_post' => $id_post,
							'from' => $from
						]);

			if($cekExist->count() > 0)
				return false;


			$getId = UsrActivityModel::insertGetId([
	            'id_usr_from' => $id_usr_from,
	            'id_usr_to' => $id_usr_to,
	            'id_activity' => $id_activity,
	            'id_post' => $id_post,
	            'from' => $from,
	            'log_activity' => date('Y-m-d H:i:s')
	        ]);
            
	        $get = UsrActivityModel::select([
				'usr_.id as id_usr',
				'usr_.full_name',
                DB::raw("CONCAT( '".self::$avatar_url."' , usr_profile.avatar) as avatar"),
				'activity.content as activity',
				'usr_activity.id_post',
				'usr_post.title_post',
				DB::raw("CONCAT( '".self::$thumbnail_url."' , usr_post.thumbnail_post) as thumbnail_post"),
				'usr_activity.status',
				DB::raw('DATE_FORMAT(usr_activity.log_activity, "%Y-%m-%dT%T") AS log_activity'),
			])
			->where('id_usr_activity', $getId)
			->join('activity', 'activity.id_activity', '=', 'usr_activity.id_activity')
			->join('usr_', 'usr_.id', '=', 'usr_activity.id_usr_from')
			->join('usr_post', 'usr_post.id_usr_post', '=', 'usr_activity.id_post')
			->join('usr_profile', 'usr_profile.id_usr', '=', 'usr_.id');

			return $get->first();
			// return $getId;
		} else {
			return false;
		}
	}

	public static function indonesian_date ($timestamp = '', $date_format = 'l, j F Y | H:i', $suffix = 'WIB') {
        if (trim ($timestamp) == '')
        {
                $timestamp = time ();
        }
        elseif (!ctype_digit ($timestamp))
        {
            $timestamp = strtotime ($timestamp);
        }
        # remove S (st,nd,rd,th) there are no such things in indonesia :p
        $date_format = preg_replace ("/S/", "", $date_format);
        $pattern = array (
            '/Mon[^day]/','/Tue[^sday]/','/Wed[^nesday]/','/Thu[^rsday]/',
            '/Fri[^day]/','/Sat[^urday]/','/Sun[^day]/','/Monday/','/Tuesday/',
            '/Wednesday/','/Thursday/','/Friday/','/Saturday/','/Sunday/',
            '/Jan[^uary]/','/Feb[^ruary]/','/Mar[^ch]/','/Apr[^il]/','/May/',
            '/Jun[^e]/','/Jul[^y]/','/Aug[^ust]/','/Sep[^tember]/','/Oct[^ober]/',
            '/Nov[^ember]/','/Dec[^ember]/','/January/','/February/','/March/',
            '/April/','/June/','/July/','/August/','/September/','/October/',
            '/November/','/December/',
        );
        $replace = array ( 'Sen','Sel','Rab','Kam','Jum','Sab','Min',
            'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu',
            'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des',
            'Januari','Februari','Maret','April','Juni','Juli','Agustus','Sepember',
            'Oktober','November','Desember',
        );
        $date = date ($date_format, $timestamp);
        $date = preg_replace ($pattern, $replace, $date);
        $date = "{$date} {$suffix}";
        return $date;
    }
}