<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Library\UrlHelper;
use App\Models\User;

class UserController extends Controller
{
    use AuthenticatesUsers;

    public function login(Request $request) {
        $data = $request->json()->all();
        $validation = Validator::make($data, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => $validation->getMessageBag()->all(),
                'status' => false,
            ]);
        } else {

            $userdata = array(
                'email'     => $request->email,
                'password'  => $request->password
            );

            if (Auth::attempt($userdata)) {
                $user = User::where(['email'=>$request->email])->with('branch.store');
                if ($user->count() > 0) {
                    $user_fetch = $user->first();
                    $user_fetch->api_token = md5(date('YmdHis').str_random(60));
                    $user_fetch->save();
                    return response()->json([
                        'data' => [
                            'id' => $user_fetch->user_id,
                            'name' => $user_fetch->name,
                            'email' => $user_fetch->email,
                            'api_token' => $user_fetch->api_token,
                            'branch' => $user_fetch->branch,
                            // 'store' => $user_fetch->branch->store
                        ],
                        'message' => 'Login success!',
                        'status' => true,
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Login failed!',
                    'status' => false,
                ]);
            }

            // $user = User::where(['email'=>$request->email])->with('branch.store');
            // if ($user->count() > 0) {
            //     $user_fetch = $user->first();

            //     try {
            //         $decrypted = decrypt($user_fetch->password);
            //         if($request->password !== $decrypted) {
            //             return response()->json([
            //                 'message' => 'Login gagal!',
            //                 'status' => false,
            //             ]);
            //         } else {
            //             $user_fetch->api_token = md5(date('YmdHis')."-".$user_fetch->email);
            //             $user_fetch->save();
            //             return response()->json([
            //                 'data' => [
            //                     'id' => $user_fetch->user_id,
            //                     'name' => $user_fetch->name,
            //                     'email' => $user_fetch->email,
            //                     'api_token' => $user_fetch->token,
            //                     'branch' => $user_fetch->branch,
            //                     // 'store' => $user_fetch->branch->store
            //                 ],
            //                 'message' => 'Login success!',
            //                 'status' => true,
            //             ]);
            //         }
            //     } catch (DecryptException $e) {
            //         // if(md5($request->password) != $getPW->password) {
            //             return response()->json([
            //                 'message' => 'Login failed!',
            //                 'status' => false,
            //             ]);
            //         // }
            //     }
            // }
        }
    }
}
