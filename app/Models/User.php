<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract
{
	use Authenticatable;
    protected $fillable = array('role_id', 'branch_id', 'name', 'email', 'phone', 'password', 'token', 'api_token', 'remember_token', 'last_login', 'status');

    // LINK THIS MODEL TO OUR DATABASE TABLE ---------------------------------
    protected $table = 'user';
    protected $primaryKey  = 'user_id';
    public $timestamps  = false;
    private $file_path = 'user';

    public function thumbnail($size=null) {
        $sizes = ['small', 'medium', 'large'];
        // $av = "assets/images/default_avatar.png";
        $av = env('CDN_URL') .'/default.png';
        if($this->image!=null || $this->image!="") {
            $path = '/';
            if($size!=null && in_array($size, $sizes))
                $path = '/'. $size .'/';

            $av = env('CDN_URL') .'/'. $this->file_path . $path . $this->image;
        }
        return $av;
    }

    public function branch() {
        return $this->hasOne('App\Models\Branch', 'branch_id', 'branch_id');
    }

    public function role() {
        return $this->hasOne('App\Models\Role', 'role_id', 'role_id');
    }
}
