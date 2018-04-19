<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // if(Auth::user()==NULL || Auth::user()->id_editor_role>$role)
        //     return Redirect::to('/');

        if(Auth::user()==NULL) {
            \Session::flash('message', 'Silahkan <strong>Login</strong> terlebih dahulu.');
            return Redirect::to('login?ref='. \Request::path());
        }
        else {
            if(Auth::user()->id_usr_role>$role)
                return Redirect::to('/');
            // else
            //     return Redirect::to('login?ref='. \Request::path());
        }

        return $next($request);
    }
}
