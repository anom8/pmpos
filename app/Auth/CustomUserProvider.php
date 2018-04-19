<?php

namespace App\Auth;

use App\Models\User; 
use Carbon\Carbon;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class CustomUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        // TODO: Implement retrieveById() method.


        $qry = User::where('user_id','=',$identifier);

        if($qry->count() >0)
        {
            $user = $qry->select('user_id', 'role_id', 'name', 'phone', 'email', 'password')->first();

            $attributes = array(
                'user_id' => $user->user_id,
                'email' => $user->email,
                'password' => $user->password,
                'name' => $user->name,
            );

            return $user;
        }
        return null;
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
        $qry = User::where('user_id','=',$identifier)->where('token','=',$token);

        if($qry->count() >0)
        {
            $user = $qry->select('user_id', 'id_media', 'user_id_role', 'name', 'email', 'password', 'avatar')->first();

            $attributes = array(
                'user_id' => $user->user_id,
                'email' => $user->email,
                'password' => $user->password,
                'name' => $user->name,
            );

            return $user;
        }
        return null;



    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
        $user->setRememberToken($token);

        $user->save();

    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // TODO: Implement retrieveByCredentials() method.
        $qry = User::where('email', '=', $credentials['email'])->orWhere('token', '=', $credentials['token']);

        if($qry->count() >0)
        {
            $user = $qry->select('user_id', 'role_id', 'name', 'phone', 'email', 'password')->first();
            return $user;
        }
        return null;


    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // TODO: Implement validateCredentials() method.
        // we'll assume if a user was retrieved, it's good

        if($user->email == $credentials['email'] && decrypt($user->getAuthPassword()) == $credentials['password'])
        {

            $user->last_login = Carbon::now();
            $user->save();

            return true;
        }
        return false;
    }
}
