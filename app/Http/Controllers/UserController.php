<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Library\Point as PointLibrary;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use File;
use Auth;
use stdClass;

class UserController extends Controller
{
    private $pageSize = 10;
    private $file_path = 'user';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $usr = User::all();
        $total_user = new stdClass;
        $total_user->active = User::where('status', '=', 1)->count();
        $total_user->inactive = User::where('status', '=', 0)->count();
        $total_user->total = $total_user->active + $total_user->inactive;

        DB::statement(DB::raw('set @rownum=0'));
        $_usr = User::select([
                    DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                    'user.*',
                ])
                ->orderBy('user_id', 'DESC');

        if(isset($_GET['sid'])) {
            if(isset($_GET['search']) && $_GET['search']!="") {
                $_usr->where([
                    ['status', '=', $_GET['sid']],
                    ['name', 'like', '%'.$_GET['search'].'%'],
                ])->orWhere([
                    ['status', '=', $_GET['sid']],
                    ['email', 'like', '%'.$_GET['search'].'%'],
                ]);
            } else {
                $_usr->where([
                    ['status', '=', $_GET['sid']],
                ]);
            }
        } else {
            if(isset($_GET['search']) && $_GET['search']!="") {
                $_usr->where([
                    ['name', 'like', '%'.$_GET['search'].'%'],
                ])->orWhere([
                    ['email', 'like', '%'.$_GET['search'].'%'],
                ]);
            }
        }

        $usr = $_usr->paginate($this->pageSize);

        // return view('user.list', compact('slide'));
        return view('user.list',compact('usr', 'total_user'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = Role::orderBy('name', 'asc')->pluck('name', 'role_id');
        return view('user.create', compact('role'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'email' => 'required|email|unique:user',
            // 'phone' => 'required|min:7',
            'password' => 'required|min:4',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return Redirect::back()
                ->withInput()
                ->withErrors($validator);
        } else {
            $usr = new User;

            if(Auth::user()->id_userrole==0)
                $status = $request->status;
            else
                $status = 0;

            $usr->name = $request->name;
            $usr->role_id = $request->role_id;
            $usr->email = $request->email;
            $usr->phone = $request->phone;
            $usr->password = bcrypt($request->password);
            $usr->token = csrf_token();
            $usr->status = $status;
            $usr->save();

            // sending back with message
            Session::flash('message', 'User created successfully!');
            return Redirect::to('user');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $point = PointLibrary::getTotalPoint($id);
        $usrPost = UsrPost::where(['id_usr'=>$id])->orderBy('id_userpost','DESC')->paginate($this->pageSize);
        $countUsrPost = UsrPost::where(['id_usr'=>$id])->count();
        $usr = User::join('userprofile', 'userprofile.id_usr', '=', 'user.id')->where('user_id', $id)->first();
        return view('user.show',compact('usr', 'point', 'usrPost', 'countUsrPost'))
            ->with('i', ($request->input('page', 1) - 1) * $this->pageSize);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::orderBy('name', 'asc')->pluck('name', 'role_id');
        $usr = User::where('user_id', $id)->first();
        return view('user.edit', compact('usr', 'role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
            'name' => 'required',
            'email' => 'required|email',
            // 'phone' => 'required|numeric|min:7',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            $usr = User::where('user_id', $id)->first();
            return Redirect::back()
                ->withInput()
                ->withErrors($validator);
        } else {
            $usr = User::where('user_id', $id);

            if(isset($request->password) && $request->password!="")
                $new_password = bcrypt($request->password);
            else
                $new_password = $usr->first()->password;

            if(Auth::user()->id_userrole==0)
                $status = $request->status;
            else
                $status = $usr->first()->status;

            $usr->update([
                'role_id' => $request->role_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $new_password,
                'status' => $status,
            ]);

            Session::flash('success', 'User successfully updated');
            return Redirect::to('user');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('user_id', $id)->delete();

        Session::flash('message', 'User deleted successfully!');
        return Redirect::back();
    }
}
